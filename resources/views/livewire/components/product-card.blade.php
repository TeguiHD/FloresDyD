{{--
ProductCard - Tarjeta de producto optimizada para conversión

Patrón F aplicado:
- Imagen (primer punto de fijación)
- Nombre y precio (segunda línea horizontal)
- CTA (zona inferior caliente)
--}}
<article @class([
    'product-card group relative',
    'max-w-xs' => $size === 'small',
    'max-w-sm' => $size === 'default',
    'max-w-md' => $size === 'large',
])>
    @php
        $priceRange = $product->variant_price_range ?? ['min' => $product->current_price, 'max' => $product->current_price];
        $priceMin = (int) $priceRange['min'];
        $priceMax = (int) $priceRange['max'];
        $hasRange = $priceMin !== $priceMax;
    @endphp

    @php
        $discount = $product->calculated_discount ?? $product->discount_percentage;
        $customBadges = collect($product->custom_badges ?? [])->filter()->take(2);
        $benefitBadges = [];
        if ($product->has_fresh_guarantee) {
            $benefitBadges[] = $product->getBadgeLabel('benefit_fresh', 'Frescura garantizada');
        }
        if ($product->has_free_delivery) {
            $benefitBadges[] = $product->getBadgeLabel(
                'benefit_free_delivery',
                $product->free_delivery_city ? 'Envío gratis {city}' : 'Envío gratis',
                ['city' => $product->free_delivery_city]
            );
        }
        if ($product->has_personalized_card) {
            $benefitBadges[] = $product->getBadgeLabel('benefit_card', 'Tarjeta incluida');
        }
        $promoLabel = $product->getBadgeLabel(
            'promo',
            $discount ? '-' . $discount . '%' : 'Oferta',
            ['discount' => $discount]
        );
        $newLabel = $product->getBadgeLabel('new', 'Nuevo');
        $featuredLabel = $product->getBadgeLabel('featured', 'Destacado');
        $bestsellerLabel = $product->getBadgeLabel('bestseller', '⭐ Popular');
        $lowStockLabel = $product->getBadgeLabel('low_stock', '¡Quedan pocas!');
        $stockLeftLabel = $product->getBadgeLabel('stock_left', '¡Solo quedan {count}!', ['count' => $product->available_stock]);
        $soldOutLabel = $product->getBadgeLabel('sold_out', 'Agotado');
    @endphp
    @php
        $hasVariants = $product->activeVariants->isNotEmpty();
    @endphp

    {{-- Imagen con overlay de acciones --}}
    <div class="relative overflow-hidden rounded-t-lg aspect-square bg-gray-100" x-data="{
            images: @js($product->image_urls),
            index: 0,
            interval: null,
            start() {
                if (!this.images || this.images.length < 2) return;
                this.stop();
                this.interval = setInterval(() => {
                    this.index = (this.index + 1) % this.images.length;
                }, 1800);
            },
            stop() {
                if (this.interval) {
                    clearInterval(this.interval);
                    this.interval = null;
                }
                this.index = 0;
            }
        }" @mouseenter="start()" @mouseleave="stop()">
        <a href="{{ route('producto', $product->slug) }}" class="block h-full">
            <img :src="images[index] || '{{ $product->main_image_url }}'" alt="{{ $product->name }}"
                class="product-card__image transition-transform duration-500 group-hover:scale-[1.06]" loading="lazy">
        </a>

        {{-- Badges (max 2 en móvil para no tapar imagen) --}}
        <div class="absolute top-2 left-2 sm:top-3 sm:left-3 flex flex-col gap-1 sm:gap-2 z-10">
            @if($product->promo_active)
                <span class="product-card__badge product-card__badge--discount">
                    {{ $promoLabel }}
                </span>
            @endif
            @if($product->is_new)
                <span class="product-card__badge product-card__badge--new">
                    {{ $newLabel }}
                </span>
            @endif
            <span class="hidden sm:inline-flex">
                @if($product->is_featured)
                    <span class="product-card__badge product-card__badge--featured">
                        {{ $featuredLabel }}
                    </span>
                @endif
            </span>
            <span class="hidden sm:inline-flex">
                @if($product->is_bestseller)
                    <span class="product-card__badge product-card__badge--bestseller">
                        {{ $bestsellerLabel }}
                    </span>
                @endif
            </span>
            @if($product->available_stock > 0 && $product->low_stock)
                <span class="product-card__badge product-card__badge--low">
                    {{ $lowStockLabel }}
                </span>
            @endif
            @foreach($customBadges as $badge)
                <span class="hidden sm:inline-flex product-card__badge product-card__badge--custom">
                    {{ $badge }}
                </span>
            @endforeach
        </div>

        {{-- Wishlist button --}}
        <button
            class="absolute top-3 right-3 w-8 h-8 bg-white/80 backdrop-blur rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-white hover:text-red-500 z-10"
            aria-label="Añadir a favoritos">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </button>

        {{-- Quick actions overlay --}}
        <div
            class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3 z-20">
            @if($showQuickView)
                <button wire:click="openQuickView"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-primary hover:text-white transition-all transform translate-y-4 group-hover:translate-y-0 duration-300"
                    title="Vista rápida">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            @endif

            @if($showAddToCart && $product->available_stock > 0)
                <button wire:click="addToCart" wire:loading.attr="disabled"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-primary hover:text-white transition-all transform translate-y-4 group-hover:translate-y-0 duration-300 delay-75"
                    title="{{ $hasVariants ? 'Elegir formato' : 'Añadir al carrito' }}">
                    <svg wire:loading.remove wire:target="addToCart" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <svg wire:loading wire:target="addToCart" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </button>
            @endif
        </div>

        {{-- Stock badge --}}
        @if($product->available_stock < 1)
            <div class="absolute inset-0 bg-black/50 flex items-center justify-center pointer-events-none z-10">
                <span class="bg-white text-dark px-3 py-1.5 sm:px-4 sm:py-2 rounded-full font-medium text-xs sm:text-sm">
                    {{ $soldOutLabel }}
                </span>
            </div>
        @elseif($product->available_stock <= 5)
            <div class="absolute bottom-2 left-2 right-2 sm:bottom-3 sm:left-3 sm:right-3">
                <div class="product-card__stock-callout">
                    {{ $stockLeftLabel }}
                </div>
            </div>
        @endif
    </div>

    {{-- Información del producto --}}
    <div class="flex flex-col flex-1 p-4 sm:p-5">
        {{-- Categoría --}}
        @if($product->category)
            <a href="{{ route('coleccion.categoria', $product->category->slug) }}"
                class="text-xs text-primary hover:underline uppercase tracking-wide">
                {{ $product->category->name }}
            </a>
        @endif

        {{-- Nombre --}}
        <h3 class="font-display text-sm sm:text-lg mt-1 mb-1 sm:mb-2 line-clamp-2">
            <a href="{{ route('producto', $product->slug) }}" class="text-dark hover:text-primary transition-colors">
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
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                </div>
                <span class="text-xs text-gray-500">({{ $product->reviews_count }})</span>
            </div>
        @endif

        {{-- Precio --}}
        <div class="flex flex-wrap items-center gap-2 mb-3">
            @if($hasRange)
                <span class="price">Desde ${{ number_format($priceMin, 0, ',', '.') }}</span>
                <span class="text-xs text-dark/50">hasta ${{ number_format($priceMax, 0, ',', '.') }}</span>
            @else
                <span class="price">${{ number_format($priceMin, 0, ',', '.') }}</span>
                @if($product->promo_active && $product->compare_price)
                    <span class="price--old text-sm">${{ number_format($product->compare_price, 0, ',', '.') }}</span>
                @endif
            @endif
        </div>

        @if($product->promo_active && $product->promo_ends_at)
            <div class="mb-3 text-xs text-ink/60">
                Oferta termina en:
                <livewire:components.countdown-timer :targetDate="$product->promo_ends_at->toIso8601String()"
                    :compact="true" :key="'promo-' . $product->id" />
            </div>
        @endif



        {{-- CTA Button (visible en móvil, oculto en hover de desktop) --}}
        <div class="mt-auto pt-2">
            @if($product->available_stock > 0)
                <button wire:click="addToCart" wire:loading.attr="disabled" class="w-full btn-primary text-sm py-2">
                    <svg wire:loading.remove wire:target="addToCart" class="w-4 h-4 sm:w-4 sm:h-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span wire:loading.remove wire:target="addToCart" class="hidden sm:inline">Añadir al carrito</span>
                    <span wire:loading.remove wire:target="addToCart" class="sm:hidden">Agregar</span>
                    <span wire:loading wire:target="addToCart">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                </button>
            @else
                <button disabled class="w-full bg-gray-200 text-gray-500 py-2 rounded-lg text-sm cursor-not-allowed">
                    {{ $soldOutLabel }}
                </button>
            @endif
        </div>
    </div>
</article>