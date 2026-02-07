{{-- Servicios - Flores D&D --}}
<div class="min-h-dvh">
    {{-- Hero --}}
    <section class="bg-gradient-to-b from-secondary/30 to-white py-16 lg:py-24">
        <div class="container mx-auto px-4 text-center">
            <h1 class="font-serif text-4xl lg:text-5xl text-primary mb-4">Nuestros Servicios</h1>
            <p class="text-dark/70 text-lg max-w-2xl mx-auto">
                Más que una floristería, somos tu aliado para crear momentos inolvidables con flores
            </p>
        </div>
    </section>

    {{-- Servicios --}}
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($services as $service)
                    <div class="bg-white rounded-2xl shadow-card p-8 hover:shadow-hover transition-shadow">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6">
                            <x-flux::icon :name="$service['icon']" class="w-7 h-7 text-primary" />
                        </div>
                        <h3 class="font-serif text-xl text-dark mb-3">{{ $service['title'] }}</h3>
                        <p class="text-dark/60 mb-6">{{ $service['description'] }}</p>
                        <ul class="space-y-2">
                            @foreach($service['features'] as $feature)
                                <li class="flex items-center gap-2 text-sm text-dark/70">
                                    <x-flux::icon name="check" class="w-4 h-4 text-primary flex-shrink-0" />
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Proceso --}}
    <section class="py-16 bg-secondary/30">
        <div class="container mx-auto px-4">
            <h2 class="font-serif text-3xl text-center text-dark mb-12">¿Cómo funciona?</h2>
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                    <h3 class="font-medium text-dark mb-2">Elige</h3>
                    <p class="text-dark/60 text-sm">Selecciona tu arreglo favorito o pide uno personalizado</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                    <h3 class="font-medium text-dark mb-2">Personaliza</h3>
                    <p class="text-dark/60 text-sm">Agrega un mensaje especial y elige la fecha de entrega</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                    <h3 class="font-medium text-dark mb-2">Creamos</h3>
                    <p class="text-dark/60 text-sm">Preparamos tu arreglo con las flores más frescas</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">4</div>
                    <h3 class="font-medium text-dark mb-2">Entregamos</h3>
                    <p class="text-dark/60 text-sm">Llevamos tu regalo con puntualidad y cuidado</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="font-serif text-3xl text-dark mb-4">¿Listo para empezar?</h2>
            <p class="text-dark/70 mb-8 max-w-xl mx-auto">
                Explora nuestra colección o contáctanos para un servicio personalizado
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a 
                    href="{{ route('coleccion') }}"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-primary text-white rounded-full hover:bg-primary-dark transition font-medium"
                >
                    Ver Colección
                </a>
                <a 
                    href="{{ route('contacto') }}"
                    class="inline-flex items-center gap-2 px-8 py-4 border-2 border-primary text-primary rounded-full hover:bg-primary hover:text-white transition font-medium"
                >
                    Contactar
                </a>
            </div>
        </div>
    </section>
</div>
