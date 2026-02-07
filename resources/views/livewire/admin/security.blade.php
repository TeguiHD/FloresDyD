<div class="admin-grid">
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Seguridad</p>
                <h1 class="font-display text-2xl">Monitoreo y prevención activa</h1>
            </div>
            <span class="admin-badge">OWASP / NIST</span>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach($stats as $stat)
                <div class="admin-card" style="background: var(--admin-panel-alt);">
                    <p class="admin-stat-label">{{ $stat['label'] }}</p>
                    <p class="admin-stat-value">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="font-display text-xl">Intentos de login</h2>
                <span class="admin-badge">Audit log</span>
            </div>
            @if(count($loginEvents) === 0)
                <p class="text-sm text-ink/60">No hay intentos registrados todavía.</p>
            @else
                <div class="space-y-3">
                    @foreach(array_slice($loginEvents, 0, 8) as $event)
                        <div class="rounded-2xl border border-[var(--admin-border)] px-4 py-3">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold">{{ $event['message'] }}</p>
                                <span class="text-xs text-ink/50">{{ $event['timestamp'] }}</span>
                            </div>
                            <p class="text-xs text-ink/50">{{ $event['context']['details']['email'] ?? 'Email no registrado' }} · {{ $event['context']['ip'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="font-display text-xl">Honeypots y escaneos</h2>
                <span class="admin-badge">Security log</span>
            </div>
            @if(count($securityEvents) === 0)
                <p class="text-sm text-ink/60">No hay eventos de seguridad recientes.</p>
            @else
                <div class="space-y-3">
                    @foreach(array_slice($securityEvents, 0, 8) as $event)
                        <div class="rounded-2xl border border-[var(--admin-border)] px-4 py-3">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold">{{ $event['message'] }}</p>
                                <span class="text-xs text-ink/50">{{ $event['timestamp'] }}</span>
                            </div>
                            @if(!empty($event['context']['trigger']))
                                <p class="text-xs text-ink/50">Trigger: {{ $event['context']['trigger'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
