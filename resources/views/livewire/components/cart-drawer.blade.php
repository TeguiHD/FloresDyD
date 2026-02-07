{{-- 
    CartDrawer - Carrito lateral deslizable
    Aparece desde la derecha con animación suave
--}}
<div x-data="{ open: $wire.entangle('isOpen') }">
    {{-- Backdrop --}}
    <div 
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40"
    ></div>
    
    {{-- Drawer --}}
    <div 
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @keydown.escape.window="open = false"
        class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-50 flex flex-col"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <h2 class="font-display text-xl text-dark">Tu Carrito</h2>
                @if($this->itemsCount > 0)
                    <span class="px-2 py-0.5 bg-primary/10 text-primary text-sm rounded-full">
                        {{ $this->itemsCount }} {{ $this->itemsCount === 1 ? 'artículo' : 'artículos' }}
                    </span>
                @endif
            </div>
            <button 
                @click="open = false"
                class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                aria-label="Cerrar carrito"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        {{-- Content --}}
        <div class="flex-1 overflow-y-auto">
            @if(empty($items))
                {{-- Carrito vacío --}}
                <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                    <svg class="w-24 h-24 text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <h3 class="font-display text-xl text-dark mb-2">Tu carrito está vacío</h3>
                    <p class="text-gray-500 mb-6">¡Descubre nuestros hermosos arreglos florales!</p>
                    <a 
                        href="{{ route('coleccion') }}"
                        @click="open = false"
                        class="btn-primary"
                    >
                        Explorar colección
                    </a>
                </div>
            @else
                {{-- Lista de productos --}}
                <ul class="divide-y divide-gray-100">
                    @foreach($items as $productId => $item)
                        <li class="p-4" wire:key="cart-item-{{ $productId }}">
                            <div class="flex gap-4">
                                {{-- Imagen --}}
                                <a 
                                    href="{{ route('producto', $item['slug']) }}"
                                    @click="open = false"
                                    class="flex-shrink-0"
                                >
                                    <img 
                                        src="{{ asset('storage/' . $item['image']) }}"
                                        alt="{{ $item['name'] }}"
                                        class="w-20 h-20 object-cover rounded-lg"
                                    >
                                </a>
                                
                                {{-- Detalles --}}
                                <div class="flex-1 min-w-0">
                                    <a 
                                        href="{{ route('producto', $item['slug']) }}"
                                        @click="open = false"
                                        class="font-medium text-dark hover:text-primary transition-colors line-clamp-2"
                                    >
                                        {{ $item['name'] }}
                                    </a>
                                    
                                    {{-- Precio --}}
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-primary font-semibold">
                                            ${{ number_format($item['price'], 2) }}
                                        </span>
                                        @if($item['original_price'] > $item['price'])
                                            <span class="text-gray-400 text-sm line-through">
                                                ${{ number_format($item['original_price'], 2) }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Mensaje de tarjeta si existe --}}
                                    @if(!empty($item['card_message']))
                                        <p class="text-xs text-gray-500 mt-1 italic">
                                            "{{ Str::limit($item['card_message'], 30) }}"
                                        </p>
                                    @endif
                                    
                                    {{-- Controles de cantidad --}}
                                    <div class="flex items-center justify-between mt-3">
                                        <div class="flex items-center gap-2">
                                            <button 
                                                wire:click="decrementItem({{ $productId }})"
                                                class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors"
                                                aria-label="Disminuir cantidad"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            <span class="w-8 text-center font-medium">{{ $item['quantity'] }}</span>
                                            <button 
                                                wire:click="incrementItem({{ $productId }})"
                                                class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors"
                                                aria-label="Aumentar cantidad"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        {{-- Subtotal del item --}}
                                        <span class="font-semibold text-dark">
                                            ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                        </span>
                                    </div>
                                </div>
                                
                                {{-- Eliminar --}}
                                <button 
                                    wire:click="removeItem({{ $productId }})"
                                    wire:confirm="¿Eliminar este producto del carrito?"
                                    class="flex-shrink-0 p-1 text-gray-400 hover:text-red-500 transition-colors"
                                    aria-label="Eliminar producto"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
                
                {{-- Vaciar carrito --}}
                <div class="px-4 pb-4">
                    <button 
                        wire:click="clearCart"
                        wire:confirm="¿Estás seguro de vaciar todo el carrito?"
                        class="text-sm text-gray-500 hover:text-red-500 transition-colors underline"
                    >
                        Vaciar carrito
                    </button>
                </div>
            @endif
        </div>
        
        {{-- Footer con totales --}}
        @if(!empty($items))
            <div class="border-t border-gray-100 p-4 bg-gray-50">
                {{-- Ahorros si hay descuentos --}}
                @if($this->savings > 0)
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-green-600">Estás ahorrando</span>
                        <span class="text-green-600 font-medium">-${{ number_format($this->savings, 2) }}</span>
                    </div>
                @endif
                
                {{-- Subtotal --}}
                <div class="flex items-center justify-between mb-4">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-display text-2xl text-dark">${{ number_format($this->subtotal, 2) }}</span>
                </div>
                
                <p class="text-xs text-gray-500 mb-4">
                    El envío se calcula en el checkout
                </p>
                
                {{-- Botón de checkout --}}
                <button 
                    wire:click="proceedToCheckout"
                    class="w-full btn-primary justify-center py-3 text-base"
                >
                    <span>Proceder al pago</span>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
                
                {{-- Seguir comprando --}}
                <button 
                    @click="open = false"
                    class="w-full mt-2 text-center text-sm text-gray-500 hover:text-primary py-2"
                >
                    Seguir comprando
                </button>
                
                {{-- Badges de confianza --}}
                <div class="flex items-center justify-center gap-4 mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Pago seguro</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <span>Entrega garantizada</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
