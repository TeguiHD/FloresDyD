<?php

namespace App\Livewire\Pages;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CheckoutSuccess extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        // Verificar que el usuario tenga acceso a esta orden
        // Por ahora permitimos acceso si tienen el link
        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.pages.checkout-success')
            ->title('¡Pedido Confirmado! - Flores D&D');
    }
}
