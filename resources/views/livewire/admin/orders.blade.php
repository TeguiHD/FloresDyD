<div class="admin-grid">
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Gestión de pedidos</p>
                <h1 class="font-display text-2xl">Control operativo y validaciones</h1>
            </div>
            <a href="{{ route('admin.analytics') }}" class="admin-cta">Ver métricas</a>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="form-label">Buscar pedido</label>
                <input type="text" class="form-input" placeholder="FDD-2026-00001" wire:model.debounce.400ms="search">
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select class="form-input" wire:model="status">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Acción rápida</label>
                <div class="flex items-center gap-3">
                    <span class="admin-pill">Validación manual</span>
                    <span class="admin-pill admin-pill--warning">Anti-fraude</span>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header">
            <h2 class="font-display text-xl">Listado de pedidos</h2>
            <span class="admin-badge">{{ $orders->total() }} registros</span>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Riesgo</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $risk = $order->getRiskLevel();
                            $riskClass = match($risk) {
                                'high' => 'admin-pill admin-pill--danger',
                                'medium' => 'admin-pill admin-pill--warning',
                                default => 'admin-pill',
                            };
                        @endphp
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customer_name ?? 'Cliente' }}</td>
                            <td>{{ $order->formatted_total }}</td>
                            <td>{{ $order->status_label }}</td>
                            <td><span class="{{ $riskClass }}">{{ ucfirst($risk) }}</span></td>
                            <td>{{ $order->created_at?->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-sm text-ink/60">No hay pedidos para los filtros actuales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </section>
</div>
