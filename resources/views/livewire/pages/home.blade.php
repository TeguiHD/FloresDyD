{{-- 
    HOME PAGE - Flores D&D
    
    Diseño minimalista y elegante:
    - Hero limpio con tipografía protagonista
    - Categorías en strip horizontal
    - Productos sin ruido visual
    - Espacios generosos de respiración
--}}

@php
    $title = 'Flores a domicilio en Valdivia y Santiago | Flores D&D';
    $metaDescription = 'Floristería artesanal en Valdivia y Santiago. Ramos, arreglos y eventos con entrega a domicilio y atención personalizada.';
@endphp

<div>
    {{-- ========================================
         HERO - Tipografía protagonista, minimalista
         ======================================== --}}
    <section class="hero relative" style="--hero-image: url('{{ asset('images/hero-flowers.svg') }}')">
        @php $whatsappNumber = \App\Models\SiteSetting::getWhatsappNumber(); @endphp
        <div class="hero__decoration hero__decoration--1"></div>
        <div class="hero__decoration hero__decoration--2"></div>
        <div class="hero__overlay"></div>
        
        <div class="hero__content">
            <p class="text-secondary/90 tracking-[0.25em] uppercase text-sm mb-6 animate-on-scroll">
                Floristería Artesanal
            </p>
            
            <h1 class="font-display text-5xl md:text-6xl lg:text-7xl text-white mb-4 animate-on-scroll animate-delay-100 leading-tight">
                Flores que expresan
            </h1>
            <div class="font-display text-4xl md:text-5xl lg:text-6xl text-white/95 mb-8 animate-on-scroll animate-delay-200">
                <livewire:components.typewriter 
                    :texts="['amor profundo', 'sorpresas elegantes', 'momentos inolvidables', 'gratitud sincera', 'celebraciones únicas']" 
                    :loop="true"
                />
            </div>
            
            <p class="text-white/70 text-base md:text-lg max-w-lg mx-auto mb-10 animate-on-scroll animate-delay-300 font-light">
                Arreglos únicos, armados a mano el día de tu pedido.
            </p>
            
            <div class="flex items-center justify-center gap-4 animate-on-scroll animate-delay-400">
                <a href="{{ route('coleccion') }}" class="group inline-flex items-center gap-2 bg-white text-primary px-7 py-3.5 rounded-full font-medium hover:bg-secondary transition-colors">
                    <span>Ver Colección</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
                @if($whatsappNumber)
                    <a 
                        href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hola, me gustaría hacer un pedido personalizado') }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 text-white/80 hover:text-white px-5 py-3.5 transition-colors text-sm"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Pedido a Medida
                    </a>
                @endif
            </div>
        </div>
        
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce opacity-50">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </div>
    </section>

    {{-- ========================================
         CATEGORÍAS - Strip horizontal minimalista
         ======================================== --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="container-custom">
            <div class="text-center mb-14 animate-on-scroll">
                <p class="text-primary tracking-[0.2em] uppercase text-xs font-medium mb-3">Nuestras Colecciones</p>
                <h2 class="font-display text-3xl lg:text-4xl text-dark">
                    Explora por Categoría
                </h2>
            </div>
            
            @php
                $catIcons = [
                    'bouquets' => [
                        'icon' => '<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 9a2 2 0 114 0 2 2 0 01-4 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7a2 2 0 114 0 2 2 0 01-4 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 13a2 2 0 114 0 2 2 0 01-4 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 11l3 6m3-6l-3 6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.5 21h7l-1-3H9.5l-1 3z"/></svg>',
                        'color' => 'bg-rose-50 hover:bg-rose-100 border-rose-100',
                    ],
                    'regalos' => [
                        'icon' => '<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 10h16"/><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 7c0 1.4 1.1 2.5 2.5 2.5H12V6c0-1.1-.9-2-2-2s-2 .9-2 3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 7c0 1.4-1.1 2.5-2.5 2.5H12V6c0-1.1.9-2 2-2s2 .9 2 3z"/></svg>',
                        'color' => 'bg-amber-50 hover:bg-amber-100 border-amber-100',
                    ],
                    'eventos' => [
                        'icon' => '<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="3.5" y="6.5" width="17" height="14" rx="2" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 3.5v3M17 3.5v3M3.5 10.5h17"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l1.5 1.5L13 13"/></svg>',
                        'color' => 'bg-yellow-50 hover:bg-yellow-100 border-yellow-100',
                    ],
                    'novios' => [
                        'icon' => '<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="9" cy="12" r="3.5" stroke-width="1.5"/><circle cx="15" cy="12" r="3.5" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.5 14.5l1 1"/></svg>',
                        'color' => 'bg-purple-50 hover:bg-purple-100 border-purple-100',
                    ],
                    'condolencias' => [
                        'icon' => '<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 20s-7-4.5-7-9a4 4 0 017-2 4 4 0 017 2c0 4.5-7 9-7 9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9.5v5.5"/></svg>',
                        'color' => 'bg-green-50 hover:bg-green-100 border-green-100',
                    ],
                ];
            @endphp
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach($categories as $index => $category)
                    @php 
                        $cat = $catIcons[$category->slug] ?? null;
                        if (!$cat) continue;
                    @endphp
                    <a 
                        href="{{ route('coleccion.categoria', $category->slug) }}"
                        class="category-card category-card--{{ $category->slug }} group p-6 rounded-3xl border border-gray-100 transition-all duration-300 hover:shadow-md animate-on-scroll animate-delay-{{ ($index + 1) * 100 }}"
                    >
                        <span class="category-card__icon block mb-4 group-hover:scale-110 transition-transform duration-300">
                            {!! $cat['icon'] !!}
                        </span>
                        <h3 class="font-display text-base text-dark mb-2">{{ $category->name }}</h3>
                        @if($category->children->count() > 0)
                            <ul class="text-xs text-gray-500 leading-relaxed space-y-1">
                                @foreach($category->children->take(3) as $child)
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary/30"></span>
                                        <span>{{ $child->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <span class="mt-4 inline-flex items-center gap-1 text-xs text-primary/80 group-hover:text-primary transition-colors">
                            Ver categoría
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================================
         PROPUESTA DE VALOR - Iconos limpios
         ======================================== --}}
    <section class="py-16 border-y border-gray-100">
        <div class="container-custom">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @php
                    $values = [
                        ['icon' => 'M5 13l4 4L19 7', 'label' => 'Flores 100% Frescas'],
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Entrega el Mismo Día'],
                        ['icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'label' => 'Hechas con Amor'],
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Compra Segura'],
                    ];
                @endphp
                @foreach($values as $v)
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $v['icon'] }}"/>
                        </svg>
                        <span class="text-sm text-gray-600 font-medium">{{ $v['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================================
         PRODUCTOS DESTACADOS
         ======================================== --}}
    @if($featuredProducts->count() > 0)
    <section class="py-20 lg:py-28 bg-cream">
        <div class="container-custom">
            <div class="flex items-end justify-between mb-12">
                <div class="animate-on-scroll">
                    <p class="text-primary tracking-[0.2em] uppercase text-xs font-medium mb-3">Los Favoritos</p>
                    <h2 class="font-display text-3xl lg:text-4xl text-dark">
                        Arreglos Más Vendidos
                    </h2>
                </div>
                <a href="{{ route('coleccion') }}?sort=bestseller" class="hidden md:inline-flex items-center gap-1.5 text-sm text-primary font-medium hover:text-primary-dark transition-colors animate-on-scroll">
                    Ver todos
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 lg:gap-6">
                @foreach($featuredProducts as $index => $product)
                    <div class="animate-on-scroll animate-delay-{{ min(($index + 1) * 100, 400) }}">
                        <livewire:components.product-card 
                            :product="$product"
                            :key="'featured-'.$product->id"
                        />
                    </div>
                @endforeach
            </div>
            
            <div class="text-center mt-10 md:hidden">
                <a href="{{ route('coleccion') }}?sort=bestseller" class="inline-flex items-center gap-1.5 text-sm text-primary font-medium">
                    Ver todos →
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================
         BANNER - Elegante y limpio
         ======================================== --}}
    @if($dealsProducts->count() > 0)
    <section class="py-20 lg:py-28 bg-primary">
        <div class="container-custom text-center">
            <p class="text-secondary/80 tracking-[0.2em] uppercase text-xs mb-4">Ofertas Especiales</p>
            <h2 class="font-display text-3xl lg:text-5xl text-white mb-4">
                Hasta 30% de descuento
            </h2>
            <p class="text-white/60 max-w-md mx-auto mb-8">
                Arreglos seleccionados a precios especiales. Por tiempo limitado.
            </p>
            <a href="{{ route('coleccion') }}?filter=ofertas" class="inline-flex items-center gap-2 bg-white text-primary px-7 py-3 rounded-full font-medium hover:bg-secondary transition-colors">
                Ver Ofertas
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </section>
    @endif

    {{-- ========================================
         NOVEDADES
         ======================================== --}}
    @if($newProducts->count() > 0)
    <section class="py-20 lg:py-28 bg-white">
        <div class="container-custom">
            <div class="flex items-end justify-between mb-12">
                <div class="animate-on-scroll">
                    <p class="text-sage-dark tracking-[0.2em] uppercase text-xs font-medium mb-3">Recién Llegados</p>
                    <h2 class="font-display text-3xl lg:text-4xl text-dark">
                        Nuevos Diseños
                    </h2>
                </div>
                <a href="{{ route('coleccion') }}?sort=newest" class="hidden md:inline-flex items-center gap-1.5 text-sm text-primary font-medium hover:text-primary-dark transition-colors animate-on-scroll">
                    Ver novedades
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6">
                @foreach($newProducts as $index => $product)
                    <div class="animate-on-scroll animate-delay-{{ ($index + 1) * 100 }}">
                        <livewire:components.product-card 
                            :product="$product"
                            :key="'new-'.$product->id"
                        />
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================
         CÓMO FUNCIONA - Minimalista
         ======================================== --}}
    <section class="py-20 lg:py-28 bg-cream">
        <div class="container-custom max-w-5xl">
            <div class="text-center mb-14 animate-on-scroll">
                <p class="text-primary tracking-[0.2em] uppercase text-xs font-medium mb-3">Proceso Simple</p>
                <h2 class="font-display text-3xl lg:text-4xl text-dark">
                    ¿Cómo Funciona?
                </h2>
            </div>
            
            @php
                $steps = [
                    [
                        'title' => 'Elige',
                        'desc' => 'Explora nuestra colección y encuentra el arreglo ideal.',
                        'icon' => 'M4 6h16M4 12h10M4 18h8',
                    ],
                    [
                        'title' => 'Personaliza',
                        'desc' => 'Agrega mensaje, colores o detalles especiales.',
                        'icon' => 'M12 6v12m6-6H6',
                    ],
                    [
                        'title' => 'Confirma',
                        'desc' => 'Paga seguro y recibe confirmación inmediata.',
                        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    ],
                    [
                        'title' => 'Recibe',
                        'desc' => 'Entregamos hoy mismo con presentación impecable.',
                        'icon' => 'M3 7l9-4 9 4-9 4-9-4z M3 7v10l9 4 9-4V7 M12 11v10',
                    ],
                ];
            @endphp

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($steps as $index => $step)
                    <div class="group p-6 rounded-3xl bg-white/70 border border-white/60 shadow-sm hover:shadow-md transition-all duration-300 animate-on-scroll animate-delay-{{ ($index + 1) * 100 }}">
                        <div class="flex items-center justify-between mb-5">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $step['icon'] }}"/>
                                </svg>
                            </div>
                            <span class="text-xs text-primary/40 font-medium">0{{ $index + 1 }}</span>
                        </div>
                        <h3 class="font-display text-lg text-dark mb-2">{{ $step['title'] }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================================
         TESTIMONIOS
         ======================================== --}}
    @if($testimonials->count() > 0)
    <section class="py-20 lg:py-28 bg-white">
        <div class="container-custom max-w-5xl">
            <div class="text-center mb-14 animate-on-scroll">
                <p class="text-primary tracking-[0.2em] uppercase text-xs font-medium mb-3">Nuestros Clientes</p>
                <h2 class="font-display text-3xl lg:text-4xl text-dark">
                    Lo Que Dicen
                </h2>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($testimonials as $index => $testimonial)
                    <div class="p-6 rounded-2xl border border-gray-100 hover:border-primary/20 transition-colors animate-on-scroll animate-delay-{{ ($index + 1) * 100 }}">
                        <div class="flex items-center gap-0.5 mb-4">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $testimonial->rating ? 'text-gold' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        
                        <p class="text-gray-600 text-sm leading-relaxed mb-5">
                            "{{ $testimonial->comment }}"
                        </p>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary text-sm font-medium">
                                {{ mb_substr($testimonial->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-dark">{{ $testimonial->name }}</p>
                                @if($testimonial->occasion)
                                    <p class="text-xs text-gray-400">{{ $testimonial->occasion }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================
         CTA FINAL - Limpio
         ======================================== --}}
    <section class="py-24 lg:py-32 home-cta border-b border-white/5">
        <div class="container-custom text-center">
            <div class="max-w-2xl mx-auto animate-on-scroll">
                <h2 class="font-display text-3xl lg:text-4xl text-white mb-4">
                    ¿Listo para sorprender?
                </h2>
                <p class="text-white/50 mb-8">
                    Haz tu pedido ahora y entregamos hoy mismo.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('coleccion') }}" class="inline-flex items-center gap-2 bg-primary text-white px-7 py-3.5 rounded-full font-medium hover:bg-primary-light transition-colors">
                        Explorar Colección
                    </a>
                    @if($whatsappNumber)
                        <a 
                            href="https://wa.me/{{ $whatsappNumber }}"
                            target="_blank"
                            class="inline-flex items-center gap-2 text-white/60 hover:text-white transition-colors text-sm"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.520-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Contáctanos por WhatsApp
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
