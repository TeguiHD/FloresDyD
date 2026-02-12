<?php

namespace App\Livewire\Account;

use App\Models\Order;
use App\Livewire\Traits\UploadsPaymentProof;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Detalle de pedido - Flores D&D')]
class OrderShow extends Component
{
    use WithFileUploads, UploadsPaymentProof;

    public Order $order;
    public $proofFile;
    public ?int $declaredAmount = null;
    public string $transactionCode = '';
    public string $bankOrigin = '';
    public string $transactionDate = '';
    public bool $proofUploaded = false;

    public function mount(Order $order): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }

        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $this->order = $order->load(['items', 'paymentProofs', 'statusHistory']);
        $this->proofUploaded = $order->paymentProofs()->exists();
    }

    public function getStatusSteps(): array
    {
        return [
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
    }

    public function render()
    {
        return view('livewire.account.order-show', [
            'statusSteps' => $this->getStatusSteps(),
        ]);
    }
}

