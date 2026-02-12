<div class="min-h-dvh bg-secondary/20 flex items-center justify-center py-16">
    <div class="container-custom">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-card p-8">
            <h1 class="font-display text-2xl text-dark mb-2">Crear cuenta</h1>
            <p class="text-sm text-dark/60 mb-6">Regístrate para guardar tus datos y ver tus pedidos.</p>

            @if($submitted)
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                    Si el correo existe, te enviamos un enlace para crear o recuperar tu contraseña.
                </div>
                <div class="mt-6 text-center text-sm text-dark/70">
                    <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium">Volver a iniciar
                        sesión</a>
                </div>
            @else
                <form wire:submit.prevent="register" class="space-y-4">
                    <div>
                        <label class="form-label">Nombre</label>
                        <input type="text" wire:model.defer="name" class="form-input" autocomplete="name">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" wire:model.defer="email" class="form-input" autocomplete="email">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Contraseña</label>
                        <input type="password" wire:model.defer="password" class="form-input" autocomplete="new-password">
                        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" wire:model.defer="password_confirmation" class="form-input"
                            autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn-primary w-full">Crear cuenta</button>
                </form>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-secondary"></div>
                    </div>
                    <div class="relative flex justify-center text-xs"><span class="bg-white px-3 text-dark/50">o regístrate
                            con</span></div>
                </div>

                <a href="{{ route('auth.google') }}"
                    class="flex items-center justify-center gap-3 w-full rounded-xl border border-secondary px-4 py-3 text-sm font-medium text-dark/80 transition hover:bg-secondary/30 hover:border-primary/30">
                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"
                            fill="#4285F4" />
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853" />
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05" />
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335" />
                    </svg>
                    Registrarse con Google
                </a>

                <div class="mt-6 text-center text-sm text-dark/70">
                    ¿Ya tienes cuenta?
                    <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium">Iniciar
                        sesión</a>
                </div>
            @endif
        </div>
    </div>
</div>