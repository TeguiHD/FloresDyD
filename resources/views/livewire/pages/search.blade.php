{{-- Búsqueda - Flores D&D --}}
<div class="min-h-dvh">
    {{-- Barra de búsqueda --}}
    <section class="bg-secondary/30 py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                <h1 class="font-serif text-3xl text-center text-primary mb-6">Buscar</h1>
                <div class="relative">
                    <input 
                        type="text"
                        wire:model.live.debounce.300ms="q"
                        placeholder="¿Qué estás buscando?"
                        class="w-full px-6 py-4 pl-14 border-2 border-secondary rounded-full text-lg focus:border-primary focus:ring-0"
                        autofocus
                    >
                    <x-flux::icon name="magnifying-glass" class="w-6 h-6 text-dark/40 absolute left-5 top-1/2 -translate-y-1/2" />
                </div>
            </div>
        </div>
    </section>

    {{-- Resultados --}}
    <section class="py-12">
        <div class="container mx-auto px-4">
            @if(!$hasProducts)
                <div class="text-center py-12">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-primary/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h2l2.5 11h9.5l2-7H7.5"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-dark mb-2">Aún no hay productos disponibles</h3>
                    <p class="text-dark/60">Pronto tendremos la colección lista para ti.</p>
                </div>
            @elseif(strlen($q) < 2)
                <div class="text-center py-12">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-primary/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <p class="text-dark/60">Escribe al menos 2 caracteres para buscar</p>
                </div>
            @elseif($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->count() > 0)
                <p class="text-dark/60 mb-6">{{ $products->total() }} resultados para "{{ $q }}"</p>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                    @foreach($products as $product)
                        <livewire:components.product-card :product="$product" :key="$product->id" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-primary/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3a9 9 0 100 18 9 9 0 000-18z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M16 10h.01M9 15c.8-.7 2-1 3-1s2.2.3 3 1"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-dark mb-2">No encontramos resultados</h3>
                    <p class="text-dark/60 mb-6">Intenta con otros términos de búsqueda</p>
                    <a 
                        href="{{ route('coleccion') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition"
                    >
                        Ver toda la colección
                    </a>
                </div>

                @if(!empty($suggestedCategories) && $suggestedCategories->count() > 0)
                    <div class="mt-12">
                        <h4 class="font-display text-2xl text-dark mb-4">Categorías relacionadas</h4>
                        <div class="flex flex-wrap gap-3">
                            @foreach($suggestedCategories as $category)
                                <a
                                    href="{{ route('coleccion.categoria', $category->slug) }}"
                                    class="px-4 py-2 rounded-full bg-secondary/50 text-dark hover:bg-secondary transition"
                                >
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($suggestedProducts) && $suggestedProducts->count() > 0)
                    <div class="mt-10">
                        <h4 class="font-display text-2xl text-dark mb-4">Productos similares</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                            @foreach($suggestedProducts as $product)
                                <livewire:components.product-card :product="$product" :key="'suggested-'.$product->id" />
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </section>
</div>
