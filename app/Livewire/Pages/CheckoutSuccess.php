<?php

namespace App\Livewire\Pages;

use App\Models\Order;
use App\Livewire\Traits\UploadsPaymentProof;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class CheckoutSuccess extends Component
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
        // Acceso por signed URL (guest) o por usuario autenticado dueño
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403);
        }
        $this->order = $order;
        $this->proofUploaded = $order->paymentProofs()->exists();
    }

    public function render()
    {
        return view('livewire.pages.checkout-success')
            ->title('¡Pedido Confirmado! - Flores D&D');
    }
}
