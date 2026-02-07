{{-- Checkout Success - Flores D&D --}}
<div class="min-h-dvh bg-secondary/20 flex items-center justify-center py-16">
    @php $whatsappNumber = \App\Models\SiteSetting::getWhatsappNumber(); @endphp
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            {{-- Animación de éxito --}}
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="font-serif text-3xl lg:text-4xl text-dark mb-4">¡Pedido Confirmado!</h1>
            <p class="text-dark/70 text-lg mb-8">
                Gracias por tu compra. Hemos recibido tu pedido y te enviaremos un correo con los detalles.
            </p>

            {{-- Detalles del Pedido --}}
            <div class="bg-white rounded-2xl shadow-card p-8 text-left mb-8">
                <div class="flex items-center justify-between mb-6 pb-6 border-b border-secondary">
                    <div>
                        <p class="text-dark/60 text-sm">Número de pedido</p>
                        <p class="text-xl font-bold text-primary">{{ $order->order_number ?? 'FDD-000001' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-dark/60 text-sm">Total</p>
                        <p class="text-xl font-bold">${{ number_format($order->total ?? 0, 0) }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-dark/60">Estado</span>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">Pendiente de pago</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-dark/60">Fecha de entrega</span>
                        <span>{{ $order->delivery_date?->format('d/m/Y') ?? 'Por confirmar' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-dark/60">Horario</span>
                        <span>{{ $order->delivery_time ?? 'Por confirmar' }}</span>
                    </div>
                </div>

                {{-- Instrucciones de pago --}}
                <div class="mt-6 pt-6 border-t border-secondary">
                    <h3 class="font-medium text-dark mb-3">Instrucciones de pago</h3>
                    <div class="bg-secondary/30 rounded-xl p-4 text-sm">
                        <p class="mb-2"><strong>Banco:</strong> BBVA</p>
                        <p class="mb-2"><strong>Cuenta:</strong> 0123456789</p>
                        <p class="mb-2"><strong>CLABE:</strong> 012345678901234567</p>
                        <p><strong>Referencia:</strong> {{ $order->order_number ?? 'FDD-000001' }}</p>
                    </div>
                    <p class="text-dark/60 text-sm mt-3">
                        Una vez realizado el pago, envíanos tu comprobante por WhatsApp para confirmar tu pedido.
                    </p>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex flex-wrap justify-center gap-4">
                <a 
                    href="{{ route('track.order.code', $order->tracking_code ?? 'test') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition"
                >
                    <x-flux::icon name="magnifying-glass" class="w-5 h-5" />
                    Rastrear pedido
                </a>
                @if($whatsappNumber)
                    <a 
                        href="https://wa.me/{{ $whatsappNumber }}?text=Hola,%20acabo%20de%20realizar%20el%20pedido%20{{ $order->order_number ?? 'FDD-000001' }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-6 py-3 border-2 border-primary text-primary rounded-full hover:bg-primary hover:text-white transition"
                    >
                        Enviar comprobante por WhatsApp
                    </a>
                @endif
            </div>

            <div class="mt-8">
                <a href="{{ route('home') }}" class="text-primary hover:underline">
                    ← Volver al inicio
                </a>
            </div>
        </div>
    </div>
</div>
