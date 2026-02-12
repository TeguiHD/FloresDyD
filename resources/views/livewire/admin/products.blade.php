<div class="admin-grid lg:grid-cols-1" x-data>
    <section class="admin-card lg:col-span-1">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Catálogo</p>
                <h1 class="font-display text-2xl">Gestión profesional de productos</h1>
            </div>
            <button type="button" class="admin-cta" wire:click="startCreate">Nuevo producto</button>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="form-label">Buscar producto</label>
                <input type="text" class="form-input" placeholder="Ramo de rosas" wire:model.debounce.400ms="search">
            </div>
            <div>
                <label class="form-label">Filtro</label>
                <select class="form-input" wire:model="filter">
                    @foreach($filters as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Resumen rápido</label>
                <div class="flex flex-wrap gap-2">
                    <span class="admin-pill">Activos</span>
                    <span class="admin-pill admin-pill--warning">Stock bajo</span>
                    <span class="admin-pill">Destacados</span>
                </div>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="admin-table admin-table--products">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Etiquetas</th>
                        <th>Formatos</th>
                        <th>Estado</th>
                        <th>Stock</th>
                        <th class="text-center">Métricas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $stockClass = $product->low_stock ? 'admin-pill admin-pill--warning' : 'admin-pill';
                            $statusClass = $product->is_active ? 'admin-pill' : 'admin-pill admin-pill--danger';
                            $variantCount = $product->variants_count ?? $product->variants()->count();
                            $priceRange = $product->variant_price_range;
                            $hasPriceRange = $priceRange['min'] !== $priceRange['max'];
                        @endphp
                        <tr class="group">
                            <td>
                                <div class="admin-product">
                                    <div class="admin-product__thumb">
                                        <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}">
                                    </div>
                                    <div class="admin-product__info">
                                        <div class="admin-product__name">{{ $product->name }}</div>
                                        <div class="admin-product__meta">
                                            <span>{{ $product->category?->name ?? 'Sin categoría' }}</span>
                                            <span class="admin-dot"></span>
                                            <span>ID {{ $product->id }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($hasPriceRange)
                                    <div class="admin-price__main text-sm">${{ number_format($priceRange['min'], 0, ',', '.') }} — ${{ number_format($priceRange['max'], 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-ink/40 mt-0.5">Rango de formatos</div>
                                @else
                                    <div class="admin-price__main">{{ $product->formatted_price }}</div>
                                @endif
                                @if($product->formatted_compare_price)
                                    <div class="admin-price__compare">{{ $product->formatted_compare_price }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1 max-w-[200px]">
                                    @if($product->is_new)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $product->getBadgeLabel('new', 'Nuevo') }}</span>
                                    @endif
                                    @if($product->is_featured)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-200">{{ $product->getBadgeLabel('featured', '⭐ Destacado') }}</span>
                                    @endif
                                    @if($product->is_bestseller)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">{{ $product->getBadgeLabel('bestseller', 'Popular') }}</span>
                                    @endif
                                    @if($product->has_fresh_guarantee)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-green-50 text-green-700 border border-green-200">{{ $product->getBadgeLabel('benefit_fresh', 'Frescura') }}</span>
                                    @endif
                                    @if($product->has_free_delivery)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">{{ $product->getBadgeLabel('benefit_free_delivery', $product->free_delivery_city ? 'Envío gratis {city}' : 'Envío gratis', ['city' => $product->free_delivery_city]) }}</span>
                                    @endif
                                    @if($product->has_personalized_card)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200">{{ $product->getBadgeLabel('benefit_card', 'Tarjeta') }}</span>
                                    @endif
                                    @foreach(collect($product->custom_badges ?? [])->filter()->take(3) as $badge)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">{{ $badge }}</span>
                                    @endforeach
                                    @if($product->promo_active)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-red-50 text-red-700 border border-red-200">{{ $product->getBadgeLabel('promo', 'Promo', ['discount' => $product->calculated_discount ?? $product->discount_percentage]) }}</span>
                                    @endif
                                    @if(empty($product->custom_badges)
                                        && !$product->is_new
                                        && !$product->is_featured
                                        && !$product->is_bestseller
                                        && !$product->promo_active
                                        && !$product->has_fresh_guarantee
                                        && !$product->has_free_delivery
                                        && !$product->has_personalized_card)
                                        <span class="text-[10px] text-ink/30 italic">Sin etiquetas</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($variantCount > 0)
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary/10 text-primary text-[10px] font-bold">{{ $variantCount }}</span>
                                        <span class="text-xs text-ink/60">{{ $variantCount === 1 ? 'formato' : 'formatos' }}</span>
                                    </div>
                                @else
                                    <span class="text-[10px] text-ink/30 italic">Precio único</span>
                                @endif
                            </td>
                            <td>
                                <div class="admin-status">
                                    <span class="{{ $statusClass }}">{{ $product->is_active ? 'Activo' : 'Inactivo' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($product->track_stock)
                                    <span class="{{ $stockClass }}">{{ $product->available_stock }}</span>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="admin-pill admin-pill--neutral">Sin control</span>
                                        <button type="button" class="text-xs text-primary hover:underline" wire:click="toggleStockControl({{ $product->id }})">
                                            Activar
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex flex-col items-center gap-0.5">
                                    <div class="flex items-center gap-1 text-xs text-ink/60" title="Vistas">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6z"/><circle cx="12" cy="12" r="3"/></svg>
                                        {{ number_format($product->views_count) }}
                                    </div>
                                    <div class="flex items-center gap-1 text-xs text-ink/60" title="Ventas">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        {{ number_format($product->sales_count) }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="admin-actions">
                                    <button
                                        type="button"
                                        class="admin-action"
                                        wire:click="startEdit({{ $product->id }})"
                                        title="Editar"
                                        aria-label="Editar"
                                    >
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="admin-action {{ $product->is_active ? 'admin-action--active' : 'admin-action--inactive' }}"
                                        wire:click="toggleActive({{ $product->id }})"
                                        title="{{ $product->is_active ? 'Desactivar' : 'Activar' }}"
                                        aria-label="{{ $product->is_active ? 'Desactivar' : 'Activar' }}"
                                        aria-pressed="{{ $product->is_active ? 'true' : 'false' }}"
                                    >
                                        @if($product->is_active)
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-5.5 0-9-6-9-6a20.77 20.77 0 0 1 5.06-5.94"/>
                                                <path d="M1 1l22 22"/>
                                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                                <path d="M14.12 14.12 9.88 9.88"/>
                                                <path d="M10.59 5.59A10.94 10.94 0 0 1 12 4c5.5 0 9 6 9 6a20.77 20.77 0 0 1-3.17 4.25"/>
                                            </svg>
                                        @endif
                                    </button>
                                    <button
                                        type="button"
                                        class="admin-action admin-action--danger"
                                        wire:click="confirmDelete({{ $product->id }})"
                                        title="Eliminar"
                                        aria-label="Eliminar"
                                    >
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"/>
                                            <path d="M8 6V4h8v2"/>
                                            <path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6"/>
                                            <path d="M14 11v6"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-sm text-ink/60">No hay productos para los filtros actuales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </section>

    {{-- Modal Crear / Editar --}}
    <div
        x-data="{
            open: @entangle('showForm'),
            tab: 'basics',
            tabs: ['basics','pricing','variants','stock','media','seo','extras'],
            labels: {
                basics: 'Básico',
                pricing: 'Precio',
                variants: 'Formatos',
                stock: 'Stock',
                media: 'Imágenes',
                seo: 'SEO',
                extras: 'Extras'
            },
            next() {
                const index = this.tabs.indexOf(this.tab);
                if (index < this.tabs.length - 1) this.tab = this.tabs[index + 1];
            },
            prev() {
                const index = this.tabs.indexOf(this.tab);
                if (index > 0) this.tab = this.tabs[index - 1];
            }
        }"
        x-effect="if (open) { tab = 'basics' } document.body.style.overflow = open ? 'hidden' : ''"
        x-cloak
    >
        <div x-show="open" x-on:keydown.escape.window="$wire.cancelForm()" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$wire.cancelForm()"></div>
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="admin-product-modal"
                @click.stop
            >
                <div class="admin-product-modal__header">
                    <div>
                        <p class="admin-product-modal__subtitle">Producto</p>
                        <h2 class="admin-product-modal__title font-display">{{ $editingProductId ? 'Editar producto' : 'Nuevo producto' }}</h2>
                    </div>
                    <div class="admin-product-modal__actions">
                        <label class="admin-switch">
                            <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="is_active">
                            Visible
                        </label>
                        @if($editingProductId)
                            <button type="button" class="admin-link text-sm text-red-600" wire:click="confirmDelete({{ $editingProductId }})">Eliminar</button>
                        @endif
                        <button type="button" class="admin-ghost-button" wire:click="cancelForm">Cerrar</button>
                    </div>
                </div>

                <div class="admin-product-modal__tabs">
                    <div class="admin-tabbar" role="tablist">
                        <template x-for="key in tabs" :key="key">
                            <button
                                type="button"
                                class="admin-tab"
                                :class="tab === key ? 'is-active' : ''"
                                @click="tab = key"
                                x-text="labels[key]"
                                role="tab"
                                :aria-selected="tab === key"
                            ></button>
                        </template>
                    </div>
                </div>

                <form wire:submit.prevent="save" class="flex-1 flex flex-col min-h-0">
                    <div class="admin-product-modal__body space-y-6">
                        {{-- Básico --}}
                        <div x-show="tab === 'basics'" x-transition>
                            <div class="admin-form-card space-y-4">
                                <div>
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-input" wire:model.defer="name" placeholder="Ramo premium">
                                    @error('name') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="form-label">Slug (opcional)</label>
                                    <input type="text" class="form-input" wire:model.defer="slug" placeholder="ramo-premium">
                                    <p class="text-xs text-ink/50 mt-2">Se genera automáticamente si lo dejas vacío.</p>
                                    @error('slug') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="form-label">Categoría</label>
                                    <select class="form-input" wire:model.defer="category_id">
                                        <option value="">Sin categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="form-label">Resumen corto</label>
                                    <input type="text" class="form-input" wire:model.defer="short_description" placeholder="Entrega express, flores premium...">
                                    @error('short_description') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="form-label">Descripción completa</label>
                                    <textarea class="form-input min-h-[140px]" wire:model.defer="description" placeholder="Descripción detallada del producto..."></textarea>
                                    @error('description') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Precio --}}
                        <div x-show="tab === 'pricing'" x-transition>
                            <div class="admin-form-card space-y-4">
                                <div class="grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="form-label">Precio</label>
                                        <input type="text" class="form-input" wire:model.defer="price" inputmode="numeric" placeholder="34990">
                                        @error('price') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="form-label">Precio oferta</label>
                                        <input type="text" class="form-input" wire:model.defer="compare_price" inputmode="numeric" placeholder="39990">
                                        <p class="text-xs text-ink/50 mt-2">El porcentaje se calcula al guardar.</p>
                                        @error('compare_price') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="form-label">Inicio oferta</label>
                                        <input type="datetime-local" class="form-input" wire:model.defer="promo_starts_at">
                                        @error('promo_starts_at') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="form-label">Fin oferta</label>
                                        <input type="datetime-local" class="form-input" wire:model.defer="promo_ends_at">
                                        @error('promo_ends_at') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Formatos --}}
                        <div x-show="tab === 'variants'" x-transition>
                            <div class="admin-form-card space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="admin-section-title">Formatos del producto</p>
                                        <p class="text-xs text-ink/50 mt-1">Define tamaños, presentaciones o estilos con precios diferenciados.</p>
                                    </div>
                                    <button type="button" class="admin-ghost-button" wire:click="addVariantRow">
                                        <svg class="w-4 h-4 mr-1 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
                                        Agregar formato
                                    </button>
                                </div>

                                @if(empty($variants))
                                    <div class="rounded-xl border-2 border-dashed border-ink/10 p-6 text-center">
                                        <svg class="w-8 h-8 text-ink/20 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        <p class="text-sm text-ink/50 font-medium">Sin formatos configurados</p>
                                        <p class="text-xs text-ink/40 mt-1">Se usará únicamente el precio base del producto.</p>
                                    </div>
                                @endif

                                <div class="space-y-3" x-data="{ expandedVariant: null }">
                                    @foreach($variants as $index => $variant)
                                        @php
                                            $vName = trim($variant['name'] ?? '');
                                            $vLabel = trim($variant['label'] ?? '');
                                            $vType = ($variant['type'] ?? 'fixed') === 'range' ? 'range' : 'fixed';
                                            $vActive = $variant['is_active'] ?? true;
                                            $vPriceOverride = $variant['price_override'] ?? null;
                                            $vPriceModifier = $variant['price_modifier'] ?? 0;
                                            $displayTitle = $vLabel ?: $vName ?: 'Formato #' . ($index + 1);
                                            $displayPrice = $vPriceOverride !== null && $vPriceOverride !== ''
                                                ? '$' . number_format((int) $vPriceOverride, 0, ',', '.')
                                                : (((int) $vPriceModifier) !== 0
                                                    ? ((int) $vPriceModifier > 0 ? '+$' : '-$') . number_format(abs((int) $vPriceModifier), 0, ',', '.')
                                                    : 'Precio base');
                                        @endphp
                                        <div
                                            class="rounded-xl border transition-colors {{ $vActive ? 'border-[var(--admin-border)]' : 'border-red-200 bg-red-50/30' }}"
                                        >
                                            {{-- Cabecera colapsable --}}
                                            <div
                                                class="flex items-center justify-between p-3 cursor-pointer select-none hover:bg-ink/[0.02] rounded-t-xl transition-colors"
                                                @click="expandedVariant = expandedVariant === {{ $index }} ? null : {{ $index }}"
                                            >
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="flex items-center justify-center w-7 h-7 rounded-lg bg-primary/10 text-primary text-xs font-bold flex-shrink-0">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-sm font-medium text-ink truncate">{{ $displayTitle }}</span>
                                                            @if(!$vActive)
                                                                <span class="px-1.5 py-0.5 rounded text-[10px] bg-red-100 text-red-600 font-medium">Inactivo</span>
                                                            @endif
                                                            <span class="px-1.5 py-0.5 rounded text-[10px] {{ $vType === 'range' ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700' }} font-medium">
                                                                {{ $vType === 'range' ? 'Personalizable' : 'Precio fijo' }}
                                                            </span>
                                                        </div>
                                                        <div class="text-xs text-ink/50 mt-0.5">{{ $displayPrice }}</div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 flex-shrink-0">
                                                    <button
                                                        type="button"
                                                        class="text-xs text-red-500 hover:text-red-700 px-2 py-1 rounded hover:bg-red-50 transition-colors"
                                                        wire:click.stop="removeVariantRow({{ $index }})"
                                                    >
                                                        Quitar
                                                    </button>
                                                    <svg
                                                        class="w-4 h-4 text-ink/30 transition-transform duration-200"
                                                        :class="expandedVariant === {{ $index }} ? 'rotate-180' : ''"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    ><path d="M6 9l6 6 6-6"/></svg>
                                                </div>
                                            </div>

                                            {{-- Contenido expandible --}}
                                            <div
                                                x-show="expandedVariant === {{ $index }}"
                                                x-collapse
                                                class="px-3 pb-4 space-y-4"
                                            >
                                                <div class="pt-3 border-t border-[var(--admin-border)]">
                                                    {{-- Identidad del formato --}}
                                                    <p class="text-xs font-semibold text-ink/70 uppercase tracking-wider mb-3">Identidad</p>
                                                    <div class="grid gap-3 md:grid-cols-2">
                                                        <div>
                                                            <label class="form-label flex items-center gap-1">
                                                                Nombre interno
                                                                <span class="text-ink/30 text-[10px]" title="Identificador para administración. No se muestra al cliente.">ⓘ</span>
                                                            </label>
                                                            <input type="text" class="form-input" wire:model.defer="variants.{{ $index }}.name" placeholder="Ej: S, M, L, XL">
                                                            <p class="text-[10px] text-ink/40 mt-1">Solo visible para ti en el panel.</p>
                                                        </div>
                                                        <div>
                                                            <label class="form-label flex items-center gap-1">
                                                                Nombre visible al cliente
                                                                <span class="text-ink/30 text-[10px]" title="Lo que el cliente ve en la tienda.">ⓘ</span>
                                                            </label>
                                                            <input type="text" class="form-input" wire:model.defer="variants.{{ $index }}.label" placeholder="Ej: Pequeño (6 tallos)">
                                                            <p class="text-[10px] text-ink/40 mt-1">Si lo dejas vacío, se usa el nombre interno.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    {{-- Tipo y Estado --}}
                                                    <p class="text-xs font-semibold text-ink/70 uppercase tracking-wider mb-3">Configuración</p>
                                                    <div class="grid gap-3 md:grid-cols-2">
                                                        <div>
                                                            <label class="form-label flex items-center gap-1">
                                                                Tipo de formato
                                                                <span class="text-ink/30 text-[10px]" title="Fijo: un precio definido. Personalizable: el cliente elige una cantidad dentro de un rango.">ⓘ</span>
                                                            </label>
                                                            <select class="form-input" wire:model.live="variants.{{ $index }}.type">
                                                                <option value="fixed">💰 Precio fijo — El precio se define directamente</option>
                                                                <option value="range">🎚️ Personalizable — El cliente elige cantidad</option>
                                                            </select>
                                                        </div>
                                                        <div class="flex items-end pb-1">
                                                            <label class="flex items-center gap-2 text-sm text-ink/70 cursor-pointer">
                                                                <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="variants.{{ $index }}.is_active">
                                                                <span>Activo y visible en la tienda</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    {{-- Precio --}}
                                                    <p class="text-xs font-semibold text-ink/70 uppercase tracking-wider mb-3">Precio</p>
                                                    @if(($variant['type'] ?? 'fixed') === 'fixed')
                                                        <div class="grid gap-3 md:grid-cols-2">
                                                            <div>
                                                                <label class="form-label flex items-center gap-1">
                                                                    Ajuste sobre precio base (CLP)
                                                                    <span class="text-ink/30 text-[10px]" title="Se suma (o resta si es negativo) al precio base del producto. Ej: +5000 = precio base + $5.000">ⓘ</span>
                                                                </label>
                                                                <input type="number" class="form-input" wire:model.defer="variants.{{ $index }}.price_modifier" placeholder="0">
                                                                <p class="text-[10px] text-ink/40 mt-1">Ej: 5000 para sumar $5.000 al precio base.</p>
                                                            </div>
                                                            <div>
                                                                <label class="form-label flex items-center gap-1">
                                                                    Precio fijo (reemplaza base)
                                                                    <span class="text-ink/30 text-[10px]" title="Si lo defines, ignora el precio base y el ajuste. El producto costará exactamente este valor.">ⓘ</span>
                                                                </label>
                                                                <input type="number" class="form-input" wire:model.defer="variants.{{ $index }}.price_override" placeholder="Dejar vacío para usar base + ajuste">
                                                                <p class="text-[10px] text-ink/40 mt-1">Opcional. Si se define, este será el precio final.</p>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="rounded-lg bg-violet-50/50 border border-violet-100 p-3 mb-3">
                                                            <p class="text-xs text-violet-700">
                                                                <strong>Formato personalizable:</strong> El cliente elige una cantidad (ej: número de tallos). El precio se calcula como:
                                                                <br><code class="bg-violet-100 px-1 rounded text-[11px]">Precio base + (cantidad elegida − mínimo) × precio por unidad</code>
                                                            </p>
                                                        </div>
                                                        <div class="grid gap-3 md:grid-cols-2 mb-3">
                                                            <div>
                                                                <label class="form-label">Precio base del formato (opcional)</label>
                                                                <input type="number" class="form-input" wire:model.defer="variants.{{ $index }}.price_override" placeholder="Vacío = precio base del producto">
                                                                <p class="text-[10px] text-ink/40 mt-1">Si lo dejas vacío, usa el precio del producto.</p>
                                                            </div>
                                                            <div>
                                                                <label class="form-label flex items-center gap-1">
                                                                    Precio por cada unidad adicional
                                                                    <span class="text-ink/30 text-[10px]" title="Cuánto se cobra por cada unidad sobre el mínimo.">ⓘ</span>
                                                                </label>
                                                                <input type="number" class="form-input" wire:model.defer="variants.{{ $index }}.price_per_unit" placeholder="1500">
                                                                <p class="text-[10px] text-ink/40 mt-1">Ej: $1.500 extra por cada tallo adicional.</p>
                                                            </div>
                                                        </div>
                                                        <div class="grid gap-3 md:grid-cols-4">
                                                            <div>
                                                                <label class="form-label">Cantidad mínima</label>
                                                                <input type="number" class="form-input" wire:model.defer="variants.{{ $index }}.min_value" placeholder="1" min="1">
                                                                <p class="text-[10px] text-ink/40 mt-1">El valor más bajo que puede elegir.</p>
                                                            </div>
                                                            <div>
                                                                <label class="form-label">Cantidad máxima</label>
                                                                <input type="number" class="form-input" wire:model.defer="variants.{{ $index }}.max_value" placeholder="10">
                                                                <p class="text-[10px] text-ink/40 mt-1">El valor más alto permitido.</p>
                                                            </div>
                                                            <div>
                                                                <label class="form-label flex items-center gap-1">
                                                                    Incremento
                                                                    <span class="text-ink/30 text-[10px]" title="De cuánto en cuánto puede cambiar el valor. Ej: si es 1, puede elegir 1, 2, 3... Si es 5: 5, 10, 15...">ⓘ</span>
                                                                </label>
                                                                <input type="number" class="form-input" wire:model.defer="variants.{{ $index }}.step_value" placeholder="1" min="1">
                                                                <p class="text-[10px] text-ink/40 mt-1">De cuánto en cuánto cambia.</p>
                                                            </div>
                                                            <div>
                                                                <label class="form-label">Unidad de medida</label>
                                                                <input type="text" class="form-input" wire:model.defer="variants.{{ $index }}.unit_label" placeholder="tallos, rosas, varas">
                                                                <p class="text-[10px] text-ink/40 mt-1">Se muestra al cliente.</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Orden --}}
                                                <div class="flex items-center gap-3 pt-2 border-t border-[var(--admin-border)]">
                                                    <label class="form-label text-ink/50 mb-0">Posición de orden:</label>
                                                    <input type="number" class="form-input w-20" wire:model.defer="variants.{{ $index }}.sort_order" placeholder="{{ $index + 1 }}" min="1">
                                                    <span class="text-[10px] text-ink/40">Menor número = aparece primero.</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Stock --}}
                        <div x-show="tab === 'stock'" x-transition>
                            <div class="admin-form-card space-y-4">
                                <label class="flex items-center gap-2 text-sm text-ink/70">
                                    <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="track_stock">
                                    Controlar stock del producto
                                </label>
                                <div class="grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="form-label">Stock disponible</label>
                                        <input type="number" class="form-input" wire:model.defer="stock" min="0">
                                        @error('stock') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="form-label">Alerta stock bajo</label>
                                        <input type="number" class="form-input" wire:model.defer="low_stock_threshold" min="0">
                                        @error('low_stock_threshold') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Imágenes --}}
                        <div x-show="tab === 'media'" x-transition>
                            <div class="admin-form-card space-y-4">
                                <div>
                                    <label class="form-label">Imagen principal (subir)</label>
                                    <input type="file" class="form-input" wire:model="mainImageUpload" accept="image/*">
                                    <p class="text-xs text-ink/50 mt-2">JPG, PNG o WebP. Máximo 5MB. Si no subes otra, se mantiene la actual.</p>
                                    @error('mainImageUpload') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                    <div class="mt-3">
                                        @if($mainImageUpload)
                                            <div class="relative">
                                                <img src="{{ $mainImageUpload->temporaryUrl() }}" class="h-32 w-full rounded-xl object-cover border border-[var(--admin-border)]" alt="Vista previa">
                                                <button
                                                    type="button"
                                                    class="absolute top-2 right-2 rounded-full bg-white/90 text-xs px-2 py-1 shadow-sm"
                                                    wire:click="clearMainImageUpload"
                                                >
                                                    Quitar
                                                </button>
                                            </div>
                                        @elseif($main_image)
                                            @php
                                                $mainImageUrl = str_starts_with($main_image, 'http')
                                                    ? $main_image
                                                    : (str_starts_with($main_image, 'storage/')
                                                        ? asset($main_image)
                                                        : asset('storage/' . $main_image));
                                            @endphp
                                            <img
                                                src="{{ $mainImageUrl }}"
                                                class="h-32 w-full rounded-xl object-cover border border-[var(--admin-border)]"
                                                alt="Imagen principal"
                                            >
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">Galería (subir)</label>
                                    <input type="file" class="form-input" wire:model="galleryUploads" accept="image/*" multiple>
                                    <p class="text-xs text-ink/50 mt-2">Puedes subir varias imágenes. Máximo 5MB cada una.</p>
                                    @error('galleryUploads.*') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                    @if(!empty($galleryUploads))
                                        <div class="mt-3 grid grid-cols-3 gap-2">
                                            @foreach($galleryUploads as $index => $upload)
                                                <div class="relative">
                                                    <img src="{{ $upload->temporaryUrl() }}" class="h-24 w-full rounded-lg object-cover border border-[var(--admin-border)]" alt="Vista previa">
                                                    <button
                                                        type="button"
                                                        class="absolute top-2 right-2 rounded-full bg-white/90 text-xs px-2 py-1 shadow-sm"
                                                        wire:click="removeGalleryUpload({{ $index }})"
                                                    >
                                                        Quitar
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <label class="form-label">Galería actual</label>
                                    <p class="text-xs text-ink/50 mt-2">Imágenes cargadas en la galería. Puedes quitarlas una a una.</p>
                                    @error('gallery_images_input') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                    @php
                                        $galleryItems = array_values(array_filter(array_map('trim', explode(',', $gallery_images_input ?? ''))));
                                    @endphp
                                    @if(!empty($galleryItems))
                                        <div class="mt-3 grid grid-cols-3 gap-2">
                                            @foreach($galleryItems as $index => $image)
                                                @php
                                                    $galleryUrl = str_starts_with($image, 'http')
                                                        ? $image
                                                        : (str_starts_with($image, 'storage/')
                                                            ? asset($image)
                                                            : asset('storage/' . $image));
                                                @endphp
                                                <div class="relative">
                                                    <img
                                                        src="{{ $galleryUrl }}"
                                                        class="h-24 w-full rounded-lg object-cover border border-[var(--admin-border)]"
                                                        alt="Galería"
                                                    >
                                                    <button
                                                        type="button"
                                                        class="absolute top-2 right-2 rounded-full bg-white/90 text-xs px-2 py-1 shadow-sm"
                                                        wire:click="removeGalleryImage({{ $index }})"
                                                    >
                                                        Quitar
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- SEO --}}
                        <div x-show="tab === 'seo'" x-transition>
                            <div class="admin-form-card space-y-4">
                                <div>
                                    <label class="form-label">Meta título</label>
                                    <input type="text" class="form-input" wire:model.defer="meta_title" placeholder="Ramo premium | Flores D&D">
                                    @error('meta_title') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="form-label">Meta descripción</label>
                                    <textarea class="form-input min-h-[100px]" wire:model.defer="meta_description" placeholder="Descripción breve para buscadores"></textarea>
                                    @error('meta_description') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Extras --}}
                        <div x-show="tab === 'extras'" x-transition>
                            <div class="space-y-4">
                                <div class="admin-form-card space-y-3">
                                    <p class="admin-section-title">Estado y etiquetas</p>
                                    <div class="grid gap-2">
                                        <label class="flex items-center gap-2 text-sm text-ink/70">
                                            <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="is_active">
                                            Visible en tienda
                                        </label>
                                        <label class="flex items-center gap-2 text-sm text-ink/70">
                                            <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="is_featured">
                                            Destacado
                                        </label>
                                        <label class="flex items-center gap-2 text-sm text-ink/70">
                                            <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="is_new">
                                            Nuevo lanzamiento
                                        </label>
                                    </div>
                                    <div class="rounded-xl border border-[var(--admin-border)] bg-white/70 p-4 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-ink/60">Etiquetas automáticas</p>
                                            <span class="text-[10px] text-ink/40">Vacío = automático</span>
                                        </div>
                                        <p class="text-xs text-ink/50">Se generan por promo, stock o estado. Puedes sobrescribir el texto por producto. Tokens: {discount}, {count}.</p>
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <label class="form-label">Promo / descuento</label>
                                                <input type="text" class="form-input" wire:model.defer="badge_overrides.promo" placeholder="-{discount}% / Oferta">
                                            </div>
                                            <div>
                                                <label class="form-label">Nuevo</label>
                                                <input type="text" class="form-input" wire:model.defer="badge_overrides.new" placeholder="Nuevo">
                                            </div>
                                            <div>
                                                <label class="form-label">Destacado</label>
                                                <input type="text" class="form-input" wire:model.defer="badge_overrides.featured" placeholder="Destacado">
                                            </div>
                                            <div>
                                                <label class="form-label">Popular</label>
                                                <input type="text" class="form-input" wire:model.defer="badge_overrides.bestseller" placeholder="Popular">
                                            </div>
                                            <div>
                                                <label class="form-label">Stock bajo</label>
                                                <input type="text" class="form-input" wire:model.defer="badge_overrides.low_stock" placeholder="¡Quedan pocas!">
                                            </div>
                                            <div>
                                                <label class="form-label">Stock exacto</label>
                                                <input type="text" class="form-input" wire:model.defer="badge_overrides.stock_left" placeholder="¡Solo quedan {count}!">
                                            </div>
                                            <div>
                                                <label class="form-label">Agotado</label>
                                                <input type="text" class="form-input" wire:model.defer="badge_overrides.sold_out" placeholder="Agotado">
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label">Etiquetas personalizadas</label>
                                        <input type="text" class="form-input" wire:model.defer="custom_badges_input" placeholder="Premium, Edición limitada">
                                        <p class="text-xs text-ink/50 mt-2">Separar con comas.</p>
                                        @error('custom_badges_input') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="admin-form-card space-y-3">
                                    <p class="admin-section-title">Logística y valor agregado</p>
                                    <div class="grid gap-2">
                                        <label class="flex items-center gap-2 text-sm text-ink/70">
                                            <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="has_fresh_guarantee">
                                            Garantía de frescura
                                        </label>
                                        <label class="flex items-center gap-2 text-sm text-ink/70">
                                            <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="has_personalized_card">
                                            Incluye tarjeta personalizada
                                        </label>
                                        <label class="flex items-center gap-2 text-sm text-ink/70">
                                            <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="has_free_delivery">
                                            Envío gratis
                                        </label>
                                    </div>
                                    <div class="rounded-xl border border-[var(--admin-border)] bg-white/70 p-4 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-ink/60">Etiquetas de beneficios</p>
                                            <span class="text-[10px] text-ink/40">Vacío = automático</span>
                                        </div>
                                        <p class="text-xs text-ink/50">Se muestran según las opciones marcadas. Token disponible: {city}.</p>
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <label class="form-label">Frescura</label>
                                                <input type="text" class="form-input" wire:model.defer="badge_overrides.benefit_fresh" placeholder="Frescura garantizada">
                                            </div>
                                            <div>
                                                <label class="form-label">Envío gratis</label>
                                                <input type="text" class="form-input" wire:model.defer="badge_overrides.benefit_free_delivery" placeholder="Envío gratis {city}">
                                            </div>
                                            <div>
                                                <label class="form-label">Tarjeta incluida</label>
                                                <input type="text" class="form-input" wire:model.defer="badge_overrides.benefit_card" placeholder="Tarjeta incluida">
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label">Ciudad de envío gratis (opcional)</label>
                                        <input type="text" class="form-input" wire:model.defer="free_delivery_city" placeholder="Valdivia">
                                        @error('free_delivery_city') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="admin-form-card space-y-3">
                                    <p class="admin-section-title">Cupones aplicables</p>
                                    @if($coupons->isEmpty())
                                        <p class="text-sm text-ink/60">No hay cupones activos en la base de datos.</p>
                                    @else
                                        <div class="space-y-2">
                                            @foreach($coupons as $coupon)
                                                <label class="flex items-center gap-2 text-sm text-ink/70">
                                                    <input type="checkbox" class="h-4 w-4 rounded border-ink/20 text-emerald-600" wire:model.defer="selectedCoupons" value="{{ $coupon->id }}">
                                                    <span class="font-medium">{{ $coupon->code }}</span>
                                                    <span class="text-xs text-ink/50">{{ $coupon->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="admin-product-modal__footer">
                        <button type="button" class="admin-ghost-button" @click="prev()" :disabled="tab === tabs[0]">Anterior</button>
                        <div class="flex items-center gap-3">
                            <button type="button" class="admin-ghost-button" wire:click="cancelForm">Cancelar</button>
                            <button type="submit" class="admin-cta">Guardar cambios</button>
                        </div>
                        <button type="button" class="admin-ghost-button" @click="next()" :disabled="tab === tabs[tabs.length - 1]">Siguiente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Confirmar eliminación --}}
    <div x-data="{ open: @entangle('confirmingDeleteId') }" x-cloak>
        <div x-show="open" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$wire.cancelDelete()"></div>
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden"
                @click.stop
            >
                <div class="p-6 space-y-3">
                    <h3 class="font-display text-xl">Eliminar producto</h3>
                    <p class="text-sm text-ink/70">
                        ¿Seguro que deseas eliminar <span class="font-semibold">{{ $confirmingDeleteName }}</span>? Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="px-6 py-4 border-t border-[var(--admin-border)] flex justify-end gap-3">
                    <button type="button" class="admin-ghost-button" wire:click="cancelDelete">Cancelar</button>
                    <button type="button" class="admin-cta bg-red-600 hover:bg-red-700" wire:click="performDelete">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</div>
