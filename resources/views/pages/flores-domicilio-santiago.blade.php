@php
    $title = 'Flores a domicilio en Santiago | Flores D&D';
    $metaDescription = 'Flores a domicilio en Santiago y alrededores. Ramos, arreglos y eventos con entrega rápida, atención personalizada y flores frescas.';
    $ogImage = asset('images/og-default.svg');
@endphp

@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-secondary/20 via-white to-white">
    <section class="py-16 lg:py-20">
        <div class="container-custom">
            <div class="max-w-4xl">
                <p class="text-primary text-xs uppercase tracking-[0.25em] mb-3">Santiago y alrededores</p>
                <h1 class="font-display text-4xl lg:text-5xl text-dark mb-4">Flores a domicilio en Santiago</h1>
                <p class="text-dark/70 text-lg mb-6">
                    Enviamos flores a domicilio en Santiago con diseño artesanal y entrega coordinada. Ramos, arreglos y eventos para cada ocasión.
                </p>
                <div class="flex flex-wrap gap-3 text-sm text-dark/70">
                    <span class="px-3 py-1.5 rounded-full bg-secondary/40">Entrega coordinada</span>
                    <span class="px-3 py-1.5 rounded-full bg-secondary/40">Ramos y eventos</span>
                    <span class="px-3 py-1.5 rounded-full bg-secondary/40">Flores frescas</span>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 lg:py-12">
        <div class="container-custom grid gap-8 lg:grid-cols-3">
            <div class="bg-white rounded-2xl shadow-card p-6">
                <h2 class="font-medium text-dark mb-2">Zonas de cobertura</h2>
                <p class="text-dark/60 text-sm">Santiago y sectores cercanos. Coordinamos entregas con anticipación para mayor precisión.</p>
            </div>
            <div class="bg-white rounded-2xl shadow-card p-6">
                <h2 class="font-medium text-dark mb-2">Arreglos por ocasión</h2>
                <p class="text-dark/60 text-sm">Cumpleaños, aniversarios, condolencias, novios y eventos corporativos.</p>
            </div>
            <div class="bg-white rounded-2xl shadow-card p-6">
                <h2 class="font-medium text-dark mb-2">Eventos en Santiago</h2>
                <p class="text-dark/60 text-sm">Decoración floral integral para matrimonios y celebraciones especiales.</p>
            </div>
        </div>
    </section>

    <section class="py-14 lg:py-16">
        <div class="container-custom grid gap-10 lg:grid-cols-2 items-center">
            <div>
                <h2 class="font-display text-3xl text-dark mb-4">Entrega y calidad garantizada</h2>
                <ul class="space-y-3 text-dark/70">
                    <li>Coordinación de horario con el destinatario.</li>
                    <li>Mensajes personalizados y presentación cuidada.</li>
                    <li>Soporte directo para pedidos corporativos o eventos.</li>
                </ul>
            </div>
            <div class="bg-white rounded-2xl shadow-card p-6">
                <h3 class="font-medium text-dark mb-3">Respuesta rápida</h3>
                <p class="text-dark/60 text-sm mb-4">
                    Sí, hacemos envíos a domicilio en Santiago. Los tiempos dependen de la zona y horario de compra.
                </p>
                <a href="{{ route('politica-envios') }}" class="text-primary text-sm font-medium hover:underline">
                    Ver política de envíos
                </a>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white/60 border-t border-secondary/30">
        <div class="container-custom">
            <h2 class="font-display text-3xl text-dark mb-6">Preguntas frecuentes</h2>
            <div class="grid gap-4 lg:grid-cols-2">
                <details class="bg-white rounded-xl p-5 shadow-card">
                    <summary class="font-medium text-dark cursor-pointer">¿Entregan el mismo día en Santiago?</summary>
                    <p class="text-dark/60 text-sm mt-3">Sí, según disponibilidad y horario. Te recomendamos pedir con anticipación.</p>
                </details>
                <details class="bg-white rounded-xl p-5 shadow-card">
                    <summary class="font-medium text-dark cursor-pointer">¿En qué comunas entregan?</summary>
                    <p class="text-dark/60 text-sm mt-3">Cubrimos Santiago y alrededores. Confirmamos la comuna durante la compra.</p>
                </details>
                <details class="bg-white rounded-xl p-5 shadow-card">
                    <summary class="font-medium text-dark cursor-pointer">¿Hacen arreglos para eventos?</summary>
                    <p class="text-dark/60 text-sm mt-3">Sí, realizamos decoración y arreglos florales para matrimonios y eventos.</p>
                </details>
                <details class="bg-white rounded-xl p-5 shadow-card">
                    <summary class="font-medium text-dark cursor-pointer">¿Puedo elegir colores específicos?</summary>
                    <p class="text-dark/60 text-sm mt-3">Sí. Puedes solicitar paleta de colores o estilo, sujeto a disponibilidad.</p>
                </details>
                <details class="bg-white rounded-xl p-5 shadow-card">
                    <summary class="font-medium text-dark cursor-pointer">¿Tienen arreglos especiales en fechas importantes?</summary>
                    <p class="text-dark/60 text-sm mt-3">Sí, preparamos diseños especiales para San Valentín, Día de la Madre y aniversarios.</p>
                </details>
            </div>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('coleccion') }}" class="inline-flex items-center gap-2 bg-primary text-white px-7 py-3.5 rounded-full font-medium hover:bg-primary-light transition-colors">
                    Ver colección en Santiago
                </a>
                <a href="{{ route('contacto') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark transition-colors text-sm font-medium">
                    Coordinar entrega hoy
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
</div>

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Inicio",
            "item": "{{ route('home') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "Flores a domicilio en Santiago",
            "item": "{{ route('flores.santiago') }}"
        }
    ]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {
            "@@type": "Question",
            "name": "¿Entregan el mismo día en Santiago?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Sí, según disponibilidad y horario. Te recomendamos pedir con anticipación."
            }
        },
        {
            "@@type": "Question",
            "name": "¿En qué comunas entregan?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Cubrimos Santiago y alrededores. Confirmamos la comuna durante la compra."
            }
        },
        {
            "@@type": "Question",
            "name": "¿Hacen arreglos para eventos?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Sí, realizamos decoración y arreglos florales para matrimonios y eventos."
            }
        },
        {
            "@@type": "Question",
            "name": "¿Puedo elegir colores específicos?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Sí. Puedes solicitar paleta de colores o estilo, sujeto a disponibilidad."
            }
        },
        {
            "@@type": "Question",
            "name": "¿Tienen arreglos especiales en fechas importantes?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Sí, preparamos diseños especiales para San Valentín, Día de la Madre y aniversarios."
            }
        }
    ]
}
</script>
@endsection
