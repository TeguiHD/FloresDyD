<div class="min-h-dvh bg-secondary/10 py-10">
    <div class="container-custom grid lg:grid-cols-[260px_1fr] gap-6">
        @include('livewire.account.partials.nav')

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-card p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-sm text-dark/60">Pedido</p>
                        <h1 class="font-display text-2xl text-dark">{{ $order->order_number }}</h1>
                        <p class="text-xs text-dark/50">Fecha: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        @if(!empty($order->tracking_code))
                            <p class="text-xs text-dark/50">Código de rastreo: {{ $order->tracking_code }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-dark/60">Estado</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-secondary text-primary">
                            {{ $order->status_label }}
                        </span>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-dark/60">Total</p>
                        <p class="font-medium">${{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-dark/60">Entrega</p>
                        <p class="font-medium">{{ $order->delivery_date?->format('d/m/Y') ?? 'Por confirmar' }}</p>
                        <p class="text-xs text-dark/50">{{ $order->delivery_time ?? 'Durante el día' }}</p>
                    </div>
                    <div>
                        <p class="text-dark/60">Dirección</p>
                        <p class="font-medium">{{ $order->delivery_method === 'pickup' ? 'Retiro en tienda' : $order->delivery_address }}</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-secondary">
                    <h2 class="font-display text-xl text-dark mb-4">Productos</h2>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="text-dark font-medium">{{ $item->product_name }}</p>
                                    @if($item->variant_label)
                                        <p class="text-xs text-dark/50">
                                            {{ $item->variant_label }}
                                            @if($item->variant_type === 'range' && $item->custom_value)
                                                · {{ $item->custom_value }}
                                            @endif
                                        </p>
                                    @endif
                                    <p class="text-xs text-dark/50">Cantidad: {{ $item->quantity }}</p>
                                </div>
                                <p class="text-dark/70">${{ number_format($item->total_price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($order->statusHistory->isNotEmpty())
                    <div class="mt-6 pt-6 border-t border-secondary">
                        <h2 class="font-display text-xl text-dark mb-4">Historial de estado</h2>
                        <div class="space-y-3 text-sm">
                            @php
                                $statusLabels = [
                                    'pending_payment' => 'Esperando comprobante',
                                    'pending_review' => 'En revisión',
                                    'payment_verified' => 'Pago verificado',
                                    'preparing' => 'En preparación',
                                    'ready_for_delivery' => 'Listo para envío',
                                    'in_delivery' => 'En camino',
                                    'delivered' => 'Entregado',
                                    'cancelled' => 'Cancelado',
                                    'refunded' => 'Reembolsado',
                                ];
                            @endphp
                            @foreach($order->statusHistory as $history)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-dark font-medium">{{ $statusLabels[$history->to_status] ?? $history->to_status }}</p>
                                        <p class="text-xs text-dark/50">{{ $history->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    @if($history->notes)
                                        <p class="text-xs text-dark/50">{{ $history->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-card p-6">
                <h2 class="font-display text-xl text-dark mb-4">Comprobantes de pago</h2>

                @if($order->paymentProofs->isEmpty())
                    <p class="text-sm text-dark/60">Aún no hay comprobantes registrados.</p>
                @else
                    <div class="space-y-3">
                        @foreach($order->paymentProofs->sortByDesc('created_at') as $proof)
                            <div class="flex flex-wrap items-center justify-between gap-3 border border-secondary rounded-xl p-3 text-sm">
                                <div>
                                    <p class="font-medium text-dark">{{ $proof->original_filename }}</p>
                                    <p class="text-xs text-dark/50">{{ $proof->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    @php
                                        $proofStatus = [
                                            'pending' => 'En revisión',
                                            'approved' => 'Aprobado',
                                            'rejected' => 'Rechazado',
                                        ][$proof->status] ?? $proof->status;
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-secondary text-primary">
                                        {{ $proofStatus }}
                                    </span>
                                </div>
                                <div>
                                    <a href="{{ route('account.orders.proof', [$order, $proof]) }}" class="text-primary hover:text-primary-dark text-sm">
                                        Descargar
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="comprobante" class="bg-white rounded-2xl shadow-card p-6">
                <h2 class="font-display text-xl text-dark mb-4">Subir comprobante</h2>

                @if(in_array($order->status, ['pending_payment', 'pending_review'], true))
                    @if($proofUploaded)
                        <div class="mb-4 text-green-700 bg-green-50 border border-green-200 rounded-lg p-3 text-sm">
                            Recibimos tu comprobante. Revisaremos y confirmaremos tu pago pronto.
                        </div>
                    @endif

                    <form wire:submit.prevent="uploadProof" class="space-y-4">
                        <div>
                            <label class="form-label">Archivo (JPG, PNG o PDF) *</label>
                            <input type="file" wire:model="proofFile" class="w-full text-sm">
                            @error('proofFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Monto declarado</label>
                                <input type="number" wire:model="declaredAmount" class="form-input">
                                @error('declaredAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Fecha de transferencia</label>
                                <input type="date" wire:model="transactionDate" class="form-input">
                                @error('transactionDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Código de transacción</label>
                                <input type="text" wire:model="transactionCode" class="form-input">
                                @error('transactionCode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label">Banco de origen</label>
                                <input type="text" wire:model="bankOrigin" class="form-input">
                                @error('bankOrigin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn-primary">Enviar comprobante</button>
                    </form>
                @else
                    <p class="text-sm text-dark/60">Este pedido ya no acepta nuevos comprobantes.</p>
                @endif
            </div>
        </div>
    </div>
</div>
