@extends('layouts.app')

@section('title', 'Política de Privacidad | Flores DyD')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#f8f6f2] via-white to-white legal-page">
    {{-- Header --}}
    <div class="legal-hero">
        <div class="legal-hero__inner max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <span class="legal-hero__badge">Política legal</span>
            <h1 class="legal-hero__title">Política de Privacidad</h1>
            <p class="legal-hero__date">Última actualización: Febrero 2026</p>
            <p class="legal-hero__description">En Flores DyD valoramos y respetamos tu privacidad. Esta política describe cómo recopilamos, usamos y protegemos tu información personal de acuerdo con la Ley 19.628 sobre Protección de la Vida Privada.</p>
            
            <div class="legal-hero__summary">
                <p class="legal-hero__summary-title">En esta política:</p>
                <ul class="legal-hero__summary-list">
                    <li>Datos que recopilamos</li>
                    <li>Finalidades y base legal</li>
                    <li>Derechos ARCO y contacto</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="lg:grid lg:grid-cols-12 lg:gap-12">
            {{-- Sidebar Index - Desktop --}}
            <aside class="hidden lg:block lg:col-span-3">
                <nav class="legal-nav sticky top-24" x-data="{ activeSection: '' }" @scroll.window="
                    let sections = ['intro', 'responsable', 'datos-recopilados', 'finalidad', 'base-legal', 'almacenamiento', 'derechos', 'seguridad', 'cookies', 'terceros', 'menores', 'cambios', 'contacto'];
                    for (let section of sections) {
                        let element = document.getElementById(section);
                        if (element) {
                            let rect = element.getBoundingClientRect();
                            if (rect.top <= 150 && rect.bottom >= 150) {
                                activeSection = section;
                                break;
                            }
                        }
                    }
                ">
                    <p class="px-3 py-2 text-sm font-semibold text-ink/70 uppercase tracking-wider">Contenido</p>
                    <a href="#intro" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'intro' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Introducción
                    </a>
                    <a href="#responsable" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'responsable' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Responsable del Tratamiento
                    </a>
                    <a href="#datos-recopilados" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'datos-recopilados' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Datos que Recopilamos
                    </a>
                    <a href="#finalidad" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'finalidad' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Finalidad del Tratamiento
                    </a>
                    <a href="#base-legal" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'base-legal' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Base Legal
                    </a>
                    <a href="#almacenamiento" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'almacenamiento' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Almacenamiento y Conservación
                    </a>
                    <a href="#derechos" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'derechos' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Tus Derechos
                    </a>
                    <a href="#seguridad" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'seguridad' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Seguridad de la Información
                    </a>
                    <a href="#cookies" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'cookies' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Cookies y Tecnologías Similares
                    </a>
                    <a href="#terceros" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'terceros' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Comunicación a Terceros
                    </a>
                    <a href="#menores" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'menores' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Menores de Edad
                    </a>
                    <a href="#cambios" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'cambios' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Cambios a esta Política
                    </a>
                    <a href="#contacto" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'contacto' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Contacto
                    </a>
                </nav>
            </aside>

            {{-- Mobile Index --}}
            <div class="lg:hidden mb-8">
                <details class="legal-mobile-index">
                    <summary class="px-4 py-3 font-medium text-ink cursor-pointer hover:bg-mist/50 transition-colors rounded-lg">
                        <span class="flex items-center justify-between">
                            <span>Índice de Contenidos</span>
                            <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </summary>
                    <nav class="px-4 pb-4 pt-2 space-y-1">
                        <a href="#intro" class="block py-2 text-sm text-ink/70 hover:text-primary">Introducción</a>
                        <a href="#responsable" class="block py-2 text-sm text-ink/70 hover:text-primary">Responsable del Tratamiento</a>
                        <a href="#datos-recopilados" class="block py-2 text-sm text-ink/70 hover:text-primary">Datos que Recopilamos</a>
                        <a href="#finalidad" class="block py-2 text-sm text-ink/70 hover:text-primary">Finalidad del Tratamiento</a>
                        <a href="#base-legal" class="block py-2 text-sm text-ink/70 hover:text-primary">Base Legal</a>
                        <a href="#almacenamiento" class="block py-2 text-sm text-ink/70 hover:text-primary">Almacenamiento y Conservación</a>
                        <a href="#derechos" class="block py-2 text-sm text-ink/70 hover:text-primary">Tus Derechos</a>
                        <a href="#seguridad" class="block py-2 text-sm text-ink/70 hover:text-primary">Seguridad de la Información</a>
                        <a href="#cookies" class="block py-2 text-sm text-ink/70 hover:text-primary">Cookies y Tecnologías Similares</a>
                        <a href="#terceros" class="block py-2 text-sm text-ink/70 hover:text-primary">Comunicación a Terceros</a>
                        <a href="#menores" class="block py-2 text-sm text-ink/70 hover:text-primary">Menores de Edad</a>
                        <a href="#cambios" class="block py-2 text-sm text-ink/70 hover:text-primary">Cambios a esta Política</a>
                        <a href="#contacto" class="block py-2 text-sm text-ink/70 hover:text-primary">Contacto</a>
                    </nav>
                </details>
            </div>

            {{-- Main Content --}}
            <main class="lg:col-span-9">
                <div class="legal-content">
                    <div class="prose prose-lg max-w-none p-6 md:p-8 lg:p-12">
                        
                        <section id="intro" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">1. Introducción</h2>
                            <p class="text-ink/80 leading-relaxed">
                                Flores DyD se compromete a proteger la privacidad y los datos personales de sus clientes y usuarios. Esta Política de Privacidad establece los términos en que Flores DyD usa y protege la información que es proporcionada por sus usuarios al momento de utilizar nuestro sitio web y servicios.
                            </p>
                            <p class="text-ink/80 leading-relaxed">
                                Esta política cumple con lo establecido en la <strong>Ley 19.628 sobre Protección de la Vida Privada</strong> y demás normativa chilena aplicable en materia de protección de datos personales.
                            </p>
                        </section>

                        <section id="responsable" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">2. Responsable del Tratamiento de Datos</h2>
                            <div class="bg-mist/30 rounded-lg p-6 border-l-4 border-primary">
                                <p class="text-ink/80 mb-2"><strong>Razón Social:</strong> Flores DyD</p>
                                <p class="text-ink/80 mb-2"><strong>Dirección:</strong> {{ \App\Models\SiteSetting::getValue('contact.address', config('flores.address')) }}</p>
                                <p class="text-ink/80 mb-2"><strong>Email:</strong> {{ \App\Models\SiteSetting::getValue('contact.email', config('flores.email')) }}</p>
                                <p class="text-ink/80"><strong>Teléfono:</strong> {{ \App\Models\SiteSetting::getValue('contact.phone_display', config('flores.phone_display')) }}</p>
                            </div>
                        </section>

                        <section id="datos-recopilados" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">3. Datos Personales que Recopilamos</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Recopilamos diferentes tipos de información según la interacción que tengas con nuestros servicios:
                            </p>
                            
                            <h3 class="text-xl font-semibold text-ink mb-3">3.1 Datos de Identificación y Contacto</h3>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-6">
                                <li>Nombre completo</li>
                                <li>RUT (Rol Único Tributario)</li>
                                <li>Correo electrónico</li>
                                <li>Número de teléfono</li>
                                <li>Dirección de entrega</li>
                                <li>Comuna y región</li>
                            </ul>

                            <h3 class="text-xl font-semibold text-ink mb-3">3.2 Datos de Navegación</h3>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-6">
                                <li>Dirección IP</li>
                                <li>Tipo de navegador</li>
                                <li>Páginas visitadas</li>
                                <li>Tiempo de permanencia</li>
                                <li>Fuente de referencia</li>
                            </ul>

                            <h3 class="text-xl font-semibold text-ink mb-3">3.3 Datos de Transacciones</h3>
                            <ul class="list-disc list-inside space-y-2 text-ink/80">
                                <li>Historial de pedidos</li>
                                <li>Productos adquiridos</li>
                                <li>Montos de compra</li>
                                <li>Método de pago utilizado (sin almacenar datos bancarios completos)</li>
                            </ul>
                        </section>

                        <section id="finalidad" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">4. Finalidad del Tratamiento de Datos</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Utilizamos tus datos personales para los siguientes fines:
                            </p>
                            <div class="space-y-4">
                                <div class="bg-rose/5 rounded-lg p-4 border-l-4 border-rose">
                                    <h4 class="font-semibold text-ink mb-2">Procesamiento de Pedidos</h4>
                                    <p class="text-ink/70 text-sm">Gestionar tus compras, procesar pagos y coordinar entregas.</p>
                                </div>
                                <div class="bg-sage/10 rounded-lg p-4 border-l-4 border-sage">
                                    <h4 class="font-semibold text-ink mb-2">Comunicación</h4>
                                    <p class="text-ink/70 text-sm">Enviar confirmaciones de pedidos, actualizaciones de estado y responder consultas.</p>
                                </div>
                                <div class="bg-gold/10 rounded-lg p-4 border-l-4 border-gold">
                                    <h4 class="font-semibold text-ink mb-2">Marketing (con tu consentimiento)</h4>
                                    <p class="text-ink/70 text-sm">Enviar ofertas, promociones y novedades sobre nuestros productos.</p>
                                </div>
                                <div class="bg-lilac/10 rounded-lg p-4 border-l-4 border-lilac">
                                    <h4 class="font-semibold text-ink mb-2">Mejora de Servicios</h4>
                                    <p class="text-ink/70 text-sm">Analizar el uso del sitio para mejorar la experiencia del usuario.</p>
                                </div>
                                <div class="bg-primary/10 rounded-lg p-4 border-l-4 border-primary">
                                    <h4 class="font-semibold text-ink mb-2">Cumplimiento Legal</h4>
                                    <p class="text-ink/70 text-sm">Cumplir con obligaciones legales y regulatorias aplicables.</p>
                                </div>
                            </div>
                        </section>

                        <section id="base-legal" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">5. Base Legal del Tratamiento</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                El tratamiento de tus datos personales se fundamenta en:
                            </p>
                            <ul class="list-disc list-inside space-y-3 text-ink/80">
                                <li><strong>Ejecución de contrato:</strong> Para procesar y entregar tus pedidos.</li>
                                <li><strong>Consentimiento:</strong> Para comunicaciones de marketing (puedes retirarlo en cualquier momento).</li>
                                <li><strong>Interés legítimo:</strong> Para mejorar nuestros servicios y prevenir fraudes.</li>
                                <li><strong>Obligación legal:</strong> Para cumplir con normativas fiscales y de protección al consumidor.</li>
                            </ul>
                        </section>

                        <section id="almacenamiento" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">6. Almacenamiento y Conservación de Datos</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Tus datos personales serán almacenados en servidores seguros y conservados durante:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-4">
                                <li><strong>Datos de cuenta:</strong> Mientras mantengas tu cuenta activa.</li>
                                <li><strong>Datos de transacciones:</strong> Por un período mínimo de 5 años según lo requiere la legislación tributaria chilena.</li>
                                <li><strong>Datos de marketing:</strong> Hasta que retires tu consentimiento.</li>
                            </ul>
                            <p class="text-ink/80 leading-relaxed">
                                Una vez cumplido el plazo de conservación, procederemos a eliminar o anonimizar tus datos de forma segura.
                            </p>
                        </section>

                        <section id="derechos" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">7. Tus Derechos como Titular de Datos</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                De acuerdo con la Ley 19.628, tienes los siguientes derechos:
                            </p>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center mr-3">
                                            <span class="text-primary font-bold">1</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-ink mb-1">Derecho de Acceso</h4>
                                            <p class="text-sm text-ink/70">Solicitar información sobre qué datos personales tenemos sobre ti.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 bg-rose/10 rounded-lg flex items-center justify-center mr-3">
                                            <span class="text-rose font-bold">2</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-ink mb-1">Derecho de Rectificación</h4>
                                            <p class="text-sm text-ink/70">Corregir datos inexactos o incompletos.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 bg-sage/10 rounded-lg flex items-center justify-center mr-3">
                                            <span class="text-sage font-bold">3</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-ink mb-1">Derecho de Cancelación</h4>
                                            <p class="text-sm text-ink/70">Solicitar la eliminación de tus datos cuando corresponda.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 bg-gold/10 rounded-lg flex items-center justify-center mr-3">
                                            <span class="text-gold font-bold">4</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-ink mb-1">Derecho de Bloqueo</h4>
                                            <p class="text-sm text-ink/70">Bloquear datos cuya exactitud no pueda establecerse.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 bg-lilac/10 rounded-lg flex items-center justify-center mr-3">
                                            <span class="text-lilac font-bold">5</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-ink mb-1">Derecho de Oposición</h4>
                                            <p class="text-sm text-ink/70">Oponerte al tratamiento de tus datos con fines de publicidad.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 bg-terracota/10 rounded-lg flex items-center justify-center mr-3">
                                            <span class="text-terracota font-bold">6</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-ink mb-1">Derecho a Revocar</h4>
                                            <p class="text-sm text-ink/70">Retirar el consentimiento previamente otorgado.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 bg-primary/5 border border-primary/20 rounded-lg p-6">
                                <p class="text-ink/80 leading-relaxed">
                                    <strong>Para ejercer estos derechos,</strong> envía un correo a <a href="mailto:{{ \App\Models\SiteSetting::getValue('contact.email', config('flores.email')) }}" class="text-primary hover:underline font-medium">{{ \App\Models\SiteSetting::getValue('contact.email', config('flores.email')) }}</a> con tu nombre completo, RUT y una descripción clara de tu solicitud. Responderemos en un plazo máximo de 10 días hábiles.
                                </p>
                            </div>
                        </section>

                        <section id="seguridad" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">8. Seguridad de la Información</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Implementamos medidas de seguridad técnicas y organizativas para proteger tus datos personales contra acceso no autorizado, pérdida o alteración:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-4">
                                <li>Cifrado SSL/TLS para todas las transmisiones de datos</li>
                                <li>Almacenamiento seguro con encriptación de datos sensibles</li>
                                <li>Acceso restringido solo a personal autorizado</li>
                                <li>Auditorías de seguridad periódicas</li>
                                <li>Políticas de contraseñas robustas</li>
                                <li>Monitoreo continuo de amenazas</li>
                            </ul>
                            <p class="text-ink/80 leading-relaxed">
                                Sin embargo, ningún sistema es completamente seguro. Te recomendamos proteger tus credenciales de acceso y notificarnos inmediatamente si sospechas de alguna actividad no autorizada en tu cuenta.
                            </p>
                        </section>

                        <section id="cookies" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">9. Cookies y Tecnologías Similares</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Utilizamos cookies y tecnologías similares para mejorar tu experiencia en nuestro sitio web:
                            </p>
                            <div class="space-y-4">
                                <div class="border border-ink/10 rounded-lg p-4">
                                    <h4 class="font-semibold text-ink mb-2">Cookies Esenciales</h4>
                                    <p class="text-sm text-ink/70">Necesarias para el funcionamiento básico del sitio (sesión, carrito de compras).</p>
                                </div>
                                <div class="border border-ink/10 rounded-lg p-4">
                                    <h4 class="font-semibold text-ink mb-2">Cookies de Rendimiento</h4>
                                    <p class="text-sm text-ink/70">Analizan cómo los usuarios interactúan con el sitio para mejorar su funcionamiento.</p>
                                </div>
                                <div class="border border-ink/10 rounded-lg p-4">
                                    <h4 class="font-semibold text-ink mb-2">Cookies de Marketing</h4>
                                    <p class="text-sm text-ink/70">Personalizan la publicidad y miden la efectividad de campañas (requieren consentimiento).</p>
                                </div>
                            </div>
                            <p class="text-ink/80 leading-relaxed mt-4">
                                Puedes configurar tu navegador para rechazar cookies, pero esto puede afectar algunas funcionalidades del sitio.
                            </p>
                        </section>

                        <section id="terceros" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">10. Comunicación de Datos a Terceros</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Podemos compartir tus datos personales con terceros en las siguientes circunstancias:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-ink/80 mb-4">
                                <li><strong>Proveedores de servicios:</strong> Empresas de envío, procesadores de pago y plataformas tecnológicas que nos ayudan a operar nuestro negocio.</li>
                                <li><strong>Autoridades legales:</strong> Cuando sea requerido por ley o para proteger nuestros derechos legales.</li>
                                <li><strong>Socios comerciales:</strong> Solo con tu consentimiento explícito.</li>
                            </ul>
                            <p class="text-ink/80 leading-relaxed">
                                Todos nuestros proveedores están obligados contractualmente a proteger tus datos y solo pueden usarlos para los fines específicos que les encomendamos.
                            </p>
                        </section>

                        <section id="menores" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">11. Menores de Edad</h2>
                            <p class="text-ink/80 leading-relaxed">
                                Nuestros servicios están dirigidos a personas mayores de 18 años. No recopilamos intencionalmente datos de menores de edad sin el consentimiento de sus padres o tutores legales. Si identificamos que hemos recopilado datos de un menor sin autorización, eliminaremos dicha información de manera inmediata.
                            </p>
                        </section>

                        <section id="cambios" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">12. Cambios a esta Política</h2>
                            <p class="text-ink/80 leading-relaxed">
                                Nos reservamos el derecho de modificar esta Política de Privacidad en cualquier momento. Los cambios serán publicados en esta página con la fecha de actualización correspondiente. Te recomendamos revisar periódicamente esta política. El uso continuado de nuestros servicios después de la publicación de cambios constituye tu aceptación de los mismos.
                            </p>
                        </section>

                        <section id="contacto" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">13. Contacto</h2>
                            <p class="text-ink/80 leading-relaxed mb-6">
                                Si tienes preguntas sobre esta Política de Privacidad o sobre el tratamiento de tus datos personales, puedes contactarnos:
                            </p>
                            <div class="bg-gradient-to-br from-primary/5 to-lilac/5 rounded-lg p-6 border border-primary/20">
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <p class="text-sm text-ink/60 mb-1">Email</p>
                                        <a href="mailto:{{ \App\Models\SiteSetting::getValue('contact.email', config('flores.email')) }}" class="text-primary hover:underline font-medium">
                                            {{ \App\Models\SiteSetting::getValue('contact.email', config('flores.email')) }}
                                        </a>
                                    </div>
                                    <div>
                                        <p class="text-sm text-ink/60 mb-1">Teléfono</p>
                                        <a href="tel:{{ \App\Models\SiteSetting::getValue('contact.phone', config('flores.phone')) }}" class="text-primary hover:underline font-medium">
                                            {{ \App\Models\SiteSetting::getValue('contact.phone_display', config('flores.phone_display')) }}
                                        </a>
                                    </div>
                                    <div class="md:col-span-2">
                                        <p class="text-sm text-ink/60 mb-1">Dirección</p>
                                        <p class="text-ink/80">{{ \App\Models\SiteSetting::getValue('contact.address', config('flores.address')) }}</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="mt-12 pt-8 border-t border-ink/10">
                            <p class="text-sm text-ink/60 text-center">
                                Esta Política de Privacidad cumple con la <strong>Ley 19.628 sobre Protección de la Vida Privada</strong> y la <strong>Ley 19.496 sobre Protección de los Derechos de los Consumidores</strong> de Chile.
                            </p>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<style>
    html {
        scroll-behavior: smooth;
    }

    .legal-page,
    .legal-page .prose {
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: #15131a;
    }

    .legal-hero {
        position: relative;
        background: linear-gradient(135deg, #5e3452 0%, #3d2640 50%, #221a28 100%);
        color: #ffffff;
        overflow: hidden;
    }

    .legal-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(800px circle at 50% 0%, rgba(255,255,255,0.12), transparent 60%);
        opacity: 0.8;
    }

    .legal-hero__inner {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .legal-hero__badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1.2rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        font-size: 0.75rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        font-weight: 500;
        color: rgba(255,255,255,0.9);
    }

    .legal-hero__title {
        margin-top: 1.5rem;
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 700;
        letter-spacing: -0.02em;
        color: #ffffff;
    }

    .legal-hero__date {
        margin-top: 0.75rem;
        font-size: 0.95rem;
        color: rgba(255,255,255,0.65);
        font-weight: 400;
    }

    .legal-hero__description {
        margin-top: 1.5rem;
        font-size: 1.1rem;
        line-height: 1.7;
        color: rgba(255,255,255,0.85);
        max-width: 42rem;
        margin-left: auto;
        margin-right: auto;
    }

    .legal-hero__summary {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255,255,255,0.15);
    }

    .legal-hero__summary-title {
        font-size: 0.85rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.6);
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .legal-hero__summary-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: inline-flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        justify-content: center;
        font-size: 0.95rem;
        color: rgba(255,255,255,0.8);
    }

    .legal-hero__summary-list li {
        position: relative;
        padding-left: 1rem;
    }

    .legal-hero__summary-list li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: rgba(255,255,255,0.6);
    }

    .legal-nav {
        background: #ffffff;
        border-radius: 1.25rem;
        padding: 1.25rem 1.1rem;
        box-shadow: 0 20px 45px rgba(21, 19, 26, 0.08);
        border: 1px solid rgba(21, 19, 26, 0.08);
    }

    .legal-nav p {
        margin-bottom: 0.5rem;
        font-size: 0.75rem;
        letter-spacing: 0.2em;
    }

    .legal-nav a {
        border-radius: 0.75rem;
        padding: 0.55rem 0.75rem;
        display: block;
        color: rgba(21, 19, 26, 0.7);
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .legal-nav a:hover {
        color: #744568;
        background: rgba(116, 69, 104, 0.08);
    }

    .legal-content {
        background: #ffffff;
        border-radius: 1.5rem;
        border: 1px solid rgba(21, 19, 26, 0.08);
        box-shadow: 0 24px 60px rgba(21, 19, 26, 0.08);
        overflow: hidden;
    }

    .legal-mobile-index {
        background: #ffffff;
        border-radius: 1rem;
        border: 1px solid rgba(21, 19, 26, 0.08);
        box-shadow: 0 12px 30px rgba(21, 19, 26, 0.08);
    }

    .legal-mobile-index summary {
        padding: 0.85rem 1.1rem;
    }

    .prose h2 {
        color: #15131a;
        margin-top: 2rem;
        margin-bottom: 1rem;
        scroll-margin-top: 6rem;
        letter-spacing: -0.01em;
    }

    .prose h3 {
        color: #15131a;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .prose h4 {
        color: #15131a;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    .prose p,
    .prose ul {
        margin-bottom: 1rem;
    }

    .prose strong {
        color: #15131a;
    }

    details summary::-webkit-details-marker {
        display: none;
    }

    details[open] summary svg {
        transform: rotate(180deg);
    }
</style>
@endsection
