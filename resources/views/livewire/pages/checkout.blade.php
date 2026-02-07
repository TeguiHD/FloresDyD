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

                            @if($deliveryMethod === 'delivery')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-dark mb-2">Dirección *</label>
                                        <input type="text" wire:model="deliveryAddress" placeholder="Calle, número, interior" class="w-full px-4 py-3 border border-secondary rounded-lg">
                                        @error('deliveryAddress') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-dark mb-2">Colonia</label>
                                            <input type="text" wire:model="deliveryColonia" class="w-full px-4 py-3 border border-secondary rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-dark mb-2">Código Postal *</label>
                                            <input type="text" wire:model="deliveryZip" maxlength="5" class="w-full px-4 py-3 border border-secondary rounded-lg">
                                            @error('deliveryZip') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-dark mb-2">Referencias</label>
                                        <input type="text" wire:model="deliveryReferences" placeholder="Ej: Casa blanca, portón negro" class="w-full px-4 py-3 border border-secondary rounded-lg">
                                    </div>
                                </div>
                            @endif

                            <div class="grid md:grid-cols-2 gap-4 mt-6">
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-2">Fecha de entrega *</label>
                                    <input type="date" wire:model="deliveryDate" min="{{ now()->addDay()->format('Y-m-d') }}" class="w-full px-4 py-3 border border-secondary rounded-lg">
                                    @error('deliveryDate') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-2">Horario *</label>
                                    <select wire:model="deliveryTime" class="w-full px-4 py-3 border border-secondary rounded-lg">
                                        <option value="">Seleccionar</option>
                                        <option value="09:00-12:00">9:00 - 12:00</option>
                                        <option value="12:00-15:00">12:00 - 15:00</option>
                                        <option value="15:00-18:00">15:00 - 18:00</option>
                                        <option value="18:00-20:00">18:00 - 20:00</option>
                                    </select>
                                    @error('deliveryTime') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
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

                                <button 
                                    wire:click="$set('paymentMethod', 'card')"
                                    class="w-full p-4 border-2 rounded-xl transition text-left flex items-center gap-4 {{ $paymentMethod === 'card' ? 'border-primary bg-primary/5' : 'border-secondary' }}"
                                >
                                    <x-flux::icon name="credit-card" class="w-8 h-8 {{ $paymentMethod === 'card' ? 'text-primary' : 'text-dark/50' }}" />
                                    <div>
                                        <p class="font-medium">Tarjeta de crédito/débito</p>
                                        <p class="text-sm text-dark/60">Pago seguro con Stripe</p>
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
                                    <span>{{ $deliveryDate }} ({{ $deliveryTime }})</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-secondary">
                                    <span class="text-dark/60">Pago:</span>
                                    <span>{{ $paymentMethod === 'transfer' ? 'Transferencia' : 'Tarjeta' }}</span>
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
                                <div class="flex gap-3">
                                    <div class="w-16 h-16 bg-secondary/30 rounded-lg flex-shrink-0"></div>
                                    <div class="flex-1">
                                        <p class="font-medium text-sm">{{ $item['name'] ?? 'Producto' }}</p>
                                        <p class="text-dark/60 text-xs">Cant: {{ $item['quantity'] }}</p>
                                    </div>
                                    <p class="font-medium">${{ number_format($item['price'] * $item['quantity'], 0) }}</p>
                                </div>
                            @endforeach
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
