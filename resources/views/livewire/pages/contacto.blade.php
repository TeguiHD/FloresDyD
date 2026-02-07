{{-- Contacto - Flores D&D --}}
<div class="min-h-dvh">
    {{-- Hero --}}
    <section class="bg-gradient-to-b from-secondary/30 to-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="font-serif text-4xl lg:text-5xl text-primary mb-4">Contacto</h1>
            <p class="text-dark/70 text-lg max-w-2xl mx-auto">
                ¿Tienes preguntas? ¿Necesitas un pedido especial? Estamos aquí para ayudarte
            </p>
        </div>
    </section>

    {{-- Contenido --}}
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="lg:flex lg:gap-16 max-w-6xl mx-auto">
                {{-- Información de Contacto --}}
                <div class="lg:w-1/3 mb-12 lg:mb-0">
                    <h2 class="font-serif text-2xl text-dark mb-6">Información</h2>
                    
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-flux::icon name="phone" class="w-6 h-6 text-primary" />
                            </div>
                            <div>
                                <h3 class="font-medium text-dark">Teléfono</h3>
                                    <a href="tel:{{ $contactPhone }}" class="text-dark/70 hover:text-primary">
                                        {{ $contactPhoneDisplay }}
                                    </a>
                            </div>
                        </div>

                        @if($contactWhatsapp)
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-dark">WhatsApp</h3>
                                        <a href="https://wa.me/{{ $contactWhatsapp }}" target="_blank" class="text-dark/70 hover:text-primary">
                                        {{ $contactWhatsappDisplay ?: 'Enviar mensaje' }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-flux::icon name="envelope" class="w-6 h-6 text-primary" />
                            </div>
                            <div>
                                <h3 class="font-medium text-dark">Email</h3>
                                    <a href="mailto:{{ $contactEmail }}" class="text-dark/70 hover:text-primary">
                                        {{ $contactEmail }}
                                    </a>
                            </div>
                        </div>

                        <div class="pt-6">
                            <h3 class="font-medium text-dark mb-3">Ubicación</h3>
                            <p class="text-dark/60 mb-4">{{ $contactAddress }}</p>
                            <div class="rounded-2xl overflow-hidden border border-secondary/40 bg-white shadow-sm">
                                @if($mapEmbedUrl)
                                    <iframe
                                        src="{{ $mapEmbedUrl }}"
                                        width="100%"
                                        height="260"
                                        style="border:0;"
                                        allowfullscreen=""
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                    ></iframe>
                                @else
                                    <div class="p-6 text-sm text-gray-500">
                                        El mapa no está disponible en este momento.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-flux::icon name="clock" class="w-6 h-6 text-primary" />
                            </div>
                            <div>
                                <h3 class="font-medium text-dark">Horario</h3>
                                <p class="text-dark/70">Lunes a Sábado: 9:00 - 20:00</p>
                                <p class="text-dark/70">Domingo: 10:00 - 14:00</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Formulario --}}
                <div class="lg:w-2/3">
                    <div class="bg-white rounded-2xl shadow-card p-8">
                        <h2 class="font-serif text-2xl text-dark mb-6">Envíanos un mensaje</h2>

                        @if($submitted)
                            <div class="text-center py-12">
                                <div class="text-6xl mb-4">✉️</div>
                                <h3 class="text-xl font-medium text-dark mb-2">¡Mensaje enviado!</h3>
                                <p class="text-dark/60 mb-6">Gracias por contactarnos. Te responderemos lo antes posible.</p>
                                <button 
                                    wire:click="$set('submitted', false)"
                                    class="text-primary hover:underline"
                                >
                                    Enviar otro mensaje
                                </button>
                            </div>
                        @else
                            <form wire:submit="submit" class="space-y-6">
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-dark mb-2">Nombre *</label>
                                        <input 
                                            type="text"
                                            wire:model="name"
                                            class="w-full px-4 py-3 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                            placeholder="Tu nombre"
                                        >
                                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-dark mb-2">Email *</label>
                                        <input 
                                            type="email"
                                            wire:model="email"
                                            class="w-full px-4 py-3 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                            placeholder="tu@email.com"
                                        >
                                        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-dark mb-2">Teléfono *</label>
                                        <input 
                                            type="tel"
                                            wire:model="phone"
                                            class="w-full px-4 py-3 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                            placeholder="55 1234 5678"
                                        >
                                        @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-dark mb-2">Asunto *</label>
                                        <select 
                                            wire:model="subject"
                                            class="w-full px-4 py-3 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                        >
                                            <option value="general">Consulta general</option>
                                            <option value="pedido">Sobre un pedido</option>
                                            <option value="evento">Cotización para evento</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                        @error('subject') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-dark mb-2">Mensaje *</label>
                                    <textarea 
                                        wire:model="message"
                                        rows="5"
                                        class="w-full px-4 py-3 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                                        placeholder="¿En qué podemos ayudarte?"
                                    ></textarea>
                                    @error('message') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <button 
                                    type="submit"
                                    class="w-full md:w-auto px-8 py-4 bg-primary text-white rounded-full hover:bg-primary-dark transition font-medium"
                                >
                                    Enviar mensaje
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
