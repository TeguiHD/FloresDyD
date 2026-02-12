<?php

namespace App\Livewire\Pages;

use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\AuditService;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\EmailService;
use App\Services\FraudDetectionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
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

    #[Validate('nullable|digits:7')]
    public string $deliveryZip = '';

    #[Validate('nullable|max:255')]
    public string $deliveryReferences = '';

    #[Validate('required|date|after_or_equal:today')]
    public string $deliveryDate = '';

    #[Validate('nullable')]
    public string $deliveryTime = '';

    // Sucursal de retiro
    #[Validate('required_if:deliveryMethod,pickup|max:100')]
    public string $pickupBranch = '';

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
    #[Validate('required|in:transfer')]
    public string $paymentMethod = 'transfer';

    // Cupón
    public string $couponCode = '';
    public ?array $appliedCoupon = null;
    public string $couponMessage = '';
    public string $couponStatus = ''; // 'success' | 'error' | ''

    // Estado
    public int $currentStep = 1;
    public array $cart = [];
    public int $subtotal = 0;
    public int $shipping = 0;
    public int $discount = 0;
    public int $total = 0;

    public function mount(): void
    {
        // Cargar carrito desde sesión
        $this->refreshCartFromSession();
        $this->calculateTotals();

        // Fecha mínima de entrega (mañana)
        $this->deliveryDate = now()->addDay()->format('Y-m-d');

        // Prefill si el usuario está autenticado
        if (Auth::check()) {
            $user = Auth::user();
            $this->customerName = $user?->name ?? '';
            $this->customerEmail = $user?->email ?? '';
            $this->customerPhone = $user?->phone ?? '';
            if (!empty($user?->address)) {
                $this->deliveryAddress = $user->address;
            }
        }
    }

    public function calculateTotals(): void
    {
        $this->subtotal = collect($this->cart)->sum(fn($item) => (int) $item['price'] * (int) $item['quantity']);

        $freeMin = (int) config('flores.delivery.free_shipping_minimum', 800);
        $shippingCost = (int) config('flores.delivery.shipping_cost', 100);

        // Envío gratis sobre el mínimo o retiro en tienda
        $this->shipping = $this->deliveryMethod === 'pickup'
            ? 0
            : ($this->subtotal >= $freeMin ? 0 : $shippingCost);

        // Aplicar descuento de cupón delegando al servicio (DRY)
        $this->discount = 0;
        if ($this->appliedCoupon && CouponService::verifyPayload($this->appliedCoupon)) {
            $coupon = new \App\Models\Coupon($this->appliedCoupon);
            $coupon->exists = true;
            $this->discount = CouponService::calculateDiscount($coupon, $this->subtotal);
        } elseif ($this->appliedCoupon) {
            // Firma inválida: el payload fue manipulado, limpiar
            $this->appliedCoupon = null;
            $this->couponCode = '';
            $this->couponMessage = '';
            $this->couponStatus = '';
        }

        $this->discount = min($this->discount, $this->subtotal);
        $this->total = max(0, $this->subtotal + $this->shipping - $this->discount);
    }

    public function applyCoupon(): void
    {
        $rateKey = 'coupon:apply:' . request()->ip() . ':' . session()->getId();
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $this->couponMessage = 'Demasiados intentos. Espera un momento.';
            $this->couponStatus = 'error';
            return;
        }
        RateLimiter::hit($rateKey, 60);

        $this->refreshCartFromSession();
        if (empty($this->cart)) {
            $this->couponMessage = 'Tu carrito está vacío';
            $this->couponStatus = 'error';
            return;
        }

        $result = CouponService::validateForCart(
            $this->couponCode,
            $this->subtotal,
            $this->cart,
            auth()->id(),
        );

        if (!$result['valid']) {
            $this->appliedCoupon = null;
            $this->couponMessage = $result['message'];
            $this->couponStatus = 'error';
            $this->calculateTotals();
            return;
        }

        $coupon = $result['coupon'];
        $this->appliedCoupon = CouponService::buildSignedPayload($coupon);
        $this->couponMessage = $result['message'];
        $this->couponStatus = 'success';
        $this->calculateTotals();
    }

    public function clearCoupon(): void
    {
        $this->couponCode = '';
        $this->appliedCoupon = null;
        $this->couponMessage = '';
        $this->couponStatus = '';
        $this->calculateTotals();
    }

    public function updatedDeliveryMethod(): void
    {
        $this->calculateTotals();
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

            if ($this->deliveryMethod === 'delivery') {
                $this->validateOnly('deliveryAddress');
            }
        }

        // Re-validar cupón al avanzar de paso (puede haber expirado o cambiado)
        if ($this->appliedCoupon) {
            $this->revalidateCoupon();
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

        $this->refreshCartFromSession();
        if (empty($this->cart)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Tu carrito está vacío',
            ]);
            return;
        }

        $rateKey = 'checkout:place:' . request()->ip() . ':' . session()->getId();
        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Demasiados intentos, espera un momento',
            ]);
            return;
        }
        RateLimiter::hit($rateKey, 60);

        try {
            $order = DB::transaction(function () {
                $user = Auth::user();

                if (!$user) {
                    $user = User::query()->firstOrCreate(
                        ['email' => $this->customerEmail],
                        [
                            'name' => $this->customerName,
                            'password' => Str::random(32),
                        ]
                    );
                }

                $productIds = collect($this->cart)->pluck('id')->all();
                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->with('activeVariants')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($products->isEmpty()) {
                    throw new \RuntimeException('No hay productos disponibles');
                }

                $subtotal = 0;
                $orderItems = [];
                foreach ($this->cart as $item) {
                    $product = $products->get($item['id']);
                    if (!$product || !$product->is_active) {
                        throw new \RuntimeException('Producto no disponible');
                    }

                    $quantity = (int) $item['quantity'];
                    if ($quantity < 1) {
                        throw new \RuntimeException('Cantidad inválida');
                    }
                    if ($product->track_stock && $product->available_stock < $quantity) {
                        throw new \RuntimeException('Stock insuficiente para ' . $product->name);
                    }

                    $variant = null;
                    $variantId = isset($item['variant_id']) ? (int) $item['variant_id'] : null;
                    if ($variantId) {
                        $variant = $product->activeVariants->firstWhere('id', $variantId);
                        if (!$variant) {
                            throw new \RuntimeException('Formato seleccionado no disponible');
                        }
                    }
                    $customValue = isset($item['custom_value']) ? (int) $item['custom_value'] : null;
                    $customValue = CartService::normalizeCustomValue($variant, $customValue);
                    $unitPrice = CartService::calculateUnitPrice($product, $variant, $customValue);
                    $lineTotal = $unitPrice * $quantity;
                    $subtotal += $lineTotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'product_variant_id' => $variant?->id,
                        'product_name' => $product->name,
                        'variant_label' => $variant ? ($variant->label ?: $variant->name) : null,
                        'variant_type' => $variant?->type,
                        'custom_value' => $customValue,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $lineTotal,
                    ];
                }

                $freeMin = (int) config('flores.delivery.free_shipping_minimum', 800);
                $shippingCost = (int) config('flores.delivery.shipping_cost', 100);
                $shipping = $this->deliveryMethod === 'pickup'
                    ? 0
                    : ($subtotal >= $freeMin ? 0 : $shippingCost);

                $coupon = null;
                $discount = 0;
                if ($this->couponCode !== '') {
                    $result = CouponService::validateForCart(
                        $this->couponCode,
                        $subtotal,
                        $this->cart,
                        $user->id,
                        true
                    );

                    if (!$result['valid']) {
                        throw new \RuntimeException($result['message']);
                    }

                    $coupon = $result['coupon'];
                    $discount = $result['discount'];
                }

                $total = max(0, $subtotal + $shipping - $discount);

                $fraud = FraudDetectionService::evaluate([
                    'total' => $total,
                    'email' => $this->customerEmail,
                    'phone' => $this->customerPhone,
                    'address' => $this->deliveryAddress,
                ], request());

                $order = new Order();
                $order->order_number = Order::generateOrderNumber();
                $order->tracking_code = Order::generateTrackingCode();
                $order->user_id = $user->id;
                $order->status = 'pending_payment';

                $order->customer_name = $this->customerName;
                $order->customer_email = $this->customerEmail;
                $order->customer_phone = $this->customerPhone;

                $order->delivery_address = $this->deliveryMethod === 'delivery' 
                    ? $this->deliveryAddress 
                    : 'Retiro en tienda — ' . ($this->pickupBranch ?: 'Sin sucursal seleccionada');
                $order->delivery_city = $this->deliveryColonia;
                $order->delivery_zip = $this->deliveryMethod === 'delivery' ? $this->deliveryZip : null;
                $order->delivery_notes = $this->deliveryReferences;
                $order->delivery_date = $this->deliveryDate;
                $order->delivery_time_slot = $this->deliveryTime;
                $order->delivery_method = $this->deliveryMethod;

                $order->card_message = $this->cardMessage;
                $order->card_recipient = $this->sameAsCustomer ? $this->customerName : $this->recipientName;
                $order->card_sender = $this->isAnonymous ? 'Anónimo' : $this->customerName;

                $order->subtotal = $subtotal;
                $order->discount_amount = $discount;
                $order->delivery_fee = $shipping;
                $order->total = $total;

                if ($coupon) {
                    $order->coupon_id = $coupon->id;
                    $order->coupon_code = $coupon->code;
                }

                $order->fraud_score = $fraud['score'];
                $order->fraud_factors = $fraud['reasons'];
                $order->ip_address = request()->ip();
                $order->user_agent = request()->userAgent();
                $order->fingerprint_hash = hash('sha256', session()->getId() . '|' . request()->userAgent() . '|' . request()->ip());

                $order->save();

                foreach ($orderItems as $item) {
                    $order->items()->create($item);
                }

                if ($coupon) {
                    $coupon->increment('uses_count');
                    CouponUsage::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'discount_applied' => $discount,
                    ]);
                }

                $order->statusHistory()->create([
                    'from_status' => null,
                    'to_status' => $order->status,
                    'changed_by' => $user->id,
                    'notes' => 'Pedido creado',
                ]);

                AuditService::orderAction('created', $order->id, [
                    'total' => $total,
                    'fraud_score' => $order->fraud_score,
                ]);

                return $order;
            });
        } catch (\RuntimeException $e) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => $e->getMessage(),
            ]);
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'No se pudo crear el pedido. Intenta nuevamente.',
            ]);
            return;
        }

        session()->forget('cart');
        $this->dispatch('cartUpdated');

        // Enviar email de confirmación de pedido
        try {
            app(EmailService::class)->sendOrderCreated($order);
        } catch (\Throwable $e) {
            report($e);
        }

        $signedUrl = URL::temporarySignedRoute(
            'checkout.success',
            now()->addHours(24),
            ['order' => $order->id]
        );

        $this->redirect($signedUrl);
    }

    public function render()
    {
        return view('livewire.pages.checkout');
    }

    /**
     * Re-valida el cupón aplicado contra el estado actual del carrito.
     * Se ejecuta silenciosamente al cambiar de paso.
     */
    private function revalidateCoupon(): void
    {
        if (!$this->appliedCoupon || $this->couponCode === '') {
            return;
        }

        $this->refreshCartFromSession();

        $result = CouponService::validateForCart(
            $this->couponCode,
            $this->subtotal,
            $this->cart,
            auth()->id(),
        );

        if (!$result['valid']) {
            $this->appliedCoupon = null;
            $this->couponMessage = $result['message'];
            $this->couponStatus = 'error';
            $this->calculateTotals();

            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Cupón removido: ' . $result['message'],
            ]);
        }
    }

    private function refreshCartFromSession(): void
    {
        $clean = CartService::sanitizeCart(session('cart', []));
        $this->cart = $clean;
        session(['cart' => $clean]);
    }
}
