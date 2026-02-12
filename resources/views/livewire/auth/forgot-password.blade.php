<div class="min-h-dvh bg-secondary/20 flex items-center justify-center py-16">
    <div class="container-custom">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-card p-8">
            <h1 class="font-display text-2xl text-dark mb-2">Recuperar contraseña</h1>
            <p class="text-sm text-dark/60 mb-6">Te enviaremos un enlace para restablecer tu contraseña.</p>

            @if($submitted)
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                    Si el correo existe, te enviamos un enlace para restablecer tu contraseña.
                </div>
                <div class="mt-6 text-center text-sm text-dark/70">
                    <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium">Volver a iniciar sesión</a>
                </div>
            @else
                <form wire:submit.prevent="send" class="space-y-4">
                    <div>
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" wire:model.defer="email" class="form-input" autocomplete="email">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-primary w-full">Enviar enlace</button>
                </form>
            @endif
        </div>
    </div>
</div>
