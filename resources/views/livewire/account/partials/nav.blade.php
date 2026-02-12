<aside class="bg-white rounded-2xl shadow-card p-5" x-data="{ open: { account: true, orders: true, admin: true } }">
    <p class="text-xs uppercase tracking-[0.3em] text-ink/50 mb-4">Mi espacio</p>
    <nav class="space-y-3">
        <div class="border border-secondary/60 rounded-xl">
            <button type="button" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-dark" @click="open.account = !open.account">
                <span>Cuenta</span>
                <x-flux::icon name="chevron-down" class="w-4 h-4 transition-transform" x-bind:class="open.account ? 'rotate-180' : ''" />
            </button>
            <div class="px-2 pb-2 space-y-1" x-show="open.account" x-collapse>
                <a href="{{ route('account.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('account.dashboard') ? 'bg-secondary text-primary font-medium' : 'text-dark hover:bg-gray-50' }}">
                    Mi cuenta
                </a>
            </div>
        </div>

        <div class="border border-secondary/60 rounded-xl">
            <button type="button" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-dark" @click="open.orders = !open.orders">
                <span>Compras</span>
                <x-flux::icon name="chevron-down" class="w-4 h-4 transition-transform" x-bind:class="open.orders ? 'rotate-180' : ''" />
            </button>
            <div class="px-2 pb-2 space-y-1" x-show="open.orders" x-collapse>
                <a href="{{ route('account.orders') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('account.orders*') ? 'bg-secondary text-primary font-medium' : 'text-dark hover:bg-gray-50' }}">
                    Mis pedidos
                </a>
            </div>
        </div>

        @if(auth()->user()->hasAnyRole(['super-admin', 'admin']))
            <div class="border border-secondary/60 rounded-xl">
                <button type="button" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium text-dark" @click="open.admin = !open.admin">
                    <span>Administración</span>
                    <x-flux::icon name="chevron-down" class="w-4 h-4 transition-transform" x-bind:class="open.admin ? 'rotate-180' : ''" />
                </button>
                <div class="px-2 pb-2 space-y-1" x-show="open.admin" x-collapse>
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-dark hover:bg-gray-50">
                        Panel de administración
                    </a>
                </div>
            </div>
        @endif
    </nav>
    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm text-dark/70 hover:text-dark hover:bg-gray-50">
            Cerrar sesión
        </button>
    </form>
</aside>
