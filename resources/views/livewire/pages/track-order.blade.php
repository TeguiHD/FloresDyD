{{-- Rastrear Pedido - Flores D&D --}}
<div class="min-h-dvh bg-secondary/20">
    @php $whatsappNumber = \App\Models\SiteSetting::getWhatsappNumber(); @endphp
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="font-serif text-3xl lg:text-4xl text-primary mb-4">Rastrear Pedido</h1>
                <p class="text-dark/70">Ingresa tu número de pedido o código de rastreo</p>
            </div>

            {{-- Formulario de búsqueda --}}
            <form wire:submit="search" class="bg-white rounded-2xl shadow-card p-6 mb-8">
                <div class="flex gap-3">
                    <input 
                        type="text"
                        wire:model="trackingCode"
                        placeholder="Ej: FDD-123456"
                        class="flex-1 px-4 py-3 border border-secondary rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    >
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition"
                    >
                        Buscar
                    </button>
                </div>
                @error('trackingCode') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
            </form>

            {{-- Resultado --}}
            @if($searched)
                @if($order)
                    <div class="bg-white rounded-2xl shadow-card p-6 lg:p-8">
                        {{-- Info del pedido --}}
                        <div class="flex items-center justify-between mb-6 pb-6 border-b border-secondary">
                            <div>
                                <p class="text-dark/60 text-sm">Pedido</p>
                                <p class="text-xl font-bold text-primary">{{ $order->order_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-dark/60 text-sm">Total</p>
                                <p class="text-xl font-bold">${{ number_format($order->total, 0) }}</p>
                            </div>
                        </div>

                        {{-- Timeline de estado --}}
                        <div class="mb-8">
                            <h3 class="font-medium text-dark mb-4">Estado del pedido</h3>
                            <div class="space-y-4">
                                @php
                                    $currentStatus = $order->status ?? 'pending';
                                    $statusOrder = array_keys($statusSteps);
                                    $currentIndex = array_search($currentStatus, $statusOrder);
                                @endphp

                                @foreach($statusSteps as $status => $step)
                                    @php
                                        $stepIndex = array_search($status, $statusOrder);
                                        $isCompleted = $stepIndex < $currentIndex;
                                        $isCurrent = $status === $currentStatus;
                                    @endphp
                                    <div class="flex gap-4">
                                        <div class="flex flex-col items-center">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $isCompleted || $isCurrent ? 'bg-primary text-white' : 'bg-secondary text-dark/40' }}">
                                                @if($isCompleted)
                                                    <x-flux::icon name="check" class="w-5 h-5" />
                                                @else
                                                    <x-flux::icon :name="$step['icon']" class="w-5 h-5" />
                                                @endif
                                            </div>
                                            @if(!$loop->last)
                                                <div class="w-0.5 h-8 {{ $isCompleted ? 'bg-primary' : 'bg-secondary' }}"></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 pb-4">
                                            <p class="font-medium {{ $isCurrent ? 'text-primary' : 'text-dark' }}">{{ $step['label'] }}</p>
                                            <p class="text-dark/60 text-sm">{{ $step['description'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Detalles --}}
                        <div class="bg-secondary/30 rounded-xl p-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-dark/60">Fecha de entrega</span>
                                <span>{{ $order->delivery_date?->format('d/m/Y') ?? 'Por confirmar' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-dark/60">Horario</span>
                                <span>{{ $order->delivery_time ?? 'Por confirmar' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-dark/60">Método de entrega</span>
                                <span>{{ $order->delivery_method === 'delivery' ? 'A domicilio' : 'Recoger en tienda' }}</span>
                            </div>
                        </div>

                        {{-- Contacto --}}
                        @if($whatsappNumber)
                            <div class="mt-6 text-center">
                                <p class="text-dark/60 text-sm mb-3">¿Tienes preguntas sobre tu pedido?</p>
                                <a 
                                    href="https://wa.me/{{ $whatsappNumber }}?text=Hola,%20tengo%20una%20pregunta%20sobre%20mi%20pedido%20{{ $order->order_number }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 text-primary hover:underline"
                                >
                                    Contáctanos por WhatsApp
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- No encontrado --}}
                    <div class="bg-white rounded-2xl shadow-card p-8 text-center">
                        <div class="flex items-center justify-center mb-4">
                            <svg class="w-12 h-12 text-primary/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7l9-4 9 4-9 4-9-4z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10l9 4 9-4V7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11v10"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-dark mb-2">Pedido no encontrado</h3>
                        <p class="text-dark/60 mb-6">{{ $errorMessage }}</p>
                        @if($whatsappNumber)
                            <a 
                                href="https://wa.me/{{ $whatsappNumber }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition"
                            >
                                Contactar soporte
                            </a>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
