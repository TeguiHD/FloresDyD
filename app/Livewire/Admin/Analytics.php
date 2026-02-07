<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Métricas - Flores D&D')]
class Analytics extends Component
{
    public array $summary = [];
    public array $ordersTrend = [];
    public array $topProducts = [];
    public array $popupStats = [];

    public function mount(): void
    {
        $this->loadMetrics();
    }

    private function loadMetrics(): void
    {
        $start14 = now()->subDays(13)->startOfDay();
        $start30 = now()->subDays(30)->startOfDay();

        $orders30d = Order::query()->where('created_at', '>=', $start30)->count();
        $delivered30d = Order::query()
            ->where('created_at', '>=', $start30)
            ->where('status', 'delivered')
            ->count();
        $pendingReview = Order::query()->where('status', 'pending_review')->count();
        $highRisk = Order::query()->highRisk()->count();

        $sessions30d = 0;
        if (Schema::hasTable('sessions')) {
            $sessions30d = (int) DB::table('sessions')
                ->where('last_activity', '>=', $start30->timestamp)
                ->count();
        }

        $conversionRate = $sessions30d > 0 ? round(($orders30d / $sessions30d) * 100, 2) : 0.0;

        $this->summary = [
            ['label' => 'Sesiones (30 días)', 'value' => $sessions30d],
            ['label' => 'Pedidos (30 días)', 'value' => $orders30d],
            ['label' => 'Entregados (30 días)', 'value' => $delivered30d],
            ['label' => 'Conversión estimada', 'value' => $conversionRate . '%'],
            ['label' => 'En revisión', 'value' => $pendingReview],
            ['label' => 'Pedidos alto riesgo', 'value' => $highRisk],
        ];

        $trendRows = Order::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', $start14)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $this->ordersTrend = collect(range(0, 13))
            ->map(function ($offset) use ($start14, $trendRows) {
                $date = $start14->copy()->addDays($offset)->toDateString();
                return [
                    'date' => $date,
                    'label' => \Illuminate\Support\Carbon::parse($date)->format('d M'),
                    'total' => (int) ($trendRows[$date] ?? 0),
                ];
            })
            ->toArray();

        $this->topProducts = Product::query()
            ->orderByDesc('views_count')
            ->limit(8)
            ->get(['id', 'name', 'views_count', 'sales_count'])
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'views' => $product->views_count,
                'sales' => $product->sales_count,
                'conversion' => $product->views_count > 0
                    ? round(($product->sales_count / $product->views_count) * 100, 1)
                    : 0,
            ])
            ->toArray();

        if (Schema::hasTable('popups')) {
            $this->popupStats = DB::table('popups')
                ->select('title', 'views_count', 'clicks_count')
                ->orderByDesc('clicks_count')
                ->limit(5)
                ->get()
                ->map(fn ($popup) => [
                    'title' => $popup->title,
                    'views' => (int) $popup->views_count,
                    'clicks' => (int) $popup->clicks_count,
                    'ctr' => $popup->views_count > 0
                        ? round(($popup->clicks_count / $popup->views_count) * 100, 1)
                        : 0,
                ])
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.admin.analytics');
    }
}
