{{-- 
    ProductCard - Tarjeta de producto optimizada para conversión
    
    Patrón F aplicado:
    - Imagen (primer punto de fijación)
    - Nombre y precio (segunda línea horizontal)
    - CTA (zona inferior caliente)
--}}
<article 
    @class([
        'product-card group relative',
        'max-w-xs' => $size === 'small',
        'max-w-sm' => $size === 'default',
        'max-w-md' => $size === 'large',
    ])
>
    {{-- Imagen con overlay de acciones --}}
    <div class="relative overflow-hidden rounded-t-lg aspect-square bg-gray-100">
        <a href="{{ route('producto', $product->slug) }}">
            <img 
                src="{{ asset('storage/' . $product->image) }}"
                alt="{{ $product->name }}"
                class="product-card__image"
                loading="lazy"
            >
        </a>
        
        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-2 z-10">
            @if($product->discount_percentage > 0)
                <span class="product-card__badge--discount">
                    -{{ $product->discount_percentage }}%
                </span>
            @endif
            @if($product->is_new)
                <span class="product-card__badge--new">
                    Nuevo
                </span>
            @endif
            @if($product->is_bestseller)
                <span class="product-card__badge bg-yellow-500">
                    ⭐ Popular
                </span>
            @endif
        </div>
        
        {{-- Wishlist button --}}
        <button 
            class="absolute top-3 right-3 w-8 h-8 bg-white/80 backdrop-blur rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-white hover:text-red-500 z-10"
            aria-label="Añadir a favoritos"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
        
        {{-- Quick actions overlay --}}
        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
            @if($showQuickView)
                <button 
                    wire:click="openQuickView"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-primary hover:text-white transition-all transform translate-y-4 group-hover:translate-y-0 duration-300"
                    title="Vista rápida"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            @endif
            
            @if($showAddToCart && $product->available_stock > 0)
                <button 
                    wire:click="addToCart"
                    wire:loading.attr="disabled"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-primary hover:text-white transition-all transform translate-y-4 group-hover:translate-y-0 duration-300 delay-75"
                    title="Añadir al carrito"
                >
                    <svg wire:loading.remove wire:target="addToCart" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <svg wire:loading wire:target="addToCart" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            @endif
        </div>
        
        {{-- Stock badge --}}
        @if($product->available_stock < 1)
            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                <span class="bg-white text-dark px-4 py-2 rounded-full font-medium text-sm">
                    Agotado
                </span>
            </div>
        @elseif($product->available_stock <= 5)
            <div class="absolute bottom-3 left-3 right-3">
                <div class="bg-orange-500 text-white text-xs text-center py-1 rounded-full">
                    ¡Solo quedan {{ $product->available_stock }}!
                </div>
            </div>
        @endif
    </div>
    
    {{-- Información del producto --}}
    <div class="p-4">
        {{-- Categoría --}}
        @if($product->category)
            <a 
                href="{{ route('coleccion.categoria', $product->category->slug) }}"
                class="text-xs text-primary hover:underline uppercase tracking-wide"
            >
                {{ $product->category->name }}
            </a>
        @endif
        
        {{-- Nombre --}}
        <h3 class="font-display text-lg mt-1 mb-2 line-clamp-2">
            <a 
                href="{{ route('producto', $product->slug) }}"
                class="text-dark hover:text-primary transition-colors"
            >
                {{ $product->name }}
            </a>
        </h3>
        
        {{-- Rating --}}
        @if($product->reviews_count > 0)
            <div class="flex items-center gap-1 mb-2">
                <div class="flex">
                    @for($i = 1; $i <= 5; $i++)
                        <svg @class([
                            'w-3.5 h-3.5',
                            'text-yellow-400' => $i <= round($product->average_rating),
                            'text-gray-200' => $i > round($product->average_rating),
                        ]) fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-xs text-gray-500">({{ $product->reviews_count }})</span>
            </div>
        @endif
        
        {{-- Precio --}}
        <div class="flex items-center gap-2 mb-3">
            <span class="price">${{ number_format($product->current_price, 2) }}</span>
            @if($product->discount_percentage > 0)
                <span class="price--old text-sm">${{ number_format($product->price, 2) }}</span>
            @endif
        </div>
        
        {{-- CTA Button (visible en móvil, oculto en hover de desktop) --}}
        @if($product->available_stock > 0)
            <button 
                wire:click="addToCart"
                wire:loading.attr="disabled"
                class="w-full btn-primary text-sm py-2 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity"
            >
                <svg wire:loading.remove wire:target="addToCart" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span wire:loading.remove wire:target="addToCart">Añadir al carrito</span>
                <span wire:loading wire:target="addToCart">Añadiendo...</span>
            </button>
        @else
            <button disabled class="w-full bg-gray-200 text-gray-500 py-2 rounded-lg text-sm cursor-not-allowed">
                Agotado
            </button>
        @endif
    </div>
</article>
