<div class="admin-grid">
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Clientes</p>
                <h1 class="font-display text-2xl">Relación y confianza</h1>
            </div>
            <span class="admin-badge">{{ $customers->total() }} registros</span>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="form-label">Buscar cliente</label>
                <input type="text" class="form-input" placeholder="correo o nombre" wire:model.debounce.400ms="search">
            </div>
            <div>
                <label class="form-label">Filtro</label>
                <select class="form-input" wire:model="filter">
                    @foreach($filters as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Segmentación</label>
                <div class="flex items-center gap-2">
                    <span class="admin-pill">Trust Score</span>
                    <span class="admin-pill admin-pill--warning">Whitelist</span>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-card">
        <div class="admin-card-header">
            <h2 class="font-display text-xl">Listado de clientes</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Trust</th>
                        <th>Compras</th>
                        <th>Whitelist</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->name ?? 'Cliente' }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->trust_score }}</td>
                            <td>{{ $customer->successful_orders }}</td>
                            <td>{{ $customer->is_whitelisted ? 'Sí' : 'No' }}</td>
                            <td>
                                @if($customer->locked_until && $customer->locked_until->isFuture())
                                    <span class="admin-pill admin-pill--danger">Bloqueado</span>
                                @else
                                    <span class="admin-pill">Activo</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-sm text-ink/60">No hay clientes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    </section>
</div>
