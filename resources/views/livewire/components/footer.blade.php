<div>
<footer class="footer">
    <div class="container-custom">
        
        {{-- Top section: Newsletter --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-10 mb-10 border-b border-white/10">
            <div>
                <h4 class="font-display text-lg text-white mb-1">Recibe ofertas exclusivas</h4>
                <p class="text-gray-500 text-sm">Sé la primera en enterarte de nuevos diseños y promociones.</p>
            </div>
            <form wire:submit="subscribeNewsletter" class="flex w-full md:w-auto gap-2">
                <input 
                    type="email"
                    wire:model="newsletterEmail"
                    placeholder="tu@email.com"
                    class="flex-1 md:w-64 px-4 py-2.5 bg-white/5 border border-white/15 rounded-full text-sm text-white placeholder-gray-500 focus:outline-none focus:border-primary-light transition-colors"
                >
                <button type="submit" class="px-5 py-2.5 bg-primary text-white text-sm rounded-full hover:bg-primary-light transition-colors font-medium">
                    Suscribirme
                </button>
            </form>
            @if($newsletterSuccess)
                <p class="text-green-400 text-xs">¡Gracias por suscribirte!</p>
            @endif
            @if($newsletterError)
                <p class="text-red-400 text-xs">{{ $newsletterError }}</p>
            @endif
            @error('newsletterEmail')
                <p class="text-red-400 text-xs">{{ $message }}</p>
            @enderror
        </div>
        
        {{-- Grid principal - 3 columnas limpias --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 lg:gap-16">
            
            {{-- Columna 1: Marca --}}
            <div>
                <a href="{{ route('home') }}" class="footer-brand inline-flex items-center gap-2 mb-4">
                    <img 
                        src="{{ asset('images/floresdyd-logo.webp') }}" 
                        alt="Flores D&D" 
                        class="h-8 w-auto"
                    >
                    <span class="font-display text-xl text-white">Flores D&D</span>
                </a>
                
                <p class="text-gray-500 text-sm leading-relaxed mb-5">
                    Arreglos florales artesanales creados con amor para cada momento especial.
                </p>
                
                {{-- Redes sociales --}}
                @if($socialLinks->count() > 0)
                    <div class="flex items-center gap-3">
                        @foreach($socialLinks as $social)
                            @if(!empty($social->url))
                                <a 
                                    href="{{ $social->url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="social-link social-link--{{ $social->platform }} w-8 h-8 rounded-full bg-white/8 flex items-center justify-center transition-colors text-gray-400"
                                    aria-label="{{ $social->name }}"
                                >
                                    @switch($social->platform)
                                        @case('facebook')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                            @break
                                        @case('instagram')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/></svg>
                                            @break
                                        @case('tiktok')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                                            @break
                                        @case('whatsapp')
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            @break
                                        @default
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    @endswitch
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
            
            {{-- Columna 2: Links rápidos --}}
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <h5 class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Tienda</h5>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('coleccion') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Catálogo</a></li>
                        @foreach($parentCategories->take(5) as $parent)
                            <li>
                                <a href="{{ route('coleccion.categoria', $parent->slug) }}" class="text-sm text-gray-400 hover:text-white transition-colors">
                                    {{ $parent->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h5 class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Información</h5>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('nosotros') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Nosotros</a></li>
                        <li><a href="{{ route('contacto') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Contacto</a></li>
                        <li><a href="{{ route('ocasiones') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Ocasiones</a></li>
                        <li><a href="{{ route('politica-envios') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Envíos</a></li>
                        <li><a href="{{ route('flores.santiago') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Flores en Santiago</a></li>
                    </ul>
                </div>
            </div>
            
            {{-- Columna 3: Contacto --}}
            @if(!empty($contactAddress) || !empty($contactPhone) || !empty($contactEmail))
                <div>
                    <h5 class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Contacto</h5>
                    <ul class="space-y-3">
                        @if(!empty($contactAddress))
                            <li class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 flex-shrink-0 text-gray-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-sm text-gray-400">{{ $contactAddress }}</span>
                            </li>
                        @endif
                        @if(!empty($contactPhone))
                            <li class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 flex-shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <a href="tel:{{ $contactPhone }}" class="text-sm text-gray-400 hover:text-white transition-colors">
                                    {{ $contactPhoneDisplay }}
                                </a>
                            </li>
                        @endif
                        @if(!empty($contactEmail))
                            <li class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 flex-shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <a href="mailto:{{ $contactEmail }}" class="text-sm text-gray-400 hover:text-white transition-colors">
                                    {{ $contactEmail }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
        
        {{-- Bottom bar --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-3 mt-10 pt-8 border-t border-white/10">
            <p class="text-gray-600 text-xs">
                © {{ date('Y') }} Flores D&D. Todos los derechos reservados.
            </p>
            
            <div class="flex flex-wrap items-center justify-center gap-4 text-xs">
                <a href="{{ route('aviso-privacidad') }}" class="text-gray-600 hover:text-gray-400 transition-colors">Privacidad</a>
                <a href="{{ route('terminos') }}" class="text-gray-600 hover:text-gray-400 transition-colors">Términos</a>
                <a href="{{ route('politica-envios') }}" class="text-gray-600 hover:text-gray-400 transition-colors">Envíos</a>
            </div>
        </div>
    </div>
</footer>

@if(!empty($whatsappNumber))
    {{-- WhatsApp floating button --}}
    <a 
        href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hola, me gustaría hacer un pedido') }}"
        target="_blank"
        rel="noopener noreferrer"
        class="fixed bottom-6 right-6 w-14 h-14 bg-green-500 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition-all hover:scale-110 z-40 group"
        aria-label="Contactar por WhatsApp"
    >
        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        {{-- Tooltip --}}
        <span class="absolute right-full mr-3 bg-dark text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-lg">
            WhatsApp: {{ $whatsappDisplay ?: '¿Necesitas ayuda?' }}
        </span>
    </a>
@endif
</div>
