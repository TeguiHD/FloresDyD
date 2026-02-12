<?php

namespace App\Livewire\Account;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Mis pedidos - Flores D&D')]
class Orders extends Component
{
    use WithPagination;

    public string $status = 'all';

    protected $queryString = [
        'status' => ['except' => 'all'],
    ];

    public array $statusOptions = [
        'all' => 'Todos',
        'pending_payment' => 'Esperando comprobante',
        'pending_review' => 'En revisión',
        'payment_verified' => 'Pago verificado',
        'preparing' => 'En preparación',
        'ready_for_delivery' => 'Listo para envío',
        'in_delivery' => 'En camino',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
        'refunded' => 'Reembolsado',
    ];

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $userId = Auth::id();
        $query = Order::query()
            ->where('user_id', $userId)
            ->withCount('paymentProofs')
            ->orderByDesc('created_at');

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        return view('livewire.account.orders', [
            'orders' => $query->paginate(10),
        ]);
    }
}
