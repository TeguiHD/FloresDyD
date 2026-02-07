<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Layout('layouts.admin')]
#[Title('Panel Admin - Flores D&D')]
class Dashboard extends Component
{
    public array $kpis = [];
    public array $ordersByStatus = [];
    public array $topViewed = [];
    public array $topSelling = [];
    public array $recentOrders = [];
    public array $alerts = [];
    public float $conversionRate = 0.0;

    public function mount(): void
    {
        $this->loadMetrics();
    }

    private function loadMetrics(): void
    {
        $now = now();
        $start30 = $now->copy()->subDays(30);

        $paidStatuses = [
            'payment_verified',
            'preparing',
            'ready_for_delivery',
            'in_delivery',
            'delivered',
        ];

        $ordersToday = Order::query()->whereDate('created_at', $now)->count();
        $ordersPending = Order::query()->pending()->count();
        $orders30d = Order::query()->where('created_at', '>=', $start30)->count();
        $revenue30d = (int) Order::query()
            ->where('created_at', '>=', $start30)
            ->whereIn('status', $paidStatuses)
            ->sum('total');

        $avgOrderValue = $orders30d > 0 ? (int) round($revenue30d / $orders30d) : 0;

        $activeProducts = Product::query()->where('is_active', true)->count();
        $lowStock = Product::query()
            ->where('track_stock', true)
            ->whereRaw('stock - reserved_stock <= low_stock_threshold')
            ->count();

        $highRisk = Order::query()->highRisk()->count();
        $avgFraudScore = (int) round(Order::query()->avg('fraud_score') ?? 0);

        $sessions30d = 0;
        if (Schema::hasTable('sessions')) {
            $sessions30d = (int) DB::table('sessions')
                ->where('last_activity', '>=', $start30->timestamp)
                ->count();
        }

        $this->conversionRate = $sessions30d > 0
            ? round(($orders30d / $sessions30d) * 100, 2)
            : 0.0;

        $this->kpis = [
            [
                'label' => 'Pedidos hoy',
                'value' => $ordersToday,
                'hint' => 'Últimas 24h',
            ],
            [
                'label' => 'Ingresos 30 días',
                'value' => '$' . number_format($revenue30d / 100, 0, ',', '.'),
                'hint' => 'Pagos verificados',
            ],
            [
                'label' => 'Ticket promedio',
                'value' => '$' . number_format($avgOrderValue / 100, 0, ',', '.'),
                'hint' => 'Base 30 días',
            ],
            [
                'label' => 'Conversión estimada',
                'value' => $this->conversionRate . '%',
                'hint' => $sessions30d > 0 ? 'Sesiones 30 días' : 'Sin sesiones',
            ],
        ];

        $this->ordersByStatus = Order::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status,
                'label' => Order::make(['status' => $row->status])->status_label,
                'total' => (int) $row->total,
            ])
            ->toArray();

        $this->topViewed = Product::query()
            ->orderByDesc('views_count')
            ->limit(5)
            ->get(['id', 'name', 'views_count', 'sales_count', 'stock'])
            ->toArray();

        $this->topSelling = Product::query()
            ->orderByDesc('sales_count')
            ->limit(5)
            ->get(['id', 'name', 'sales_count', 'views_count', 'stock'])
            ->toArray();

        $this->recentOrders = Order::query()
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Order $order) => [
                'id' => $order->id,
                'number' => $order->order_number,
                'total' => $order->formatted_total,
                'status' => $order->status_label,
                'risk' => $order->getRiskLevel(),
                'created_at' => $order->created_at?->format('d M, H:i'),
            ])
            ->toArray();

        $lockedUsers = User::query()
            ->whereNotNull('locked_until')
            ->where('locked_until', '>', $now)
            ->count();

        $this->alerts = [
            [
                'label' => 'Productos con stock bajo',
                'value' => $lowStock,
                'type' => $lowStock > 0 ? 'warning' : 'ok',
            ],
            [
                'label' => 'Pedidos en revisión',
                'value' => $ordersPending,
                'type' => $ordersPending > 0 ? 'warning' : 'ok',
            ],
            [
                'label' => 'Pedidos de alto riesgo',
                'value' => $highRisk,
                'type' => $highRisk > 0 ? 'danger' : 'ok',
            ],
            [
                'label' => 'Cuentas bloqueadas',
                'value' => $lockedUsers,
                'type' => $lockedUsers > 0 ? 'danger' : 'ok',
            ],
            [
                'label' => 'Score fraude promedio',
                'value' => $avgFraudScore,
                'type' => $avgFraudScore < 50 ? 'warning' : 'ok',
            ],
            [
                'label' => 'Productos activos',
                'value' => $activeProducts,
                'type' => 'ok',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
