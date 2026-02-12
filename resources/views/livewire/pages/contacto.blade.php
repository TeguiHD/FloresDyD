{{-- 
    CONTACTO - Flores D&D (2026 Redesign)
    
    UX research-backed:
    - Phone/WhatsApp/email prominently visible (NNGroup: never hide phone numbers)
    - Form as supplement, not replacement for direct contact
    - 4 fields only (NNGroup: limit to 3-5 fields)
    - Hours + expected response time (builds trust & sets expectations)
    - Address + map (trust + local SEO)
    - Accessible form validation (aria-required, aria-describedby)
    - Consistent design language with homepage 2026
--}}

<div>
    {{-- ========================================
         HERO - Minimal, warm, inviting
         ======================================== --}}
    <section class="contact-hero">
        <div class="contact-hero__mesh" aria-hidden="true"></div>
        <div class="contact-hero__noise" aria-hidden="true"></div>
        
        <div class="container-custom contact-hero__content">
            <span class="section-tag section-tag--light reveal-up" style="--delay: 0s">Contacto</span>
            <h1 class="contact-hero__title reveal-up" style="--delay: 0.1s">
                Estamos aquí para ti
            </h1>
            <p class="contact-hero__subtitle reveal-up" style="--delay: 0.2s">
                ¿Necesitas un arreglo especial o tienes alguna consulta? <br class="hidden sm:block">
                Escríbenos y te respondemos en menos de 2 horas.
            </p>
        </div>
    </section>

    {{-- ========================================
         CANALES DE CONTACTO DIRECTOS
         NNGroup: Siempre mostrar teléfono + email + otros
         ======================================== --}}
    <section class="contact-channels" aria-label="Canales de contacto directo">
        <div class="container-custom">
            <div class="contact-channels__grid">
                {{-- WhatsApp (canal principal) --}}
                @if($contactWhatsapp)
                <a 
                    href="https://wa.me/{{ $contactWhatsapp }}?text={{ urlencode('Hola, me gustaría consultar sobre...') }}"
                    target="_blank"
                    class="contact-channel contact-channel--primary reveal-up"
                    style="--delay: 0s"
                >
                    <div class="contact-channel__icon contact-channel__icon--whatsapp">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    </div>
                    <div class="contact-channel__body">
                        <h3 class="contact-channel__title">WhatsApp</h3>
                        <p class="contact-channel__detail">{{ $contactWhatsappDisplay ?: 'Enviar mensaje' }}</p>
                        <span class="contact-channel__meta">
                            <span class="contact-channel__dot"></span>
                            Respuesta inmediata
                        </span>
                    </div>
                    <svg class="contact-channel__arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                    </svg>
                </a>
                @endif

                {{-- Teléfono --}}
                <a 
                    href="tel:{{ $contactPhone }}"
                    class="contact-channel reveal-up"
                    style="--delay: 0.08s"
                >
                    <div class="contact-channel__icon contact-channel__icon--phone">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                        </svg>
                    </div>
                    <div class="contact-channel__body">
                        <h3 class="contact-channel__title">Teléfono</h3>
                        <p class="contact-channel__detail">{{ $contactPhoneDisplay }}</p>
                        <span class="contact-channel__meta">Lun–Sáb 9:00–20:00</span>
                    </div>
                    <svg class="contact-channel__arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                    </svg>
                </a>

                {{-- Email --}}
                <a 
                    href="mailto:{{ $contactEmail }}"
                    class="contact-channel reveal-up"
                    style="--delay: 0.16s"
                >
                    <div class="contact-channel__icon contact-channel__icon--email">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                    </div>
                    <div class="contact-channel__body">
                        <h3 class="contact-channel__title">Email</h3>
                        <p class="contact-channel__detail">{{ $contactEmail }}</p>
                        <span class="contact-channel__meta">Respondemos en &lt;2 horas</span>
                    </div>
                    <svg class="contact-channel__arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ========================================
         FORMULARIO + INFO
         Layout asimétrico: form (principal) + sidebar info
         ======================================== --}}
    <section class="contact-main">
        <div class="container-custom">
            <div class="contact-layout">
                {{-- Formulario --}}
                <div class="contact-form-wrapper reveal-up" style="--delay: 0s">
                    <div class="contact-form-card">
                        <div class="contact-form-card__header">
                            <h2 class="contact-form-card__title">Envíanos un mensaje</h2>
                            <p class="contact-form-card__desc">
                                Completa el formulario y te contactaremos pronto. Los campos con <span aria-hidden="true" class="contact-form-card__required-symbol">*</span> son obligatorios.
                            </p>
                        </div>

                        @if($submitted)
                            <div class="contact-success">
                                <div class="contact-success__icon">
                                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
                                    </svg>
                                </div>
                                <h3 class="contact-success__title">¡Mensaje enviado!</h3>
                                <p class="contact-success__text">
                                    Gracias por escribirnos. Te responderemos lo antes posible.
                                </p>
                                <button 
                                    wire:click="$set('submitted', false)"
                                    class="contact-success__btn"
                                >
                                    Enviar otro mensaje
                                </button>
                            </div>
                        @else
                            <form wire:submit="submit" novalidate class="contact-form">
                                <div class="contact-form__row">
                                    <div class="contact-form__field">
                                        <label for="contact-name" class="contact-form__label">
                                            Nombre <span aria-hidden="true" class="contact-form-card__required-symbol">*</span>
                                        </label>
                                        <input 
                                            id="contact-name"
                                            type="text"
                                            wire:model="name"
                                            class="contact-form__input @error('name') contact-form__input--error @enderror"
                                            placeholder="Tu nombre completo"
                                            aria-required="true"
                                            @error('name') aria-invalid="true" aria-describedby="error-name" @enderror
                                        >
                                        @error('name') <p id="error-name" class="contact-form__error" role="alert">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="contact-form__field">
                                        <label for="contact-email" class="contact-form__label">
                                            Email <span aria-hidden="true" class="contact-form-card__required-symbol">*</span>
                                        </label>
                                        <input 
                                            id="contact-email"
                                            type="email"
                                            wire:model="email"
                                            class="contact-form__input @error('email') contact-form__input--error @enderror"
                                            placeholder="tu@email.com"
                                            aria-required="true"
                                            @error('email') aria-invalid="true" aria-describedby="error-email" @enderror
                                        >
                                        @error('email') <p id="error-email" class="contact-form__error" role="alert">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="contact-form__row">
                                    <div class="contact-form__field">
                                        <label for="contact-phone" class="contact-form__label">
                                            Teléfono <span aria-hidden="true" class="contact-form-card__required-symbol">*</span>
                                        </label>
                                        <input 
                                            id="contact-phone"
                                            type="tel"
                                            wire:model="phone"
                                            class="contact-form__input @error('phone') contact-form__input--error @enderror"
                                            placeholder="+56 9 1234 5678"
                                            aria-required="true"
                                            @error('phone') aria-invalid="true" aria-describedby="error-phone" @enderror
                                        >
                                        @error('phone') <p id="error-phone" class="contact-form__error" role="alert">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="contact-form__field">
                                        <label for="contact-subject" class="contact-form__label">
                                            Asunto <span aria-hidden="true" class="contact-form-card__required-symbol">*</span>
                                        </label>
                                        <div class="contact-form__select-wrap">
                                            <select 
                                                id="contact-subject"
                                                wire:model="subject"
                                                class="contact-form__input contact-form__input--select @error('subject') contact-form__input--error @enderror"
                                                aria-required="true"
                                                @error('subject') aria-invalid="true" aria-describedby="error-subject" @enderror
                                            >
                                                <option value="general">Consulta general</option>
                                                <option value="pedido">Sobre un pedido</option>
                                                <option value="evento">Cotización para evento</option>
                                                <option value="otro">Otro</option>
                                            </select>
                                            <svg class="contact-form__select-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                            </svg>
                                        </div>
                                        @error('subject') <p id="error-subject" class="contact-form__error" role="alert">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="contact-form__field">
                                    <label for="contact-message" class="contact-form__label">
                                        Mensaje <span aria-hidden="true" class="contact-form-card__required-symbol">*</span>
                                    </label>
                                    <textarea 
                                        id="contact-message"
                                        wire:model="message"
                                        rows="5"
                                        class="contact-form__input contact-form__input--textarea @error('message') contact-form__input--error @enderror"
                                        placeholder="¿En qué podemos ayudarte?"
                                        aria-required="true"
                                        @error('message') aria-invalid="true" aria-describedby="error-message" @enderror
                                    ></textarea>
                                    @error('message') <p id="error-message" class="contact-form__error" role="alert">{{ $message }}</p> @enderror
                                </div>

                                <div class="contact-form__footer">
                                    <button type="submit" class="contact-form__submit" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="submit">Enviar mensaje</span>
                                        <span wire:loading wire:target="submit" class="contact-form__loading">
                                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                            Enviando...
                                        </span>
                                    </button>
                                    <p class="contact-form__privacy">
                                        Al enviar, aceptas nuestra 
                                        <a href="{{ route('aviso-privacidad') }}" class="contact-form__privacy-link">política de privacidad</a>.
                                    </p>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Sidebar: Horarios + Trust --}}
                <aside class="contact-sidebar reveal-up" style="--delay: 0.12s">
                    {{-- Horarios --}}
                    <div class="contact-sidebar__card">
                        <div class="contact-sidebar__card-header">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="contact-sidebar__card-title">Horario de Atención</h3>
                        </div>
                        <ul class="contact-sidebar__hours">
                            <li class="contact-sidebar__hours-row">
                                <span class="contact-sidebar__hours-day">Lunes – Viernes</span>
                                <span class="contact-sidebar__hours-time">9:00 – 20:00</span>
                            </li>
                            <li class="contact-sidebar__hours-row">
                                <span class="contact-sidebar__hours-day">Sábado</span>
                                <span class="contact-sidebar__hours-time">9:00 – 18:00</span>
                            </li>
                            <li class="contact-sidebar__hours-row">
                                <span class="contact-sidebar__hours-day">Domingo</span>
                                <span class="contact-sidebar__hours-time">10:00 – 14:00</span>
                            </li>
                        </ul>
                        <p class="contact-sidebar__hours-note">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                            </svg>
                            Horario Chile (UTC-3)
                        </p>
                    </div>

                    @if(!empty($instagramEmbeds))
                        <div class="contact-sidebar__card">
                            <div class="contact-sidebar__card-header">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7.5 3h9A4.5 4.5 0 0121 7.5v9A4.5 4.5 0 0116.5 21h-9A4.5 4.5 0 013 16.5v-9A4.5 4.5 0 017.5 3zm9 1.5h-9A3 3 0 004.5 7.5v9A3 3 0 007.5 19.5h9a3 3 0 003-3v-9a3 3 0 00-3-3zm-4.5 3.75A4.25 4.25 0 1112 16.5a4.25 4.25 0 010-8.5zm0 1.5a2.75 2.75 0 100 5.5 2.75 2.75 0 000-5.5zm5.25-1.75a1 1 0 11-.002-2 1 1 0 01.002 2z"/>
                                </svg>
                                <h3 class="contact-sidebar__card-title">Instagram en vivo</h3>
                            </div>
                            <div class="contact-sidebar__map">
                                <iframe
                                    src="{{ $instagramEmbeds[0] }}"
                                    width="100%"
                                    height="320"
                                    style="border:0;"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="Publicación reciente de Instagram"
                                ></iframe>
                            </div>
                            @if($instagramUsername)
                                <a href="https://instagram.com/{{ $instagramUsername }}" data-external="true" class="mt-3 inline-flex text-sm font-medium text-primary hover:underline">
                                    Ver perfil completo
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Ubicación mini --}}
                    <div class="contact-sidebar__card">
                        <div class="contact-sidebar__card-header">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                            </svg>
                            <h3 class="contact-sidebar__card-title">Nuestras Sucursales</h3>
                        </div>
                        @foreach(config('flores.sucursales', []) as $sucursal)
                            <div class="mb-3 last:mb-0">
                                <p class="font-medium text-sm text-dark">{{ $sucursal['nombre'] }}</p>
                                <p class="contact-sidebar__address text-dark/60">{{ $sucursal['direccion'] }}, {{ $sucursal['comuna'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Trust signals --}}
                    <div class="contact-sidebar__trust">
                        <div class="contact-sidebar__trust-item">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                            <span>Tu información está protegida</span>
                        </div>
                        <div class="contact-sidebar__trust-item">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Respondemos en menos de 2 horas</span>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- ========================================
         MAPA - Sección completa, ancho total
         ======================================== --}}
    <section class="contact-map-section reveal-up" style="--delay: 0.15s" aria-label="Nuestra ubicación">
        <div class="container-custom">
            <div class="contact-map-header">
                <div class="contact-map-header__text">
                    <h2 class="contact-map-header__title">Encuéntranos</h2>
                    @foreach(config('flores.sucursales', []) as $sucursal)
                        <p class="contact-map-header__address">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                            </svg>
                            {{ $sucursal['nombre'] }}: {{ $sucursal['direccion'] }}, {{ $sucursal['comuna'] }}
                        </p>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="contact-map-frame">
            @if($mapEmbedUrl)
                <iframe
                    src="{{ $mapEmbedUrl }}"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Ubicación de Flores D&D en el mapa"
                ></iframe>
            @else
                <div class="contact-map-placeholder">
                    <svg class="w-12 h-12 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/>
                    </svg>
                    <span>Mapa no disponible</span>
                </div>
            @endif
        </div>
    </section>
</div>
