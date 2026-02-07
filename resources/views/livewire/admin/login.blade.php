<div class="min-h-[calc(100vh-120px)] flex items-center justify-center">
    <div class="relative w-full max-w-4xl">
        <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-primary/30 via-fuchsia-300/30 to-emerald-300/30 blur-2xl"></div>

        <div class="relative grid md:grid-cols-2 overflow-hidden rounded-3xl border border-white/60 bg-white/80 shadow-2xl backdrop-blur-xl">
            <div class="hidden md:flex flex-col justify-between p-10 bg-gradient-to-br from-primary/90 to-secondary/70 text-white">
                <div>
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/floresdyd-logo.webp') }}" class="h-10 w-auto" alt="Flores D&D">
                        <div>
                            <p class="text-sm/none tracking-wider uppercase opacity-80">Flores D&D</p>
                            <h2 class="font-display text-2xl">Panel Administrativo</h2>
                        </div>
                    </div>
                    <p class="mt-6 text-white/90 leading-relaxed">
                        Acceso seguro para gestión de pedidos, catálogo y clientes.
                    </p>
                </div>
                <div class="mt-10 space-y-4 text-sm text-white/80">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-2 w-2 rounded-full bg-emerald-300"></span>
                        Autenticación protegida con control de intentos
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-2 w-2 rounded-full bg-emerald-300"></span>
                        Acceso solo para roles autorizados
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-10" x-data="{ showPassword: false }">
                <div class="mb-8">
                    <h1 class="font-display text-3xl text-dark">Bienvenido/a</h1>
                    <p class="text-gray-500">Inicia sesión para continuar al panel.</p>
                </div>

                <form wire:submit.prevent="authenticate" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Correo</label>
                        <div class="relative">
                            <input
                                type="email"
                                wire:model.defer="email"
                                class="w-full rounded-xl border border-gray-200 bg-white/70 px-4 py-3 pr-10 text-sm shadow-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/20"
                                placeholder="superadmin@floresdyd.cl"
                                autocomplete="username"
                            >
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5c-5.523 0-10 4.477-10 10 0 1.247.228 2.44.64 3.543C4.296 16.49 7.93 14 12 14s7.704 2.49 9.36 4.043c.412-1.103.64-2.296.64-3.543 0-5.523-4.477-10-10-10z" />
                                </svg>
                            </span>
                        </div>
                        @error('email') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Contraseña</label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                wire:model.defer="password"
                                class="w-full rounded-xl border border-gray-200 bg-white/70 px-4 py-3 pr-12 text-sm shadow-sm outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/20"
                                placeholder="••••••••"
                                autocomplete="current-password"
                            >
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 transition hover:text-dark"
                                @click="showPassword = !showPassword"
                                :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                            >
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3l18 18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.477 10.48a3 3 0 104.243 4.243" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228C4.484 7.533 3.176 9.202 2.458 12c1.274 4.057 5.064 7 9.542 7 1.66 0 3.24-.402 4.637-1.116" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.88 4.243A9.956 9.956 0 0112 4c4.478 0 8.268 2.943 9.542 7a10.03 10.03 0 01-3.343 4.6" />
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-dark px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-dark/20 transition hover:-translate-y-0.5 hover:bg-dark/90">
                        Ingresar al panel
                    </button>
                </form>

                <p class="mt-6 text-xs text-gray-500">
                    Acceso restringido. Tus intentos quedan registrados.
                </p>
            </div>
        </div>
    </div>
</div>
