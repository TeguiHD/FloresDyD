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
                        @if(!empty($order->tracking_code))
                            <p class="text-xs text-dark/60 mt-1">Código de rastreo: {{ $order->tracking_code }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-dark/60 text-sm">Total</p>
                        <p class="text-xl font-bold">${{ number_format($order->total ?? 0, 0) }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-dark/60">Estado</span>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">{{ $order->status_label ?? 'Pendiente de pago' }}</span>
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
                    
                    {{-- Transferencia Bancaria --}}
                    <div class="bg-secondary/30 rounded-xl p-4 text-sm mb-3">
                        <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2">Transferencia Bancaria</p>
                        <p class="mb-1"><strong>Titular:</strong> {{ config('flores.payment.bank_transfer.titular') }}</p>
                        <p class="mb-1"><strong>RUT:</strong> {{ config('flores.payment.bank_transfer.rut') }}</p>
                        <p class="mb-1"><strong>Banco:</strong> {{ config('flores.payment.bank_transfer.banco') }}</p>
                        <p class="mb-1"><strong>Tipo de cuenta:</strong> {{ config('flores.payment.bank_transfer.tipo_cuenta') }}</p>
                        <p class="mb-1"><strong>N° de cuenta:</strong> {{ config('flores.payment.bank_transfer.numero_cuenta') }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ config('flores.payment.bank_transfer.email') }}</p>
                        <p class="mt-2"><strong>Referencia:</strong> {{ $order->order_number ?? 'FDD-000001' }}</p>
                    </div>

                    {{-- Mercado Pago --}}
                    <div class="bg-secondary/30 rounded-xl p-4 text-sm">
                        <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2">Mercado Pago</p>
                        <p class="mb-1"><strong>Titular:</strong> {{ config('flores.payment.mercado_pago.titular') }}</p>
                        <p class="mb-1"><strong>RUT:</strong> {{ config('flores.payment.mercado_pago.rut') }}</p>
                        <p class="mb-1"><strong>Banco:</strong> {{ config('flores.payment.mercado_pago.banco') }}</p>
                        <p class="mb-1"><strong>Tipo de cuenta:</strong> {{ config('flores.payment.mercado_pago.tipo_cuenta') }}</p>
                        <p class="mb-1"><strong>N° de cuenta:</strong> {{ config('flores.payment.mercado_pago.numero_cuenta') }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ config('flores.payment.mercado_pago.email') }}</p>
                        <p class="mt-2"><strong>Referencia:</strong> {{ $order->order_number ?? 'FDD-000001' }}</p>
                    </div>

                    <p class="text-dark/60 text-sm mt-3">
                        Una vez realizado el pago, puedes subir tu comprobante aquí o enviarlo por WhatsApp.
                    </p>
                </div>
            </div>

            {{-- Subir comprobante --}}
            <div class="bg-white rounded-2xl shadow-card p-8 text-left mb-8">
                <h3 class="font-medium text-dark mb-4">Sube tu comprobante aquí</h3>

                @if($proofUploaded)
                    <div class="mb-4 text-green-700 bg-green-50 border border-green-200 rounded-lg p-3 text-sm">
                        Recibimos tu comprobante. Revisaremos y confirmaremos tu pago pronto.
                    </div>
                @endif

                <form wire:submit.prevent="uploadProof" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Archivo (JPG, PNG o PDF) *</label>
                        <input type="file" wire:model="proofFile" class="w-full text-sm">
                        @error('proofFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Monto declarado</label>
                            <input type="number" wire:model="declaredAmount" class="w-full px-3 py-2 border border-secondary rounded-lg text-sm">
                            @error('declaredAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Fecha de transferencia</label>
                            <input type="date" wire:model="transactionDate" class="w-full px-3 py-2 border border-secondary rounded-lg text-sm">
                            @error('transactionDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Código de transacción</label>
                            <input type="text" wire:model="transactionCode" class="w-full px-3 py-2 border border-secondary rounded-lg text-sm">
                            @error('transactionCode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Banco de origen</label>
                            <input type="text" wire:model="bankOrigin" class="w-full px-3 py-2 border border-secondary rounded-lg text-sm">
                            @error('bankOrigin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition"
                    >
                        Enviar comprobante
                    </button>
                </form>
            </div>

            {{-- Acciones --}}
            <div class="flex flex-wrap justify-center gap-4">
                <a 
                    href="{{ route('track.order.code', $order->tracking_code) }}"
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
