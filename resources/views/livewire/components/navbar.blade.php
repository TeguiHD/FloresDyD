{{-- 
    Navbar - Mega Menu con Categorías Jerárquicas
    
    Categorías principales:
    - Bouquets (ramos primaverales, rosas, tulipanes, girasoles)
    - Regalos (nacimientos, aniversario, cumpleaños)
    - Eventos (centros de mesa, floreros, decoraciones, atriles, graduaciones)
    - Novios (ramos de novia, caminos de luz, atriles, centros, aros/arcos, altar)
    - Condolencias (cubre urnas, arreglos frontales)
    
    Diseño:
    - Logo izquierda (Patrón F)
    - Mega menu desktop con subcategorías + iconos por categoría
    - Acordeón en móvil
--}}
<nav 
    x-data="{ 
        scrolled: false,
        megaOpen: false,
        userMenuOpen: false,
        mobileMenuOpen: false,
        mobileAccordion: null,
        mobileCatalogOpen: null,
        init() {
            window.addEventListener('scroll', () => {
                this.scrolled = window.scrollY > 20;
            });
        }
    }" 
    class="navbar"
    :class="{ 'navbar--scrolled': scrolled }"
    @close-catalog.window="megaOpen = false"
    @click.outside="megaOpen = false"
>
    {{-- Banner promocional superior --}}
    @php
        $bannerPath = '/' . ltrim(request()->path(), '/');
        $promoBanner = \App\Models\PromoBanner::active()
            ->get()
            ->first(fn ($banner) => $banner->isVisibleOnPath($bannerPath));
    @endphp
    @if($promoBanner)
        <div 
            x-data="{ visible: true }"
            x-show="visible"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="bg-primary text-white text-center py-2 text-sm relative"
        >
            <div class="container-custom flex flex-wrap items-center justify-center gap-2">
                @if($promoBanner->icon)
                    <span class="text-base">{{ $promoBanner->icon }}</span>
                @endif
                <span>{{ $promoBanner->text }}</span>
                @if($promoBanner->has_countdown)
                    <livewire:components.countdown-timer :target-date="$promoBanner->ends_at" />
                @endif
                @if($promoBanner->button_text && $promoBanner->button_url)
                    @php
                        $isExternal = str_starts_with($promoBanner->button_url, 'http');
                    @endphp
                    <a
                        href="{{ $promoBanner->button_url }}"
                        class="ml-2 underline hover:no-underline"
                        @if($isExternal)
                            data-external="true"
                            target="_blank"
                            rel="noopener"
                        @endif
                    >
                        {{ $promoBanner->button_text }}
                    </a>
                @endif
                @if($promoBanner->is_dismissible)
                    <button 
                        @click="visible = false"
                        class="absolute right-4 top-1/2 -translate-y-1/2 hover:opacity-70"
                        aria-label="Cerrar banner"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    @endif
    
    {{-- Navbar principal --}}
    <div class="container-custom">
        <div class="flex items-center justify-between h-16 lg:h-20">
            
            {{-- Logo (Punto de fijación #1 en Patrón F) --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0" @mouseenter="$dispatch('close-catalog')">
                <img 
                    src="{{ asset('images/floresdyd-logo.webp') }}" 
                    alt="Flores D&D" 
                    class="h-10 lg:h-12 w-auto"
                >
                <span class="font-display text-xl lg:text-2xl text-dark hidden sm:block">
                    Flores D&D
                </span>
            </a>
            
            {{-- Navegación Desktop con Mega Menu --}}
            <nav class="hidden lg:flex items-center gap-1" aria-label="Navegación principal">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'nav-link--active' : '' }}" @mouseenter="$dispatch('close-catalog')">
                    Inicio
                </a>
                
                {{-- Catálogo con Mega Menu --}}
                <div class="relative">
                    <button 
                        @mouseenter="megaOpen = true"
                        @click="megaOpen = !megaOpen"
                        class="nav-link flex items-center gap-1"
                        :class="{ 'nav-link--active': {{ request()->routeIs('coleccion*') ? 'true' : 'false' }} }"
                        :aria-expanded="megaOpen"
                        aria-haspopup="true"
                    >
                        Catálogo
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': megaOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
                
                <a href="{{ route('ocasiones') }}" class="nav-link {{ request()->routeIs('ocasiones*') ? 'nav-link--active' : '' }}" @mouseenter="$dispatch('close-catalog')">
                    Ocasiones
                </a>
                
                <a href="{{ route('nosotros') }}" class="nav-link {{ request()->routeIs('nosotros') ? 'nav-link--active' : '' }}" @mouseenter="$dispatch('close-catalog')">
                    Nosotros
                </a>
                
                <a href="{{ route('contacto') }}" class="nav-link {{ request()->routeIs('contacto') ? 'nav-link--active' : '' }}" @mouseenter="$dispatch('close-catalog')">
                    Contacto
                </a>
            </nav>
            
            {{-- Acciones (derecha) --}}
            <div class="flex items-center gap-2 lg:gap-3">
                {{-- Usuario --}}
                <div class="relative" x-ref="userBtn">
                    <button
                        @click="
                            userMenuOpen = !userMenuOpen;
                            if (userMenuOpen) { 
                                mobileMenuOpen = false; 
                                megaOpen = false; 
                            }
                        "
                        class="p-2 text-dark hover:text-primary transition-colors"
                        aria-label="Cuenta de usuario"
                        :aria-expanded="userMenuOpen"
                    >
                        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.5 20a7.5 7.5 0 0113 0M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                        </svg>
                    </button>
                </div>

                {{-- Búsqueda --}}
                <button 
                    wire:click="openSearch"
                    class="p-2 text-dark hover:text-primary transition-colors"
                    @mouseenter="$dispatch('close-catalog')"
                    aria-label="Buscar"
                >
                    <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                
                {{-- Carrito con contador --}}
                <button 
                    wire:click="$dispatch('toggleCart')"
                    class="p-2 text-dark hover:text-primary transition-colors relative"
                    @mouseenter="$dispatch('close-catalog')"
                    aria-label="Carrito de compras"
                >
                    <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-primary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-medium animate-pulse">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </button>
                
                {{-- Menú móvil toggle --}}
                <button 
                    @click="
                        mobileMenuOpen = !mobileMenuOpen;
                        if (mobileMenuOpen) { 
                            userMenuOpen = false; 
                            megaOpen = false; 
                        } else {
                            mobileAccordion = null;
                            mobileCatalogOpen = null;
                        }
                    "
                    class="lg:hidden p-2 text-dark hover:text-primary transition-colors"
                    aria-label="Menú"
                    :aria-expanded="mobileMenuOpen"
                >
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Dropdown de usuario (fuera del flex para evitar overflow) --}}
        <div class="relative">
            <div
                x-show="userMenuOpen"
                @click.away="userMenuOpen = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                class="absolute right-0 top-0 w-56 max-w-[calc(100vw-2rem)] rounded-xl bg-white shadow-card border border-secondary p-2 z-50"
                x-cloak
            >
                @auth
                    <div class="px-3 py-2 text-sm">
                        <p class="font-medium text-dark truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-dark/60 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="h-px bg-secondary my-2"></div>
                    @if(auth()->user()->hasAnyRole(['super-admin', 'admin']))
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-dark hover:bg-gray-50">
                            Panel de administración
                        </a>
                    @endif
                    <a href="{{ route('account.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-dark hover:bg-gray-50">
                        Mi cuenta
                    </a>
                    <a href="{{ route('account.orders') }}" class="block px-3 py-2 rounded-lg text-sm text-dark hover:bg-gray-50">
                        Mis pedidos
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm text-dark hover:bg-gray-50">
                            Cerrar sesión
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm text-dark hover:bg-gray-50">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg text-sm text-dark hover:bg-gray-50">
                        Crear cuenta
                    </a>
                    <div class="h-px bg-secondary my-2"></div>
                    <a href="{{ route('track.order') }}" class="block px-3 py-2 rounded-lg text-sm text-dark hover:bg-gray-50">
                        Rastrear pedido
                    </a>
                @endauth
            </div>
        </div>
        
        {{-- Mega Menu --}}
        <div 
            x-show="megaOpen"
            @mouseenter="megaOpen = true"
            @mouseleave="megaOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="mega-menu hidden lg:block"
            x-cloak
        >
            <div class="mega-menu__inner">
                <div class="grid grid-cols-5 gap-2">
                    @foreach($parentCategories as $parent)
                        @php
                            $style = $categoryStyles[$parent->slug] ?? null;
                            if (!$style) continue;
                        @endphp
                        <div class="mega-menu__category">
                            <a href="{{ route('coleccion.categoria', $parent->slug) }}" class="block group">
                                <div class="mega-menu__icon mega-menu__icon--{{ $style['class'] }}">
                                    @switch($style['icon'])
                                        @case('bouquets')
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 9a2 2 0 114 0 2 2 0 01-4 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7a2 2 0 114 0 2 2 0 01-4 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 13a2 2 0 114 0 2 2 0 01-4 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 11l3 6m3-6l-3 6"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.5 21h7l-1-3H9.5l-1 3z"/>
                                            </svg>
                                            @break
                                        @case('regalos')
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 10h16"/>
                                                <rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.5"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v10"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 7c0 1.4 1.1 2.5 2.5 2.5H12V6c0-1.1-.9-2-2-2s-2 .9-2 3z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 7c0 1.4-1.1 2.5-2.5 2.5H12V6c0-1.1.9-2 2-2s2 .9 2 3z"/>
                                            </svg>
                                            @break
                                        @case('eventos')
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <rect x="3.5" y="6.5" width="17" height="14" rx="2" stroke-width="1.5"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 3.5v3M17 3.5v3M3.5 10.5h17"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l1.5 1.5L13 13"/>
                                            </svg>
                                            @break
                                        @case('novios')
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <circle cx="9" cy="12" r="3.5" stroke-width="1.5"/>
                                                <circle cx="15" cy="12" r="3.5" stroke-width="1.5"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.5 14.5l1 1"/>
                                            </svg>
                                            @break
                                        @case('condolencias')
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 20s-7-4.5-7-9a4 4 0 017-2 4 4 0 017 2c0 4.5-7 9-7 9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9.5v5.5"/>
                                            </svg>
                                            @break
                                    @endswitch
                                </div>
                                <div class="mega-menu__title group-hover:text-primary transition-colors">
                                    {{ $parent->name }}
                                </div>
                            </a>
                            
                            @if($parent->children->count() > 0)
                                <ul class="mega-menu__links">
                                    @foreach($parent->children as $child)
                                        <li>
                                            <a href="{{ route('coleccion.categoria', $child->slug) }}">
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-5 pt-4 border-t border-gray-100 text-center">
                    <a 
                        href="{{ route('coleccion') }}"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary-dark transition-colors"
                    >
                        Ver toda la colección
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Menú móvil con acordeón de categorías --}}
    <div 
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="lg:hidden bg-white border-t border-gray-100 shadow-lg max-h-[80dvh] overflow-y-auto"
        x-cloak
    >
        <div class="container-custom py-4">
            {{-- Páginas principales --}}
            <a href="{{ route('home') }}" class="block py-3 px-4 rounded-lg {{ request()->routeIs('home') ? 'bg-secondary text-primary font-medium' : 'text-dark hover:bg-gray-50' }}">
                Inicio
            </a>
            
            {{-- Catálogo - Acordeón de Categorías --}}
            <div class="mobile-category">
                <button 
                    @click="
                        mobileAccordion = mobileAccordion === 'catalogo' ? null : 'catalogo';
                        if (mobileAccordion !== 'catalogo') { 
                            mobileCatalogOpen = null; 
                        }
                    "
                    class="mobile-category__trigger"
                    :class="{ 'text-primary': mobileAccordion === 'catalogo' }"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        Catálogo
                    </span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': mobileAccordion === 'catalogo' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div 
                    x-show="mobileAccordion === 'catalogo'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-cloak
                >
                    @foreach($parentCategories as $parent)
                        @php
                            $style = $categoryStyles[$parent->slug] ?? null;
                            if (!$style) continue;
                        @endphp
                        <div class="mobile-category__group">
                            @if($parent->children->count() > 0)
                                <button 
                                    type="button"
                                    class="mobile-category__parent"
                                    :class="{ 'mobile-category__parent--active': mobileCatalogOpen === '{{ $parent->slug }}' }"
                                    @click="mobileCatalogOpen = mobileCatalogOpen === '{{ $parent->slug }}' ? null : '{{ $parent->slug }}'"
                                >
                                    <span class="flex items-center gap-2">
                                        <span class="mobile-category__icon mega-menu__icon--{{ $style['class'] }}">
                                            @switch($style['icon'])
                                                @case('bouquets')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 9a2 2 0 114 0 2 2 0 01-4 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7a2 2 0 114 0 2 2 0 01-4 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 13a2 2 0 114 0 2 2 0 01-4 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 11l3 6m3-6l-3 6"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.5 21h7l-1-3H9.5l-1 3z"/>
                                                    </svg>
                                                    @break
                                                @case('regalos')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 10h16"/>
                                                        <rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.5"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v10"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 7c0 1.4 1.1 2.5 2.5 2.5H12V6c0-1.1-.9-2-2-2s-2 .9-2 3z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 7c0 1.4-1.1 2.5-2.5 2.5H12V6c0-1.1.9-2 2-2s2 .9 2 3z"/>
                                                    </svg>
                                                    @break
                                                @case('eventos')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <rect x="3.5" y="6.5" width="17" height="14" rx="2" stroke-width="1.5"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 3.5v3M17 3.5v3M3.5 10.5h17"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l1.5 1.5L13 13"/>
                                                    </svg>
                                                    @break
                                                @case('novios')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <circle cx="9" cy="12" r="3.5" stroke-width="1.5"/>
                                                        <circle cx="15" cy="12" r="3.5" stroke-width="1.5"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.5 14.5l1 1"/>
                                                    </svg>
                                                    @break
                                                @case('condolencias')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 20s-7-4.5-7-9a4 4 0 017-2 4 4 0 017 2c0 4.5-7 9-7 9z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9.5v5.5"/>
                                                    </svg>
                                                    @break
                                            @endswitch
                                        </span>
                                        {{ $parent->name }}
                                    </span>
                                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': mobileCatalogOpen === '{{ $parent->slug }}' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div 
                                    x-show="mobileCatalogOpen === '{{ $parent->slug }}'"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-cloak
                                    class="mobile-category__children"
                                >
                                    <a href="{{ route('coleccion.categoria', $parent->slug) }}" class="is-parent">
                                        Ver todo {{ $parent->name }}
                                    </a>
                                    @foreach($parent->children as $child)
                                        <a href="{{ route('coleccion.categoria', $child->slug) }}">
                                            {{ $child->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <a 
                                    href="{{ route('coleccion.categoria', $parent->slug) }}"
                                    class="mobile-category__parent"
                                >
                                    <span class="flex items-center gap-2">
                                        <span class="mobile-category__icon mega-menu__icon--{{ $style['class'] }}">
                                            @switch($style['icon'])
                                                @case('bouquets')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 9a2 2 0 114 0 2 2 0 01-4 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7a2 2 0 114 0 2 2 0 01-4 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 13a2 2 0 114 0 2 2 0 01-4 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 11l3 6m3-6l-3 6"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.5 21h7l-1-3H9.5l-1 3z"/>
                                                    </svg>
                                                    @break
                                                @case('regalos')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 10h16"/>
                                                        <rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.5"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v10"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 7c0 1.4 1.1 2.5 2.5 2.5H12V6c0-1.1-.9-2-2-2s-2 .9-2 3z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 7c0 1.4-1.1 2.5-2.5 2.5H12V6c0-1.1.9-2 2-2s2 .9 2 3z"/>
                                                    </svg>
                                                    @break
                                                @case('eventos')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <rect x="3.5" y="6.5" width="17" height="14" rx="2" stroke-width="1.5"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 3.5v3M17 3.5v3M3.5 10.5h17"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l1.5 1.5L13 13"/>
                                                    </svg>
                                                    @break
                                                @case('novios')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <circle cx="9" cy="12" r="3.5" stroke-width="1.5"/>
                                                        <circle cx="15" cy="12" r="3.5" stroke-width="1.5"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.5 14.5l1 1"/>
                                                    </svg>
                                                    @break
                                                @case('condolencias')
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 20s-7-4.5-7-9a4 4 0 017-2 4 4 0 017 2c0 4.5-7 9-7 9z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9.5v5.5"/>
                                                    </svg>
                                                    @break
                                            @endswitch
                                        </span>
                                        {{ $parent->name }}
                                    </span>
                                </a>
                            @endif
                        </div>
                    @endforeach
                    
                    <a 
                        href="{{ route('coleccion') }}"
                        class="block px-4 py-2 text-sm font-medium text-primary hover:text-primary-dark"
                    >
                        Ver toda la colección →
                    </a>
                </div>
            </div>
            
            <a href="{{ route('ocasiones') }}" class="block py-3 px-4 rounded-lg {{ request()->routeIs('ocasiones*') ? 'bg-secondary text-primary font-medium' : 'text-dark hover:bg-gray-50' }}">
                Ocasiones
            </a>
            <a href="{{ route('nosotros') }}" class="block py-3 px-4 rounded-lg {{ request()->routeIs('nosotros') ? 'bg-secondary text-primary font-medium' : 'text-dark hover:bg-gray-50' }}">
                Nosotros
            </a>
            <a href="{{ route('contacto') }}" class="block py-3 px-4 rounded-lg {{ request()->routeIs('contacto') ? 'bg-secondary text-primary font-medium' : 'text-dark hover:bg-gray-50' }}">
                Contacto
            </a>
            
            <hr class="my-3 border-gray-100">
            
        </div>
    </div>
    
    {{-- Modal de búsqueda --}}
    @if($searchOpen)
        <div 
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50"
            @click="$wire.closeSearch()"
        >
            <div 
                class="fixed top-0 left-0 right-0 bg-white p-4 shadow-lg"
                @click.stop
            >
                <div class="container-custom">
                    <form wire:submit="search" class="flex items-center gap-4">
                        <div class="flex-1 relative">
                            <input 
                                type="text"
                                wire:model.live.debounce.300ms="searchQuery"
                                placeholder="Buscar flores, arreglos, ocasiones..."
                                class="w-full form-input pr-10"
                                autofocus
                            >
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <button 
                            type="button"
                            wire:click="closeSearch"
                            class="p-2 text-gray-500 hover:text-dark"
                        >
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                    
                    {{-- Sugerencias rápidas --}}
                    @if(strlen($searchQuery) < 2)
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="text-xs text-gray-400 uppercase tracking-wider mr-2">Populares:</span>
                            @foreach(['Rosas', 'Bouquets', 'Cumpleaños', 'Novias', 'Condolencias'] as $tag)
                                <button 
                                    wire:click="$set('searchQuery', '{{ $tag }}')"
                                    class="px-3 py-1 text-sm bg-secondary rounded-full text-dark hover:bg-primary hover:text-white transition-colors"
                                >
                                    {{ $tag }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                    
                    @if(!$hasProducts)
                        <p class="text-sm text-gray-500 mt-3">
                            Aún no hay productos disponibles. Pronto tendremos la colección lista para ti.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</nav>
