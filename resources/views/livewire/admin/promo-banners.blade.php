<div x-data="{ confirmDelete: null }">
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Marketing</p>
                <h1 class="font-display text-2xl">Banners promocionales</h1>
            </div>
            <button type="button" class="admin-cta" wire:click="startCreate">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nuevo banner
            </button>
        </div>

        <div class="grid gap-4 md:grid-cols-2 mt-6">
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" class="form-input" wire:model.live="search" placeholder="Texto o CTA...">
            </div>
            <div class="text-sm text-ink/60 pt-7">
                Estos banners aparecen en la parte superior del sitio con soporte para countdown y CTA.
            </div>
        </div>

        {{-- Tabla --}}
        <div class="mt-6 overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Banner</th>
                        <th>Colores</th>
                        <th>Estado</th>
                        <th>Vigencia</th>
                        <th>Páginas</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                        @php
                            $statusClass = 'admin-pill';
                            $statusLabel = 'Activo';
                            if (!$banner->is_active) {
                                $statusClass = 'admin-pill';
                                $statusLabel = 'Inactivo';
                            } elseif ($banner->starts_at && $banner->starts_at->isFuture()) {
                                $statusClass = 'admin-pill admin-pill--warning';
                                $statusLabel = 'Programado';
                            } elseif ($banner->ends_at && $banner->ends_at->isPast()) {
                                $statusClass = 'admin-pill admin-pill--danger';
                                $statusLabel = 'Expirado';
                            }
                            $pages = $banner->show_on_pages;
                            $pagesLabel = (empty($pages) || in_array('*', $pages)) ? 'Todas' : implode(', ', $pages);
                        @endphp
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($banner->icon)
                                        <span class="text-lg">{{ $banner->icon }}</span>
                                    @endif
                                    <div>
                                        <p class="font-medium text-ink text-sm">{{ $banner->text }}</p>
                                        @if($banner->button_text)
                                            <p class="text-xs text-ink/50">CTA: {{ $banner->button_text }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-md border border-ink/10 inline-block" style="background: {{ $banner->bg_color ?? '#1a1a2e' }}"></span>
                                    <span class="w-5 h-5 rounded-md border border-ink/10 inline-block" style="background: {{ $banner->text_color ?? '#ffffff' }}"></span>
                                </div>
                            </td>
                            <td>
                                <span class="{{ $statusClass }}" @if(!$banner->is_active) style="background: rgba(107,114,128,0.12); color: #6b7280;" @endif>{{ $statusLabel }}</span>
                                @if($banner->has_countdown)
                                    <span class="admin-pill admin-pill--warning text-xs ml-1">⏱ Timer</span>
                                @endif
                            </td>
                            <td class="text-xs text-ink/60">
                                <div>{{ $banner->starts_at?->format('d/m/Y H:i') ?? 'Inmediato' }}</div>
                                <div>{{ $banner->ends_at?->format('d/m/Y H:i') ?? 'Sin fin' }}</div>
                            </td>
                            <td class="text-xs text-ink/60 max-w-[120px] truncate" title="{{ $pagesLabel }}">
                                {{ \Illuminate\Support\Str::limit($pagesLabel, 30) }}
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" class="admin-link text-sm" wire:click="startEdit({{ $banner->id }})">Editar</button>
                                    <button type="button" class="admin-link text-sm text-red-500" x-on:click="confirmDelete = {{ $banner->id }}">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-sm text-ink/60 py-8 text-center">
                                No hay banners creados. Crea uno con el botón de arriba.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $banners->links() }}
        </div>
    </section>

    {{-- ========== MODAL CREAR/EDITAR ========== --}}
    @if($showForm)
    <div class="admin-overlay" wire:click="cancelForm"></div>
    <div class="admin-modal">
        <div class="admin-modal__header">
            <div>
                <h2 class="font-display text-xl">{{ $editingBannerId ? 'Editar banner' : 'Nuevo banner' }}</h2>
                <p class="text-xs text-ink/50 mt-0.5">Configura el banner promocional que aparecerá en la parte superior</p>
            </div>
            <button type="button" class="admin-modal__close" wire:click="cancelForm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form wire:submit.prevent="save">
            <div class="admin-modal__body">
                {{-- Contenido --}}
                <fieldset class="admin-fieldset">
                    <legend class="admin-fieldset__legend">📝 Contenido</legend>

                    <div class="mb-3">
                        <label class="form-label">Texto del banner <span class="text-red-400">*</span></label>
                        <input type="text" class="form-input" wire:model.defer="text" placeholder="🎁 ¡20% en todas las rosas este fin de semana!">
                        @error('text') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Icono / Emoji (opcional)</label>
                        <input type="text" class="form-input" wire:model.defer="icon" placeholder="🎁 🌹 🎉">
                        <p class="text-xs text-ink/40 mt-1">Se muestra antes del texto</p>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="form-label">Texto del botón CTA</label>
                            <input type="text" class="form-input" wire:model.defer="button_text" placeholder="Ver ofertas">
                        </div>
                        <div>
                            <label class="form-label">URL del botón</label>
                            <input type="text" class="form-input" wire:model.defer="button_url" placeholder="/coleccion">
                        </div>
                    </div>
                </fieldset>

                {{-- Apariencia --}}
                <fieldset class="admin-fieldset">
                    <legend class="admin-fieldset__legend">🎨 Apariencia</legend>

                    <div class="grid gap-4 md:grid-cols-2 mb-3">
                        <div>
                            <label class="form-label">Color de fondo</label>
                            <div class="flex items-center gap-3">
                                <input type="color" class="w-10 h-10 rounded-lg border border-ink/10 cursor-pointer p-0.5" wire:model.defer="bg_color">
                                <input type="text" class="form-input flex-1 font-mono text-sm" wire:model.defer="bg_color" placeholder="#1a1a2e">
                            </div>
                            @error('bg_color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Color del texto</label>
                            <div class="flex items-center gap-3">
                                <input type="color" class="w-10 h-10 rounded-lg border border-ink/10 cursor-pointer p-0.5" wire:model.defer="text_color">
                                <input type="text" class="form-input flex-1 font-mono text-sm" wire:model.defer="text_color" placeholder="#ffffff">
                            </div>
                            @error('text_color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Vista previa --}}
                    <div class="rounded-xl border border-ink/10 p-3 text-center text-sm font-medium" style="background: {{ $bg_color }}; color: {{ $text_color }}">
                        {{ $icon }} {{ $text ?: 'Vista previa del banner' }}
                        @if($button_text)
                            <span class="ml-2 underline">{{ $button_text }}</span>
                        @endif
                    </div>
                </fieldset>

                {{-- Programación --}}
                <fieldset class="admin-fieldset">
                    <legend class="admin-fieldset__legend">📅 Programación</legend>

                    <div class="space-y-2 mb-3">
                        <label class="flex items-center gap-2 text-sm text-ink/70">
                            <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="has_countdown">
                            Mostrar cuenta regresiva (countdown)
                        </label>
                        <label class="flex items-center gap-2 text-sm font-medium text-ink/80">
                            <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="is_active">
                            Banner activo
                            <span class="text-xs font-normal text-ink/40">(desactiva para pausar)</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-ink/70">
                            <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="is_dismissible">
                            El usuario puede cerrar el banner
                        </label>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="form-label">Inicio</label>
                            <input type="datetime-local" class="form-input" wire:model.defer="starts_at">
                            <p class="text-xs text-ink/40 mt-1">Vacío = inmediato</p>
                        </div>
                        <div>
                            <label class="form-label">Fin</label>
                            <input type="datetime-local" class="form-input" wire:model.defer="ends_at">
                            @error('ends_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Páginas --}}
                <fieldset class="admin-fieldset" x-data="{ selectedPages: @entangle('selected_pages').defer }">
                    <legend class="admin-fieldset__legend">📄 Mostrar en páginas</legend>
                    <p class="text-xs text-ink/50 mb-3">Selecciona en qué páginas se muestra este banner. Si no seleccionas ninguna, se muestra en todas.</p>

                    <div class="flex flex-wrap gap-2">
                        @foreach($availablePages as $path => $label)
                            <label class="admin-page-toggle">
                                <input
                                    type="checkbox"
                                    value="{{ $path }}"
                                    wire:model.defer="selected_pages"
                                    class="sr-only peer"
                                >
                                <span class="admin-page-toggle__label peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            </div>

            <div class="admin-modal__footer">
                <button type="submit" class="admin-cta">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    {{ $editingBannerId ? 'Guardar cambios' : 'Crear banner' }}
                </button>
                <button type="button" class="admin-ghost-button" wire:click="cancelForm">Cancelar</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Confirmación de eliminación --}}
    <template x-if="confirmDelete">
        <div>
            <div class="admin-overlay" x-on:click="confirmDelete = null"></div>
            <div class="admin-modal admin-modal--sm">
                <div class="p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    </div>
                    <h3 class="font-display text-lg mb-2">¿Eliminar este banner?</h3>
                    <p class="text-sm text-ink/60 mb-6">Se dejará de mostrar inmediatamente en el sitio.</p>
                    <div class="flex justify-center gap-3">
                        <button type="button" class="admin-ghost-button" x-on:click="confirmDelete = null">Cancelar</button>
                        <button type="button" class="admin-cta" style="background: #dc2626" x-on:click="$wire.deleteBanner(confirmDelete); confirmDelete = null">Sí, eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
