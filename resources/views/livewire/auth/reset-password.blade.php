<div class="min-h-dvh bg-secondary/20 flex items-center justify-center py-16">
    <div class="container-custom">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-card p-8">
            <h1 class="font-display text-2xl text-dark mb-2">Restablecer contraseña</h1>
            <p class="text-sm text-dark/60 mb-6">Crea una nueva contraseña segura para tu cuenta.</p>

            @if($invalid)
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    El enlace es inválido o ha expirado. Solicita uno nuevo.
                </div>
                <div class="mt-6 text-center text-sm text-dark/70">
                    <a href="{{ route('password.request') }}" class="text-primary hover:text-primary-dark font-medium">Solicitar nuevo enlace</a>
                </div>
            @else
                <form wire:submit.prevent="resetPassword" class="space-y-4">
                    <input type="hidden" wire:model.defer="email">

                    <div>
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" wire:model.defer="password" class="form-input" autocomplete="new-password">
                        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" wire:model.defer="password_confirmation" class="form-input" autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn-primary w-full">Actualizar contraseña</button>
                </form>
            @endif
        </div>
    </div>
</div>
