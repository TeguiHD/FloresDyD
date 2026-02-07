{{-- Colección de Flores - Flores D&D --}}
<div>
    <div class="min-h-dvh">
    {{-- Hero Section --}}
    <section class="bg-gradient-to-b from-secondary/30 to-white py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="font-serif text-4xl lg:text-5xl text-primary mb-4">Nuestra Colección</h1>
                <p class="text-dark/70 text-lg">Descubre arreglos florales únicos, creados con amor y las flores más frescas</p>
            </div>
        </div>
    </section>

    {{-- Filtros y Productos --}}
    <section class="py-8 lg:py-12">
        <div class="container mx-auto px-4">
            <div class="lg:flex lg:gap-8">
                {{-- Sidebar Filtros (Desktop) --}}
                <aside class="hidden lg:block w-64 flex-shrink-0">
                    <div class="sticky top-24 space-y-6">
                        {{-- Búsqueda --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Buscar</label>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search"
                                placeholder="Buscar flores..."
                                class="w-full px-4 py-2 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                        </div>

                        {{-- Categorías --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Categoría</label>
                            @php
                                $selectedCategoryId = $selectedCategory?->id;
                            @endphp
                            <div class="space-y-3">
                                <button 
                                    wire:click="$set('category', null)"
                                    class="block w-full text-left px-3 py-2 rounded-lg transition {{ !$category ? 'bg-primary text-white' : 'hover:bg-secondary/50' }}"
                                >
                                    Todas las categorías
                                </button>
                                @foreach($categories as $parent)
                                    @php
                                        $childIds = $parent->children->pluck('id');
                                        $isParentActive = $selectedCategoryId === $parent->id;
                                        $isChildActive = $childIds->contains($selectedCategoryId);
                                        $isOpen = $isParentActive || $isChildActive;
                                    @endphp
                                    <details class="group border border-secondary/60 rounded-xl bg-white/70 overflow-hidden" {{ $isOpen ? 'open' : '' }}>
                                        <summary class="flex items-center justify-between px-3 py-2 cursor-pointer text-sm font-medium text-dark hover:bg-secondary/40">
                                            <span>{{ $parent->name }}</span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </summary>
                                        <div class="px-3 pb-3 pt-1 space-y-1">
                                            <button 
                                                wire:click="$set('category', {{ $parent->id }})"
                                                class="block w-full text-left text-sm px-2 py-1.5 rounded-md transition {{ $isParentActive ? 'bg-primary/10 text-primary' : 'text-dark/70 hover:bg-secondary/40' }}"
                                            >
                                                Ver todo en {{ $parent->name }}
                                            </button>
                                            @foreach($parent->children as $child)
                                                <button 
                                                    wire:click="$set('category', {{ $child->id }})"
                                                    class="block w-full text-left text-sm px-2 py-1.5 rounded-md transition {{ $selectedCategoryId === $child->id ? 'bg-primary text-white' : 'text-dark/70 hover:bg-secondary/40' }}"
                                                >
                                                    {{ $child->name }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        </div>

                        {{-- Rango de Precio --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Precio</label>
                            <div class="flex gap-2 items-center">
                                <input 
                                    type="number" 
                                    wire:model.live.debounce.500ms="minPrice"
                                    placeholder="Mín"
                                    class="w-full px-3 py-2 border border-secondary rounded-lg text-sm"
                                >
                                <span class="text-dark/50">-</span>
                                <input 
                                    type="number" 
                                    wire:model.live.debounce.500ms="maxPrice"
                                    placeholder="Máx"
                                    class="w-full px-3 py-2 border border-secondary rounded-lg text-sm"
                                >
                            </div>
                        </div>

                        {{-- Limpiar Filtros --}}
                        @if($search || $category || $minPrice || $maxPrice)
                            <button 
                                wire:click="clearFilters"
                                class="w-full py-2 text-primary hover:text-primary-dark underline text-sm"
                            >
                                Limpiar filtros
                            </button>
                        @endif
                    </div>
                </aside>

                {{-- Contenido Principal --}}
                <div class="flex-1">
                    {{-- Barra de herramientas --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <p class="text-dark/60 text-sm">
                            {{ $products->total() }} productos encontrados
                        </p>

                        <div class="flex items-center gap-4">
                            {{-- Ordenar --}}
                            <select 
                                wire:model.live="sort"
                                class="px-4 py-2 border border-secondary rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                            >
                                <option value="newest">Más recientes</option>
                                <option value="price_asc">Precio: Menor a Mayor</option>
                                <option value="price_desc">Precio: Mayor a Menor</option>
                                <option value="name">Nombre A-Z</option>
                                <option value="popular">Más populares</option>
                            </select>

                            {{-- Filtros móvil --}}
                            <button 
                                x-data
                                @click="$dispatch('open-filters')"
                                class="lg:hidden flex items-center gap-2 px-4 py-2 border border-secondary rounded-lg text-sm"
                            >
                                <x-flux::icon name="adjustments-horizontal" class="w-5 h-5" />
                                Filtros
                            </button>
                        </div>
                    </div>

                    {{-- Grid de Productos --}}
                    @if($products->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6">
                            @foreach($products as $product)
                                <livewire:components.product-card :product="$product" :key="$product->id" />
                            @endforeach
                        </div>

                        {{-- Paginación --}}
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="flex items-center justify-center mb-4">
                                <svg class="w-12 h-12 text-primary/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3l1.5 3L10 7l-3.5 1L5 12l-1.5-4L0 7l3.5-1L5 3zm9 4l1 2 2 1-2 1-1 2-1-2-2-1 2-1 1-2zm-2 7l2 4 4 2-4 2-2 4-2-4-4-2 4-2 2-4z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-medium text-dark mb-2">No encontramos productos</h3>
                            <p class="text-dark/60 mb-6">Intenta con otros filtros o términos de búsqueda</p>
                            <button 
                                wire:click="clearFilters"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition"
                            >
                                Ver toda la colección
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    </div>

    {{-- Modal de Filtros Móvil --}}
    <div 
        x-data="{ open: false }"
        @open-filters.window="open = true"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 lg:hidden"
    >
        <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-black/50"></div>
        
        <div 
            x-show="open" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute right-0 top-0 bottom-0 w-80 bg-white shadow-xl p-6 overflow-y-auto"
        >
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium">Filtros</h3>
                <button @click="open = false" class="p-2 hover:bg-secondary/50 rounded-lg">
                    <x-flux::icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- Contenido de filtros igual que sidebar --}}
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Buscar</label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar flores..."
                        class="w-full px-4 py-2 border border-secondary rounded-lg"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark mb-2">Categoría</label>
                    @php
                        $selectedCategoryId = $selectedCategory?->id;
                    @endphp
                    <div class="space-y-3">
                        <button 
                            wire:click="$set('category', null)"
                            @click="open = false"
                            class="block w-full text-left px-3 py-2 rounded-lg transition {{ !$category ? 'bg-primary text-white' : 'hover:bg-secondary/50' }}"
                        >
                            Todas las categorías
                        </button>
                        @foreach($categories as $parent)
                            @php
                                $childIds = $parent->children->pluck('id');
                                $isParentActive = $selectedCategoryId === $parent->id;
                                $isChildActive = $childIds->contains($selectedCategoryId);
                                $isOpen = $isParentActive || $isChildActive;
                            @endphp
                            <details class="group border border-secondary/60 rounded-xl bg-white overflow-hidden" {{ $isOpen ? 'open' : '' }}>
                                <summary class="flex items-center justify-between px-3 py-2 cursor-pointer text-sm font-medium text-dark hover:bg-secondary/40">
                                    <span>{{ $parent->name }}</span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </summary>
                                <div class="px-3 pb-3 pt-1 space-y-1">
                                    <button 
                                        wire:click="$set('category', {{ $parent->id }})"
                                        @click="open = false"
                                        class="block w-full text-left text-sm px-2 py-1.5 rounded-md transition {{ $isParentActive ? 'bg-primary/10 text-primary' : 'text-dark/70 hover:bg-secondary/40' }}"
                                    >
                                        Ver todo en {{ $parent->name }}
                                    </button>
                                    @foreach($parent->children as $child)
                                        <button 
                                            wire:click="$set('category', {{ $child->id }})"
                                            @click="open = false"
                                            class="block w-full text-left text-sm px-2 py-1.5 rounded-md transition {{ $selectedCategoryId === $child->id ? 'bg-primary text-white' : 'text-dark/70 hover:bg-secondary/40' }}"
                                        >
                                            {{ $child->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
