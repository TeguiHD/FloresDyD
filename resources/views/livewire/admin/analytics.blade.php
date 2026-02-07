<div class="admin-grid">
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Analítica</p>
                <h1 class="font-display text-2xl">Métricas clave del negocio</h1>
            </div>
            <span class="admin-badge">Últimos 30 días</span>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($summary as $item)
                <div class="admin-card" style="background: var(--admin-panel-alt);">
                    <p class="admin-stat-label">{{ $item['label'] }}</p>
                    <p class="admin-stat-value">{{ $item['value'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header">
            <h2 class="font-display text-xl">Tendencia de pedidos (14 días)</h2>
            <span class="admin-badge">Volumen diario</span>
        </div>
        @php
            $maxTrend = 0;
            foreach ($ordersTrend as $point) {
                $maxTrend = max($maxTrend, $point['total']);
            }
        @endphp
        <div class="flex items-end gap-2 h-32">
            @foreach($ordersTrend as $point)
                @php
                    $height = $maxTrend > 0 ? max(10, ($point['total'] / $maxTrend) * 100) : 10;
                @endphp
                <div class="flex flex-col items-center flex-1">
                    <div class="w-full rounded-full" style="height: {{ $height }}px; background: var(--admin-accent);"></div>
                    <span class="text-[0.65rem] text-ink/60 mt-2">{{ $point['label'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="font-display text-xl">Productos top por vistas</h2>
                <span class="admin-badge">Conversión por producto</span>
            </div>
            @if(count($topProducts) === 0)
                <p class="text-sm text-ink/60">Aún no hay datos suficientes.</p>
            @else
                <div class="space-y-3">
                    @foreach($topProducts as $product)
                        <div class="flex items-center justify-between rounded-2xl border border-[var(--admin-border)] px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold">{{ $product['name'] }}</p>
                                <p class="text-xs text-ink/50">{{ $product['views'] }} vistas · {{ $product['sales'] }} ventas</p>
                            </div>
                            <span class="admin-pill">{{ $product['conversion'] }}% conv.</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="font-display text-xl">Popups con mejor rendimiento</h2>
                <span class="admin-badge">CTR</span>
            </div>
            @if(count($popupStats) === 0)
                <p class="text-sm text-ink/60">No hay popups con métricas todavía.</p>
            @else
                <div class="space-y-3">
                    @foreach($popupStats as $popup)
                        <div class="flex items-center justify-between rounded-2xl border border-[var(--admin-border)] px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold">{{ $popup['title'] }}</p>
                                <p class="text-xs text-ink/50">{{ $popup['views'] }} vistas · {{ $popup['clicks'] }} clics</p>
                            </div>
                            <span class="admin-pill">{{ $popup['ctr'] }}% CTR</span>
                        </div>
                    @endforeach
                </div>
            @endif
            <p class="text-xs text-ink/50 mt-4">Para medir WhatsApp, mapa y botones clave, activa el tracking de eventos en el frontend.</p>
        </div>
    </section>
</div>
