<div class="admin-grid">
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Auditoría</p>
                <h1 class="font-display text-2xl">Eventos críticos y trazabilidad</h1>
            </div>
            <span class="admin-badge">Últimos registros</span>
        </div>

        @if(count($entries) === 0)
            <p class="text-sm text-ink/60">No hay eventos registrados todavía.</p>
        @else
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Acción</th>
                            <th>Usuario</th>
                            <th>IP</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $entry)
                            @php
                                $context = $entry['context'] ?? [];
                            @endphp
                            <tr>
                                <td>{{ $entry['timestamp'] ?? 'N/D' }}</td>
                                <td>{{ $entry['message'] }}</td>
                                <td>{{ $context['user_email'] ?? 'Sistema' }}</td>
                                <td>{{ $context['ip'] ?? '—' }}</td>
                                <td class="text-xs text-ink/50">{{ $context['category'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
