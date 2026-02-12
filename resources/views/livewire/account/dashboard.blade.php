<div class="min-h-dvh bg-secondary/10 py-10">
    <div class="container-custom grid lg:grid-cols-[260px_1fr] gap-6">
        @include('livewire.account.partials.nav')

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-card p-6">
                <h1 class="font-display text-2xl text-dark mb-2">Mi cuenta</h1>
                <p class="text-sm text-dark/60 mb-6">Actualiza tus datos personales y ubicación.</p>

                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nombre</label>
                            <input type="text" wire:model.defer="name" class="form-input" autocomplete="name">
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" wire:model.defer="email" class="form-input bg-gray-50" readonly>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Teléfono</label>
                            <input type="text" wire:model.defer="phone" class="form-input" autocomplete="tel">
                            @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Ubicación (dirección)</label>
                            <input type="text" wire:model.defer="address" class="form-input" autocomplete="street-address">
                            @error('address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">Guardar cambios</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-card p-6">
                <h2 class="font-display text-xl text-dark mb-4">Historial de nombres</h2>
                @if($nameHistory->isEmpty())
                    <p class="text-sm text-dark/60">Aún no tienes cambios registrados.</p>
                @else
                    <div class="space-y-3">
                        @foreach($nameHistory as $history)
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="text-dark font-medium">{{ $history->old_name }} → {{ $history->new_name }}</p>
                                    <p class="text-dark/60">{{ $history->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
