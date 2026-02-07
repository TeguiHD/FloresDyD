<div>
    @if($isOpen && $product)
    <div 
        x-data="{ show: @entangle('isOpen') }"
        x-show="show"
        x-on:keydown.escape.window="$wire.close()"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
    {{-- Backdrop --}}
    <div 
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm"
        @click="$wire.close()"
    ></div>
    
    {{-- Modal content --}}
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90dvh] overflow-hidden"
            @click.stop
        >
            {{-- Close button --}}
            <button 
                wire:click="close"
                class="absolute top-4 right-4 z-10 w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow-lg hover:bg-white transition-colors"
                aria-label="Cerrar"
            >
                <svg class="w-5 h-5 text-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            
            <div class="grid md:grid-cols-2 h-full">
                {{-- Galería de imágenes --}}
                <div class="relative bg-gray-50 aspect-square md:aspect-auto">
                    @php
                        $images = $product->gallery ?: [$product->image];
                    @endphp
                    
                    {{-- Imagen principal --}}
                    <img 
                        src="{{ asset('storage/' . ($images[$selectedImageIndex] ?? $product->image)) }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover"
                    >
                    
                    {{-- Badges --}}
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        @if($product->discount_percentage > 0)
                            <span class="product-card__badge--discount px-3 py-1 text-xs font-bold rounded-full bg-red-500 text-white">
                                -{{ $product->discount_percentage }}%
                            </span>
                        @endif
                        @if($product->is_new)
                            <span class="product-card__badge--new px-3 py-1 text-xs font-bold rounded-full bg-green-500 text-white">
                                Nuevo
                            </span>
                        @endif
                        @if($product->is_bestseller)
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-500 text-white">
                                Más vendido
                            </span>
                        @endif
                    </div>
                    
                    {{-- Navegación de imágenes --}}
                    @if(count($images) > 1)
                        <button 
                            wire:click="previousImage"
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow hover:bg-white transition-colors"
                            aria-label="Imagen anterior"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button 
                            wire:click="nextImage"
                            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center shadow hover:bg-white transition-colors"
                            aria-label="Siguiente imagen"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        
                        {{-- Thumbnails --}}
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                            @foreach($images as $index => $image)
                                <button 
                                    wire:click="selectImage({{ $index }})"
                                    class="w-12 h-12 rounded-lg overflow-hidden border-2 transition-colors {{ $selectedImageIndex === $index ? 'border-primary' : 'border-white/50' }}"
                                >
                                    <img src="{{ asset('storage/' . $image) }}" alt="" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                {{-- Información del producto --}}
                <div class="p-6 lg:p-8 overflow-y-auto max-h-[50dvh] md:max-h-none">
                    {{-- Categoría --}}
                    @if($product->category)
                        <a 
                            href="{{ route('coleccion.categoria', $product->category->slug) }}"
                            class="text-sm text-primary hover:underline"
                        >
                            {{ $product->category->name }}
                        </a>
                    @endif
                    
                    {{-- Nombre --}}
                    <h2 id="modal-title" class="font-display text-2xl lg:text-3xl text-dark mt-1 mb-2">
                        {{ $product->name }}
                    </h2>
                    
                    {{-- Rating --}}
                    @if($product->reviews_count > 0)
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-sm text-gray-500">({{ $product->reviews_count }} reseñas)</span>
                        </div>
                    @endif
                    
                    {{-- Precio --}}
                    <div class="flex items-center gap-3 mb-4">
                        <span class="price text-2xl">${{ number_format($product->current_price, 2) }}</span>
                        @if($product->discount_percentage > 0)
                            <span class="price--old">${{ number_format($product->price, 2) }}</span>
                            <span class="discount-badge">-{{ $product->discount_percentage }}%</span>
                        @endif
                    </div>
                    
                    {{-- Descripción corta --}}
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ Str::limit($product->description, 200) }}
                    </p>
                    
                    {{-- Disponibilidad --}}
                    <div class="flex items-center gap-2 mb-6">
                        @if($product->available_stock > 0)
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-sm text-green-600">
                                Disponible 
                                @if($product->available_stock <= 5)
                                    <span class="text-orange-500">(Solo quedan {{ $product->available_stock }})</span>
                                @endif
                            </span>
                        @else
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            <span class="text-sm text-red-600">Agotado</span>
                        @endif
                    </div>
                    
                    {{-- Selector de cantidad --}}
                    @if($product->available_stock > 0)
                        <div class="mb-6">
                            <label class="form-label text-sm mb-2">Cantidad</label>
                            <div class="flex items-center gap-3">
                                <button 
                                    wire:click="decrementQuantity"
                                    class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors"
                                    :disabled="$wire.quantity <= 1"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="w-12 text-center font-medium text-lg">{{ $quantity }}</span>
                                <button 
                                    wire:click="incrementQuantity"
                                    class="w-10 h-10 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-colors"
                                    :disabled="$wire.quantity >= $wire.product.available_stock"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        {{-- Mensaje para tarjeta (opcional) --}}
                        @if($product->accepts_card_message)
                            <div class="mb-6">
                                <label for="cardMessage" class="form-label text-sm mb-2">
                                    Mensaje para la tarjeta (opcional)
                                </label>
                                <textarea 
                                    wire:model="cardMessage"
                                    id="cardMessage"
                                    rows="2"
                                    maxlength="150"
                                    placeholder="Ej: ¡Feliz cumpleaños! Te quiero mucho..."
                                    class="form-input text-sm"
                                ></textarea>
                                <p class="text-xs text-gray-400 mt-1">{{ strlen($cardMessage) }}/150 caracteres</p>
                            </div>
                        @endif
                        
                        {{-- Botones de acción --}}
                        <div class="flex gap-3">
                            <button 
                                wire:click="addToCart"
                                wire:loading.attr="disabled"
                                class="flex-1 btn-primary"
                            >
                                <svg wire:loading.remove wire:target="addToCart" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <svg wire:loading wire:target="addToCart" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="addToCart">Añadir al carrito</span>
                                <span wire:loading wire:target="addToCart">Añadiendo...</span>
                            </button>
                            
                            <button 
                                wire:click="goToProduct"
                                class="btn-outline"
                                title="Ver detalles completos"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    @else
                        {{-- Producto agotado --}}
                        <div class="bg-red-50 border border-red-100 rounded-lg p-4 text-center">
                            <p class="text-red-600 font-medium">Este producto está agotado</p>
                            <p class="text-sm text-red-500 mt-1">Contáctanos para conocer disponibilidad</p>
                        </div>
                    @endif
                    
                    {{-- Información adicional --}}
                    <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                            <span>Entrega el mismo día*</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Satisfacción garantizada</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>Atención personalizada por WhatsApp</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @endif
</div>
