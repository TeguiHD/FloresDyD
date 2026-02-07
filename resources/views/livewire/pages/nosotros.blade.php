{{-- Sobre Nosotros - Flores D&D --}}
<div class="min-h-dvh">
    {{-- Hero --}}
    <section class="bg-gradient-to-b from-secondary/30 to-white py-16 lg:py-24">
        <div class="container mx-auto px-4">
            <div class="lg:flex lg:items-center lg:gap-16">
                <div class="lg:w-1/2 mb-8 lg:mb-0">
                    <h1 class="font-serif text-4xl lg:text-5xl text-primary mb-6">Sobre Nosotros</h1>
                    <p class="text-dark/70 text-lg mb-6">
                        Somos <strong>Flores D&D</strong>, una floristería familiar fundada con la misión de llevar 
                        alegría y belleza a través de las flores más frescas y hermosas.
                    </p>
                    <p class="text-dark/70">
                        Cada arreglo que creamos cuenta una historia. Combinamos la tradición artesanal con diseños 
                        modernos para ofrecerte opciones únicas que expresen exactamente lo que quieres decir.
                    </p>
                </div>
                <div class="lg:w-1/2">
                    <div class="aspect-[4/3] bg-secondary/50 rounded-2xl overflow-hidden">
                        <img 
                            src="/images/about-hero.jpg" 
                            alt="Flores D&D - Nuestra floristería"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Valores --}}
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="font-serif text-3xl text-center text-dark mb-12">Nuestros Valores</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($values as $value)
                    <div class="text-center">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <x-flux::icon :name="$value['icon']" class="w-8 h-8 text-primary" />
                        </div>
                        <h3 class="font-medium text-dark mb-2">{{ $value['title'] }}</h3>
                        <p class="text-dark/60 text-sm">{{ $value['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Equipo --}}
    <section class="py-16 bg-secondary/30">
        <div class="container mx-auto px-4">
            <h2 class="font-serif text-3xl text-center text-dark mb-12">Conoce al Equipo</h2>
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                @foreach($team as $member)
                    <div class="bg-white rounded-2xl p-8 text-center shadow-card">
                        <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 20a8 8 0 0116 0"/>
                            </svg>
                        </div>
                        <h3 class="font-serif text-xl text-dark mb-1">{{ $member['name'] }}</h3>
                        <p class="text-primary text-sm mb-4">{{ $member['role'] }}</p>
                        <p class="text-dark/60">{{ $member['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonios --}}
    @if($testimonials->count() > 0)
        <section class="py-16">
            <div class="container mx-auto px-4">
                <h2 class="font-serif text-3xl text-center text-dark mb-12">Lo que dicen nuestros clientes</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($testimonials as $testimonial)
                        <div class="bg-secondary/20 rounded-2xl p-6">
                            <div class="flex gap-1 mb-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                @endfor
                            </div>
                            <p class="text-dark/70 mb-4 italic">"{{ $testimonial->comment }}"</p>
                            <p class="font-medium text-dark">{{ $testimonial->name }}</p>
                            @if($testimonial->occasion)
                                <p class="text-sm text-gray-500">{{ $testimonial->occasion }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="py-16 bg-primary text-white">
        @php $whatsappNumber = \App\Models\SiteSetting::getWhatsappNumber(); @endphp
        <div class="container mx-auto px-4 text-center">
            <h2 class="font-serif text-3xl mb-4">¿Quieres conocernos mejor?</h2>
            <p class="text-white/80 mb-8 max-w-xl mx-auto">
                Visítanos en nuestra floristería o contáctanos por WhatsApp
            </p>
            @if($whatsappNumber)
                <a 
                    href="https://wa.me/{{ $whatsappNumber }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary rounded-full hover:bg-secondary transition font-medium"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    Escríbenos por WhatsApp
                </a>
            @endif
        </div>
    </section>
</div>
