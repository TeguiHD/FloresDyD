<div class="min-h-dvh bg-secondary/10 py-10">
    <div class="container-custom grid lg:grid-cols-[260px_1fr] gap-6">
        @include('livewire.account.partials.nav')

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-card p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="font-display text-2xl text-dark mb-1">Mis pedidos</h1>
                        <p class="text-sm text-dark/60">Revisa el estado y comprobantes de tus compras.</p>
                    </div>
                    <div class="min-w-[200px]">
                        <label class="text-xs text-dark/60 block mb-1">Filtrar por estado</label>
                        <select wire:model="status" class="form-input">
                            @foreach($statusOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($orders->isEmpty())
                    <p class="text-sm text-dark/60">Aún no tienes pedidos registrados.</p>
                @else
                    <div class="space-y-4">
                        @foreach($orders as $order)
                            <div
                                class="border border-secondary rounded-xl p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div>
                                    <p class="text-sm text-dark/60">Pedido</p>
                                    <p class="font-medium text-dark">{{ $order->order_number }}</p>
                                    <p class="text-xs text-dark/50">Fecha: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-dark/60">Estado</p>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-secondary text-primary">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-dark/60">Total</p>
                                    <p class="font-medium">${{ number_format($order->total, 0, ',', '.') }}</p>
                                    <p class="text-xs text-dark/50">Comprobantes: {{ $order->payment_proofs_count }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('account.orders.show', $order) }}" class="btn-outline text-sm">Ver
                                        detalle</a>
                                    @if($order->tracking_code)
                                        <a href="{{ route('track.order.code', $order->tracking_code) }}"
                                            class="btn-outline text-sm">Rastrear pedido</a>
                                    @endif
                                    @if(in_array($order->status, ['pending_payment', 'pending_review'], true))
                                        <a href="{{ route('account.orders.show', $order) }}#comprobante"
                                            class="btn-primary text-sm">Subir comprobante</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($orders->hasPages())
                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>