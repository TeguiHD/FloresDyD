@extends('layouts.app')

@section('title', 'Política de Envíos | Flores DyD')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#f8f6f2] via-white to-white legal-page">
    {{-- Header --}}
    <div class="legal-hero">
        <div class="legal-hero__inner max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <span class="legal-hero__badge">Política comercial</span>
            <h1 class="legal-hero__title">Política de Envíos</h1>
            <p class="legal-hero__date">Última actualización: Febrero 2026</p>
            <p class="legal-hero__description">Información detallada sobre nuestros servicios de despacho, zonas de cobertura, plazos de entrega y costos, cumpliendo con los estándares de la Ley 19.496 de Protección del Consumidor.</p>
            
            <div class="legal-hero__summary">
                <p class="legal-hero__summary-title">En esta política:</p>
                <ul class="legal-hero__summary-list">
                    <li>Zonas de cobertura</li>
                    <li>Plazos estimados</li>
                    <li>Costos por destino</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="lg:grid lg:grid-cols-12 lg:gap-12">
            {{-- Sidebar Index - Desktop --}}
            <aside class="hidden lg:block lg:col-span-3">
                <nav class="legal-nav sticky top-24" x-data="{ activeSection: '' }" @scroll.window="
                    let sections = ['introduccion', 'zonas', 'plazos', 'costos', 'contacto'];
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
                    <a href="#introduccion" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'introduccion' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Introducción
                    </a>
                    <a href="#zonas" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'zonas' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Zonas de Cobertura
                    </a>
                    <a href="#plazos" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'plazos' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Plazos de Entrega
                    </a>
                    <a href="#costos" class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors" :class="activeSection === 'costos' ? 'bg-primary/10 text-primary' : 'text-ink/70 hover:bg-mist hover:text-primary'">
                        Costos de Envío
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
                        <a href="#introduccion" class="block py-2 text-sm text-ink/70 hover:text-primary">Introducción</a>
                        <a href="#zonas" class="block py-2 text-sm text-ink/70 hover:text-primary">Zonas de Cobertura</a>
                        <a href="#plazos" class="block py-2 text-sm text-ink/70 hover:text-primary">Plazos de Entrega</a>
                        <a href="#costos" class="block py-2 text-sm text-ink/70 hover:text-primary">Costos de Envío</a>
                        <a href="#contacto" class="block py-2 text-sm text-ink/70 hover:text-primary">Contacto</a>
                    </nav>
                </details>
            </div>

            {{-- Main Content --}}
            <main class="lg:col-span-9">
                <div class="legal-content">
                    <div class="prose prose-lg max-w-none p-6 md:p-8 lg:p-12">
                        
                        <section id="introduccion" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">1. Introducción</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                En Flores DyD nos comprometemos a entregar tus pedidos de manera oportuna y segura. Esta política detalla nuestros procesos de envío, plazos, costos y condiciones para garantizar la mejor experiencia posible.
                            </p>
                            <div class="bg-primary/5 border border-primary/20 rounded-lg p-5">
                                <p class="text-ink/80">
                                    <strong>Compromiso de Calidad:</strong> Tratamos cada pedido con el máximo cuidado, asegurando que nuestros productos lleguen frescos y en perfectas condiciones. Trabajamos con flores de la más alta calidad y floristas profesionales.
                                </p>
                            </div>
                        </section>

                        <section id="zonas" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">2. Zonas de Cobertura</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Realizamos despachos a nivel nacional en Chile con diferentes modalidades según la ubicación.
                            </p>
                            
                            <div class="space-y-4">
                                <div class="bg-gradient-to-r from-sage/10 to-sage/5 rounded-lg p-6 border-l-4 border-sage">
                                    <h3 class="text-xl font-semibold text-ink mb-3 flex items-center">
                                        <span class="w-10 h-10 bg-sage text-white rounded-full flex items-center justify-center mr-3">1</span>
                                        Zona Metropolitana
                                    </h3>
                                    <p class="text-ink/70 mb-3">Cobertura completa en Región Metropolitana, incluyendo todas las comunas de Santiago.</p>
                                    <ul class="list-disc list-inside space-y-1 text-sm text-ink/70">
                                        <li>Entrega el mismo día disponible</li>
                                        <li>Entregas programadas con rango horario</li>
                                        <li>Cobertura urbana y sectores periféricos</li>
                                    </ul>
                                </div>

                                <div class="bg-gradient-to-r from-primary/10 to-primary/5 rounded-lg p-6 border-l-4 border-primary">
                                    <h3 class="text-xl font-semibold text-ink mb-3 flex items-center">
                                        <span class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center mr-3">2</span>
                                        Regiones Principales
                                    </h3>
                                    <p class="text-ink/70 mb-3">Valparaíso, Viña del Mar, Concepción, La Serena, Temuco y ciudades principales.</p>
                                    <ul class="list-disc list-inside space-y-1 text-sm text-ink/70">
                                        <li>Entrega en 1-2 días hábiles</li>
                                        <li>Despacho a través de courier especializado</li>
                                        <li>Seguimiento en tiempo real</li>
                                    </ul>
                                </div>

                                <div class="bg-gradient-to-r from-rose/10 to-rose/5 rounded-lg p-6 border-l-4 border-rose">
                                    <h3 class="text-xl font-semibold text-ink mb-3 flex items-center">
                                        <span class="w-10 h-10 bg-rose text-white rounded-full flex items-center justify-center mr-3">3</span>
                                        Otras Regiones
                                    </h3>
                                    <p class="text-ink/70 mb-3">Resto de ciudades y comunas del territorio nacional.</p>
                                    <ul class="list-disc list-inside space-y-1 text-sm text-ink/70">
                                        <li>Entrega en 2-4 días hábiles</li>
                                        <li>Consulta disponibilidad en el checkout</li>
                                        <li>Coordinación previa para zonas extremas</li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <section id="plazos" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">3. Plazos de Entrega</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Los plazos de entrega dependen de varios factores:
                            </p>
                            
                            <div class="grid md:grid-cols-2 gap-4 mb-6">
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                                    <h4 class="font-semibold text-ink mb-2">Horario de Corte</h4>
                                    <p class="text-sm text-ink/70">Pedidos antes de las 14:00 hrs pueden ser entregados el mismo día (RM).</p>
                                </div>
                                <div class="bg-white border border-ink/10 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow">
                                    <h4 class="font-semibold text-ink mb-2">Días Hábiles</h4>
                                    <p class="text-sm text-ink/70">Lunes a Sábado. Domingos y festivos bajo solicitud especial.</p>
                                </div>
                            </div>

                            <div class="bg-rose/5 border border-rose/20 rounded-lg p-5">
                                <p class="text-ink/80">
                                    <strong>Importante:</strong> Los plazos son estimados y pueden variar por condiciones climáticas adversas o situaciones de fuerza mayor.
                                </p>
                            </div>
                        </section>

                        <section id="costos" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">4. Costos de Envío</h2>
                            <p class="text-ink/80 leading-relaxed mb-4">
                                Los costos de envío se calculan automáticamente según la zona de destino.
                            </p>
                            
                            <div class="bg-gold/10 border border-gold/30 rounded-lg p-4 mb-6">
                                <p class="text-ink/80 text-sm">
                                    <strong>Envío Gratis:</strong> En compras sobre $50.000 en Región Metropolitana.
                                </p>
                            </div>

                            <p class="text-ink/80">Los costos varían según zona y se muestran al momento del checkout.</p>
                        </section>

                        <section id="contacto" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">5. Contacto para Consultas</h2>
                            <p class="text-ink/80 leading-relaxed mb-6">
                                ¿Tienes dudas sobre tu envío?
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
                                </div>
                            </div>
                        </section>

                        <section id="faq-envios" class="scroll-mt-24 mb-12">
                            <h2 class="text-2xl md:text-3xl font-bold text-ink mb-4">6. Preguntas Frecuentes</h2>
                            <div class="space-y-4">
                                <details class="bg-white border border-ink/10 rounded-lg p-5">
                                    <summary class="flex items-center justify-between cursor-pointer font-semibold text-ink">
                                        ¿Entregan el mismo día?
                                        <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </summary>
                                    <p class="text-sm text-ink/70 mt-3">
                                        Sí, sujeto a disponibilidad y horario de compra. Recomendamos pedir con anticipación.
                                    </p>
                                </details>
                                <details class="bg-white border border-ink/10 rounded-lg p-5">
                                    <summary class="flex items-center justify-between cursor-pointer font-semibold text-ink">
                                        ¿Cómo se calculan los costos?
                                        <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </summary>
                                    <p class="text-sm text-ink/70 mt-3">
                                        Los costos dependen de la zona de entrega y se muestran durante el checkout.
                                    </p>
                                </details>
                                <details class="bg-white border border-ink/10 rounded-lg p-5">
                                    <summary class="flex items-center justify-between cursor-pointer font-semibold text-ink">
                                        ¿Qué pasa si no hay nadie en el domicilio?
                                        <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </summary>
                                    <p class="text-sm text-ink/70 mt-3">
                                        Intentamos coordinar con el destinatario o reagendar la entrega cuando sea posible.
                                    </p>
                                </details>
                                <details class="bg-white border border-ink/10 rounded-lg p-5">
                                    <summary class="flex items-center justify-between cursor-pointer font-semibold text-ink">
                                        ¿Puedo pedir entrega en Valdivia o Santiago?
                                        <svg class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </summary>
                                    <p class="text-sm text-ink/70 mt-3">
                                        Sí. Cubrimos Valdivia y Santiago con zonas aledañas según disponibilidad.
                                    </p>
                                </details>
                            </div>
                        </section>

                        <div class="mt-12 pt-8 border-t border-ink/10">
                            <p class="text-sm text-ink/60 text-center">
                                Esta Política de Envíos cumple con la <strong>Ley 19.496 sobre Protección de los Derechos de los Consumidores</strong> de Chile.
                            </p>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {
            "@@type": "Question",
            "name": "¿Entregan el mismo día?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Sí, sujeto a disponibilidad y horario de compra. Recomendamos pedir con anticipación."
            }
        },
        {
            "@@type": "Question",
            "name": "¿Cómo se calculan los costos?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Los costos dependen de la zona de entrega y se muestran durante el checkout."
            }
        },
        {
            "@@type": "Question",
            "name": "¿Qué pasa si no hay nadie en el domicilio?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Intentamos coordinar con el destinatario o reagendar la entrega cuando sea posible."
            }
        },
        {
            "@@type": "Question",
            "name": "¿Puedo pedir entrega en Valdivia o Santiago?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Sí. Cubrimos Valdivia y Santiago con zonas aledañas según disponibilidad."
            }
        }
    ]
}
</script>

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
    
    .prose p {
        margin-bottom: 1rem;
    }
    
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
