@php
    $title = 'Flores a domicilio en Valdivia | Flores D&D';
    $metaDescription = 'Flores a domicilio en Valdivia y alrededores. Ramos, arreglos y eventos con entrega rápida, atención personalizada y flores frescas.';
    $ogImage = asset('images/og-default.svg');
@endphp

@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-secondary/20 via-white to-white">
    <section class="py-16 lg:py-20">
        <div class="container-custom">
            <div class="max-w-4xl">
                <p class="text-primary text-xs uppercase tracking-[0.25em] mb-3">Valdivia y alrededores</p>
                <h1 class="font-display text-4xl lg:text-5xl text-dark mb-4">Flores a domicilio en Valdivia</h1>
                <p class="text-dark/70 text-lg mb-6">
                    Ramos y arreglos florales artesanales con entrega a domicilio en Valdivia. Flores frescas, diseño personalizado y atención rápida para sorprender.
                </p>
                <div class="flex flex-wrap gap-3 text-sm text-dark/70">
                    <span class="px-3 py-1.5 rounded-full bg-secondary/40">Entrega rápida</span>
                    <span class="px-3 py-1.5 rounded-full bg-secondary/40">Arreglos por ocasión</span>
                    <span class="px-3 py-1.5 rounded-full bg-secondary/40">Atención personalizada</span>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 lg:py-12">
        <div class="container-custom grid gap-8 lg:grid-cols-3">
            <div class="bg-white rounded-2xl shadow-card p-6">
                <h2 class="font-medium text-dark mb-2">Zonas de cobertura</h2>
                <p class="text-dark/60 text-sm">Valdivia y sectores cercanos. Coordinamos entregas en comunas aledañas según disponibilidad.</p>
            </div>
            <div class="bg-white rounded-2xl shadow-card p-6">
                <h2 class="font-medium text-dark mb-2">Tipos de arreglos</h2>
                <p class="text-dark/60 text-sm">Ramos, arreglos florales, centros de mesa, condolencias y eventos. Personalizamos colores y estilo.</p>
            </div>
            <div class="bg-white rounded-2xl shadow-card p-6">
                <h2 class="font-medium text-dark mb-2">Eventos</h2>
                <p class="text-dark/60 text-sm">Decoración floral para matrimonios, aniversarios, corporativos y celebraciones especiales.</p>
            </div>
        </div>
    </section>

    <section class="py-14 lg:py-16">
        <div class="container-custom grid gap-10 lg:grid-cols-2 items-center">
            <div>
                <h2 class="font-display text-3xl text-dark mb-4">Entrega confiable en Valdivia</h2>
                <ul class="space-y-3 text-dark/70">
                    <li>Entrega coordinada con el destinatario si es necesario.</li>
                    <li>Opciones de tarjeta personalizada y mensaje.</li>
                    <li>Seguimiento cercano para eventos y pedidos grandes.</li>
                </ul>
            </div>
            <div class="bg-white rounded-2xl shadow-card p-6">
                <h3 class="font-medium text-dark mb-3">Respuesta rápida</h3>
                <p class="text-dark/60 text-sm mb-4">
                    Sí, realizamos envíos a domicilio en Valdivia. Los tiempos y costos dependen de la zona y el horario de compra.
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
                    <summary class="font-medium text-dark cursor-pointer">¿Entregan el mismo día en Valdivia?</summary>
                    <p class="text-dark/60 text-sm mt-3">Sí, según disponibilidad y horario. Recomendamos pedir con anticipación.</p>
                </details>
                <details class="bg-white rounded-xl p-5 shadow-card">
                    <summary class="font-medium text-dark cursor-pointer">¿En qué sectores de Valdivia entregan?</summary>
                    <p class="text-dark/60 text-sm mt-3">Cubrimos Valdivia y sectores cercanos. Confirmamos la zona al momento de la compra.</p>
                </details>
                <details class="bg-white rounded-xl p-5 shadow-card">
                    <summary class="font-medium text-dark cursor-pointer">¿Qué zonas cubren?</summary>
                    <p class="text-dark/60 text-sm mt-3">Valdivia y alrededores. Para sectores más alejados coordinamos costos y tiempos.</p>
                </details>
                <details class="bg-white rounded-xl p-5 shadow-card">
                    <summary class="font-medium text-dark cursor-pointer">¿Qué tipos de arreglos ofrecen?</summary>
                    <p class="text-dark/60 text-sm mt-3">Ramos, arreglos florales, condolencias, centros de mesa y decoraciones para eventos.</p>
                </details>
                <details class="bg-white rounded-xl p-5 shadow-card">
                    <summary class="font-medium text-dark cursor-pointer">¿Puedo personalizar el ramo?</summary>
                    <p class="text-dark/60 text-sm mt-3">Sí. Podemos ajustar colores, tamaño y tipo de flores según disponibilidad.</p>
                </details>
                <details class="bg-white rounded-xl p-5 shadow-card">
                    <summary class="font-medium text-dark cursor-pointer">¿Tienen arreglos especiales en fechas importantes?</summary>
                    <p class="text-dark/60 text-sm mt-3">Sí, ofrecemos diseños especiales para San Valentín, Día de la Madre y aniversarios.</p>
                </details>
            </div>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('coleccion') }}" class="inline-flex items-center gap-2 bg-primary text-white px-7 py-3.5 rounded-full font-medium hover:bg-primary-light transition-colors">
                    Ver colección en Valdivia
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
            "name": "Flores a domicilio en Valdivia",
            "item": "{{ route('flores.valdivia') }}"
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
            "name": "¿Entregan el mismo día en Valdivia?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Sí, según disponibilidad y horario. Recomendamos pedir con anticipación."
            }
        },
        {
            "@@type": "Question",
            "name": "¿En qué sectores de Valdivia entregan?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Cubrimos Valdivia y sectores cercanos. Confirmamos la zona al momento de la compra."
            }
        },
        {
            "@@type": "Question",
            "name": "¿Qué zonas cubren?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Valdivia y alrededores. Para sectores más alejados coordinamos costos y tiempos."
            }
        },
        {
            "@@type": "Question",
            "name": "¿Qué tipos de arreglos ofrecen?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Ramos, arreglos florales, condolencias, centros de mesa y decoraciones para eventos."
            }
        },
        {
            "@@type": "Question",
            "name": "¿Puedo personalizar el ramo?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Sí. Podemos ajustar colores, tamaño y tipo de flores según disponibilidad."
            }
        },
        {
            "@@type": "Question",
            "name": "¿Tienen arreglos especiales en fechas importantes?",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "Sí, ofrecemos diseños especiales para San Valentín, Día de la Madre y aniversarios."
            }
        }
    ]
}
</script>
@endsection
