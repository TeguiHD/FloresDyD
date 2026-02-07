{{-- Detalle de Producto - Flores D&D --}}
<div class="min-h-dvh">
    {{-- Breadcrumb --}}
    <nav class="bg-secondary/30 py-3">
        <div class="container mx-auto px-4">
            <div class="flex items-center gap-2 text-sm text-dark/60">
                <a href="{{ route('home') }}" class="hover:text-primary">Inicio</a>
                <span>/</span>
                <a href="{{ route('coleccion') }}" class="hover:text-primary">Colección</a>
                @if($product->category)
                    <span>/</span>
                    <a href="{{ route('coleccion.categoria', $product->category->slug) }}" class="hover:text-primary">
                        {{ $product->category->name }}
                    </a>
                @endif
                <span>/</span>
                <span class="text-primary">{{ $product->name }}</span>
            </div>
        </div>
    </nav>

    {{-- Producto Principal --}}
    <section class="py-8 lg:py-12">
        <div class="container mx-auto px-4">
            <div class="lg:flex lg:gap-12">
                {{-- Galería de Imágenes --}}
                <div class="lg:w-1/2 mb-8 lg:mb-0">
                    <div 
                        x-data="{ 
                            activeImage: '{{ $product->main_image }}',
                            images: {{ json_encode($product->images ?? [$product->main_image]) }}
                        }"
                        class="space-y-4"
                    >
                        {{-- Imagen Principal --}}
                        <div class="aspect-square bg-secondary/20 rounded-2xl overflow-hidden">
                            <img 
                                :src="activeImage"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover"
                            >
                        </div>

                        {{-- Thumbnails --}}
                        @if($product->images && count($product->images) > 1)
                            <div class="flex gap-3 overflow-x-auto pb-2">
                                @foreach($product->images as $image)
                                    <button 
                                        @click="activeImage = '{{ $image }}'"
                                        :class="{ 'ring-2 ring-primary': activeImage === '{{ $image }}' }"
                                        class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden"
                                    >
                                        <img src="{{ $image }}" alt="" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Información del Producto --}}
                <div class="lg:w-1/2">
                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        @if($product->is_featured)
                            <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full">
                                ⭐ Destacado
                            </span>
                        @endif
                        @if($product->stock <= 5 && $product->stock > 0)
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">
                                ¡Últimas {{ $product->stock }} unidades!
                            </span>
                        @endif
                    </div>

                    {{-- Nombre y Precio --}}
                    <h1 class="font-serif text-3xl lg:text-4xl text-dark mb-2">{{ $product->name }}</h1>
                    
                    <div class="flex items-baseline gap-3 mb-4">
                        <span class="text-3xl font-bold text-primary">${{ number_format($product->price, 0) }}</span>
                        @if($product->compare_price)
                            <span class="text-lg text-dark/40 line-through">${{ number_format($product->compare_price, 0) }}</span>
                            <span class="text-sm text-green-600 font-medium">
                                {{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}% OFF
                            </span>
                        @endif
                    </div>

                    {{-- Descripción corta --}}
                    @if($product->short_description)
                        <p class="text-dark/70 mb-6">{{ $product->short_description }}</p>
                    @endif

                    {{-- Opciones de Tamaño --}}
                    @if($product->sizes && count($product->sizes) > 0)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-dark mb-2">Tamaño</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->sizes as $size)
                                    <button 
                                        wire:click="$set('selectedSize', '{{ $size['name'] }}')"
                                        class="px-4 py-2 border rounded-lg transition {{ $selectedSize === $size['name'] ? 'border-primary bg-primary/5 text-primary' : 'border-secondary hover:border-primary/50' }}"
                                    >
                                        {{ $size['name'] }}
                                        @if(isset($size['price_modifier']) && $size['price_modifier'] > 0)
                                            <span class="text-xs text-dark/50">(+${{ $size['price_modifier'] }})</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Mensaje para Tarjeta --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-dark mb-2">
                            Mensaje para la tarjeta (opcional)
                        </label>
                        <textarea 
                            wire:model="cardMessage"
                            rows="3"
                            maxlength="200"
                            placeholder="Escribe un mensaje personalizado para incluir en tu arreglo..."
                            class="w-full px-4 py-3 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                        ></textarea>
                        <p class="text-xs text-dark/50 mt-1">{{ strlen($cardMessage ?? '') }}/200 caracteres</p>
                    </div>

                    {{-- Cantidad y Agregar al Carrito --}}
                    <div class="flex flex-wrap items-center gap-4 mb-6">
                        {{-- Selector de Cantidad --}}
                        <div class="flex items-center border border-secondary rounded-lg">
                            <button 
                                wire:click="decrementQuantity"
                                class="w-12 h-12 flex items-center justify-center hover:bg-secondary/50 transition"
                                :disabled="$quantity <= 1"
                            >
                                <x-flux::icon name="minus" class="w-5 h-5" />
                            </button>
                            <span class="w-12 text-center font-medium">{{ $quantity }}</span>
                            <button 
                                wire:click="incrementQuantity"
                                class="w-12 h-12 flex items-center justify-center hover:bg-secondary/50 transition"
                            >
                                <x-flux::icon name="plus" class="w-5 h-5" />
                            </button>
                        </div>

                        {{-- Botón Agregar --}}
                        <button 
                            wire:click="addToCart"
                            class="flex-1 flex items-center justify-center gap-2 px-8 py-4 bg-primary text-white rounded-full hover:bg-primary-dark transition font-medium"
                        >
                            <x-flux::icon name="shopping-bag" class="w-5 h-5" />
                            Agregar al carrito
                        </button>

                        {{-- Wishlist --}}
                        <button 
                            wire:click="addToWishlist"
                            class="w-12 h-12 flex items-center justify-center border border-secondary rounded-full hover:border-primary hover:text-primary transition"
                        >
                            <x-flux::icon name="heart" class="w-5 h-5" />
                        </button>
                    </div>

                    {{-- Info de Envío --}}
                    <div class="bg-secondary/30 rounded-xl p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <x-flux::icon name="truck" class="w-5 h-5 text-primary" />
                            <span class="text-sm">Envío gratis en compras mayores a $800</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-flux::icon name="clock" class="w-5 h-5 text-primary" />
                            <span class="text-sm">Entrega el mismo día (pedidos antes de las 2pm)</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-flux::icon name="shield-check" class="w-5 h-5 text-primary" />
                            <span class="text-sm">Garantía de frescura o te reponemos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Descripción Completa --}}
    @if($product->description)
        <section class="py-8 border-t border-secondary">
            <div class="container mx-auto px-4">
                <h2 class="font-serif text-2xl text-dark mb-4">Descripción</h2>
                <div class="prose prose-lg max-w-none text-dark/70">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        </section>
    @endif

    {{-- Reseñas --}}
    @if($reviews->count() > 0)
        <section class="py-8 border-t border-secondary">
            <div class="container mx-auto px-4">
                <h2 class="font-serif text-2xl text-dark mb-6">Reseñas de clientes</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($reviews as $review)
                        <div class="bg-secondary/20 rounded-xl p-6">
                            <div class="flex items-center gap-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                @endfor
                            </div>
                            <p class="text-dark/70 mb-3">{{ $review->comment }}</p>
                            <p class="text-sm text-dark/50">— {{ $review->customer_name }}, {{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Productos Relacionados --}}
    @if($relatedProducts->count() > 0)
        <section class="py-12 bg-secondary/20">
            <div class="container mx-auto px-4">
                <h2 class="font-serif text-2xl text-dark mb-6 text-center">También te puede gustar</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
                    @foreach($relatedProducts as $related)
                        <livewire:components.product-card :product="$related" :key="'related-'.$related->id" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
