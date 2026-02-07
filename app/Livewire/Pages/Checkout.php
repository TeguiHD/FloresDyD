<?php

namespace App\Livewire\Pages;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
#[Title('Checkout - Flores D&D')]
class Checkout extends Component
{
    // Datos del cliente
    #[Validate('required|min:3|max:100')]
    public string $customerName = '';

    #[Validate('required|email')]
    public string $customerEmail = '';

    #[Validate('required|min:10|max:15')]
    public string $customerPhone = '';

    // Datos de entrega
    #[Validate('required|in:delivery,pickup')]
    public string $deliveryMethod = 'delivery';

    #[Validate('required_if:deliveryMethod,delivery|min:5|max:255')]
    public string $deliveryAddress = '';

    #[Validate('nullable|max:100')]
    public string $deliveryColonia = '';

    #[Validate('required_if:deliveryMethod,delivery|digits:5')]
    public string $deliveryZip = '';

    #[Validate('nullable|max:255')]
    public string $deliveryReferences = '';

    #[Validate('required|date|after:today')]
    public string $deliveryDate = '';

    #[Validate('required')]
    public string $deliveryTime = '';

    // Datos del destinatario
    public bool $sameAsCustomer = true;

    #[Validate('required_if:sameAsCustomer,false|min:3|max:100')]
    public string $recipientName = '';

    #[Validate('required_if:sameAsCustomer,false|min:10|max:15')]
    public string $recipientPhone = '';

    // Extras
    #[Validate('nullable|max:500')]
    public string $cardMessage = '';

    public bool $isAnonymous = false;

    // Pago
    #[Validate('required|in:transfer,card')]
    public string $paymentMethod = 'transfer';

    // Cupón
    public string $couponCode = '';
    public ?array $appliedCoupon = null;

    // Estado
    public int $currentStep = 1;
    public array $cart = [];
    public float $subtotal = 0;
    public float $shipping = 0;
    public float $discount = 0;
    public float $total = 0;

    public function mount(): void
    {
        // Cargar carrito desde sesión
        $this->cart = session('cart', []);
        $this->calculateTotals();

        // Fecha mínima de entrega (mañana)
        $this->deliveryDate = now()->addDay()->format('Y-m-d');
    }

    public function calculateTotals(): void
    {
        $this->subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        
        // Envío gratis arriba de $800
        $this->shipping = $this->subtotal >= 800 ? 0 : 100;
        
        // Aplicar descuento de cupón
        if ($this->appliedCoupon) {
            if ($this->appliedCoupon['type'] === 'percentage') {
                $this->discount = $this->subtotal * ($this->appliedCoupon['value'] / 100);
            } else {
                $this->discount = $this->appliedCoupon['value'];
            }
        }

        $this->total = $this->subtotal + $this->shipping - $this->discount;
    }

    public function applyCoupon(): void
    {
        // Aquí validarías el cupón en la BD
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Cupón no válido o expirado'
        ]);
    }

    public function nextStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validateOnly('customerName');
            $this->validateOnly('customerEmail');
            $this->validateOnly('customerPhone');
        }

        if ($this->currentStep === 2) {
            $this->validateOnly('deliveryMethod');
            $this->validateOnly('deliveryDate');
            $this->validateOnly('deliveryTime');
            
            if ($this->deliveryMethod === 'delivery') {
                $this->validateOnly('deliveryAddress');
                $this->validateOnly('deliveryZip');
            }
        }

        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step <= $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    public function placeOrder(): void
    {
        $this->validate();

        // Crear orden
        // $order = Order::create([...]);

        // Redirigir a éxito
        // return redirect()->route('checkout.success', $order);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => '¡Pedido realizado con éxito!'
        ]);
    }

    public function render()
    {
        return view('livewire.pages.checkout');
    }
}
