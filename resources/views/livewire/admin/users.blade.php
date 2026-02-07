<div class="admin-grid lg:grid-cols-3" x-data>
    <section class="admin-card lg:col-span-2">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Usuarios internos</p>
                <h1 class="font-display text-2xl">Gestión de accesos</h1>
            </div>
            <button type="button" class="admin-cta" wire:click="startCreate">Nuevo usuario</button>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="form-label">Buscar usuario</label>
                <input type="text" class="form-input" placeholder="nombre o email" wire:model.debounce.400ms="search">
            </div>
            <div>
                <label class="form-label">Roles disponibles</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($roleOptions as $role)
                        <span class="admin-pill">{{ $role }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        @if($flashMessage)
            <p class="mt-4 text-sm text-ink/70">{{ $flashMessage }}</p>
        @endif

        <div class="mt-6 overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $roleName = $user->roles->first()?->name ?? 'viewer';
                        @endphp
                        <tr>
                            <td>{{ $user->name ?? 'Admin' }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $roleName }}</td>
                            <td>
                                @if($roleName === 'super-admin')
                                    <span class="admin-pill">Super Admin</span>
                                @else
                                    <span class="admin-pill admin-pill--warning">Editable</span>
                                @endif
                            </td>
                            <td class="flex items-center gap-2">
                                <button type="button" class="admin-link" wire:click="startEdit({{ $user->id }})">Editar</button>
                                @if($roleName !== 'super-admin')
                                    <button
                                        type="button"
                                        class="text-sm text-red-600"
                                        x-on:click.prevent="if(confirm('¿Eliminar este usuario?')) { $wire.deleteUser({{ $user->id }}) }"
                                    >
                                        Eliminar
                                    </button>
                                @else
                                    <span class="text-xs text-ink/50">Protegido</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-sm text-ink/60">No hay usuarios internos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </section>

    <aside class="admin-card">
        <div class="admin-card-header">
            <h2 class="font-display text-xl">{{ $editingUserId ? 'Editar usuario' : 'Nuevo usuario' }}</h2>
            <span class="admin-badge">{{ $editingUserId ? 'Edición' : 'Creación' }}</span>
        </div>

        @if(!$showForm)
            <p class="text-sm text-ink/60">Selecciona un usuario o crea uno nuevo para editar sus datos.</p>
        @else
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-input" wire:model.defer="name" placeholder="Nombre completo">
                    @error('name') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" wire:model.defer="email" placeholder="correo@floresdyd.cl">
                    @error('email') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Rol</label>
                    <select class="form-input" wire:model.defer="role">
                        @foreach($roleOptions as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Contraseña {{ $editingUserId ? '(opcional)' : '' }}</label>
                    <input type="password" class="form-input" wire:model.defer="password" placeholder="********">
                    @error('password') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="admin-cta">Guardar</button>
                    <button type="button" class="admin-ghost-button" wire:click="cancelForm">Cancelar</button>
                </div>

                <p class="text-xs text-ink/50">Los usuarios con rol super-admin solo pueden eliminarse desde la base de datos.</p>
            </form>
        @endif
    </aside>
</div>
