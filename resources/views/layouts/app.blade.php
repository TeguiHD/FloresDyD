<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>{{ $title ?? config('app.name', 'Flores D&D') }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Floristería artesanal con arreglos únicos. Flores frescas, entrega a domicilio y tarjetas personalizadas.' }}">
    <meta name="keywords" content="flores, floristería, arreglos florales, rosas, regalos, entrega a domicilio">
    <meta name="author" content="Flores D&D">
    <link rel="canonical" href="{{ url()->current() }}">
    @if(app()->environment('production'))
        <meta name="robots" content="index,follow">
    @else
        <meta name="robots" content="noindex,nofollow">
    @endif
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Floristería artesanal con arreglos únicos.' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/og-default.svg') }}">
    <meta property="og:site_name" content="Flores D&D">
    <meta property="og:locale" content="es_CL">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Floristería artesanal con arreglos únicos.' }}">
    <meta name="twitter:image" content="{{ $ogImage ?? asset('images/og-default.svg') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="{{ asset('images/floresdyd-favicon.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/floresdyd-logo.webp') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">

    <style>
        @font-face {
            font-family: 'Alkatra';
            src: local('Alkatra'),
                 url('{{ asset('fonts/Alkatra-VariableFont_wght.ttf') }}') format('truetype');
            font-weight: 400 700;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Inter';
            src: local('Inter Medium'),
                 url('{{ asset('fonts/Inter_24pt-Medium.ttf') }}') format('truetype');
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }
    </style>
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
    
    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Florist",
        "name": "Flores D&D",
        "description": "Floristería artesanal con arreglos únicos y flores frescas",
        "url": "{{ config('app.url') }}",
        "logo": "{{ asset('images/floresdyd-logo.webp') }}",
        "image": "{{ $ogImage ?? asset('images/og-default.svg') }}",
        "priceRange": "$$",
        "telephone": "{{ \App\Models\SiteSetting::getValue('contact.phone', config('flores.phone')) }}",
        "email": "{{ \App\Models\SiteSetting::getValue('contact.email', config('flores.email')) }}",
        "address": {
            "@@type": "PostalAddress",
            "streetAddress": "{{ \App\Models\SiteSetting::getValue('contact.address', config('flores.address')) }}",
            "addressCountry": "CL"
        },
        "areaServed": ["Valdivia", "Santiago", "Región de Los Ríos", "Región Metropolitana"],
        "sameAs": [
            "{{ config('flores.social.facebook') }}",
            "{{ config('flores.social.instagram') }}",
            "{{ config('flores.social.tiktok') }}"
        ],
        "contactPoint": {
            "@@type": "ContactPoint",
            "contactType": "customer service",
            "availableLanguage": ["es"]
        }
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "name": "Flores D&D",
        "url": "{{ config('app.url') }}",
        "potentialAction": {
            "@@type": "SearchAction",
            "target": "{{ route('search') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    {{ $head ?? '' }}
</head>
<body 
    class="min-h-dvh bg-white text-dark font-inter"
    x-data="{ 
        mobileMenuOpen: false,
        cartOpen: false,
        quickViewOpen: false,
        quickViewProduct: null,
        externalModalOpen: false,
        externalLinkUrl: '',
        externalLinkHost: '',
        openExternalLink(url) {
            this.externalLinkUrl = url;
            try {
                this.externalLinkHost = new URL(url).host;
            } catch (e) {
                this.externalLinkHost = '';
            }
            this.externalModalOpen = true;
        }
    }"
    x-on:open-external-link.window="openExternalLink($event.detail.url)"
>
    <!-- Skip to content for accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary text-white px-4 py-2 rounded-lg z-50">
        Saltar al contenido
    </a>

    <!-- Promo Banner (opcional) -->
    @if(isset($promoBanner) && $promoBanner)
    <div 
        x-data="{ dismissed: false }"
        x-show="!dismissed"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-full"
        class="bg-primary text-white py-2 px-4 text-center text-sm relative"
    >
        <div class="container mx-auto flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13H8.5a2.5 2.5 0 110-5H12m0 5h3.5a2.5 2.5 0 110-5H12m0 5h8.5a1.5 1.5 0 011.5 1.5V20a1 1 0 01-1 1H3a1 1 0 01-1-1V9.5A1.5 1.5 0 013.5 8H12z"/>
            </svg>
            <span>{{ $promoBanner->text }}</span>
            @if($promoBanner->has_countdown)
            <livewire:components.countdown-timer :endsAt="$promoBanner->ends_at" />
            @endif
            @if($promoBanner->button_text)
            <a href="{{ $promoBanner->button_url }}" class="ml-2 underline hover:no-underline">
                {{ $promoBanner->button_text }}
            </a>
            @endif
        </div>
        @if($promoBanner->is_dismissible)
        <button 
            @click="dismissed = true" 
            class="absolute right-4 top-1/2 -translate-y-1/2 hover:opacity-70"
            aria-label="Cerrar banner"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        @endif
    </div>
    @endif

    <!-- Navbar -->
    <livewire:components.navbar />

    <!-- Main Content -->
    <main id="main-content" class="min-h-[calc(100dvh-theme(spacing.16)-theme(spacing.64))]">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <livewire:components.footer />

    <!-- Quick View Modal -->
    <livewire:components.quick-view />

    <!-- Cart Drawer -->
    <livewire:components.cart-drawer />

    <!-- External Link Warning Modal -->
    <div
        x-show="externalModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center"
        aria-modal="true"
        role="dialog"
    >
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="externalModalOpen = false"></div>
        <div class="relative mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="mx-auto w-14 h-14 bg-secondary/30 rounded-full flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </div>
            <h3 class="text-lg font-display font-medium text-dark mb-2 text-center">Saliendo de Flores D&D</h3>
            <p class="text-gray-600 text-sm mb-2 text-center">
                Estás a punto de visitar un sitio externo. ¿Deseas continuar?
            </p>
            <p class="text-xs text-gray-500 mb-6 text-center" x-text="externalLinkHost"></p>
            <div class="flex gap-3 justify-center">
                <button class="px-4 py-2 rounded-full text-sm text-gray-600 hover:text-dark" @click="externalModalOpen = false">
                    Cancelar
                </button>
                <button class="px-5 py-2 rounded-full text-sm bg-primary text-white hover:bg-primary-dark" @click="window.open(externalLinkUrl, '_blank', 'noopener'); externalModalOpen = false;">
                    Continuar
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notifications Container -->
    <div 
        x-data="toastNotifications()"
        @toast.window="add($event.detail)"
        class="fixed bottom-4 right-4 z-50 flex flex-col gap-2"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-full"
                :class="{
                    'bg-green-500': toast.type === 'success',
                    'bg-red-500': toast.type === 'error',
                    'bg-yellow-500': toast.type === 'warning',
                    'bg-blue-500': toast.type === 'info',
                    'bg-primary': !toast.type
                }"
                class="text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 min-w-[300px]"
            >
                <span x-text="toast.message"></span>
                <button @click="remove(toast.id)" class="ml-auto hover:opacity-70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    @livewireScripts
    @fluxScripts
    
    <!-- Custom Scripts -->
    <script>
        // Toast notifications system
        function toastNotifications() {
            return {
                toasts: [],
                add(toast) {
                    const id = Date.now();
                    this.toasts.push({ ...toast, id, visible: true });
                    
                    setTimeout(() => {
                        this.remove(id);
                    }, toast.duration || 5000);
                },
                remove(id) {
                    const index = this.toasts.findIndex(t => t.id === id);
                    if (index > -1) {
                        this.toasts[index].visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 300);
                    }
                }
            }
        }

        // External link handler
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href^="http"]').forEach(link => {
                if (!link.href.includes(window.location.hostname)) {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.href;
                        
                        // Show modal
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'external-link-warning' }));
                        
                        // Set continue button action
                        const btn = document.querySelector('[x-ref="externalLinkBtn"]');
                        if (btn) {
                            btn.onclick = () => window.open(url, '_blank');
                        }
                    });
                }
            });
        });
    </script>
    
    {{ $scripts ?? '' }}
</body>
</html>
