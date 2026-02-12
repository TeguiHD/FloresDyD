{{-- Checkout - Flores D&D --}}
<div class="min-h-dvh bg-secondary/20">
    <div class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block mb-4">
                <img src="/images/logo.webp" alt="Flores D&D" class="h-12">
            </a>
            <h1 class="font-serif text-3xl text-primary">Finalizar Compra</h1>
        </div>

        @if(count($cart) === 0)
            {{-- Carrito Vacío --}}
            <div class="max-w-md mx-auto text-center py-16">
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-primary/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h2l2.5 11h9.5l2-7H7.5"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-medium text-dark mb-2">Tu carrito está vacío</h2>
                <p class="text-dark/60 mb-6">Agrega algunos productos antes de continuar</p>
                <a 
                    href="{{ route('coleccion') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition"
                >
                    Ver colección
                </a>
            </div>
        @else
            <div class="lg:flex lg:gap-8">
                {{-- Formulario Principal --}}
                <div class="lg:w-2/3 mb-8 lg:mb-0">
                    {{-- Progress Steps --}}
                    <div class="flex items-center justify-center mb-8 overflow-x-auto">
                        @foreach(['Datos', 'Entrega', 'Pago', 'Confirmar'] as $index => $step)
                            <div class="flex items-center">
                                <button 
                                    wire:click="goToStep({{ $index + 1 }})"
                                    class="flex items-center gap-2 px-4 py-2 rounded-full transition {{ $currentStep >= $index + 1 ? 'bg-primary text-white' : 'bg-white text-dark/50' }}"
                                >
                                    <span class="w-6 h-6 rounded-full border-2 flex items-center justify-center text-sm {{ $currentStep > $index + 1 ? 'bg-white text-primary border-white' : 'border-current' }}">
                                        @if($currentStep > $index + 1) ✓ @else {{ $index + 1 }} @endif
                                    </span>
                                    <span class="hidden sm:inline">{{ $step }}</span>
                                </button>
                                @if($index < 3)
                                    <div class="w-8 h-0.5 {{ $currentStep > $index + 1 ? 'bg-primary' : 'bg-gray-200' }}"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Step 1: Datos del Cliente --}}
                    @if($currentStep === 1)
                        <div class="bg-white rounded-2xl shadow-card p-6 lg:p-8">
                            <h2 class="font-serif text-xl text-dark mb-6">Tus datos</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-2">Nombre completo *</label>
                                    <input type="text" wire:model="customerName" class="w-full px-4 py-3 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    @error('customerName') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-dark mb-2">Email *</label>
                                        <input type="email" wire:model="customerEmail" class="w-full px-4 py-3 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                        @error('customerEmail') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-dark mb-2">Teléfono *</label>
                                        <input type="tel" wire:model="customerPhone" class="w-full px-4 py-3 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                        @error('customerPhone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end">
                                <button wire:click="nextStep" class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition">
                                    Continuar
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Step 2: Entrega --}}
                    @if($currentStep === 2)
                        <div class="bg-white rounded-2xl shadow-card p-6 lg:p-8">
                            <h2 class="font-serif text-xl text-dark mb-6">Datos de entrega</h2>
                            
                            {{-- Método de entrega --}}
                            <div class="flex gap-4 mb-6">
                                <button 
                                    wire:click="$set('deliveryMethod', 'delivery')"
                                    class="flex-1 p-4 border-2 rounded-xl transition {{ $deliveryMethod === 'delivery' ? 'border-primary bg-primary/5' : 'border-secondary' }}"
                                >
                                    <x-flux::icon name="truck" class="w-6 h-6 mx-auto mb-2 {{ $deliveryMethod === 'delivery' ? 'text-primary' : 'text-dark/50' }}" />
                                    <p class="font-medium">Envío a domicilio</p>
                                </button>
                                <button 
                                    wire:click="$set('deliveryMethod', 'pickup')"
                                    class="flex-1 p-4 border-2 rounded-xl transition {{ $deliveryMethod === 'pickup' ? 'border-primary bg-primary/5' : 'border-secondary' }}"
                                >
                                    <x-flux::icon name="building-storefront" class="w-6 h-6 mx-auto mb-2 {{ $deliveryMethod === 'pickup' ? 'text-primary' : 'text-dark/50' }}" />
                                    <p class="font-medium">Recoger en tienda</p>
                                </button>
                            </div>

                            @if($deliveryMethod === 'pickup')
                                <div class="space-y-3 mb-6">
                                    <p class="text-sm font-medium text-dark">Selecciona la sucursal para recoger:</p>
                                    @foreach(config('flores.sucursales', []) as $index => $sucursal)
                                        <label class="block p-4 border-2 rounded-xl cursor-pointer transition {{ ($pickupBranch ?? '') === $sucursal['nombre'] ? 'border-primary bg-primary/5' : 'border-secondary hover:border-primary/30' }}">
                                            <div class="flex items-start gap-3">
                                                <input type="radio" wire:model="pickupBranch" value="{{ $sucursal['nombre'] }}" class="mt-1 text-primary focus:ring-primary">
                                                <div>
                                                    <p class="font-medium text-dark">{{ $sucursal['nombre'] }}</p>
                                                    <p class="text-sm text-dark/60">{{ $sucursal['direccion'] }}, {{ $sucursal['comuna'] }}</p>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            @if($deliveryMethod === 'delivery')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-dark mb-2">Dirección *</label>
                                        <input type="text" wire:model="deliveryAddress" placeholder="Calle, número, interior" class="w-full px-4 py-3 border border-secondary rounded-lg">
                                        @error('deliveryAddress') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-dark mb-2">Comuna</label>
                                            <input type="text" wire:model="deliveryColonia" class="w-full px-4 py-3 border border-secondary rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-dark mb-2">Código Postal (opcional)</label>
                                            <input
                                                type="text"
                                                wire:model="deliveryZip"
                                                maxlength="7"
                                                inputmode="numeric"
                                                placeholder="Ej: 9540000"
                                                class="w-full px-4 py-3 border border-secondary rounded-lg"
                                            >
                                            @error('deliveryZip') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-dark mb-2">Referencias</label>
                                        <input type="text" wire:model="deliveryReferences" placeholder="Ej: Casa blanca, portón negro" class="w-full px-4 py-3 border border-secondary rounded-lg">
                                    </div>
                                </div>
                            @endif

                            <div class="mt-6">
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-2">Fecha de entrega *</label>
                                    <input type="date" wire:model="deliveryDate" min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-3 border border-secondary rounded-lg">
                                    @error('deliveryDate') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="mt-8 flex justify-between">
                                <button wire:click="previousStep" class="px-8 py-3 border border-secondary rounded-full hover:bg-secondary/50 transition">
                                    Atrás
                                </button>
                                <button wire:click="nextStep" class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition">
                                    Continuar
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Step 3: Pago --}}
                    @if($currentStep === 3)
                        <div class="bg-white rounded-2xl shadow-card p-6 lg:p-8">
                            <h2 class="font-serif text-xl text-dark mb-6">Método de pago</h2>
                            
                            <div class="space-y-4">
                                <button 
                                    wire:click="$set('paymentMethod', 'transfer')"
                                    class="w-full p-4 border-2 rounded-xl transition text-left flex items-center gap-4 {{ $paymentMethod === 'transfer' ? 'border-primary bg-primary/5' : 'border-secondary' }}"
                                >
                                    <x-flux::icon name="banknotes" class="w-8 h-8 {{ $paymentMethod === 'transfer' ? 'text-primary' : 'text-dark/50' }}" />
                                    <div>
                                        <p class="font-medium">Transferencia bancaria</p>
                                        <p class="text-sm text-dark/60">Paga directamente a nuestra cuenta</p>
                                    </div>
                                </button>

                                @if($paymentMethod === 'transfer')
                                    <div class="bg-secondary/30 rounded-xl p-5 text-sm space-y-4">
                                        <h4 class="font-medium text-dark mb-3">Datos para transferencia</h4>
                                        
                                        {{-- Opción 1: Banco de Chile --}}
                                        <div class="bg-white rounded-lg p-4 border border-secondary">
                                            <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2">Transferencia Bancaria</p>
                                            <p><strong>Titular:</strong> {{ config('flores.payment.bank_transfer.titular') }}</p>
                                            <p><strong>RUT:</strong> {{ config('flores.payment.bank_transfer.rut') }}</p>
                                            <p><strong>Banco:</strong> {{ config('flores.payment.bank_transfer.banco') }}</p>
                                            <p><strong>Tipo de cuenta:</strong> {{ config('flores.payment.bank_transfer.tipo_cuenta') }}</p>
                                            <p><strong>N° de cuenta:</strong> {{ config('flores.payment.bank_transfer.numero_cuenta') }}</p>
                                            <p><strong>Email:</strong> {{ config('flores.payment.bank_transfer.email') }}</p>
                                        </div>

                                        {{-- Opción 2: Mercado Pago --}}
                                        <div class="bg-white rounded-lg p-4 border border-secondary">
                                            <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2">Mercado Pago</p>
                                            <p><strong>Titular:</strong> {{ config('flores.payment.mercado_pago.titular') }}</p>
                                            <p><strong>RUT:</strong> {{ config('flores.payment.mercado_pago.rut') }}</p>
                                            <p><strong>Banco:</strong> {{ config('flores.payment.mercado_pago.banco') }}</p>
                                            <p><strong>Tipo de cuenta:</strong> {{ config('flores.payment.mercado_pago.tipo_cuenta') }}</p>
                                            <p><strong>N° de cuenta:</strong> {{ config('flores.payment.mercado_pago.numero_cuenta') }}</p>
                                            <p><strong>Email:</strong> {{ config('flores.payment.mercado_pago.email') }}</p>
                                        </div>

                                        <p class="text-dark/60 text-xs mt-3">Una vez confirmado tu pedido, usa tu número de pedido como referencia de la transferencia.</p>
                                    </div>
                                @endif

                                <button
                                    type="button"
                                    disabled
                                    class="w-full p-4 border-2 rounded-xl text-left flex items-center gap-4 border-secondary opacity-60 cursor-not-allowed"
                                >
                                    <x-flux::icon name="credit-card" class="w-8 h-8 text-dark/50" />
                                    <div>
                                        <p class="font-medium">Tarjeta de crédito/débito</p>
                                        <p class="text-sm text-dark/60">No disponible aún (pasarela de pagos en integración)</p>
                                    </div>
                                </button>
                            </div>

                            <div class="mt-8 flex justify-between">
                                <button wire:click="previousStep" class="px-8 py-3 border border-secondary rounded-full hover:bg-secondary/50 transition">
                                    Atrás
                                </button>
                                <button wire:click="nextStep" class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition">
                                    Continuar
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Step 4: Confirmar --}}
                    @if($currentStep === 4)
                        <div class="bg-white rounded-2xl shadow-card p-6 lg:p-8">
                            <h2 class="font-serif text-xl text-dark mb-6">Confirmar pedido</h2>
                            
                            <div class="space-y-4 text-sm">
                                <div class="flex justify-between py-2 border-b border-secondary">
                                    <span class="text-dark/60">Cliente:</span>
                                    <span>{{ $customerName }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-secondary">
                                    <span class="text-dark/60">Email:</span>
                                    <span>{{ $customerEmail }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-secondary">
                                    <span class="text-dark/60">Entrega:</span>
                                    <span>{{ $deliveryMethod === 'delivery' ? 'A domicilio' : 'Recoger en tienda' }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-secondary">
                                    <span class="text-dark/60">Fecha:</span>
                                    <span>{{ $deliveryDate }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-secondary">
                                    <span class="text-dark/60">Pago:</span>
                                    <span>Transferencia bancaria</span>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-between">
                                <button wire:click="previousStep" class="px-8 py-3 border border-secondary rounded-full hover:bg-secondary/50 transition">
                                    Atrás
                                </button>
                                <button wire:click="placeOrder" class="px-8 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition">
                                    Confirmar Pedido
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Resumen del Pedido --}}
                <div class="lg:w-1/3">
                    <div class="bg-white rounded-2xl shadow-card p-6 sticky top-24">
                        <h3 class="font-serif text-lg text-dark mb-4">Resumen</h3>
                        
                        <div class="space-y-3 mb-6">
                            @foreach($cart as $item)
                                @php
                                    $imageUrl = str_starts_with($item['image'], 'http')
                                        ? $item['image']
                                        : asset('storage/' . $item['image']);
                                @endphp
                                <div class="flex gap-3">
                                    <img src="{{ $imageUrl }}" alt="{{ $item['name'] ?? 'Producto' }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                    <div class="flex-1">
                                        <p class="font-medium text-sm">{{ $item['name'] ?? 'Producto' }}</p>
                                        @if(!empty($item['variant_label']))
                                            <p class="text-xs text-ink/60">
                                                {{ $item['variant_label'] }}
                                                @if(!empty($item['custom_value']))
                                                    · {{ $item['custom_value'] }}{{ $item['unit_label'] ? ' ' . $item['unit_label'] : '' }}
                                                @endif
                                            </p>
                                        @endif
                                        <p class="text-dark/60 text-xs">Cant: {{ $item['quantity'] }}</p>
                                    </div>
                                    <p class="font-medium">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Cupón --}}
                        <div class="border-t border-secondary pt-4 mb-4">
                            <p class="text-sm font-medium text-dark mb-2">¿Tienes un cupón?</p>
                            @if($appliedCoupon)
                                {{-- Cupón aplicado --}}
                                <div class="flex items-center gap-2 px-3 py-2.5 bg-green-50 border border-green-200 rounded-lg">
                                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-green-800 truncate">{{ $appliedCoupon['code'] }}</p>
                                        @if($couponMessage)
                                            <p class="text-xs text-green-600">{{ $couponMessage }}</p>
                                        @endif
                                    </div>
                                    <button 
                                        type="button" 
                                        wire:click="clearCoupon" 
                                        class="text-xs text-red-500 hover:text-red-700 font-medium hover:underline shrink-0"
                                    >
                                        Quitar
                                    </button>
                                </div>
                            @else
                                {{-- Input de cupón --}}
                                <div class="flex gap-2">
                                    <input 
                                        type="text"
                                        wire:model.defer="couponCode"
                                        wire:keydown.enter="applyCoupon"
                                        placeholder="Ej: FDD-ABCD1234"
                                        maxlength="20"
                                        class="flex-1 px-3 py-2 border rounded-lg text-sm uppercase transition-colors
                                            {{ $couponStatus === 'error' ? 'border-red-300 focus:border-red-400 focus:ring-red-200' : 'border-secondary focus:border-primary focus:ring-primary/20' }}"
                                    >
                                    <button 
                                        type="button"
                                        wire:click="applyCoupon"
                                        wire:loading.attr="disabled"
                                        wire:target="applyCoupon"
                                        class="px-4 py-2 bg-primary text-white rounded-lg text-sm hover:bg-primary-dark transition disabled:opacity-50 flex items-center gap-2"
                                    >
                                        <svg wire:loading wire:target="applyCoupon" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="applyCoupon">Aplicar</span>
                                        <span wire:loading wire:target="applyCoupon">...</span>
                                    </button>
                                </div>
                                {{-- Mensaje de error inline --}}
                                @if($couponStatus === 'error' && $couponMessage)
                                    <div class="mt-2 flex items-start gap-1.5">
                                        <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-xs text-red-600">{{ $couponMessage }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="border-t border-secondary pt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-dark/60">Subtotal</span>
                                <span>${{ number_format($subtotal, 0) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-dark/60">Envío</span>
                                <span>{{ $shipping > 0 ? '$' . number_format($shipping, 0) : 'Gratis' }}</span>
                            </div>
                            @if($discount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Descuento</span>
                                    <span>-${{ number_format($discount, 0) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-lg font-bold pt-2 border-t border-secondary">
                                <span>Total</span>
                                <span class="text-primary">${{ number_format($total, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
