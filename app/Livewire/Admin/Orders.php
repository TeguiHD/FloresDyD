<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Pedidos - Flores D&D')]
class Orders extends Component
{
    use WithPagination;

    public string $status = 'all';
    public string $search = '';

    protected $queryString = [
        'status' => ['except' => 'all'],
        'search' => ['except' => ''],
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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::query()->latest();

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->search !== '') {
            $query->where('order_number', 'like', '%' . $this->search . '%');
        }

        return view('livewire.admin.orders', [
            'orders' => $query->paginate(12),
            'statusOptions' => $this->statusOptions,
        ]);
    }
}
