<div class="admin-grid">
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Resumen ejecutivo</p>
                <h1 class="font-display text-2xl">Panorama general del negocio</h1>
            </div>
            <span class="admin-badge">Actualizado en tiempo real</span>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($kpis as $kpi)
                <div class="admin-stat admin-card" style="background: var(--admin-panel-alt);">
                    <p class="admin-stat-label">{{ $kpi['label'] }}</p>
                    <p class="admin-stat-value">{{ $kpi['value'] }}</p>
                    <p class="text-xs text-ink/50">{{ $kpi['hint'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-3">
        <div class="admin-card lg:col-span-2">
            <div class="admin-card-header">
                <h2 class="font-display text-xl">Pedidos recientes</h2>
                <span class="admin-badge">Últimos movimientos</span>
            </div>

            @if(count($recentOrders) === 0)
                <p class="text-sm text-ink/60">Aún no hay pedidos registrados. Cuando lleguen, aparecerán aquí con su estado y nivel de riesgo.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Riesgo</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                                @php
                                    $riskClass = match($order['risk']) {
                                        'high' => 'admin-pill admin-pill--danger',
                                        'medium' => 'admin-pill admin-pill--warning',
                                        default => 'admin-pill',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $order['number'] ?? ('#' . $order['id']) }}</td>
                                    <td>{{ $order['total'] }}</td>
                                    <td>{{ $order['status'] }}</td>
                                    <td><span class="{{ $riskClass }}">{{ ucfirst($order['risk']) }}</span></td>
                                    <td>{{ $order['created_at'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="font-display text-xl">Alertas inteligentes</h2>
                <span class="admin-badge">Operación</span>
            </div>
            <div class="grid gap-3">
                @foreach($alerts as $alert)
                    @php
                        $pill = match($alert['type']) {
                            'danger' => 'admin-pill admin-pill--danger',
                            'warning' => 'admin-pill admin-pill--warning',
                            default => 'admin-pill',
                        };
                    @endphp
                    <div class="flex items-center justify-between rounded-2xl border border-[var(--admin-border)] px-4 py-3 bg-[var(--admin-panel-alt)]">
                        <div>
                            <p class="text-sm font-semibold">{{ $alert['label'] }}</p>
                            <p class="text-xs text-ink/50">Revisión prioritaria</p>
                        </div>
                        <span class="{{ $pill }}">{{ $alert['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="font-display text-xl">Productos más vistos</h2>
                <a href="{{ route('admin.products') }}" class="admin-link text-sm">Ver catálogo</a>
            </div>
            @if(count($topViewed) === 0)
                <p class="text-sm text-ink/60">No hay productos con visitas registradas todavía.</p>
            @else
                <div class="space-y-3">
                    @foreach($topViewed as $product)
                        <div class="flex items-center justify-between rounded-2xl border border-[var(--admin-border)] px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold">{{ $product['name'] }}</p>
                                <p class="text-xs text-ink/50">Stock: {{ $product['stock'] }}</p>
                            </div>
                            <span class="admin-pill">{{ $product['views_count'] }} vistas</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="font-display text-xl">Productos más vendidos</h2>
                <a href="{{ route('admin.orders') }}" class="admin-link text-sm">Ver pedidos</a>
            </div>
            @if(count($topSelling) === 0)
                <p class="text-sm text-ink/60">Aún no hay ventas registradas.</p>
            @else
                <div class="space-y-3">
                    @foreach($topSelling as $product)
                        <div class="flex items-center justify-between rounded-2xl border border-[var(--admin-border)] px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold">{{ $product['name'] }}</p>
                                <p class="text-xs text-ink/50">Vistas: {{ $product['views_count'] }}</p>
                            </div>
                            <span class="admin-pill">{{ $product['sales_count'] }} ventas</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header">
            <h2 class="font-display text-xl">Distribución de estados de pedidos</h2>
            <span class="admin-badge">Visión operativa</span>
        </div>
        @if(count($ordersByStatus) === 0)
            <p class="text-sm text-ink/60">Cuando existan pedidos, verás la distribución por estado aquí.</p>
        @else
            @php
                $maxStatus = max(array_column($ordersByStatus, 'total'));
            @endphp
            <div class="space-y-4">
                @foreach($ordersByStatus as $status)
                    <div>
                        <div class="flex items-center justify-between text-sm font-medium">
                            <span>{{ $status['label'] }}</span>
                            <span class="text-ink/50">{{ $status['total'] }}</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-[var(--admin-accent-soft)]">
                            <div
                                class="h-2 rounded-full"
                                style="width: {{ $maxStatus > 0 ? ($status['total'] / $maxStatus) * 100 : 0 }}%; background: var(--admin-accent);"
                            ></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
