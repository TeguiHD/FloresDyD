<div class="admin-grid lg:grid-cols-3">
    <section class="admin-card">
        <div class="admin-card-header">
            <h1 class="font-display text-2xl">Roles</h1>
            <span class="admin-badge">RBAC</span>
        </div>
        <div class="space-y-2">
            @foreach($roles as $role)
                <button
                    type="button"
                    class="w-full text-left px-4 py-3 rounded-2xl border border-[var(--admin-border)] transition"
                    style="background: {{ $activeRoleId === $role['id'] ? 'var(--admin-accent-soft)' : 'var(--admin-panel-alt)' }};"
                    wire:click="setActiveRole({{ $role['id'] }})"
                >
                    <p class="text-sm font-semibold">{{ $role['name'] }}</p>
                    <p class="text-xs text-ink/50">{{ count($role['permissions']) }} permisos</p>
                </button>
            @endforeach
        </div>
    </section>

    <section class="admin-card lg:col-span-2">
        @php
            $activeRole = collect($roles)->firstWhere('id', $activeRoleId);
            $roleName = $activeRole['name'] ?? 'Rol';
            $isSuper = $roleName === 'super-admin';
        @endphp

        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Permisos</p>
                <h2 class="font-display text-2xl">{{ $roleName }}</h2>
            </div>
            @if($saved)
                <span class="admin-badge">Actualizado</span>
            @endif
        </div>

        @if($isSuper)
            <p class="text-sm text-ink/60">El rol super-admin tiene acceso total por diseño. No se requiere edición manual.</p>
        @else
            <form wire:submit.prevent="save" class="space-y-5">
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach($permissions as $permission)
                        <label class="flex items-center gap-2 rounded-xl border border-[var(--admin-border)] px-3 py-2 bg-[var(--admin-panel-alt)]">
                            <input
                                type="checkbox"
                                class="accent-[var(--admin-accent)]"
                                value="{{ $permission }}"
                                wire:model="selectedPermissions"
                            >
                            <span class="text-sm">{{ $permission }}</span>
                        </label>
                    @endforeach
                </div>
                <button type="submit" class="admin-cta">Guardar permisos</button>
            </form>
        @endif
    </section>
</div>
