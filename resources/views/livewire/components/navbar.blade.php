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
        mobileMenuOpen: false,
        mobileAccordion: null,
        init() {
            window.addEventListener('scroll', () => {
                this.scrolled = window.scrollY > 20;
            });
        }
    }" 
    class="navbar"
    :class="{ 'navbar--scrolled': scrolled }"
>
    {{-- Banner promocional superior --}}
    @if($promoBanner = \App\Models\PromoBanner::active()->first())
        <div 
            x-data="{ visible: true }"
            x-show="visible"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="bg-primary text-white text-center py-2 text-sm relative"
        >
            <div class="container-custom flex items-center justify-center gap-2">
                <span>{{ $promoBanner->text }}</span>
                @if($promoBanner->has_countdown)
                    <livewire:components.countdown-timer :target-date="$promoBanner->ends_at" />
                @endif
                <button 
                    @click="visible = false"
                    class="absolute right-4 top-1/2 -translate-y-1/2 hover:opacity-70"
                    aria-label="Cerrar banner"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
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
                <div class="relative" x-data="{ open: false }" @close-catalog.window="open = false">
                    <button 
                        @mouseenter="open = true"
                        @click="open = !open"
                        class="nav-link flex items-center gap-1"
                        :class="{ 'nav-link--active': {{ request()->routeIs('coleccion*') ? 'true' : 'false' }} }"
                        aria-expanded="open"
                        aria-haspopup="true"
                    >
                        Catálogo
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    {{-- Mega Menu --}}
                    <div 
                        x-show="open"
                        @mouseenter="open = true"
                        @mouseleave="open = false"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mega-menu"
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
                    @click="mobileMenuOpen = !mobileMenuOpen"
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
                    @click="mobileAccordion = mobileAccordion === 'catalogo' ? null : 'catalogo'"
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
                        <div class="mb-3">
                            <a 
                                href="{{ route('coleccion.categoria', $parent->slug) }}"
                                class="flex items-center gap-2 px-4 py-2 font-medium text-dark hover:text-primary transition-colors"
                            >
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
                            </a>
                            @if($parent->children->count() > 0)
                                <div class="mobile-category__children">
                                    @foreach($parent->children as $child)
                                        <a href="{{ route('coleccion.categoria', $child->slug) }}">
                                            {{ $child->name }}
                                        </a>
                                    @endforeach
                                </div>
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
