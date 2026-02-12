<?php

namespace App\Livewire\Pages;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
#[Title('Rastrear Pedido - Flores D&D')]
class TrackOrder extends Component
{
    #[Validate('required|min:6|max:20')]
    public string $trackingCode = '';

    public ?Order $order = null;
    public bool $searched = false;
    public ?string $errorMessage = null;

    public function mount(?string $tracking_code = null): void
    {
        if ($tracking_code) {
            $this->trackingCode = $tracking_code;
            $this->search();
        }
    }

    public function search(): void
    {
        $this->validate();
        $this->trackingCode = strtoupper(trim($this->trackingCode));
        $this->searched = true;
        $this->errorMessage = null;

        $this->order = Order::where('tracking_code', $this->trackingCode)->first();

        if (!$this->order) {
            $this->errorMessage = 'No encontramos ningún pedido con ese código. Verifica que esté escrito correctamente.';
        }
    }

    public function getStatusSteps(): array
    {
        return [
            'pending_payment' => [
                'label' => 'Pago pendiente',
                'description' => 'Esperamos tu comprobante de pago',
                'icon' => 'clipboard-document-check',
            ],
            'pending_review' => [
                'label' => 'En revisión',
                'description' => 'Estamos revisando tu comprobante',
                'icon' => 'check-circle',
            ],
            'payment_verified' => [
                'label' => 'Pago verificado',
                'description' => 'Confirmamos tu pago',
                'icon' => 'check-circle',
            ],
            'preparing' => [
                'label' => 'En Preparación',
                'description' => 'Estamos preparando tu arreglo',
                'icon' => 'sparkles',
            ],
            'ready_for_delivery' => [
                'label' => 'Listo',
                'description' => 'Tu pedido está listo para entrega',
                'icon' => 'gift',
            ],
            'in_delivery' => [
                'label' => 'En Camino',
                'description' => 'Tu pedido va en camino',
                'icon' => 'truck',
            ],
            'delivered' => [
                'label' => 'Entregado',
                'description' => '¡Pedido entregado!',
                'icon' => 'heart',
            ],
            'cancelled' => [
                'label' => 'Cancelado',
                'description' => 'El pedido fue cancelado',
                'icon' => 'x-circle',
            ],
            'refunded' => [
                'label' => 'Reembolsado',
                'description' => 'El pago fue reembolsado',
                'icon' => 'arrow-path',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.pages.track-order', [
            'statusSteps' => $this->getStatusSteps(),
        ]);
    }
}
