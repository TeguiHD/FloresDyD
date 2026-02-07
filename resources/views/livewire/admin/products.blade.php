<div class="admin-grid lg:grid-cols-3" x-data>
    <section class="admin-card lg:col-span-2">
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
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Stock</th>
                        <th>Vistas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $stockClass = $product->low_stock ? 'admin-pill admin-pill--warning' : 'admin-pill';
                            $statusClass = $product->is_active ? 'admin-pill' : 'admin-pill admin-pill--danger';
                        @endphp
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $product->name }}</div>
                                <div class="text-xs text-ink/50">{{ $product->category?->name ?? 'Sin categoría' }}</div>
                            </td>
                            <td>
                                <div>{{ $product->formatted_price }}</div>
                                @if($product->formatted_compare_price)
                                    <div class="text-xs text-ink/50 line-through">{{ $product->formatted_compare_price }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <span class="{{ $statusClass }}">{{ $product->is_active ? 'Activo' : 'Inactivo' }}</span>
                                    @if($product->is_featured)
                                        <span class="admin-pill">Destacado</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($product->track_stock)
                                    <span class="{{ $stockClass }}">{{ $product->available_stock }}</span>
                                @else
                                    <span class="text-xs text-ink/50">Sin control</span>
                                @endif
                            </td>
                            <td>{{ $product->views_count }}</td>
                            <td class="flex items-center gap-2">
                                <button type="button" class="admin-link" wire:click="startEdit({{ $product->id }})">Editar</button>
                                <button
                                    type="button"
                                    class="text-sm text-red-600"
                                    x-on:click.prevent="if(confirm('¿Eliminar este producto?')) { $wire.deleteProduct({{ $product->id }}) }"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-sm text-ink/60">No hay productos para los filtros actuales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </section>

    <aside class="admin-card">
        <div class="admin-card-header">
            <h2 class="font-display text-xl">{{ $editingProductId ? 'Editar producto' : 'Nuevo producto' }}</h2>
            <span class="admin-badge">{{ $editingProductId ? 'Edición' : 'Creación' }}</span>
        </div>

        @if(!$showForm)
            <p class="text-sm text-ink/60">Selecciona un producto para editar o crea uno nuevo desde el listado.</p>
        @else
            <form wire:submit.prevent="save" class="space-y-4">
                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Información base</p>
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
                </div>

                <div class="space-y-3 pt-4 border-t border-[var(--admin-border)]">
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Precio y oferta</p>
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
                </div>

                <div class="space-y-3 pt-4 border-t border-[var(--admin-border)]">
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Stock</p>
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

                <div class="space-y-3 pt-4 border-t border-[var(--admin-border)]">
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Estado y etiquetas</p>
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
                    <div>
                        <label class="form-label">Badges personalizados</label>
                        <input type="text" class="form-input" wire:model.defer="custom_badges_input" placeholder="Premium, Edición limitada">
                        <p class="text-xs text-ink/50 mt-2">Separar con comas.</p>
                        @error('custom_badges_input') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-[var(--admin-border)]">
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Descripción</p>
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

                <div class="space-y-3 pt-4 border-t border-[var(--admin-border)]">
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Logística y valor agregado</p>
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
                    <div>
                        <label class="form-label">Ciudad de envío gratis (opcional)</label>
                        <input type="text" class="form-input" wire:model.defer="free_delivery_city" placeholder="Valdivia">
                        @error('free_delivery_city') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-[var(--admin-border)]">
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Imágenes</p>
                    <div>
                        <label class="form-label">Imagen principal (subir)</label>
                        <input type="file" class="form-input" wire:model="mainImageUpload" accept="image/*">
                        <p class="text-xs text-ink/50 mt-2">JPG, PNG o WebP. Máximo 5MB.</p>
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
                        <label class="form-label">Imagen principal (URL externa)</label>
                        <input type="text" class="form-input" wire:model.defer="main_image" placeholder="https://...">
                        <p class="text-xs text-ink/50 mt-2">Si subes una imagen, esta URL se reemplaza.</p>
                        @error('main_image') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
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
                        <label class="form-label">Galería (URLs)</label>
                        <input type="text" class="form-input" wire:model.defer="gallery_images_input" placeholder="url1, url2, url3">
                        <p class="text-xs text-ink/50 mt-2">Separar con comas.</p>
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

                <div class="space-y-3 pt-4 border-t border-[var(--admin-border)]">
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">SEO</p>
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

                <div class="space-y-3 pt-4 border-t border-[var(--admin-border)]">
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Cupones aplicables</p>
                    @if($coupons->isEmpty())
                        <p class="text-sm text-ink/60">No hay cupones activos en la base de datos.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($coupons as $coupon)
                                @php
                                    $couponValue = $coupon->type === 'percentage'
                                        ? $coupon->value . '%'
                                        : '$' . number_format($coupon->value / 100, 0, ',', '.');
                                @endphp
                                <label class="flex items-start gap-2 text-sm text-ink/70">
                                    <input
                                        type="checkbox"
                                        class="mt-1 h-4 w-4 rounded border-ink/20 text-emerald-600"
                                        value="{{ $coupon->id }}"
                                        wire:model.defer="selectedCoupons"
                                    >
                                    <span>
                                        <span class="font-semibold">{{ $coupon->code }}</span>
                                        <span class="text-ink/50">({{ $couponValue }})</span>
                                        <span class="block text-xs text-ink/50">{{ $coupon->name }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="admin-cta">Guardar</button>
                    <button type="button" class="admin-ghost-button" wire:click="cancelForm">Cancelar</button>
                </div>
            </form>
        @endif
    </aside>
</div>
