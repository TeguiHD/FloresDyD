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
        $this->searched = true;
        $this->errorMessage = null;

        $this->order = Order::where('tracking_code', $this->trackingCode)
            ->orWhere('order_number', $this->trackingCode)
            ->first();

        if (!$this->order) {
            $this->errorMessage = 'No encontramos ningún pedido con ese código. Verifica que esté escrito correctamente.';
        }
    }

    public function getStatusSteps(): array
    {
        return [
            'pending' => [
                'label' => 'Pedido Recibido',
                'description' => 'Hemos recibido tu pedido',
                'icon' => 'clipboard-document-check',
            ],
            'confirmed' => [
                'label' => 'Confirmado',
                'description' => 'Tu pedido ha sido confirmado',
                'icon' => 'check-circle',
            ],
            'preparing' => [
                'label' => 'En Preparación',
                'description' => 'Estamos preparando tu arreglo',
                'icon' => 'sparkles',
            ],
            'ready' => [
                'label' => 'Listo',
                'description' => 'Tu pedido está listo para entrega',
                'icon' => 'gift',
            ],
            'delivering' => [
                'label' => 'En Camino',
                'description' => 'Tu pedido va en camino',
                'icon' => 'truck',
            ],
            'delivered' => [
                'label' => 'Entregado',
                'description' => '¡Pedido entregado!',
                'icon' => 'heart',
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
