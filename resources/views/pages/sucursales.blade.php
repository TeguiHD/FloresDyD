@extends('layouts.app')

@section('title', 'Nuestras Sucursales - Flores D&D')

@section('content')
<div class="min-h-dvh bg-secondary/20">
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-primary/10 to-secondary/30 py-16 lg:py-24">
        <div class="container mx-auto px-4 text-center">
            <h1 class="font-serif text-4xl lg:text-5xl text-dark mb-4">Nuestras Sucursales</h1>
            <p class="text-dark/70 text-lg max-w-2xl mx-auto">
                Visítanos en cualquiera de nuestras sucursales. Realizamos envíos a domicilio en Santiago y Valdivia.
            </p>
        </div>
    </section>

    {{-- Sucursales --}}
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                @foreach(config('flores.sucursales', []) as $sucursal)
                    <div class="bg-white rounded-2xl shadow-card p-8 hover:shadow-lg transition">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                </svg>
                            </div>
                            <h2 class="font-serif text-xl text-dark">{{ $sucursal['nombre'] }}</h2>
                        </div>
                        <div class="space-y-2 text-dark/70">
                            <p><strong>Dirección:</strong> {{ $sucursal['direccion'] }}</p>
                            <p><strong>Comuna:</strong> {{ $sucursal['comuna'] }}</p>
                            <p><strong>Ciudad:</strong> {{ $sucursal['ciudad'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Zonas de envío --}}
            <div class="max-w-4xl mx-auto mt-16">
                <div class="bg-white rounded-2xl shadow-card p-8">
                    <h2 class="font-serif text-2xl text-dark mb-6 text-center">Zonas de Envío</h2>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                                </svg>
                            </div>
                            <h3 class="font-medium text-lg text-dark mb-2">Santiago</h3>
                            <p class="text-dark/60">Envío a domicilio en toda la Región Metropolitana</p>
                        </div>
                        <div class="text-center">
                            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                                </svg>
                            </div>
                            <h3 class="font-medium text-lg text-dark mb-2">Valdivia</h3>
                            <p class="text-dark/60">Envío a domicilio en Valdivia y alrededores</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datos de pago --}}
            <div class="max-w-4xl mx-auto mt-16">
                <div class="bg-white rounded-2xl shadow-card p-8">
                    <h2 class="font-serif text-2xl text-dark mb-6 text-center">Medios de Pago</h2>
                    <div class="grid md:grid-cols-2 gap-8">
                        {{-- Transferencia --}}
                        <div class="border border-secondary rounded-xl p-6">
                            <h3 class="text-xs font-semibold text-primary uppercase tracking-wide mb-4">Transferencia Bancaria</h3>
                            <div class="space-y-2 text-sm text-dark/80">
                                <p><strong>Titular:</strong> {{ config('flores.payment.bank_transfer.titular') }}</p>
                                <p><strong>RUT:</strong> {{ config('flores.payment.bank_transfer.rut') }}</p>
                                <p><strong>Banco:</strong> {{ config('flores.payment.bank_transfer.banco') }}</p>
                                <p><strong>Tipo:</strong> {{ config('flores.payment.bank_transfer.tipo_cuenta') }}</p>
                                <p><strong>N° cuenta:</strong> {{ config('flores.payment.bank_transfer.numero_cuenta') }}</p>
                                <p><strong>Email:</strong> {{ config('flores.payment.bank_transfer.email') }}</p>
                            </div>
                        </div>

                        {{-- Mercado Pago --}}
                        <div class="border border-secondary rounded-xl p-6">
                            <h3 class="text-xs font-semibold text-primary uppercase tracking-wide mb-4">Mercado Pago</h3>
                            <div class="space-y-2 text-sm text-dark/80">
                                <p><strong>Titular:</strong> {{ config('flores.payment.mercado_pago.titular') }}</p>
                                <p><strong>RUT:</strong> {{ config('flores.payment.mercado_pago.rut') }}</p>
                                <p><strong>Banco:</strong> {{ config('flores.payment.mercado_pago.banco') }}</p>
                                <p><strong>Tipo:</strong> {{ config('flores.payment.mercado_pago.tipo_cuenta') }}</p>
                                <p><strong>N° cuenta:</strong> {{ config('flores.payment.mercado_pago.numero_cuenta') }}</p>
                                <p><strong>Email:</strong> {{ config('flores.payment.mercado_pago.email') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
