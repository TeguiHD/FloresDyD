<div x-data="{ confirmDelete: null }">
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Marketing</p>
                <h1 class="font-display text-2xl">Popups inteligentes</h1>
            </div>
            <button type="button" class="admin-cta" wire:click="startCreate">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo popup
            </button>
        </div>

        {{-- Filtros --}}
        <div class="grid gap-4 md:grid-cols-3 mt-6">
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" class="form-input" wire:model.live.debounce.400ms="search"
                    placeholder="Título o mensaje...">
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select class="form-input" wire:model.live="filter">
                    @foreach($filters as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Referencia</label>
                <div class="flex flex-wrap gap-2 pt-1.5">
                    <span class="admin-pill">✅ Activo</span>
                    <span class="admin-pill admin-pill--warning">📅 Programado</span>
                    <span class="admin-pill admin-pill--danger">⏰ Expirado</span>
                    <span class="admin-pill" style="background: rgba(107,114,128,0.12); color: #6b7280;">⛔
                        Inactivo</span>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="mt-6 overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Popup</th>
                        <th>Trigger</th>
                        <th>Estado</th>
                        <th>Páginas</th>
                        <th>Métricas</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($popups as $popup)
                        @php
                            $statusClass = 'admin-pill';
                            $statusLabel = 'Activo';
                            if (!$popup->is_active) {
                                $statusClass = 'admin-pill';
                                $statusLabel = 'Inactivo';
                            } elseif ($popup->starts_at && $popup->starts_at->isFuture()) {
                                $statusClass = 'admin-pill admin-pill--warning';
                                $statusLabel = 'Programado';
                            } elseif ($popup->expires_at && $popup->expires_at->isPast()) {
                                $statusClass = 'admin-pill admin-pill--danger';
                                $statusLabel = 'Expirado';
                            }
                            $triggerLabel = match ($popup->trigger) {
                                'exit_intent' => '🚪 Salida',
                                'scroll' => '📜 Scroll',
                                'time_delay' => '⏱ Delay',
                                default => '📄 Carga',
                            };
                            $pages = $popup->show_on_pages ? implode(', ', $popup->show_on_pages) : 'Todas';
                            $ctr = $popup->views_count > 0
                                ? round(($popup->clicks_count / $popup->views_count) * 100, 1)
                                : 0;
                        @endphp
                        <tr>
                            <td>
                                <div>
                                    <p class="font-medium text-ink text-sm">{{ $popup->title }}</p>
                                    <p class="text-xs text-ink/50">{{ $popup->subtitle ?: 'Sin subtítulo' }}</p>
                                </div>
                            </td>
                            <td>
                                <span class="admin-pill">{{ $triggerLabel }}</span>
                                @if($popup->trigger_value)
                                    <p class="text-xs text-ink/50 mt-1">
                                        {{ $popup->trigger_value }}{{ $popup->trigger === 'scroll' ? '%' : 's' }}
                                    </p>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $statusClass }}" @if(!$popup->is_active)
                                    style="background: rgba(107,114,128,0.12); color: #6b7280;"
                                @endif>{{ $statusLabel }}</span>
                            </td>
                            <td class="text-xs text-ink/60 max-w-[120px] truncate" title="{{ $pages }}">
                                {{ \Illuminate\Support\Str::limit($pages, 30) }}
                            </td>
                            <td>
                                <p class="text-sm font-medium">{{ $popup->views_count }} vistas</p>
                                <p class="text-xs text-ink/50">{{ $popup->clicks_count }} clics · {{ $ctr }}% CTR</p>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" class="admin-link text-sm"
                                        wire:click="startEdit({{ $popup->id }})">Editar</button>
                                    <button type="button" class="admin-link text-sm text-red-500"
                                        x-on:click="confirmDelete = {{ $popup->id }}">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-sm text-ink/60 py-8 text-center">
                                No hay popups creados. Crea uno con el botón de arriba.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $popups->links() }}
        </div>
    </section>

    {{-- ========== MODAL CREAR/EDITAR ========== --}}
    @if($showForm)
        <div class="admin-overlay" wire:click="cancelForm"></div>
        <div class="admin-modal">
            <div class="admin-modal__header">
                <div>
                    <h2 class="font-display text-xl">{{ $editingPopupId ? 'Editar popup' : 'Nuevo popup' }}</h2>
                    <p class="text-xs text-ink/50 mt-0.5">Configura un popup inteligente para captar la atención del usuario
                    </p>
                </div>
                <button type="button" class="admin-modal__close" wire:click="cancelForm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="save">
                <div class="admin-modal__body">
                    {{-- Contenido --}}
                    <fieldset class="admin-fieldset">
                        <legend class="admin-fieldset__legend">📝 Contenido</legend>

                        <div class="mb-3">
                            <label class="form-label">Título <span class="text-red-400">*</span></label>
                            <input type="text" class="form-input" wire:model.defer="title"
                                placeholder="¡Lanzamiento especial!">
                            @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subtítulo</label>
                            <input type="text" class="form-input" wire:model.defer="subtitle"
                                placeholder="Oferta por tiempo limitado">
                        </div>

                        <div>
                            <label class="form-label">Mensaje principal</label>
                            <textarea class="form-input min-h-[100px]" wire:model.defer="content"
                                placeholder="Describe el beneficio o acción para el usuario"></textarea>
                            @error('content') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </fieldset>

                    {{-- Imagen --}}
                    <fieldset class="admin-fieldset">
                        <legend class="admin-fieldset__legend">🖼️ Imagen</legend>
                        <p class="text-xs text-ink/50 mb-3">Pega una URL externa o sube un archivo. Se convertirá
                            automáticamente a <strong>WebP</strong>.</p>

                        <div class="mb-3">
                            <label class="form-label">URL de imagen (enlace externo)</label>
                            <input type="text" class="form-input" wire:model.live.debounce.500ms="image"
                                placeholder="https://ejemplo.com/imagen.jpg">
                            @error('image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label">O sube un archivo</label>
                            <input type="file" class="form-input" wire:model="imageUpload" accept="image/*">
                            @error('imageUpload') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-ink/40 mt-1">Máx. 5 MB · Se convertirá a WebP automáticamente</p>
                        </div>

                        {{-- Vista previa --}}
                        <div class="mt-3">
                            @if($imageUpload)
                                <div class="admin-image-preview">
                                    @if($imageUpload->isPreviewable())
                                        <img src="{{ $imageUpload->temporaryUrl() }}" alt="Vista previa">
                                    @else
                                        <div class="admin-image-preview__error">📎 {{ $imageUpload->getClientOriginalName() }}</div>
                                    @endif
                                    <button type="button" class="admin-image-preview__remove" wire:click="clearImageUpload">✕
                                        Quitar</button>
                                </div>
                            @elseif($image && str_starts_with($image, 'http'))
                                <div class="admin-image-preview" x-data="{ error: false }">
                                    <img x-show="!error" src="{{ $image }}" alt="Vista previa URL" x-on:error="error = true"
                                        x-on:load="error = false">
                                    <div x-show="error" class="admin-image-preview__error">
                                        ⚠️ No se pudo cargar la imagen
                                    </div>
                                </div>
                            @elseif($image && !str_starts_with($image, 'http'))
                                <div class="admin-image-preview">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Imagen actual">
                                </div>
                            @endif
                        </div>
                    </fieldset>

                    {{-- Botón CTA --}}
                    <fieldset class="admin-fieldset">
                        <legend class="admin-fieldset__legend">🔗 Botón de acción (opcional)</legend>

                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="form-label">Texto del botón</label>
                                <input type="text" class="form-input" wire:model.defer="button_text"
                                    placeholder="Ver ofertas">
                            </div>
                            <div>
                                <label class="form-label">URL destino</label>
                                <input type="text" class="form-input" wire:model.defer="button_url"
                                    placeholder="https://instagram.com/tu-tienda">
                                <p class="admin-url-hint">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    Acepta rutas internas (/ruta) o externas (https://...)
                                </p>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Trigger --}}
                    <fieldset class="admin-fieldset">
                        <legend class="admin-fieldset__legend">⚡ Disparador (trigger)</legend>
                        <p class="text-xs text-ink/50 mb-3">Define cuándo aparece el popup al usuario.</p>

                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="form-label">Tipo de trigger</label>
                                <select class="form-input" wire:model.defer="trigger">
                                    <option value="page_load">📄 Al cargar la página</option>
                                    <option value="exit_intent">🚪 Intención de salida (desktop)</option>
                                    <option value="scroll">📜 Al hacer scroll (%)</option>
                                    <option value="time_delay">⏱ Después de X segundos</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Valor</label>
                                <input type="number" class="form-input" wire:model.defer="trigger_value"
                                    placeholder="Ej: 20" min="1">
                                <p class="text-xs text-ink/40 mt-1">Scroll = %, Delay = segundos</p>
                                @error('trigger_value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- Páginas --}}
                    <fieldset class="admin-fieldset">
                        <legend class="admin-fieldset__legend">📄 Mostrar en páginas</legend>
                        <p class="text-xs text-ink/50 mb-3">Selecciona en qué páginas aparece este popup. Si no seleccionas
                            ninguna, se muestra en todas.</p>

                        <div class="flex flex-wrap gap-2">
                            @foreach($availablePages as $path => $label)
                                <label class="admin-page-toggle">
                                    <input type="checkbox" value="{{ $path }}" wire:model.defer="selected_pages"
                                        class="sr-only">
                                    <span class="admin-page-toggle__label">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- Programación --}}
                    <fieldset class="admin-fieldset">
                        <legend class="admin-fieldset__legend">📅 Programación y estado</legend>

                        <div class="space-y-2 mb-4">
                            <label class="flex items-center gap-2 text-sm font-medium text-ink/80">
                                <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600"
                                    wire:model.defer="is_active">
                                Popup activo
                                <span class="text-xs font-normal text-ink/40">(desactiva para pausar manualmente)</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm text-ink/70">
                                <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600"
                                    wire:model.defer="show_once_per_session">
                                Mostrar solo una vez por sesión
                            </label>
                            <label class="flex items-center gap-2 text-sm text-ink/70">
                                <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600"
                                    wire:model.defer="show_once_per_user">
                                Mostrar solo una vez por usuario (cookie permanente)
                            </label>
                        </div>

                        <div class="grid gap-3 md:grid-cols-2">
                            {{-- Date picker: Inicio --}}
                            <div x-data="adminDatepicker($wire, 'starts_at')" x-on:click.outside="open = false"
                                class="admin-datepicker">
                                <label class="form-label">Inicio</label>
                                <button type="button" class="admin-datepicker__trigger" x-on:click="toggle()">
                                    <svg class="admin-datepicker__trigger-icon" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span x-text="displayValue || 'Inmediato'"
                                        x-bind:class="{ 'admin-datepicker__trigger--placeholder': !displayValue }"></span>
                                    <template x-if="displayValue">
                                        <button type="button" class="admin-datepicker__trigger-clear"
                                            x-on:click.stop="clear()" title="Limpiar">&times;</button>
                                    </template>
                                </button>
                                <div x-show="open" x-transition class="admin-datepicker__dropdown">
                                    <div class="admin-datepicker__nav">
                                        <button type="button" class="admin-datepicker__nav-btn" x-on:click="prevMonth()">
                                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                        <span class="admin-datepicker__month-label" x-text="monthLabel"></span>
                                        <button type="button" class="admin-datepicker__nav-btn" x-on:click="nextMonth()">
                                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="admin-datepicker__weekdays">
                                        <template x-for="d in ['Lu','Ma','Mi','Ju','Vi','Sa','Do']"><span
                                                class="admin-datepicker__weekday" x-text="d"></span></template>
                                    </div>
                                    <div class="admin-datepicker__days">
                                        <template x-for="cell in calendarDays" :key="cell.key">
                                            <button type="button" class="admin-datepicker__day" x-bind:class="{
                                                        'admin-datepicker__day--today': cell.isToday,
                                                        'admin-datepicker__day--selected': cell.isSelected,
                                                        'admin-datepicker__day--disabled': cell.disabled,
                                                        'admin-datepicker__day--outside': cell.outside,
                                                    }" x-text="cell.day" x-on:click="selectDay(cell)"
                                                x-bind:disabled="cell.disabled"></button>
                                        </template>
                                    </div>
                                    <div class="admin-datepicker__time">
                                        <span class="admin-datepicker__time-label">Hora</span>
                                        <input type="number" class="admin-datepicker__time-input" min="0" max="23"
                                            x-model="hour" x-on:change="syncToWire()">
                                        <span class="admin-datepicker__time-sep">:</span>
                                        <input type="number" class="admin-datepicker__time-input" min="0" max="59"
                                            x-model="minute" x-on:change="syncToWire()">
                                    </div>
                                </div>
                                <p class="text-xs text-ink/40 mt-1">Vacío = se muestra inmediatamente</p>
                            </div>

                            {{-- Date picker: Fin --}}
                            <div x-data="adminDatepicker($wire, 'expires_at')" x-on:click.outside="open = false"
                                class="admin-datepicker">
                                <label class="form-label">Fin</label>
                                <button type="button" class="admin-datepicker__trigger" x-on:click="toggle()">
                                    <svg class="admin-datepicker__trigger-icon" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span x-text="displayValue || 'Sin límite'"
                                        x-bind:class="{ 'admin-datepicker__trigger--placeholder': !displayValue }"></span>
                                    <template x-if="displayValue">
                                        <button type="button" class="admin-datepicker__trigger-clear"
                                            x-on:click.stop="clear()" title="Limpiar">&times;</button>
                                    </template>
                                </button>
                                <div x-show="open" x-transition class="admin-datepicker__dropdown">
                                    <div class="admin-datepicker__nav">
                                        <button type="button" class="admin-datepicker__nav-btn" x-on:click="prevMonth()">
                                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                        <span class="admin-datepicker__month-label" x-text="monthLabel"></span>
                                        <button type="button" class="admin-datepicker__nav-btn" x-on:click="nextMonth()">
                                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="admin-datepicker__weekdays">
                                        <template x-for="d in ['Lu','Ma','Mi','Ju','Vi','Sa','Do']"><span
                                                class="admin-datepicker__weekday" x-text="d"></span></template>
                                    </div>
                                    <div class="admin-datepicker__days">
                                        <template x-for="cell in calendarDays" :key="cell.key">
                                            <button type="button" class="admin-datepicker__day" x-bind:class="{
                                                        'admin-datepicker__day--today': cell.isToday,
                                                        'admin-datepicker__day--selected': cell.isSelected,
                                                        'admin-datepicker__day--disabled': cell.disabled,
                                                        'admin-datepicker__day--outside': cell.outside,
                                                    }" x-text="cell.day" x-on:click="selectDay(cell)"
                                                x-bind:disabled="cell.disabled"></button>
                                        </template>
                                    </div>
                                    <div class="admin-datepicker__time">
                                        <span class="admin-datepicker__time-label">Hora</span>
                                        <input type="number" class="admin-datepicker__time-input" min="0" max="23"
                                            x-model="hour" x-on:change="syncToWire()">
                                        <span class="admin-datepicker__time-sep">:</span>
                                        <input type="number" class="admin-datepicker__time-input" min="0" max="59"
                                            x-model="minute" x-on:change="syncToWire()">
                                    </div>
                                </div>
                                @error('expires_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="admin-modal__footer">
                    <button type="submit" class="admin-cta">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        {{ $editingPopupId ? 'Guardar cambios' : 'Crear popup' }}
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
                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </div>
                    <h3 class="font-display text-lg mb-2">¿Eliminar este popup?</h3>
                    <p class="text-sm text-ink/60 mb-6">Se dejará de mostrar inmediatamente en el sitio. Las métricas se
                        perderán.</p>
                    <div class="flex justify-center gap-3">
                        <button type="button" class="admin-ghost-button"
                            x-on:click="confirmDelete = null">Cancelar</button>
                        <button type="button" class="admin-cta" style="background: #dc2626"
                            x-on:click="$wire.deletePopup(confirmDelete); confirmDelete = null">Sí, eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>