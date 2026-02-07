<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin - Flores D&D' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>
<body class="admin-theme text-dark">
    @auth
    <div x-data="{
            sidebarOpen: true,
            isMobile: false,
            init() {
                this.isMobile = window.innerWidth < 1024;
                const stored = window.localStorage.getItem('adminSidebarOpen');
                this.sidebarOpen = this.isMobile ? false : (stored === null ? true : stored === '1');
                window.addEventListener('resize', () => {
                    this.isMobile = window.innerWidth < 1024;
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                        return;
                    }
                    const nextStored = window.localStorage.getItem('adminSidebarOpen');
                    this.sidebarOpen = nextStored === null ? true : nextStored === '1';
                });
                this.$watch('sidebarOpen', (value) => {
                    if (!this.isMobile) {
                        window.localStorage.setItem('adminSidebarOpen', value ? '1' : '0');
                    }
                });
            },
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
            }
        }"
        class="admin-shell"
        :class="{ 'is-sidebar-hidden': !sidebarOpen }"
    >
        <div
            class="admin-overlay"
            x-show="sidebarOpen && isMobile"
            x-transition.opacity
            @click="sidebarOpen = false"
            x-cloak
        ></div>

        <aside class="admin-sidebar" :class="{ 'is-open': sidebarOpen }">
            <div class="admin-brand">
                <img src="{{ asset('images/floresdyd-logo.webp') }}" class="h-9 w-auto" alt="Flores D&D">
                <div class="admin-brand-text">
                    <p class="text-xs uppercase tracking-[0.35em] text-ink/60">Flores D&D</p>
                    <p class="font-display text-lg">Panel Administrativo</p>
                </div>
            </div>

            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" @class(['admin-nav-item', 'is-active' => request()->routeIs('admin.dashboard')])>
                    <span class="admin-nav-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5L12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Dashboard</span>
                </a>
                <a href="{{ route('admin.orders') }}" @class(['admin-nav-item', 'is-active' => request()->routeIs('admin.orders')])>
                    <span class="admin-nav-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8l3 4v11a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8l3-4z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 9h10" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Pedidos</span>
                </a>
                <a href="{{ route('admin.products') }}" @class(['admin-nav-item', 'is-active' => request()->routeIs('admin.products')])>
                    <span class="admin-nav-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9c2.5-2.5 9.5-2.5 12 0" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 15c1.8-1.5 8.2-1.5 10 0" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Productos</span>
                </a>
                <a href="{{ route('admin.customers') }}" @class(['admin-nav-item', 'is-active' => request()->routeIs('admin.customers')])>
                    <span class="admin-nav-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1" />
                            <circle cx="10" cy="7" r="3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M23 20v-1a4 4 0 0 0-3-3.87" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a3 3 0 0 1 0 5.74" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Clientes</span>
                </a>
                <a href="{{ route('admin.messages') }}" @class(['admin-nav-item', 'is-active' => request()->routeIs('admin.messages')])>
                    <span class="admin-nav-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v10H7l-3 3V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Mensajes</span>
                </a>
                <a href="{{ route('admin.analytics') }}" @class(['admin-nav-item', 'is-active' => request()->routeIs('admin.analytics')])>
                    <span class="admin-nav-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 15l4-4 3 3 5-6" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Métricas</span>
                </a>
                <a href="{{ route('admin.security') }}" @class(['admin-nav-item', 'is-active' => request()->routeIs('admin.security')])>
                    <span class="admin-nav-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3v5c0 5-3.5 8-7 10-3.5-2-7-5-7-10V6l7-3z" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Seguridad</span>
                </a>
                <a href="{{ route('admin.access') }}" @class(['admin-nav-item', 'is-active' => request()->routeIs('admin.access')])>
                    <span class="admin-nav-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 0 1 8 0v4" />
                            <rect x="5" y="11" width="14" height="9" rx="2" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Roles y permisos</span>
                </a>
                <a href="{{ route('admin.users') }}" @class(['admin-nav-item', 'is-active' => request()->routeIs('admin.users')])>
                    <span class="admin-nav-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 21v-1a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1" />
                            <circle cx="8.5" cy="7" r="3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21v-1a4 4 0 0 0-2.5-3.7" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Usuarios</span>
                </a>
                <a href="{{ route('admin.audit') }}" @class(['admin-nav-item', 'is-active' => request()->routeIs('admin.audit')])>
                    <span class="admin-nav-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5a2 2 0 0 1 2-2h9l5 5v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6M9 17h6" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Auditoría</span>
                </a>
                <a href="{{ route('admin.settings') }}" @class(['admin-nav-item', 'is-active' => request()->routeIs('admin.settings')])>
                    <span class="admin-nav-icon">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.07-6.07-1.41 1.41M7.34 16.66l-1.41 1.41m0-11.31 1.41 1.41m10.32 10.32 1.41 1.41" />
                            <circle cx="12" cy="12" r="3.2" />
                        </svg>
                    </span>
                    <span class="admin-nav-label">Ajustes</span>
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <div class="admin-user">
                    <div class="admin-user-avatar">
                        <span>{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                    </div>
                    <div class="admin-user-meta">
                        <p class="text-sm font-semibold">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-ink/50">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="admin-ghost-button" type="submit">Cerrar sesión</button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <button class="admin-icon-button" type="button" @click="toggleSidebar()">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M3 12h18M3 18h12" />
                    </svg>
                </button>

                <div>
                    <p class="text-[0.65rem] uppercase tracking-[0.32em] text-ink/50">Panel Admin</p>
                    <p class="font-display text-xl text-ink">{{ $title ?? 'Dashboard' }}</p>
                </div>

                <div class="admin-topbar-actions">
                    <span class="admin-chip">Entorno local</span>
                    <a href="{{ route('admin.settings') }}" class="admin-icon-button" aria-label="Ajustes">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.07-6.07-1.41 1.41M7.34 16.66l-1.41 1.41m0-11.31 1.41 1.41m10.32 10.32 1.41 1.41" />
                            <circle cx="12" cy="12" r="3.2" />
                        </svg>
                    </a>
                </div>
            </header>

            <main class="admin-content">
                {{ $slot }}
            </main>
        </div>
    </div>
    @else
        <main class="min-h-dvh flex items-center justify-center px-6 py-12">
            {{ $slot }}
        </main>
    @endauth

    @livewireScripts
</body>
</html>
