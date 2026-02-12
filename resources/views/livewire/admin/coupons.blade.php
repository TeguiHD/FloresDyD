<div x-data="{ confirmDelete: null }">
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Marketing</p>
                <h1 class="font-display text-2xl">Cupones y descuentos</h1>
            </div>
            <button type="button" class="admin-cta" wire:click="startCreate">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo cupón
            </button>
        </div>

        {{-- Filtros --}}
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="form-label">Buscar cupón</label>
                <input type="text" class="form-input" placeholder="Código o nombre..."
                    wire:model.live.debounce.400ms="search">
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select class="form-input" wire:model.live="filter">
                    @foreach($filters as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="form-label">Referencia</label>
                <div class="flex flex-wrap gap-2 pt-1.5">
                    <span class="admin-pill">✅ Activo</span>
                    <span class="admin-pill admin-pill--warning">📅 Programado</span>
                    <span class="admin-pill admin-pill--danger">⏰ Expirado</span>
                    <span class="admin-pill" style="background: rgba(107,114,128,0.12)">⛔ Inactivo</span>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="mt-6 overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descuento</th>
                        <th>Uso</th>
                        <th>Vigencia</th>
                        <th>Alcance</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        @php
                            $isScheduled = $coupon->starts_at && $coupon->starts_at->isFuture();
                            $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();

                            if (!$coupon->is_active) {
                                $statusLabel = 'Inactivo';
                                $statusClass = 'admin-pill';
                                $statusStyle = 'background: rgba(107,114,128,0.12); color: #6b7280;';
                            } elseif ($isScheduled) {
                                $statusLabel = 'Programado';
                                $statusClass = 'admin-pill admin-pill--warning';
                                $statusStyle = '';
                            } elseif ($isExpired) {
                                $statusLabel = 'Expirado';
                                $statusClass = 'admin-pill admin-pill--danger';
                                $statusStyle = '';
                            } else {
                                $statusLabel = 'Activo';
                                $statusClass = 'admin-pill';
                                $statusStyle = '';
                            }

                            $hasProducts = !empty($coupon->applicable_products);
                            $hasCategories = !empty($coupon->applicable_categories);
                            $hasExcluded = !empty($coupon->excluded_products);
                            $scope = [];
                            if ($hasProducts)
                                $scope[] = count($coupon->applicable_products) . ' productos';
                            if ($hasCategories)
                                $scope[] = count($coupon->applicable_categories) . ' categorías';
                            if ($hasExcluded)
                                $scope[] = count($coupon->excluded_products) . ' excluidos';
                        @endphp
                        <tr>
                            <td>
                                <div class="font-semibold font-mono text-sm">{{ $coupon->code }}</div>
                                <div class="text-xs text-ink/50">{{ $coupon->name }}</div>
                            </td>
                            <td class="text-sm">
                                @if($coupon->type === 'percentage')
                                    <span class="font-semibold text-emerald-700">{{ $coupon->value }}%</span>
                                @else
                                    <span
                                        class="font-semibold text-emerald-700">${{ number_format($coupon->value, 0, ',', '.') }}</span>
                                @endif
                                <div class="text-xs text-ink/50">
                                    {{ $coupon->type === 'percentage' ? 'Porcentaje' : 'Monto fijo' }}
                                    @if($coupon->max_discount_amount)
                                        · máx ${{ number_format($coupon->max_discount_amount, 0, ',', '.') }}
                                    @endif
                                </div>
                            </td>
                            <td class="text-sm">
                                <div class="font-medium">
                                    {{ $coupon->uses_count }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : '' }}</div>
                                <div class="text-xs text-ink/50">máx {{ $coupon->max_uses_per_user }} por cliente</div>
                            </td>
                            <td class="text-xs text-ink/60">
                                <div>📅 {{ $coupon->starts_at?->format('d/m/Y H:i') ?? 'Inmediato' }}</div>
                                <div>🏁 {{ $coupon->expires_at?->format('d/m/Y H:i') ?? 'Sin límite' }}</div>
                            </td>
                            <td class="text-xs text-ink/60">
                                @if(empty($scope))
                                    <span class="text-emerald-600 font-medium">Todo el catálogo</span>
                                @else
                                    {{ implode(', ', $scope) }}
                                @endif
                                @if($coupon->first_purchase_only)
                                    <div class="text-amber-600 font-medium mt-0.5">Solo 1ª compra</div>
                                @endif
                            </td>
                            <td><span class="{{ $statusClass }}" @if($statusStyle) style="{{ $statusStyle }}"
                            @endif>{{ $statusLabel }}</span></td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" class="admin-link text-sm"
                                        wire:click="startEdit({{ $coupon->id }})">Editar</button>
                                    <button type="button" class="admin-link text-sm text-red-500"
                                        x-on:click.prevent="confirmDelete = {{ $coupon->id }}">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-sm text-ink/60 py-8 text-center">
                                No hay cupones registrados. Crea el primero con el botón de arriba.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $coupons->links() }}
        </div>
    </section>

    {{-- ========== MODAL CREAR/EDITAR ========== --}}
    @if($showForm)
        <div class="admin-overlay" wire:click="cancelForm"></div>
        <div class="admin-modal">
            <div class="admin-modal__header">
                <div>
                    <h2 class="font-display text-xl">{{ $editingCouponId ? 'Editar cupón' : 'Nuevo cupón' }}</h2>
                    <p class="text-xs text-ink/50 mt-0.5">
                        {{ $editingCouponId ? 'Modifica los datos del cupón' : 'Configura un nuevo descuento para tus clientes' }}
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

                    {{-- ── Identidad ── --}}
                    <fieldset class="admin-fieldset">
                        <legend class="admin-fieldset__legend">🏷️ Identidad del cupón</legend>

                        <div class="admin-coupon-identity">
                            {{-- Código --}}
                            <div class="admin-code-field">
                                <label class="form-label">Código</label>
                                <div class="admin-code-toggle">
                                    <label class="admin-toggle-inline" title="Generar código automáticamente">
                                        <input type="checkbox" wire:model.live="autoCode">
                                        <span class="admin-toggle-inline__track"></span>
                                    </label>
                                    <span class="text-xs text-ink/50">{{ $autoCode ? 'Auto' : 'Manual' }}</span>
                                </div>
                                @if(!$autoCode)
                                    <input type="text" class="form-input font-mono uppercase" wire:model.defer="code"
                                        placeholder="MICODIGO">
                                    <p class="text-xs text-ink/40 mt-1">Solo letras, números y guiones.</p>
                                    @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                @else
                                    <div class="admin-auto-code-badge">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                                        </svg>
                                        Se generará al guardar
                                    </div>
                                @endif
                            </div>

                            {{-- Nombre --}}
                            <div>
                                <label class="form-label">Nombre interno <span class="text-red-400">*</span></label>
                                <input type="text" class="form-input" wire:model.defer="name"
                                    placeholder="Campaña Día de la Madre">
                                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Descripción colapsable --}}
                        <div class="mt-3" x-data="{ open: {{ $description ? 'true' : 'false' }} }">
                            <button type="button" class="admin-collapsible-trigger" @click="open = !open">
                                <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-90' : ''" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                                Agregar descripción (opcional)
                            </button>
                            <div x-show="open" x-collapse>
                                <textarea class="form-input mt-2" rows="2" wire:model.defer="description"
                                    placeholder="Notas internas sobre este cupón..."></textarea>
                            </div>
                        </div>
                    </fieldset>

                    {{-- ── Descuento ── --}}
                    <fieldset class="admin-fieldset">
                        <legend class="admin-fieldset__legend">💰 Configuración del descuento</legend>

                        {{-- Input group: Tipo + Valor --}}
                        <div class="admin-input-group mb-3">
                            <select class="admin-input-group__addon" wire:model.live="type">
                                <option value="percentage">%</option>
                                <option value="fixed">CLP $</option>
                            </select>
                            <input type="text" class="admin-input-group__input" wire:model.defer="value"
                                placeholder="{{ $type === 'percentage' ? 'Ej: 15' : 'Ej: 5000' }}">
                        </div>
                        <p class="text-xs text-ink/40 -mt-1 mb-3">
                            {{ $type === 'percentage' ? 'Porcentaje entre 1 y 100' : 'Monto fijo en pesos chilenos' }}
                        </p>
                        @error('value') <p class="text-xs text-red-500 mb-3">{{ $message }}</p> @enderror

                        {{-- Compra mínima + Descuento máximo --}}
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="form-label">
                                    <svg class="w-3.5 h-3.5 inline -mt-0.5 text-ink/40" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                    </svg>
                                    Compra mínima
                                </label>
                                <input type="text" class="form-input" wire:model.defer="min_purchase_amount"
                                    placeholder="Ej: 20000">
                                <p class="text-xs text-ink/40 mt-1">Monto mínimo del carrito</p>
                            </div>
                            <div>
                                <label class="form-label">
                                    <svg class="w-3.5 h-3.5 inline -mt-0.5 text-ink/40" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Descuento máximo
                                </label>
                                <input type="text" class="form-input" wire:model.defer="max_discount_amount"
                                    placeholder="Ej: 15000">
                                <p class="text-xs text-ink/40 mt-1">Tope del descuento</p>
                            </div>
                        </div>
                    </fieldset>

                    {{-- ── Vigencia ── --}}
                    <fieldset class="admin-fieldset">
                        <legend class="admin-fieldset__legend">📅 Vigencia</legend>

                        <div class="admin-date-grid">
                            <div class="admin-date-card">
                                <label class="form-label">Inicio</label>
                                <div class="admin-date-row">
                                    <input type="date" class="form-input" wire:model.defer="starts_at_date"
                                        min="{{ now()->format('Y-m-d') }}">
                                    <input type="time" class="form-input" wire:model.defer="starts_at_time" step="60">
                                </div>
                                <p class="text-xs text-ink/40 mt-1">Vacío = inmediato</p>
                                @error('starts_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="admin-date-card">
                                <label class="form-label">Expiración</label>
                                <div class="admin-date-row">
                                    <input type="date" class="form-input" wire:model.defer="expires_at_date"
                                        min="{{ now()->format('Y-m-d') }}">
                                    <input type="time" class="form-input" wire:model.defer="expires_at_time" step="60">
                                </div>
                                <p class="text-xs text-ink/40 mt-1">Vacío = sin límite</p>
                                @error('expires_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- ── Límites de uso ── --}}
                    <fieldset class="admin-fieldset">
                        <legend class="admin-fieldset__legend">🔒 Límites de uso</legend>

                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="form-label">
                                    <svg class="w-3.5 h-3.5 inline -mt-0.5 text-ink/40" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                    </svg>
                                    Tope total
                                </label>
                                <input type="number" class="form-input" wire:model.defer="max_uses" placeholder="Ilimitado"
                                    min="1" step="1" inputmode="numeric" pattern="[0-9]*">
                                <p class="text-xs text-ink/40 mt-1">Usos en todas las ventas</p>
                                @error('max_uses') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">
                                    <svg class="w-3.5 h-3.5 inline -mt-0.5 text-ink/40" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    Tope por cliente
                                </label>
                                <input type="number" class="form-input" wire:model.defer="max_uses_per_user" placeholder="1"
                                    min="1" step="1" inputmode="numeric" pattern="[0-9]*">
                                <p class="text-xs text-ink/40 mt-1">Límite por persona</p>
                                @error('max_uses_per_user') <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- ── Segmentación ── --}}
                    <fieldset class="admin-fieldset"
                        x-data="{ tab: 'categories', searchCategory: '', searchProduct: '', searchExclude: '' }">
                        <legend class="admin-fieldset__legend">🎯 Segmentación</legend>
                        <p class="text-xs text-ink/50 mb-3">Si no seleccionas nada, aplica a todo el catálogo.</p>

                        @php
                            $selectedOverlap = array_values(array_intersect($applicable_products ?? [], $excluded_products ?? []));
                            $overlapNames = collect($products)
                                ->whereIn('id', $selectedOverlap)
                                ->pluck('name')
                                ->take(5)
                                ->values()
                                ->all();
                            $hasOverlap = !empty($selectedOverlap);

                            $selectedCategoryIds = collect($applicable_categories ?? [])
                                ->map(fn($id) => (int) $id)
                                ->filter()
                                ->values();

                            $applicableProductIds = collect($applicable_products ?? [])
                                ->map(fn($id) => (int) $id)
                                ->filter()
                                ->values();

                            $filteredProducts = collect($products);
                            if ($selectedCategoryIds->isNotEmpty()) {
                                $filteredProducts = $filteredProducts->whereIn('category_id', $selectedCategoryIds->all());
                            }

                            $excludedCandidates = collect($products)->reject(function ($product) use ($applicableProductIds) {
                                return $applicableProductIds->contains((int) $product->id);
                            });

                            $filteredCount = $filteredProducts->count();
                            $totalCount = collect($products)->count();
                            $excludedCount = $excludedCandidates->count();
                            $categoriesCount = collect($categories)->count();
                        @endphp
                        @if($hasOverlap)
                            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                Hay productos que están en "Productos" y también en "Excluir".
                                @if(!empty($overlapNames))
                                    <span class="font-semibold">({{ implode(', ', $overlapNames) }})</span>
                                @endif
                                Quita uno de los dos para continuar.
                            </div>
                        @endif

                        <div class="admin-seg-tabs" role="tablist">
                            <button type="button" class="admin-seg-tab" :class="tab === 'categories' ? 'is-active' : ''"
                                @click="tab = 'categories'" role="tab">
                                Categorías
                                <span class="admin-pill">{{ count($applicable_categories) }}</span>
                            </button>
                            <button type="button" class="admin-seg-tab" :class="tab === 'products' ? 'is-active' : ''"
                                @click="tab = 'products'" role="tab">
                                Productos
                                <span class="admin-pill">{{ count($applicable_products) }}</span>
                            </button>
                            <button type="button" class="admin-seg-tab" :class="tab === 'exclude' ? 'is-active' : ''"
                                @click="tab = 'exclude'" role="tab">
                                Excluir
                                <span class="admin-pill admin-pill--neutral">{{ count($excluded_products) }}</span>
                            </button>
                        </div>

                        <div class="admin-seg-panel" x-cloak>
                            <div x-show="tab === 'categories'" x-transition>
                                <div class="admin-select-card">
                                    <div class="admin-select-header">
                                        <label class="form-label">Solo estas categorías</label>
                                        <span class="admin-pill">{{ count($applicable_categories) }} seleccionadas</span>
                                    </div>
                                    <p class="text-xs text-ink/50">Mostrando {{ $categoriesCount }} categorías.</p>
                                    <input type="text" class="form-input admin-search-input" placeholder="Buscar categoría"
                                        x-model="searchCategory">
                                    <div class="admin-select-panel">
                                        @foreach($categories as $category)
                                            <label class="admin-check" data-name="{{ mb_strtolower($category->name, 'UTF-8') }}"
                                                x-show="searchCategory === '' || $el.dataset.name.includes(searchCategory.toLowerCase())">
                                                <input type="checkbox" class="admin-check__input"
                                                    wire:model.live="applicable_categories" value="{{ $category->id }}">
                                                <span class="admin-check__label">{{ $category->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-ink/40 mt-1">Aplica solo a las categorías seleccionadas.</p>
                                    @error('applicable_categories') <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div x-show="tab === 'products'" x-transition>
                                <div class="admin-select-card">
                                    <div class="admin-select-header">
                                        <label class="form-label">Solo estos productos</label>
                                        <span class="admin-pill">{{ count($applicable_products) }} seleccionados</span>
                                    </div>
                                    <p class="text-xs text-ink/50">
                                        Mostrando {{ $filteredCount }} de {{ $totalCount }} productos
                                        @if($selectedCategoryIds->isNotEmpty())
                                            según las categorías seleccionadas.
                                        @endif
                                    </p>
                                    <input type="text" class="form-input admin-search-input" placeholder="Buscar producto"
                                        x-model="searchProduct">
                                    <div class="admin-select-panel">
                                        @foreach($filteredProducts as $product)
                                            <label class="admin-check" data-name="{{ mb_strtolower($product->name, 'UTF-8') }}"
                                                x-show="searchProduct === '' || $el.dataset.name.includes(searchProduct.toLowerCase())">
                                                <input type="checkbox" class="admin-check__input"
                                                    wire:model.live="applicable_products" value="{{ $product->id }}">
                                                <span class="admin-check__label">{{ $product->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @if($filteredProducts->isEmpty())
                                        <p class="text-xs text-ink/50 mt-2">
                                            No hay productos para las categorías seleccionadas.
                                        </p>
                                    @endif
                                    <p class="text-xs text-ink/40 mt-1">Solo los productos marcados recibirán el descuento.
                                    </p>
                                    @error('applicable_products') <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div x-show="tab === 'exclude'" x-transition>
                                <div class="admin-select-card">
                                    <div class="admin-select-header">
                                        <label class="form-label">Excluir estos productos</label>
                                        <span class="admin-pill admin-pill--neutral">{{ count($excluded_products) }}
                                            excluidos</span>
                                    </div>
                                    <p class="text-xs text-ink/50">Mostrando {{ $excludedCount }} productos disponibles para
                                        excluir.</p>
                                    <input type="text" class="form-input admin-search-input"
                                        placeholder="Buscar producto a excluir" x-model="searchExclude">
                                    <div class="admin-select-panel admin-select-panel--sm">
                                        @foreach($excludedCandidates as $product)
                                            <label class="admin-check" data-name="{{ mb_strtolower($product->name, 'UTF-8') }}"
                                                x-show="searchExclude === '' || $el.dataset.name.includes(searchExclude.toLowerCase())">
                                                <input type="checkbox" class="admin-check__input"
                                                    wire:model.live="excluded_products" value="{{ $product->id }}">
                                                <span class="admin-check__label">{{ $product->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @if($excludedCandidates->isEmpty())
                                        <p class="text-xs text-ink/50 mt-2">
                                            No hay productos disponibles para excluir.
                                        </p>
                                    @endif
                                    <p class="text-xs text-ink/40 mt-1">Estos productos nunca tendrán descuento con este
                                        cupón.</p>
                                    @error('excluded_products') <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                {{-- ── Footer con switches + acciones ── --}}
                <div class="admin-modal__footer admin-modal__footer--split">
                    <div class="admin-switch-bar">
                        <label class="admin-switch-bar__item" title="Activar o pausar el cupón">
                            <input type="checkbox" wire:model.defer="is_active">
                            <span class="admin-toggle-inline__track"></span>
                            <span class="admin-switch-bar__label">Activo</span>
                        </label>
                        <label class="admin-switch-bar__item" title="Restringir a primera compra del cliente">
                            <input type="checkbox" wire:model.defer="first_purchase_only">
                            <span class="admin-toggle-inline__track"></span>
                            <span class="admin-switch-bar__label">1ª compra</span>
                        </label>
                    </div>
                    <div class="admin-modal__actions">
                        <button type="button" class="admin-ghost-button" wire:click="cancelForm">Cancelar</button>
                        <button type="submit" class="admin-cta admin-tooltip" @disabled($hasOverlap)
                            aria-disabled="{{ $hasOverlap ? 'true' : 'false' }}"
                            data-tooltip="{{ $hasOverlap ? 'Hay productos seleccionados y excluidos al mismo tiempo.' : '' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            {{ $editingCouponId ? 'Guardar cambios' : 'Crear cupón' }}
                        </button>
                    </div>
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
                    <h3 class="font-display text-lg mb-2">¿Eliminar este cupón?</h3>
                    <p class="text-sm text-ink/60 mb-6">Esta acción no se puede deshacer.</p>
                    <div class="flex justify-center gap-3">
                        <button type="button" class="admin-ghost-button"
                            x-on:click="confirmDelete = null">Cancelar</button>
                        <button type="button" class="admin-cta" style="background: #dc2626"
                            x-on:click="$wire.deleteCoupon(confirmDelete); confirmDelete = null">Sí, eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>