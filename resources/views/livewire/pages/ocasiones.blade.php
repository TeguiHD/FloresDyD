{{-- Ocasiones Especiales - Flores D&D --}}
<div class="min-h-dvh">
    {{-- Hero --}}
    <section class="bg-gradient-to-b from-secondary/30 to-white py-16 lg:py-24">
        <div class="container mx-auto px-4 text-center">
            <h1 class="font-serif text-4xl lg:text-5xl text-primary mb-4">Ocasiones Especiales</h1>
            <p class="text-dark/70 text-lg max-w-2xl mx-auto">
                Cada momento merece flores perfectas. Encuentra el arreglo ideal para celebrar, agradecer o consolar.
            </p>
        </div>
    </section>

    {{-- Grid de Ocasiones --}}
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($occasions as $slug => $occasion)
                    <a 
                        href="{{ route('coleccion') }}?occasion={{ $slug }}"
                        class="group bg-white rounded-2xl shadow-card hover:shadow-hover transition-all duration-300 overflow-hidden"
                    >
                        <div class="aspect-square bg-gradient-to-br from-primary/5 to-secondary flex items-center justify-center">
                            <span class="text-7xl group-hover:scale-110 transition-transform duration-300">
                                {{ $occasion['icon'] }}
                            </span>
                        </div>
                        <div class="p-6">
                            <h3 class="font-serif text-xl text-dark mb-2 group-hover:text-primary transition">
                                {{ $occasion['title'] }}
                            </h3>
                            <p class="text-dark/60 text-sm">{{ $occasion['description'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 bg-primary text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="font-serif text-3xl mb-4">¿No encuentras lo que buscas?</h2>
            <p class="text-white/80 mb-8 max-w-xl mx-auto">
                Creamos arreglos personalizados para cualquier ocasión. Cuéntanos qué necesitas y lo haremos realidad.
            </p>
            <a 
                href="{{ route('contacto') }}"
                class="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary rounded-full hover:bg-secondary transition font-medium"
            >
                Solicitar arreglo personalizado
            </a>
        </div>
    </section>
</div>
