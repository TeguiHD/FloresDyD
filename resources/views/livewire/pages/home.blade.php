{{-- 
    HOME PAGE - Flores D&D (2026 Redesign)
    
    Diseño moderno, elegante y conversion-optimized:
    - Hero inmersivo con typewriter + video/image-ready bg
    - Bento Grid de categorías (visual scanning optimizado)
    - Social proof integrado (sesgo psicológico)
    - Layer-cake pattern (anti F-scan) 
    - Glassmorphism + gradient mesh backgrounds
    - Scroll-driven reveal animations
    - CTAs estratégicos con contraste y urgencia
    - Responsive-first con clamp/dvh
--}}

@php
    $title = 'Flores a domicilio en Valdivia y Santiago | Flores D&D';
    $metaDescription = 'Floristería artesanal en Valdivia y Santiago. Ramos, arreglos y eventos con entrega a domicilio y atención personalizada.';
@endphp

<div>
    {{-- ========================================
         HERO - Inmersivo, Parallax-ready, Video BG ready
         ======================================== --}}
    <section class="hero-2026" x-data="heroParallax()">
        @php $whatsappNumber = \App\Models\SiteSetting::getWhatsappNumber(); @endphp
        
        {{-- Gradient mesh background --}}
        <div class="hero-2026__mesh"></div>
        
        {{-- Floating organic shapes --}}
        <div class="hero-2026__orb hero-2026__orb--1" aria-hidden="true"></div>
        <div class="hero-2026__orb hero-2026__orb--2" aria-hidden="true"></div>
        <div class="hero-2026__orb hero-2026__orb--3" aria-hidden="true"></div>
        
        {{-- Noise texture overlay --}}
        <div class="hero-2026__noise" aria-hidden="true"></div>
        
        <div class="hero-2026__content">
            {{-- Trust badge: Social proof (sesgo de autoridad) --}}
            <div class="hero-2026__trust-pill reveal-up" style="--delay: 0s">
                <span class="hero-2026__trust-dot"></span>
                <span>+500 arreglos entregados este mes</span>
            </div>
            
            <h1 class="hero-2026__title reveal-up" style="--delay: 0.15s">
                Flores que expresan
            </h1>
            
            <div class="hero-2026__typewriter reveal-up" style="--delay: 0.3s">
                <livewire:components.typewriter 
                    :texts="['amor profundo', 'sorpresas elegantes', 'momentos inolvidables', 'gratitud sincera', 'celebraciones únicas']" 
                    :loop="true"
                />
            </div>
            
            <p class="hero-2026__subtitle reveal-up" style="--delay: 0.45s">
                Arreglos únicos, armados a mano el día de tu pedido.<br class="hidden sm:block">
                <span class="text-rose-light">Entrega el mismo día</span> en Santiago.
            </p>
            
            <div class="hero-2026__actions reveal-up" style="--delay: 0.6s">
                <a href="{{ route('coleccion') }}" class="btn-hero-primary group">
                    <span>Ver Colección</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
                @if($whatsappNumber)
                    <a 
                        href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hola, me gustaría hacer un pedido personalizado') }}"
                        target="_blank"
                        class="btn-hero-ghost group"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <span>Pedido a Medida</span>
                    </a>
                @endif
            </div>
        </div>
        
        {{-- Scroll indicator --}}
        <div class="hero-2026__scroll" aria-hidden="true">
            <div class="hero-2026__scroll-line"></div>
        </div>
    </section>

    {{-- ========================================
         SOCIAL PROOF BAR - Sesgo de autoridad + bandwagon
         ======================================== --}}
    <section class="trust-bar" aria-label="Indicadores de confianza">
        <div class="container-custom">
            <div class="trust-bar__inner">
                <div class="trust-bar__item reveal-up" style="--delay: 0s">
                    <div class="trust-bar__icon trust-bar__icon--fresh">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <span class="trust-bar__label">Flores 100% Frescas</span>
                        <span class="trust-bar__sub">Cortadas el mismo día</span>
                    </div>
                </div>
                <div class="trust-bar__divider" aria-hidden="true"></div>
                <div class="trust-bar__item reveal-up" style="--delay: 0.1s">
                    <div class="trust-bar__icon trust-bar__icon--fast">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="trust-bar__label">Entrega Mismo Día</span>
                        <span class="trust-bar__sub">Pedidos antes de las 14h</span>
                    </div>
                </div>
                <div class="trust-bar__divider" aria-hidden="true"></div>
                <div class="trust-bar__item reveal-up" style="--delay: 0.2s">
                    <div class="trust-bar__icon trust-bar__icon--love">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="trust-bar__label">Hechas con Amor</span>
                        <span class="trust-bar__sub">Artesanales y únicas</span>
                    </div>
                </div>
                <div class="trust-bar__divider" aria-hidden="true"></div>
                <div class="trust-bar__item reveal-up" style="--delay: 0.3s">
                    <div class="trust-bar__icon trust-bar__icon--safe">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="trust-bar__label">Compra Segura</span>
                        <span class="trust-bar__sub">Pago 100% protegido</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================
         CATEGORÍAS - Bento Grid moderno
         Anti F-pattern: distintas alturas rompen escaneo lineal
         ======================================== --}}
    <section class="section-categories" id="categorias">
        <div class="container-custom">
            <div class="section-header reveal-up">
                <span class="section-tag">Nuestras Colecciones</span>
                <h2 class="section-title">
                    Encuentra el arreglo <em class="section-title__em">perfecto</em>
                </h2>
                <p class="section-desc">Cada ocasión merece flores únicas. Explora nuestras colecciones curadas.</p>
            </div>
            
            @php
                $catStyles = [
                    'bouquets' => [
                        'gradient' => 'linear-gradient(to bottom right, rgba(220,130,143,0.20), rgba(247,219,224,0.10), transparent)',
                        'accent'   => 'var(--color-rose-dark)',
                        'emoji'    => '💐',
                    ],
                    'regalos' => [
                        'gradient' => 'linear-gradient(to bottom right, rgba(191,113,80,0.20), rgba(228,185,162,0.10), transparent)',
                        'accent'   => 'var(--color-terracota-dark)',
                        'emoji'    => '🎁',
                    ],
                    'eventos' => [
                        'gradient' => 'linear-gradient(to bottom right, rgba(197,165,90,0.20), rgba(237,224,180,0.10), transparent)',
                        'accent'   => 'var(--color-gold-dark)',
                        'emoji'    => '✨',
                    ],
                    'novios' => [
                        'gradient' => 'linear-gradient(to bottom right, rgba(183,160,200,0.20), rgba(237,224,244,0.10), transparent)',
                        'accent'   => 'var(--color-primary-600)',
                        'emoji'    => '💒',
                    ],
                    'condolencias' => [
                        'gradient' => 'linear-gradient(to bottom right, rgba(139,166,136,0.20), rgba(207,224,204,0.10), transparent)',
                        'accent'   => 'var(--color-sage-dark)',
                        'emoji'    => '🕊️',
                    ],
                ];
            @endphp
            
            <div class="bento-grid">
                @foreach($categories as $index => $category)
                    @php 
                        $style = $catStyles[$category->slug] ?? null;
                        $isLarge = $index === 0 || $index === 3;
                    @endphp
                    @if(!$style) @continue @endif
                    <a 
                        href="{{ route('coleccion.categoria', $category->slug) }}"
                        class="bento-card {{ $isLarge ? 'bento-card--lg' : '' }} reveal-up"
                        style="--delay: {{ ($index + 1) * 0.1 }}s"
                    >
                        <div class="bento-card__gradient" style="background: {{ $style['gradient'] }}"></div>
                        
                        <div class="bento-card__content">
                            <span class="bento-card__emoji" aria-hidden="true">{{ $style['emoji'] }}</span>
                            <h3 class="bento-card__title">{{ $category->name }}</h3>
                            @if($category->children->count() > 0)
                                <ul class="bento-card__tags">
                                    @foreach($category->children->take($isLarge ? 4 : 3) as $child)
                                        <li class="bento-card__tag">{{ $child->name }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <span class="bento-card__cta" style="color: {{ $style['accent'] }}">
                                Explorar
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================================
         PRODUCTOS DESTACADOS
         Sesgo: Prueba social (badges de "más vendido")
         ======================================== --}}
    @if($featuredProducts->count() > 0)
    <section class="section-featured">
        <div class="container-custom">
            <div class="section-header section-header--split reveal-up">
                <div>
                    <span class="section-tag section-tag--accent">Los Favoritos</span>
                    <h2 class="section-title">Arreglos Más Vendidos</h2>
                </div>
                <a href="{{ route('coleccion') }}?sort=bestseller" class="btn-text-arrow group">
                    <span>Ver todos</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
            
            <div class="product-grid">
                @foreach($featuredProducts as $index => $product)
                    <div class="reveal-up" style="--delay: {{ min(($index + 1) * 0.08, 0.5) }}s">
                        <livewire:components.product-card 
                            :product="$product"
                            :key="'featured-'.$product->id"
                        />
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 text-center md:hidden">
                <a href="{{ route('coleccion') }}?sort=bestseller" class="btn-text-arrow text-primary text-sm font-medium">
                    Ver todos los favoritos →
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================
         BANNER INMERSIVO - Ofertas con urgencia + escasez
         ======================================== --}}
    @if($dealsProducts->count() > 0)
    <section class="deals-immersive" x-data="{ visible: false }" x-intersect:enter="visible = true">
        <div class="deals-immersive__mesh" aria-hidden="true"></div>
        <div class="deals-immersive__noise" aria-hidden="true"></div>
        
        <div class="container-custom deals-immersive__inner">
            <div class="deals-immersive__text" :class="visible && 'is-visible'">
                <span class="deals-immersive__badge">
                    <span class="deals-immersive__badge-dot"></span>
                    Ofertas Activas
                </span>
                <h2 class="deals-immersive__title">
                    Hasta <span class="deals-immersive__highlight">30% off</span>
                </h2>
                <p class="deals-immersive__desc">
                    Arreglos seleccionados a precios especiales.<br>
                    Disponibilidad limitada por temporada.
                </p>
                <a href="{{ route('coleccion') }}?filter=ofertas" class="btn-deals group">
                    <span>Ver Ofertas</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
            
            <div class="deals-immersive__products" :class="visible && 'is-visible'">
                @foreach($dealsProducts->take(3) as $index => $product)
                    <a href="{{ route('producto.detalle', $product->slug) }}" class="deals-product-mini" style="--deal-delay: {{ $index * 0.15 }}s">
                        @if($product->primaryImage)
                            <img 
                                src="{{ $product->primaryImage->url }}" 
                                alt="{{ $product->name }}"
                                class="deals-product-mini__img"
                                loading="lazy"
                            >
                        @endif
                        <div class="deals-product-mini__info">
                            <span class="deals-product-mini__name">{{ $product->name }}</span>
                            @if($product->discount_percentage)
                                <span class="deals-product-mini__discount">-{{ $product->discount_percentage }}%</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================
         NOVEDADES
         ======================================== --}}
    @if($newProducts->count() > 0)
    <section class="section-new">
        <div class="container-custom">
            <div class="section-header section-header--split reveal-up">
                <div>
                    <span class="section-tag section-tag--sage">Recién Llegados</span>
                    <h2 class="section-title">Nuevos Diseños</h2>
                </div>
                <a href="{{ route('coleccion') }}?sort=newest" class="btn-text-arrow group hidden md:inline-flex">
                    <span>Ver novedades</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
            
            <div class="product-grid product-grid--4">
                @foreach($newProducts as $index => $product)
                    <div class="reveal-up" style="--delay: {{ ($index + 1) * 0.1 }}s">
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
         CÓMO FUNCIONA - Timeline moderno
         ======================================== --}}
    <section class="section-process">
        <div class="container-custom">
            <div class="section-header reveal-up">
                <span class="section-tag">Proceso Simple</span>
                <h2 class="section-title">
                    Pedir flores nunca fue <em class="section-title__em">tan fácil</em>
                </h2>
            </div>
            
            @php
                $steps = [
                    [
                        'title' => 'Elige',
                        'desc' => 'Explora nuestra colección y encuentra el arreglo ideal para la ocasión.',
                        'icon' => 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z',
                        'color' => 'process-step--rose',
                    ],
                    [
                        'title' => 'Personaliza',
                        'desc' => 'Agrega un mensaje, elige colores o solicita detalles especiales.',
                        'icon' => 'M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42',
                        'color' => 'process-step--lilac',
                    ],
                    [
                        'title' => 'Confirma',
                        'desc' => 'Paga de forma segura y recibe confirmación al instante.',
                        'icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
                        'color' => 'process-step--sage',
                    ],
                    [
                        'title' => 'Recibe',
                        'desc' => 'Entregamos el mismo día con presentación impecable.',
                        'icon' => 'M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
                        'color' => 'process-step--gold',
                    ],
                ];
            @endphp

            <div class="process-timeline">
                @foreach($steps as $index => $step)
                    <div class="process-step {{ $step['color'] }} reveal-up" style="--delay: {{ ($index + 1) * 0.12 }}s">
                        <div class="process-step__number">
                            <span>0{{ $index + 1 }}</span>
                        </div>
                        <div class="process-step__icon">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $step['icon'] }}"/>
                            </svg>
                        </div>
                        <h3 class="process-step__title">{{ $step['title'] }}</h3>
                        <p class="process-step__desc">{{ $step['desc'] }}</p>
                        @if($index < count($steps) - 1)
                            <div class="process-step__connector" aria-hidden="true"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================================
         TESTIMONIOS - Marquee infinito glassmorphism
         Sesgo: Prueba social, consenso, pertenencia
         ======================================== --}}
    @if($testimonials->count() > 0)
    <section class="section-testimonials">
        <div class="container-custom">
            <div class="section-header reveal-up">
                <span class="section-tag">Lo Que Dicen</span>
                <h2 class="section-title">
                    Clientes que <em class="section-title__em">confían</em> en nosotros
                </h2>
            </div>
            
            <div class="testimonials-marquee" x-data="testimonialScroll()">
                <div class="testimonials-track" :class="paused && 'is-paused'" @mouseenter="paused = true" @mouseleave="paused = false">
                    @for($set = 0; $set < 2; $set++)
                        @foreach($testimonials as $index => $testimonial)
                            <div class="testimonial-card">
                                <div class="testimonial-card__stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $testimonial->rating ? 'text-gold' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                
                                <blockquote class="testimonial-card__text">
                                    "{{ $testimonial->comment }}"
                                </blockquote>
                                
                                <div class="testimonial-card__author">
                                    <div class="testimonial-card__avatar">
                                        {{ mb_substr($testimonial->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <cite class="testimonial-card__name">{{ $testimonial->name }}</cite>
                                        @if($testimonial->occasion)
                                            <span class="testimonial-card__occasion">{{ $testimonial->occasion }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endfor
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================
         INSTAGRAM FEED - Publicaciones recientes
         ======================================== --}}
    @if(!empty($instagramEmbeds))
    <section class="section-instagram">
        <div class="container-custom">
            <div class="section-header reveal-up">
                <span class="section-tag">Instagram</span>
                <h2 class="section-title">
                    Inspiración diaria en <em class="section-title__em">{{ '@' . ($instagramUsername ?: 'floresdyd') }}</em>
                </h2>
                <p class="section-desc">Publicaciones recientes con nuestros arreglos y entregas reales.</p>
                @if($instagramUsername)
                    <a href="https://instagram.com/{{ $instagramUsername }}" data-external="true" class="instagram-cta">
                        Ver perfil completo
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @endif
            </div>

            <div class="instagram-grid">
                @foreach(array_slice($instagramEmbeds, 0, 3) as $embed)
                    <div class="instagram-card reveal-up">
                        <iframe src="{{ $embed }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Publicación de Instagram"></iframe>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================
         COMPROMISO DE MARCA - Storytelling visual
         Sesgo: Reciprocidad, conexión emocional
         ======================================== --}}
    <section class="section-commitment reveal-up">
        <div class="container-custom">
            <div class="commitment-card">
                <div class="commitment-card__bg" aria-hidden="true"></div>
                <div class="commitment-card__content">
                    <span class="section-tag section-tag--light">Nuestro Compromiso</span>
                    <h2 class="commitment-card__title">
                        Cada arreglo cuenta una historia
                    </h2>
                    <p class="commitment-card__text">
                        Seleccionamos cada flor, cuidamos cada detalle y entregamos con la misma emoción que tú sentirías al recibirlo. 
                        Porque detrás de cada pedido hay un sentimiento que merece ser expresado de la manera más hermosa.
                    </p>
                    <div class="commitment-card__stats">
                        <div class="commitment-stat">
                            <span class="commitment-stat__number" x-data="counterUp(500)" x-intersect:enter.once="start()" x-text="display">0</span>
                            <span class="commitment-stat__label">Pedidos / mes</span>
                        </div>
                        <div class="commitment-stat">
                            <span class="commitment-stat__number" x-data="counterUp(98)" x-intersect:enter.once="start()" x-text="display + '%'">0%</span>
                            <span class="commitment-stat__label">Satisfacción</span>
                        </div>
                        <div class="commitment-stat">
                            <span class="commitment-stat__number" x-data="counterUp(4.9, true)" x-intersect:enter.once="start()" x-text="display">0</span>
                            <span class="commitment-stat__label">Rating promedio</span>
                        </div>
                    </div>
                    <a href="{{ route('nosotros') }}" class="btn-commitment group">
                        Conoce nuestra historia
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================
         CTA FINAL - Inmersivo
         ======================================== --}}
    <section class="cta-final">
        @php $whatsappNumber = \App\Models\SiteSetting::getWhatsappNumber(); @endphp
        <div class="cta-final__mesh" aria-hidden="true"></div>
        <div class="cta-final__noise" aria-hidden="true"></div>
        
        <div class="container-custom cta-final__inner reveal-up">
            <div class="cta-final__badge">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                </svg>
                <span>Hecho con amor</span>
            </div>
            
            <h2 class="cta-final__title">
                ¿Listo para<br class="sm:hidden"> sorprender?
            </h2>
            <p class="cta-final__desc">
                Haz tu pedido y recíbelo con presentación premium y atención personalizada.
            </p>
            <div class="cta-final__actions">
                <a href="{{ route('coleccion') }}" class="btn-cta-primary group">
                    <span>Explorar Colección</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
                @if($whatsappNumber)
                    <a 
                        href="https://wa.me/{{ $whatsappNumber }}"
                        target="_blank"
                        class="btn-cta-ghost group"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <span>Contactar por WhatsApp</span>
                    </a>
                @endif
            </div>
        </div>
    </section>
</div>
