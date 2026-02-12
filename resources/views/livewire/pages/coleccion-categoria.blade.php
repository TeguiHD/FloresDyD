{{-- Colección por Categoría - Flores D&D --}}
<div class="min-h-dvh">
    {{-- Hero Section --}}
    <section class="bg-gradient-to-b from-secondary/30 to-white py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto">
                {{-- Breadcrumb --}}
                <nav class="flex items-center justify-center gap-2 text-sm text-dark/60 mb-4">
                    <a href="{{ route('coleccion') }}" class="hover:text-primary">Colección</a>
                    <span>/</span>
                    <span class="text-primary">{{ $category->name }}</span>
                </nav>

                <h1 class="font-serif text-4xl lg:text-5xl text-primary mb-4">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="text-dark/70 text-lg">{{ $category->description }}</p>
                @endif
            </div>
        </div>
    </section>

    {{-- Productos --}}
    <section class="py-8 lg:py-12">
        <div class="container mx-auto px-4">
            {{-- Toolbar --}}
            <div class="flex items-center justify-between gap-4 mb-6">
                <p class="text-dark/60 text-sm">
                    {{ $products->total() }} productos en esta categoría
                </p>

                <select 
                    wire:model.live="sort"
                    class="px-4 py-2 border border-secondary rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                >
                    <option value="newest">Más recientes</option>
                    <option value="price_asc">Precio: Menor a Mayor</option>
                    <option value="price_desc">Precio: Mayor a Menor</option>
                    <option value="name">Nombre A-Z</option>
                </select>
            </div>

            {{-- Grid de Productos --}}
            @if($products->count() > 0)
                {{-- Paginación superior --}}
                @if($products->hasPages())
                    <div class="mb-6">
                        {{ $products->links() }}
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                    @foreach($products as $product)
                        <livewire:components.product-card :product="$product" :key="$product->id" />
                    @endforeach
                </div>

                {{-- Paginación inferior --}}
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
                    <h3 class="text-xl font-medium text-dark mb-2">Aún no hay productos en esta categoría</h3>
                    <p class="text-dark/60 mb-6">Pronto agregaremos más opciones para ti</p>
                    <a 
                        href="{{ route('coleccion') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition"
                    >
                        Ver toda la colección
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- FAQ por categoría (AEO) --}}
    @php
        $faqBySlug = [
            'bouquets' => [
                [
                    'question' => '¿Qué incluye un bouquet y cómo se arma?',
                    'answer' => 'Incluye flores frescas seleccionadas, envoltorio premium y presentación lista para regalar. La mezcla se arma según temporada y estilo elegido.',
                ],
                [
                    'question' => '¿Puedo elegir colores o tipo de flores?',
                    'answer' => 'Sí. Puedes pedir paleta de colores y estilo. Confirmamos disponibilidad y sugerimos la mejor combinación para sorprender.',
                ],
                [
                    'question' => '¿Entregan bouquets el mismo día?',
                    'answer' => 'Sí, según horario y disponibilidad. Ideal para regalos de último minuto, especialmente en fechas especiales.',
                ],
                [
                    'question' => '¿Incluye tarjeta con mensaje?',
                    'answer' => 'Sí, puedes agregar un mensaje personalizado sin costo adicional para hacer el regalo más especial.',
                ],
                [
                    'question' => '¿Hay bouquets especiales para fechas clave?',
                    'answer' => 'Sí, preparamos ediciones especiales para San Valentín, Día de la Madre y aniversarios con diseños exclusivos.',
                ],
            ],
            'regalos' => [
                [
                    'question' => '¿Qué tipos de regalos incluyen los arreglos?',
                    'answer' => 'Podemos incluir chocolates, peluches, globos o detalles personalizados para convertir el arreglo en un regalo completo.',
                ],
                [
                    'question' => '¿Se puede agregar un mensaje personalizado?',
                    'answer' => 'Sí, añadimos tarjeta con tu mensaje para nacimientos, cumpleaños o aniversarios. Tu mensaje es el toque final.',
                ],
                [
                    'question' => '¿Pueden coordinar entrega sorpresa?',
                    'answer' => 'Sí, coordinamos horario y entrega discreta para que la sorpresa salga perfecta.',
                ],
                [
                    'question' => '¿Tienen opciones para empresas?',
                    'answer' => 'Sí, ofrecemos regalos corporativos con presentación cuidada, personalización y facturación.',
                ],
                [
                    'question' => '¿Tienen regalos especiales para fechas importantes?',
                    'answer' => 'Sí, contamos con opciones especiales para San Valentín, Día de la Madre y cumpleaños.',
                ],
            ],
            'eventos' => [
                [
                    'question' => '¿Qué incluye la decoración para eventos?',
                    'answer' => 'Incluye propuesta floral, montaje básico y coordinación de entrega. Diseñamos para que el espacio se vea impecable.',
                ],
                [
                    'question' => '¿Atienden matrimonios y eventos corporativos?',
                    'answer' => 'Sí, diseñamos arreglos para bodas, graduaciones y eventos corporativos con estilo y coherencia visual.',
                ],
                [
                    'question' => '¿Con cuánta anticipación debo reservar?',
                    'answer' => 'Recomendamos reservar con anticipación para asegurar disponibilidad de flores y agenda en fechas clave.',
                ],
                [
                    'question' => '¿Se puede solicitar paleta de colores específica?',
                    'answer' => 'Sí, trabajamos con paletas personalizadas según la temática del evento y estilo del lugar.',
                ],
                [
                    'question' => '¿Hacen montajes en temporadas altas?',
                    'answer' => 'Sí, pero la agenda se llena rápido. Recomendamos reservar con anticipación en fechas altas.',
                ],
            ],
            'novios' => [
                [
                    'question' => '¿Qué incluye un ramo de novia?',
                    'answer' => 'Incluye selección de flores frescas, diseño personalizado y terminación elegante para un día perfecto.',
                ],
                [
                    'question' => '¿Hacen ramos a juego con la decoración?',
                    'answer' => 'Sí, coordinamos paleta y estilo con la decoración del matrimonio para un look coherente.',
                ],
                [
                    'question' => '¿Ofrecen arreglos para altar o centros?',
                    'answer' => 'Sí, realizamos arreglos para altar, mesas, pasillos y arcos, todo coordinado.',
                ],
                [
                    'question' => '¿Con cuánta anticipación debo pedir?',
                    'answer' => 'Idealmente con anticipación para definir estilo y asegurar disponibilidad en fechas alta demanda.',
                ],
                [
                    'question' => '¿Ofrecen packs para novios?',
                    'answer' => 'Sí, podemos armar packs con ramo de novia, boutonniere y decoración coordinada.',
                ],
            ],
            'condolencias' => [
                [
                    'question' => '¿Qué tipos de arreglos de condolencias ofrecen?',
                    'answer' => 'Ofrecemos cubre urnas, arreglos frontales y ramos sobrios con flores de temporada y presentación respetuosa.',
                ],
                [
                    'question' => '¿Se puede enviar un mensaje de pésame?',
                    'answer' => 'Sí, incluimos tarjeta con un mensaje respetuoso y personalizado para acompañar a la familia.',
                ],
                [
                    'question' => '¿Entregan en horarios especiales?',
                    'answer' => 'Podemos coordinar horarios según disponibilidad y necesidades del servicio.',
                ],
                [
                    'question' => '¿Puedo solicitar tonos específicos?',
                    'answer' => 'Sí, trabajamos con tonos sobrios o según preferencia de la familia.',
                ],
                [
                    'question' => '¿Pueden entregar en iglesias o cementerios?',
                    'answer' => 'Sí, coordinamos la entrega con el lugar y el horario del servicio.',
                ],
            ],
        ];

        $faqItems = $faqBySlug[$category->slug] ?? [
            [
                'question' => "¿Qué incluye un {$category->name}?",
                'answer' => "Incluye flores frescas seleccionadas y diseño artesanal. La composición puede variar según temporada y disponibilidad.",
            ],
            [
                'question' => "¿Puedo personalizar mi {$category->name}?",
                'answer' => "Sí. Podemos ajustar colores, tamaño y estilo. Indícalo al momento de la compra o por contacto directo.",
            ],
            [
                'question' => "¿Hacen entregas a domicilio para {$category->name}?",
                'answer' => "Sí. Realizamos entregas en Valdivia y Santiago, con cobertura en alrededores según disponibilidad.",
            ],
            [
                'question' => "¿Con cuánta anticipación debería pedir?",
                'answer' => "Recomendamos pedir con anticipación para asegurar disponibilidad, especialmente en fechas especiales.",
            ],
        ];
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function ($item) {
                return [
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item['answer'],
                    ],
                ];
            }, $faqItems),
        ];
    @endphp

    <section class="py-12 lg:py-16 bg-white/70 border-t border-secondary/30">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="font-serif text-3xl text-dark mb-6">Preguntas frecuentes sobre {{ $category->name }}</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($faqItems as $item)
                        <details class="bg-white rounded-xl p-5 shadow-card">
                            <summary class="font-medium text-dark cursor-pointer">{{ $item['question'] }}</summary>
                            <p class="text-dark/60 text-sm mt-3">{{ $item['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('coleccion') }}" class="inline-flex items-center gap-2 bg-primary text-white px-7 py-3.5 rounded-full font-medium hover:bg-primary-light transition-colors">
                        Ver toda la colección
                    </a>
                    <a href="{{ route('contacto') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark transition-colors text-sm font-medium">
                        Cotiza tu pedido o evento
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="application/ld+json">
{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
