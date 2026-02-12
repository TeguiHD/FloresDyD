{{-- Detalle de Producto - Flores D&D --}}
<div class="min-h-dvh">
    {{-- Breadcrumb --}}
    <nav class="bg-secondary/30 py-3">
        <div class="container mx-auto px-4">
            <div class="flex items-center gap-2 text-sm text-dark/60">
                <a href="{{ route('home') }}" class="hover:text-primary">Inicio</a>
                <span>/</span>
                <a href="{{ route('coleccion') }}" class="hover:text-primary">Colección</a>
                @if($product->category)
                    <span>/</span>
                    <a href="{{ route('coleccion.categoria', $product->category->slug) }}" class="hover:text-primary">
                        {{ $product->category->name }}
                    </a>
                @endif
                <span>/</span>
                <span class="text-primary">{{ $product->name }}</span>
            </div>
        </div>
    </nav>

    {{-- Producto Principal --}}
    <section class="py-8 lg:py-12">
        <div class="container mx-auto px-4">
            <div class="lg:flex lg:gap-12">
                {{-- Galería de Imágenes --}}
                <div class="lg:w-1/2 mb-8 lg:mb-0">
                    <div 
                        x-data="{ 
                            activeImage: '{{ $product->image_urls[0] ?? $product->main_image_url }}',
                            images: @js($product->image_urls)
                        }"
                        class="space-y-4"
                    >
                        {{-- Imagen Principal --}}
                        <div class="aspect-square bg-secondary/20 rounded-2xl overflow-hidden">
                            <img 
                                :src="activeImage"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover transition-transform duration-500 hover:scale-[1.05]"
                            >
                        </div>

                        {{-- Thumbnails --}}
                        @if($product->image_urls && count($product->image_urls) > 1)
                            <div class="flex gap-3 overflow-x-auto pb-2">
                                @foreach($product->image_urls as $image)
                                    <button 
                                        @click="activeImage = '{{ $image }}'"
                                        :class="{ 'ring-2 ring-primary': activeImage === '{{ $image }}' }"
                                        class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden"
                                    >
                                        <img src="{{ $image }}" alt="" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Información del Producto --}}
                <div class="lg:w-1/2">
                    {{-- Badges --}}
                    @php
                        $discount = $product->calculated_discount ?? $product->discount_percentage;
                        $customBadges = collect($product->custom_badges ?? [])->filter()->take(3);
                        $promoLabel = $product->getBadgeLabel(
                            'promo',
                            $discount ? '-' . $discount . '% off' : 'Oferta activa',
                            ['discount' => $discount]
                        );
                        $newLabel = $product->getBadgeLabel('new', 'Nuevo');
                        $featuredLabel = $product->getBadgeLabel('featured', '⭐ Destacado');
                        $bestsellerLabel = $product->getBadgeLabel('bestseller', 'Más vendido');
                        $stockLeftLabel = $product->getBadgeLabel('stock_left', '¡Últimas {count} unidades!', ['count' => $product->available_stock]);
                    @endphp
                    <div class="flex flex-wrap gap-2 mb-4">
                        @if($product->promo_active)
                            <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">
                                {{ $promoLabel }}
                            </span>
                        @endif
                        @if($product->is_new)
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-full">
                                {{ $newLabel }}
                            </span>
                        @endif
                        @if($product->is_featured)
                            <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">
                                {{ $featuredLabel }}
                            </span>
                        @endif
                        @if($product->is_bestseller)
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                {{ $bestsellerLabel }}
                            </span>
                        @endif
                        @if($product->available_stock <= 5 && $product->available_stock > 0)
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">
                                {{ $stockLeftLabel }}
                            </span>
                        @endif
                        @foreach($customBadges as $badge)
                            <span class="px-3 py-1 bg-slate-900 text-white text-xs font-medium rounded-full">
                                {{ $badge }}
                            </span>
                        @endforeach
                    </div>

                    {{-- Nombre y Precio --}}
                    <h1 class="font-serif text-3xl lg:text-4xl text-dark mb-2">{{ $product->name }}</h1>
                    
                    {{-- Precio base (visible cuando no hay variantes o como precio inicial) --}}
                    @php
                        $selectedVariant = $product->activeVariants->firstWhere('id', $selectedVariantId);
                        $displayPrice = \App\Services\CartService::calculateUnitPrice($product, $selectedVariant, $customValue);
                        $displayCompare = \App\Services\CartService::calculateComparePrice($product, $selectedVariant);
                        $hasPromo = $product->promo_active && $displayCompare > $displayPrice;
                    @endphp
                    @if($product->activeVariants->isEmpty())
                    <div class="flex flex-wrap items-baseline gap-3 mb-4">
                        <span class="text-3xl font-bold text-primary">${{ number_format($displayPrice, 0, ',', '.') }}</span>
                        @if($hasPromo)
                            <span class="text-lg text-dark/40 line-through">${{ number_format($displayCompare, 0, ',', '.') }}</span>
                            <span class="text-sm text-green-600 font-medium">
                                {{ $displayCompare > 0 ? round((($displayCompare - $displayPrice) / $displayCompare) * 100) : 0 }}% OFF
                            </span>
                        @endif
                    </div>
                    @else
                    {{-- Precio con variantes: se muestra desde Alpine.js más abajo --}}
                    <div class="flex flex-wrap items-baseline gap-3 mb-4">
                        @php
                            $priceRange = $product->variant_price_range;
                            $showRange = $priceRange['min'] !== $priceRange['max'];
                        @endphp
                        @if($showRange)
                            <span class="text-2xl font-bold text-primary">Desde ${{ number_format($priceRange['min'], 0, ',', '.') }}</span>
                            <span class="text-sm text-dark/40">hasta ${{ number_format($priceRange['max'], 0, ',', '.') }}</span>
                        @else
                            <span class="text-3xl font-bold text-primary">${{ number_format($displayPrice, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    @endif

                    @if($product->promo_active && $product->promo_ends_at)
                        <div class="mb-4 text-sm text-ink/60">
                            Oferta termina en:
                            <livewire:components.countdown-timer :targetDate="$product->promo_ends_at->toIso8601String()" :key="'promo-detail-'.$product->id" />
                        </div>
                    @endif

                    {{-- Descripción corta --}}
                    @if($product->short_description)
                        <p class="text-dark/70 mb-6">{{ $product->short_description }}</p>
                    @endif

                    @if($product->has_fresh_guarantee || $product->has_free_delivery || $product->has_personalized_card)
                        <div class="product-card__chips mb-6">
                            @if($product->has_fresh_guarantee)
                                <span class="product-card__chip">{{ $product->getBadgeLabel('benefit_fresh', 'Frescura garantizada') }}</span>
                            @endif
                            @if($product->has_free_delivery)
                                <span class="product-card__chip">{{ $product->getBadgeLabel('benefit_free_delivery', $product->free_delivery_city ? 'Envío gratis {city}' : 'Envío gratis', ['city' => $product->free_delivery_city]) }}</span>
                            @endif
                            @if($product->has_personalized_card)
                                <span class="product-card__chip">{{ $product->getBadgeLabel('benefit_card', 'Tarjeta personalizada') }}</span>
                            @endif
                        </div>
                    @endif

                    {{-- Opciones de Formato --}}
                    @if($product->activeVariants->isNotEmpty())
                        <div
                            class="mb-6"
                            x-data="{
                                selectedId: @entangle('selectedVariantId'),
                                customVal: @entangle('customValue'),
                                basePrice: {{ (int) $product->price }},
                                comparePrice: {{ (int) ($product->compare_price ?: $product->price) }},
                                promoActive: {{ $product->promo_active ? 'true' : 'false' }},
                                variants: @js($product->activeVariants->map(fn($v) => [
                                    'id' => $v->id,
                                    'name' => $v->name,
                                    'label' => $v->label ?: $v->name,
                                    'type' => $v->type,
                                    'price_modifier' => (int) $v->price_modifier,
                                    'price_override' => $v->price_override !== null ? (int) $v->price_override : null,
                                    'min_value' => (int) ($v->min_value ?? 1),
                                    'max_value' => (int) ($v->max_value ?? ($v->min_value ?? 1)),
                                    'step_value' => (int) ($v->step_value ?? 1),
                                    'price_per_unit' => (int) ($v->price_per_unit ?? 0),
                                    'unit_label' => $v->unit_label,
                                ])->values()),
                                get selected() {
                                    return this.variants.find(v => v.id === this.selectedId) || null;
                                },
                                get displayPrice() {
                                    const v = this.selected;
                                    if (!v) return Math.max(0, this.basePrice);
                                    if (v.type === 'range') {
                                        const min = Math.max(1, v.min_value);
                                        const cVal = Math.max(min, Math.min(this.customVal || min, v.max_value));
                                        const extra = Math.max(0, cVal - min) * v.price_per_unit;
                                        const base = v.price_override !== null ? v.price_override : this.basePrice;
                                        return Math.max(0, base + extra);
                                    }
                                    if (v.price_override !== null) return Math.max(0, v.price_override);
                                    return Math.max(0, this.basePrice + v.price_modifier);
                                },
                                get displayCompare() {
                                    const v = this.selected;
                                    const base = this.comparePrice;
                                    if (!v) return Math.max(0, base);
                                    if (v.price_override !== null) return Math.max(0, v.price_override);
                                    return Math.max(0, base + v.price_modifier);
                                },
                                get hasPromo() {
                                    return this.promoActive && this.displayCompare > this.displayPrice;
                                },
                                get discount() {
                                    if (!this.hasPromo || this.displayCompare <= 0) return 0;
                                    return Math.round(((this.displayCompare - this.displayPrice) / this.displayCompare) * 100);
                                },
                                formatCLP(n) {
                                    return '$' + new Intl.NumberFormat('es-CL').format(n);
                                },
                                selectVariant(id) {
                                    this.selectedId = id;
                                    const v = this.variants.find(v => v.id === id);
                                    if (v && v.type === 'range') {
                                        this.customVal = v.min_value;
                                    } else {
                                        this.customVal = null;
                                    }
                                },
                                clampCustom() {
                                    const v = this.selected;
                                    if (!v || v.type !== 'range') return;
                                    let val = parseInt(this.customVal) || v.min_value;
                                    val = Math.max(v.min_value, Math.min(val, v.max_value));
                                    const step = Math.max(1, v.step_value);
                                    val = v.min_value + Math.floor((val - v.min_value) / step) * step;
                                    this.customVal = val;
                                }
                            }"
                        >
                            <label class="block text-sm font-medium text-dark mb-3">Elige tu formato</label>

                            {{-- Selector visual de formatos --}}
                            <div class="grid gap-2 sm:grid-cols-2">
                                @foreach($product->activeVariants as $variant)
                                    @php
                                        $variantPrice = $variant->price_override !== null
                                            ? $variant->price_override
                                            : $product->price + $variant->price_modifier;
                                    @endphp
                                    <button
                                        type="button"
                                        @click="selectVariant({{ $variant->id }})"
                                        :class="selectedId === {{ $variant->id }}
                                            ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
                                            : 'border-secondary hover:border-primary/40'"
                                        class="relative flex items-center gap-3 p-3 border rounded-xl transition-all text-left"
                                    >
                                        {{-- Indicador de selección --}}
                                        <div
                                            :class="selectedId === {{ $variant->id }}
                                                ? 'bg-primary border-primary'
                                                : 'border-gray-300'"
                                            class="flex items-center justify-center w-5 h-5 rounded-full border-2 flex-shrink-0 transition-colors"
                                        >
                                            <div
                                                x-show="selectedId === {{ $variant->id }}"
                                                class="w-2 h-2 bg-white rounded-full"
                                            ></div>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="font-medium text-dark text-sm truncate">{{ $variant->label ?: $variant->name }}</span>
                                                @if($variant->type === 'fixed')
                                                    <span class="text-sm font-semibold text-primary flex-shrink-0">${{ number_format($variantPrice, 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-xs text-dark/50 flex-shrink-0">Desde ${{ number_format($variantPrice, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                            @if($variant->type === 'range' && $variant->unit_label)
                                                <p class="text-xs text-dark/40 mt-0.5">{{ $variant->min_value ?? 1 }} a {{ $variant->max_value ?? 1 }} {{ $variant->unit_label }}</p>
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            </div>

                            {{-- Selector de cantidad personalizable (range) --}}
                            <template x-if="selected && selected.type === 'range'">
                                <div class="mt-4 p-4 bg-secondary/30 rounded-xl space-y-3">
                                    <label class="block text-sm font-medium text-dark">
                                        <span x-text="selected.label || 'Personaliza tu pedido'"></span>
                                        <span class="text-dark/40 font-normal" x-text="selected.unit_label ? '(' + selected.unit_label + ')' : ''"></span>
                                    </label>

                                    <div class="flex items-center gap-3">
                                        <button
                                            type="button"
                                            @click="customVal = Math.max(selected.min_value, (customVal || selected.min_value) - selected.step_value); clampCustom()"
                                            :disabled="customVal <= selected.min_value"
                                            class="w-10 h-10 flex items-center justify-center rounded-lg border border-secondary bg-white hover:bg-secondary/50 transition disabled:opacity-30"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 12H4"/></svg>
                                        </button>

                                        <input
                                            type="number"
                                            x-model.number="customVal"
                                            @change="clampCustom()"
                                            :min="selected.min_value"
                                            :max="selected.max_value"
                                            :step="selected.step_value"
                                            class="w-20 text-center text-lg font-semibold px-3 py-2 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >

                                        <button
                                            type="button"
                                            @click="customVal = Math.min(selected.max_value, (customVal || selected.min_value) + selected.step_value); clampCustom()"
                                            :disabled="customVal >= selected.max_value"
                                            class="w-10 h-10 flex items-center justify-center rounded-lg border border-secondary bg-white hover:bg-secondary/50 transition disabled:opacity-30"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
                                        </button>

                                        <span class="text-sm text-dark/50" x-text="selected.unit_label || ''"></span>
                                    </div>

                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-dark/40">
                                            Rango: <span x-text="selected.min_value"></span> — <span x-text="selected.max_value"></span>
                                            <span x-text="selected.unit_label || ''"></span>
                                        </span>
                                        <span class="text-primary font-medium" x-show="selected.price_per_unit > 0">
                                            +<span x-text="formatCLP(selected.price_per_unit)"></span> por <span x-text="selected.unit_label || 'unidad'"></span> adicional
                                        </span>
                                    </div>

                                    {{-- Barra de progreso visual --}}
                                    <div class="w-full h-1.5 bg-secondary rounded-full overflow-hidden">
                                        <div
                                            class="h-full bg-primary rounded-full transition-all duration-300"
                                            :style="'width: ' + (((customVal - selected.min_value) / Math.max(1, selected.max_value - selected.min_value)) * 100) + '%'"
                                        ></div>
                                    </div>
                                </div>
                            </template>

                            {{-- Precio en tiempo real (Alpine.js) --}}
                            <div class="mt-4 flex flex-wrap items-baseline gap-3" x-show="selected">
                                <span class="text-2xl font-bold text-primary" x-text="formatCLP(displayPrice)"></span>
                                <template x-if="hasPromo">
                                    <span class="text-lg text-dark/40 line-through" x-text="formatCLP(displayCompare)"></span>
                                </template>
                                <template x-if="hasPromo">
                                    <span class="text-sm text-green-600 font-medium" x-text="discount + '% OFF'"></span>
                                </template>
                            </div>
                        </div>
                    @endif

                    {{-- Mensaje para Tarjeta --}}
                    @if($product->has_personalized_card)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-dark mb-2">
                                Mensaje para la tarjeta (opcional)
                            </label>
                            <textarea 
                                wire:model="cardMessage"
                                rows="3"
                                maxlength="200"
                                placeholder="Escribe un mensaje personalizado para incluir en tu arreglo..."
                                class="w-full px-4 py-3 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                            ></textarea>
                            <p class="text-xs text-dark/50 mt-1">{{ strlen($cardMessage ?? '') }}/200 caracteres</p>
                        </div>
                    @endif

                    {{-- Cantidad y Agregar al Carrito --}}
                    <div class="flex flex-wrap items-center gap-4 mb-6">
                        {{-- Selector de Cantidad --}}
                        <div class="flex items-center border border-secondary rounded-lg">
                            <button 
                                wire:click="decrementQuantity"
                                class="w-12 h-12 flex items-center justify-center hover:bg-secondary/50 transition"
                                :disabled="$quantity <= 1"
                            >
                                <x-flux::icon name="minus" class="w-5 h-5" />
                            </button>
                            <span class="w-12 text-center font-medium">{{ $quantity }}</span>
                            <button 
                                wire:click="incrementQuantity"
                                class="w-12 h-12 flex items-center justify-center hover:bg-secondary/50 transition"
                            >
                                <x-flux::icon name="plus" class="w-5 h-5" />
                            </button>
                        </div>

                        {{-- Botón Agregar --}}
                        <button 
                            wire:click="addToCart"
                            class="flex-1 flex items-center justify-center gap-2 px-8 py-4 bg-primary text-white rounded-full hover:bg-primary-dark transition font-medium"
                        >
                            <x-flux::icon name="shopping-bag" class="w-5 h-5" />
                            Agregar al carrito
                        </button>

                        {{-- Wishlist --}}
                        <button 
                            wire:click="addToWishlist"
                            class="w-12 h-12 flex items-center justify-center border border-secondary rounded-full hover:border-primary hover:text-primary transition"
                        >
                            <x-flux::icon name="heart" class="w-5 h-5" />
                        </button>
                    </div>

                    {{-- Info de Envío --}}
                    <div class="bg-secondary/30 rounded-xl p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <x-flux::icon name="truck" class="w-5 h-5 text-primary" />
                            <span class="text-sm">Envío gratis en compras mayores a $800</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-flux::icon name="clock" class="w-5 h-5 text-primary" />
                            <span class="text-sm">Entrega el mismo día (pedidos antes de las 2pm)</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-flux::icon name="shield-check" class="w-5 h-5 text-primary" />
                            <span class="text-sm">Garantía de frescura o te reponemos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Descripción Completa --}}
    @if($product->description)
        <section class="py-8 border-t border-secondary">
            <div class="container mx-auto px-4">
                <h2 class="font-serif text-2xl text-dark mb-4">Descripción</h2>
                <div class="prose prose-lg max-w-none text-dark/70">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        </section>
    @endif

    @php
        $variantLabels = $product->activeVariants->map(fn ($variant) => $variant->label ?: $variant->name)->filter()->values();
        $hasVariants = $variantLabels->isNotEmpty();
        $includesText = $product->short_description
            ?: \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 140, '');
        if (!$includesText) {
            $includesText = 'Incluye flores frescas seleccionadas y presentación lista para regalar.';
        } else {
            $includesText = rtrim($includesText, '.') . '. Presentación lista para regalar.';
        }
        $colorSensitive = (bool) preg_match('/rosa|tulip[aá]n/i', (string) $product->name);

        $faqItems = [
            [
                'question' => "¿Qué incluye {$product->name}?",
                'answer' => $includesText,
            ],
            [
                'question' => '¿Qué tamaños tiene?',
                'answer' => $hasVariants
                    ? 'Disponible en: ' . $variantLabels->implode(', ') . '. El precio se actualiza al elegir el formato.'
                    : 'Formato único con precio fijo.',
            ],
            [
                'question' => '¿Puedo agregar un mensaje personalizado?',
                'answer' => $product->has_personalized_card
                    ? 'Sí, puedes añadir un mensaje personalizado sin costo adicional.'
                    : 'Puedes solicitarlo al momento de la compra y confirmamos disponibilidad.',
            ],
            [
                'question' => '¿Entregan el mismo día en Santiago?',
                'answer' => 'Sí, según disponibilidad. Recomendamos pedir antes de las 14:00 para entrega el mismo día.',
            ],
        ];

        if ($colorSensitive) {
            $faqItems[] = [
                'question' => '¿Puedo elegir tonos o colores?',
                'answer' => 'Sí, puedes consultar por tonos disponibles y confirmamos stock según temporada.',
            ];
        }

        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function ($item) {
                return [
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item['answer'],
                    ],
                ];
            }, $faqItems),
        ];
    @endphp

    <section class="py-10 border-t border-secondary/60 bg-white/70">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="font-serif text-2xl text-dark mb-6">Preguntas frecuentes</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($faqItems as $item)
                        <details class="bg-white rounded-xl p-5 shadow-card">
                            <summary class="font-medium text-dark cursor-pointer">{{ $item['question'] }}</summary>
                            <p class="text-dark/60 text-sm mt-3">{{ $item['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('contacto') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark transition-colors text-sm font-medium">
                        ¿Necesitas ayuda? Contáctanos
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Reseñas --}}
    @if($reviews->count() > 0)
        <section class="py-8 border-t border-secondary">
            <div class="container mx-auto px-4">
                <h2 class="font-serif text-2xl text-dark mb-6">Reseñas de clientes</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($reviews as $review)
                        <div class="bg-secondary/20 rounded-xl p-6">
                            <div class="flex items-center gap-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                @endfor
                            </div>
                            <p class="text-dark/70 mb-3">{{ $review->comment }}</p>
                            <p class="text-sm text-dark/50">— {{ $review->customer_name }}, {{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Productos Relacionados --}}
    @if($relatedProducts->count() > 0)
        <section class="py-12 bg-secondary/20">
            <div class="container mx-auto px-4">
                <h2 class="font-serif text-2xl text-dark mb-6 text-center">También te puede gustar</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
                    @foreach($relatedProducts as $related)
                        <livewire:components.product-card :product="$related" :key="'related-'.$related->id" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>

<script type="application/ld+json">
{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
