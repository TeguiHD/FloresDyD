<div class="admin-grid lg:grid-cols-3" x-data>
    <section class="admin-card lg:col-span-2">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Contacto</p>
                <h1 class="font-display text-2xl">Bandeja de mensajes</h1>
            </div>
            <span class="admin-badge">{{ $messages->total() }} mensajes</span>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="form-label">Buscar</label>
                <input type="text" class="form-input" placeholder="Nombre, email, teléfono" wire:model.debounce.400ms="search">
            </div>
            <div>
                <label class="form-label">Estado</label>
                <select class="form-input" wire:model="status">
                    <option value="all">Todos</option>
                    <option value="new">Nuevos</option>
                    <option value="handled">Gestionados</option>
                </select>
            </div>
            <div>
                <label class="form-label">Acciones rápidas</label>
                <div class="flex flex-wrap gap-2">
                    <span class="admin-pill">Responder</span>
                    <span class="admin-pill admin-pill--warning">Revisar</span>
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
                        <th>Remitente</th>
                        <th>Asunto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                        @php
                            $isSelected = $selectedMessageId === $message->id;
                            $statusClass = $message->status === 'handled' ? 'admin-pill' : 'admin-pill admin-pill--warning';
                        @endphp
                        <tr class="{{ $isSelected ? 'bg-[var(--admin-panel-alt)]' : '' }}">
                            <td class="cursor-pointer" wire:click="selectMessage({{ $message->id }})">
                                <div class="font-semibold">{{ $message->name }}</div>
                                <div class="text-xs text-ink/50">{{ $message->email }}</div>
                            </td>
                            <td class="cursor-pointer" wire:click="selectMessage({{ $message->id }})">
                                {{ ucfirst($message->subject) }}
                            </td>
                            <td>
                                <span class="{{ $statusClass }}">{{ $message->status === 'handled' ? 'Gestionado' : 'Nuevo' }}</span>
                            </td>
                            <td>{{ $message->created_at?->format('d M Y') }}</td>
                            <td class="flex items-center gap-2">
                                <button type="button" class="admin-link" wire:click="selectMessage({{ $message->id }})">Ver</button>
                                <button
                                    type="button"
                                    class="text-sm text-red-600"
                                    x-on:click.prevent="if(confirm('¿Eliminar este mensaje?')) { $wire.deleteMessage({{ $message->id }}) }"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-sm text-ink/60">No hay mensajes para los filtros actuales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $messages->links() }}
        </div>
    </section>

    <aside class="admin-card">
        <div class="admin-card-header">
            <h2 class="font-display text-xl">Detalle del mensaje</h2>
            <span class="admin-badge">Inbox</span>
        </div>

        @php $selectedMessage = $this->selectedMessage; @endphp

        @if(!$selectedMessage)
            <p class="text-sm text-ink/60">Selecciona un mensaje para ver el detalle completo.</p>
        @else
            <div class="space-y-4 text-sm text-ink/70">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Remitente</p>
                    <p class="font-semibold text-ink">{{ $selectedMessage->name }}</p>
                    <p>{{ $selectedMessage->email }}</p>
                    @if($selectedMessage->phone)
                        <p>{{ $selectedMessage->phone }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Asunto</p>
                    <p>{{ ucfirst($selectedMessage->subject) }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Mensaje</p>
                    <p class="whitespace-pre-line">{{ $selectedMessage->message }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="{{ $selectedMessage->status === 'handled' ? 'admin-pill' : 'admin-pill admin-pill--warning' }}">
                        {{ $selectedMessage->status === 'handled' ? 'Gestionado' : 'Nuevo' }}
                    </span>
                    <span class="text-xs text-ink/50">{{ $selectedMessage->created_at?->format('d M Y H:i') }}</span>
                </div>

                <div>
                    <label class="form-label">Notas internas</label>
                    <textarea class="form-input min-h-[120px]" wire:model.defer="admin_notes" placeholder="Seguimiento interno..."></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" class="admin-cta" wire:click="markHandled">Marcar gestionado</button>
                    <button type="button" class="admin-ghost-button" wire:click="markNew">Marcar nuevo</button>
                </div>

                <button type="button" class="admin-ghost-button" wire:click="saveNotes">Guardar notas</button>
            </div>
        @endif
    </aside>
</div>
