<div class="admin-grid">
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Gestion de pedidos</p>
                <h1 class="font-display text-2xl">Control operativo y validaciones</h1>
            </div>
            <a href="{{ route('admin.analytics') }}" class="admin-cta">Ver metricas</a>
        </div>

        <div class="admin-seg-tabs">
            @foreach($segmentOptions as $value => $label)
                <button
                    type="button"
                    class="admin-seg-tab {{ $segment === $value ? 'is-active' : '' }}"
                    wire:click="setSegment('{{ $value }}')"
                >
                    {{ $label }}
                    <span class="admin-pill admin-pill--neutral">{{ $segmentCounts[$value] ?? 0 }}</span>
                </button>
            @endforeach
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="form-label">Buscar pedido</label>
                <input type="text" class="form-input" placeholder="FDD-2026-00001 o TRK" wire:model.debounce.400ms="search">
            </div>
            <div>
                <label class="form-label">Estado exacto</label>
                <select class="form-input" wire:model="status">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Comprobante</label>
                <select class="form-input" wire:model="proof">
                    @foreach($proofOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Riesgo</label>
                <select class="form-input" wire:model="risk">
                    @foreach($riskOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid gap-4 mt-4 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="form-label">Creado desde</label>
                <input type="date" class="form-input" wire:model="createdFrom">
            </div>
            <div>
                <label class="form-label">Creado hasta</label>
                <input type="date" class="form-input" wire:model="createdTo">
            </div>
            <div>
                <label class="form-label">Entrega desde</label>
                <input type="date" class="form-input" wire:model="deliveryFrom">
            </div>
            <div>
                <label class="form-label">Entrega hasta</label>
                <input type="date" class="form-input" wire:model="deliveryTo">
            </div>
        </div>

        <div class="grid gap-4 mt-4 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="form-label">Total minimo</label>
                <input type="number" min="0" class="form-input" wire:model.lazy="minTotal">
            </div>
            <div>
                <label class="form-label">Total maximo</label>
                <input type="number" min="0" class="form-input" wire:model.lazy="maxTotal">
            </div>
            <div>
                <label class="form-label">Ordenar por</label>
                <select class="form-input" wire:model="sort">
                    @foreach($sortOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Registros por pagina</label>
                <select class="form-input" wire:model="perPage">
                    @foreach($perPageOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="admin-pill">Validacion manual</span>
                <span class="admin-pill admin-pill--warning">Anti-fraude</span>
                <span class="admin-pill admin-pill--neutral">OCR</span>
            </div>
            <button type="button" class="admin-cta admin-cta--ghost" wire:click="clearFilters">Limpiar filtros</button>
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
                        <th>Comprobante</th>
                        <th>Riesgo</th>
                        <th>Entrega</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
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

                            $statusClass = match($order->status) {
                                'pending_payment' => 'admin-pill admin-pill--warning',
                                'pending_review' => 'admin-pill admin-pill--warning',
                                'payment_verified' => 'admin-pill',
                                'preparing' => 'admin-pill',
                                'ready_for_delivery' => 'admin-pill',
                                'in_delivery' => 'admin-pill',
                                'delivered' => 'admin-pill admin-pill--neutral',
                                'cancelled' => 'admin-pill admin-pill--danger',
                                'refunded' => 'admin-pill admin-pill--danger',
                                default => 'admin-pill',
                            };

                            $latestProof = $order->latestPaymentProof;
                            $proofLabel = 'Sin comprobante';
                            $proofClass = 'admin-pill admin-pill--neutral';
                            $proofMeta = null;
                            if ($latestProof) {
                                $proofLabel = match($latestProof->status) {
                                    'pending' => 'Pendiente',
                                    'approved' => 'Aprobado',
                                    'rejected' => 'Rechazado',
                                    default => ucfirst($latestProof->status),
                                };

                                $proofClass = match($latestProof->status) {
                                    'pending' => 'admin-pill admin-pill--warning',
                                    'rejected' => 'admin-pill admin-pill--danger',
                                    default => 'admin-pill',
                                };

                                if (is_array($latestProof->file_metadata) && !empty($latestProof->file_metadata['analysis_status'])) {
                                    $proofMeta = $latestProof->file_metadata['analysis_status'];
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                <p class="text-sm font-semibold">{{ $order->order_number }}</p>
                                @if(!empty($order->tracking_code))
                                    <p class="text-xs text-ink/60">{{ $order->tracking_code }}</p>
                                @endif
                            </td>
                            <td>{{ $order->customer_name ?? 'Cliente' }}</td>
                            <td>{{ $order->formatted_total }}</td>
                            <td><span class="{{ $statusClass }}">{{ $order->status_label }}</span></td>
                            <td>
                                <div class="flex flex-col gap-1">
                                    <span class="{{ $proofClass }}">{{ $proofLabel }}</span>
                                    @if($proofMeta)
                                        <span class="text-xs text-ink/50">OCR: {{ $proofMeta }}</span>
                                    @endif
                                </div>
                            </td>
                            <td><span class="{{ $riskClass }}">{{ ucfirst($risk) }}</span></td>
                            <td>
                                <p class="text-sm">{{ $order->delivery_date?->format('d M Y') ?? 'Sin fecha' }}</p>
                                <p class="text-xs text-ink/60">{{ $order->delivery_time_slot ?? 'Horario por definir' }}</p>
                            </td>
                            <td>{{ $order->created_at?->format('d M Y') }}</td>
                            <td>
                                <button class="admin-cta admin-cta--ghost" wire:click="selectOrder({{ $order->id }})">
                                    Ver
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-sm text-ink/60">No hay pedidos para los filtros actuales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </section>

    @if($showDetails && $selectedOrder)
        @php
            $mask = function (?string $value, int $visible = 3): string {
                $value = (string) $value;
                if ($value === '') { return '-'; }
                $length = strlen($value);
                if ($length <= $visible) { return str_repeat('*', $length); }
                return substr($value, 0, $visible) . str_repeat('*', $length - $visible);
            };

            $nextStatuses = $statusTransitions[$selectedOrder->status] ?? [];
            $nextStatusLabels = [
                'preparing' => 'Iniciar preparación',
                'ready_for_delivery' => 'Listo para envío',
                'in_delivery' => 'Marcar en camino',
                'delivered' => 'Marcar entregado',
            ];
            $statusLabels = $statusOptions;
            unset($statusLabels['all']);

            $statusClass = match($selectedOrder->status) {
                'pending_payment', 'pending_review' => 'admin-pill admin-pill--warning',
                'payment_verified', 'preparing', 'ready_for_delivery', 'in_delivery' => 'admin-pill',
                'delivered' => 'admin-pill admin-pill--neutral',
                'cancelled', 'refunded' => 'admin-pill admin-pill--danger',
                default => 'admin-pill',
            };

            $riskLevel = $selectedOrder->getRiskLevel();
            $coupon = $selectedOrder->coupon;
        @endphp

        {{-- Overlay --}}
        <div class="drawer-overlay" wire:click="closeDetails"></div>

        {{-- Drawer Panel --}}
        <aside class="drawer-panel" x-data="{ openSections: { client: true, delivery: true, items: true, finance: true, proofs: true, flow: true, timeline: false, cancel: false } }">
            {{-- Header --}}
            <div class="drawer-header">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-ink/50">Detalle de pedido</p>
                    <h2 class="font-display text-xl">{{ $selectedOrder->order_number }}</h2>
                    @if(!empty($selectedOrder->tracking_code))
                        <p class="text-xs text-ink/40 font-mono">{{ $selectedOrder->tracking_code }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <span class="{{ $statusClass }}">{{ $selectedOrder->status_label }}</span>
                    <button type="button" class="admin-switch text-lg" wire:click="toggleSensitive" title="{{ $maskSensitive ? 'Mostrar datos sensibles' : 'Ocultar datos sensibles' }}">
                        {{ $maskSensitive ? '👁' : '🔒' }}
                    </button>
                    <button class="drawer-close" wire:click="closeDetails">&times;</button>
                </div>
            </div>

            <div class="drawer-body">
                {{-- Quick stats --}}
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="drawer-stat">
                        <p class="drawer-stat-label">Total</p>
                        <p class="drawer-stat-value">{{ $selectedOrder->formatted_total }}</p>
                    </div>
                    <div class="drawer-stat">
                        <p class="drawer-stat-label">Riesgo</p>
                        <p class="drawer-stat-value">
                            <span class="admin-pill {{ $riskLevel === 'high' ? 'admin-pill--danger' : ($riskLevel === 'medium' ? 'admin-pill--warning' : '') }}" style="font-size: 0.75rem;">
                                {{ ucfirst($riskLevel) }} ({{ $selectedOrder->fraud_score }})
                            </span>
                        </p>
                    </div>
                    <div class="drawer-stat">
                        <p class="drawer-stat-label">Creado</p>
                        <p class="drawer-stat-value" style="font-size: 0.8rem;">{{ $selectedOrder->created_at?->format('d M Y H:i') }}</p>
                    </div>
                </div>

                {{-- SECTION: Cliente --}}
                <div class="drawer-section">
                    <button class="drawer-section-toggle" @click="openSections.client = !openSections.client">
                        <span>👤 Cliente</span>
                        <span x-text="openSections.client ? '−' : '+'" class="text-lg text-ink/40"></span>
                    </button>
                    <div x-show="openSections.client" x-collapse class="drawer-section-body">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="drawer-meta-label">Nombre</p>
                                <p class="drawer-meta-value">{{ $maskSensitive ? $mask($selectedOrder->customer_name) : ($selectedOrder->customer_name ?? '-') }}</p>
                            </div>
                            <div>
                                <p class="drawer-meta-label">Email</p>
                                <p class="drawer-meta-value">{{ $maskSensitive ? $mask($selectedOrder->customer_email) : ($selectedOrder->customer_email ?? '-') }}</p>
                            </div>
                            <div>
                                <p class="drawer-meta-label">Teléfono</p>
                                <p class="drawer-meta-value">{{ $maskSensitive ? $mask($selectedOrder->customer_phone) : ($selectedOrder->customer_phone ?? '-') }}</p>
                            </div>
                            @if($selectedOrder->user)
                                <div>
                                    <p class="drawer-meta-label">Cuenta</p>
                                    <p class="drawer-meta-value">{{ $selectedOrder->user->name }} <span class="text-xs text-ink/40">#{{ $selectedOrder->user->id }}</span></p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- SECTION: Entrega --}}
                <div class="drawer-section">
                    <button class="drawer-section-toggle" @click="openSections.delivery = !openSections.delivery">
                        <span>📦 Entrega</span>
                        <span x-text="openSections.delivery ? '−' : '+'" class="text-lg text-ink/40"></span>
                    </button>
                    <div x-show="openSections.delivery" x-collapse class="drawer-section-body">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="drawer-meta-label">Método</p>
                                <p class="drawer-meta-value">{{ $selectedOrder->delivery_method === 'pickup' ? '🏪 Retiro en tienda' : '🚗 Delivery' }}</p>
                            </div>
                            <div>
                                <p class="drawer-meta-label">Fecha</p>
                                <p class="drawer-meta-value">{{ $selectedOrder->delivery_date?->format('d M Y') ?? 'Por confirmar' }}</p>
                            </div>
                            <div>
                                <p class="drawer-meta-label">Horario</p>
                                <p class="drawer-meta-value">{{ $selectedOrder->delivery_time_slot ?? 'Durante el día' }}</p>
                            </div>
                            @if(!empty($selectedOrder->tracking_code))
                                <div>
                                    <p class="drawer-meta-label">Tracking</p>
                                    <p class="drawer-meta-value font-mono text-sm">{{ $selectedOrder->tracking_code }}</p>
                                </div>
                            @endif
                        </div>
                        @if($selectedOrder->delivery_method !== 'pickup')
                            <div class="mt-3 space-y-2">
                                <div>
                                    <p class="drawer-meta-label">Dirección</p>
                                    <p class="drawer-meta-value">{{ $maskSensitive ? $mask($selectedOrder->delivery_address, 5) : ($selectedOrder->delivery_address ?? '-') }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="drawer-meta-label">Ciudad</p>
                                        <p class="drawer-meta-value">{{ $maskSensitive ? $mask($selectedOrder->delivery_city) : ($selectedOrder->delivery_city ?? '-') }}</p>
                                    </div>
                                    <div>
                                        <p class="drawer-meta-label">CP</p>
                                        <p class="drawer-meta-value">{{ $maskSensitive ? $mask($selectedOrder->delivery_zip) : ($selectedOrder->delivery_zip ?? '-') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($selectedOrder->delivery_notes)
                            <div class="mt-3">
                                <p class="drawer-meta-label">Notas de entrega</p>
                                <p class="drawer-meta-value text-sm italic">{{ $maskSensitive ? '***' : $selectedOrder->delivery_notes }}</p>
                            </div>
                        @endif
                        @if($selectedOrder->card_message)
                            <div class="mt-3 p-3 rounded-lg" style="background: rgba(236, 72, 153, 0.06); border: 1px solid rgba(236, 72, 153, 0.15);">
                                <p class="drawer-meta-label">💌 Tarjeta</p>
                                <p class="text-sm italic" style="color: var(--ink);">"{{ $maskSensitive ? '***' : $selectedOrder->card_message }}"</p>
                                @if($selectedOrder->card_sender || $selectedOrder->card_recipient)
                                    <p class="text-xs mt-1" style="color: var(--ink); opacity: 0.5;">
                                        {{ $selectedOrder->card_sender ? 'De: ' . $selectedOrder->card_sender : '' }}
                                        {{ $selectedOrder->card_recipient ? ' → Para: ' . $selectedOrder->card_recipient : '' }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- SECTION: Productos --}}
                <div class="drawer-section">
                    <button class="drawer-section-toggle" @click="openSections.items = !openSections.items">
                        <span>🛒 Productos ({{ $selectedOrder->items->count() }})</span>
                        <span x-text="openSections.items ? '−' : '+'" class="text-lg text-ink/40"></span>
                    </button>
                    <div x-show="openSections.items" x-collapse class="drawer-section-body">
                        <div class="space-y-2">
                            @foreach($selectedOrder->items as $item)
                                <div class="flex items-center justify-between gap-3 p-3 rounded-lg" style="border: 1px solid rgba(var(--ink-rgb, 0 0 0) / 0.08); background: rgba(var(--ink-rgb, 0 0 0) / 0.02);">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium truncate">{{ $item->product?->name ?? $item->product_name ?? 'Producto' }}</p>
                                        @if($item->variant_label)
                                            <p class="text-xs text-ink/50">
                                                {{ $item->variant_label }}
                                                @if($item->variant_type === 'range' && $item->custom_value) - {{ $item->custom_value }} @endif
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-sm font-medium">${{ number_format($item->total_price, 0, ',', '.') }}</p>
                                        <p class="text-xs text-ink/50">{{ $item->quantity }} × ${{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- SECTION: Resumen financiero --}}
                <div class="drawer-section">
                    <button class="drawer-section-toggle" @click="openSections.finance = !openSections.finance">
                        <span>💰 Resumen financiero</span>
                        <span x-text="openSections.finance ? '−' : '+'" class="text-lg text-ink/40"></span>
                    </button>
                    <div x-show="openSections.finance" x-collapse class="drawer-section-body">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-ink/60">Subtotal</span>
                                <span>${{ number_format($selectedOrder->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($selectedOrder->delivery_fee > 0)
                                <div class="flex justify-between">
                                    <span class="text-ink/60">Envío</span>
                                    <span>${{ number_format($selectedOrder->delivery_fee, 0, ',', '.') }}</span>
                                </div>
                            @elseif($selectedOrder->delivery_method !== 'pickup')
                                <div class="flex justify-between">
                                    <span class="text-ink/60">Envío</span>
                                    <span style="color: #16a34a;">Gratis</span>
                                </div>
                            @endif
                            @if($selectedOrder->discount_amount > 0)
                                <div class="flex justify-between" style="color: #16a34a;">
                                    <span>Descuento</span>
                                    <span>-${{ number_format($selectedOrder->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between pt-2 font-semibold text-base" style="border-top: 1px solid rgba(var(--ink-rgb, 0 0 0) / 0.1);">
                                <span>Total</span>
                                <span>{{ $selectedOrder->formatted_total }}</span>
                            </div>
                        </div>

                        @if($coupon)
                            <div class="mt-3 p-3 rounded-lg" style="background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.15);">
                                <p class="text-xs uppercase tracking-widest font-semibold" style="color: #059669;">Cupón aplicado</p>
                                <p class="text-sm font-medium mt-1">{{ $selectedOrder->coupon_code }}</p>
                                <p class="text-xs" style="color: rgba(5, 150, 105, 0.7);">
                                    {{ $coupon->type === 'percentage' ? $coupon->value . '% de descuento' : '$' . number_format($coupon->value, 0, ',', '.') . ' de descuento' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- SECTION: Comprobantes --}}
                <div class="drawer-section">
                    <button class="drawer-section-toggle" @click="openSections.proofs = !openSections.proofs">
                        <span>🧾 Comprobantes ({{ $selectedOrder->paymentProofs->count() }})</span>
                        <span x-text="openSections.proofs ? '−' : '+'" class="text-lg text-ink/40"></span>
                    </button>
                    <div x-show="openSections.proofs" x-collapse class="drawer-section-body">
                        @forelse($selectedOrder->paymentProofs as $proof)
                            @php
                                $proofStatusClass = match($proof->status) {
                                    'pending' => 'admin-pill admin-pill--warning',
                                    'approved' => 'admin-pill',
                                    'rejected' => 'admin-pill admin-pill--danger',
                                    default => 'admin-pill',
                                };
                            @endphp
                            <div class="rounded-xl p-4 mb-3" style="border: 1px solid rgba(var(--ink-rgb, 0 0 0) / 0.1);">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-sm font-medium truncate">{{ $proof->original_filename }}</p>
                                            <span class="{{ $proofStatusClass }}">{{ ucfirst($proof->status) }}</span>
                                        </div>
                                        <p class="text-xs text-ink/50">{{ $proof->created_at?->format('d M Y H:i') }}</p>
                                        @if($proof->declared_amount)
                                            <p class="text-xs text-ink/50">Monto: ${{ number_format($proof->declared_amount, 0, ',', '.') }}</p>
                                        @endif
                                        @if($proof->transaction_date)
                                            <p class="text-xs text-ink/50">Fecha: {{ $proof->transaction_date->format('d M Y') }}</p>
                                        @endif
                                        @if(is_array($proof->file_metadata))
                                            <div class="mt-1 text-xs text-ink/40 space-y-0.5">
                                                <p>OCR: {{ $proof->file_metadata['analysis_status'] ?? 'pendiente' }}
                                                    @if(!empty($proof->file_metadata['analysis_score'])) · Score: {{ $proof->file_metadata['analysis_score'] }}/100 @endif
                                                </p>
                                                @if(!empty($proof->file_metadata['extracted_amount_raw']))
                                                    <p>Monto OCR: {{ $proof->file_metadata['extracted_amount_raw'] }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-2 shrink-0">
                                        <a class="admin-cta admin-cta--ghost text-xs" href="{{ route('admin.orders.proof.download', [$selectedOrder->id, $proof->id]) }}">
                                            Descargar
                                        </a>
                                        @if($proof->status === 'pending')
                                            <button class="admin-cta text-xs" wire:click="approveProof({{ $proof->id }})">
                                                Aprobar
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                @if($proof->status === 'pending')
                                    <div class="mt-3 grid gap-2 items-end" style="grid-template-columns: 1fr auto;">
                                        <div>
                                            <label class="form-label text-xs">Motivo de rechazo</label>
                                            <input type="text" class="form-input text-sm" wire:model.defer="rejectionReasons.{{ $proof->id }}" placeholder="Ej: Monto no coincide">
                                        </div>
                                        <button class="admin-cta admin-cta--danger text-xs" wire:click="rejectProof({{ $proof->id }})">
                                            Rechazar
                                        </button>
                                    </div>
                                    @if(is_array($proof->file_metadata) && !empty($proof->file_metadata['analysis_flags']))
                                        <p class="mt-2 text-xs text-ink/40">Señales: {{ implode(', ', $proof->file_metadata['analysis_flags']) }}</p>
                                    @endif
                                @elseif($proof->status === 'rejected' && $proof->rejection_reason)
                                    <p class="text-xs mt-2" style="color: #dc2626;">Motivo: {{ $proof->rejection_reason }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-ink/50">Este pedido aún no tiene comprobantes.</p>
                        @endforelse
                    </div>
                </div>

                {{-- SECTION: Flujo operativo --}}
                <div class="drawer-section">
                    <button class="drawer-section-toggle" @click="openSections.flow = !openSections.flow">
                        <span>⚡ Flujo operativo</span>
                        <span x-text="openSections.flow ? '−' : '+'" class="text-lg text-ink/40"></span>
                    </button>
                    <div x-show="openSections.flow" x-collapse class="drawer-section-body">
                        <div class="space-y-3">
                            <div>
                                <label class="form-label">Nota interna</label>
                                <input type="text" class="form-input" wire:model.defer="statusNote" placeholder="Detalle operativo o validación">
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if(count($nextStatuses) > 0)
                                    @foreach($nextStatuses as $nextStatus)
                                        <button class="admin-cta" wire:click="updateStatus({{ $selectedOrder->id }}, '{{ $nextStatus }}')"
                                                wire:confirm="¿Cambiar estado a '{{ $nextStatusLabels[$nextStatus] ?? ucfirst($nextStatus) }}'?">
                                            {{ $nextStatusLabels[$nextStatus] ?? ucfirst($nextStatus) }}
                                        </button>
                                    @endforeach
                                @else
                                    <p class="text-sm text-ink/50">No hay acciones disponibles para este estado.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION: Historial --}}
                @if($selectedOrder->statusHistory->isNotEmpty())
                    <div class="drawer-section">
                        <button class="drawer-section-toggle" @click="openSections.timeline = !openSections.timeline">
                            <span>📋 Historial ({{ $selectedOrder->statusHistory->count() }})</span>
                            <span x-text="openSections.timeline ? '−' : '+'" class="text-lg text-ink/40"></span>
                        </button>
                        <div x-show="openSections.timeline" x-collapse class="drawer-section-body">
                            <div class="relative pl-6 space-y-4">
                                @foreach($selectedOrder->statusHistory as $i => $history)
                                    <div class="relative">
                                        <div class="absolute -left-6 top-1 w-3 h-3 rounded-full {{ $loop->first ? 'bg-primary' : 'bg-ink/20' }}" style="outline: 2px solid white;"></div>
                                        @if(!$loop->last)
                                            <div class="absolute top-4 w-px h-full bg-ink/10" style="left: -18px;"></div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium">{{ \App\Models\Order::make(['status' => $history->to_status])->status_label }}</p>
                                            <p class="text-xs text-ink/50">{{ $history->created_at?->format('d M Y H:i') }} · {{ $history->changer?->name ?? 'Sistema' }}</p>
                                            @if($history->notes)
                                                <p class="text-xs text-ink/40 mt-0.5 italic">{{ $history->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- SECTION: Cancelar --}}
                @if(!in_array($selectedOrder->status, ['cancelled', 'refunded', 'delivered'], true))
                    <div class="drawer-section">
                        <button class="drawer-section-toggle" @click="openSections.cancel = !openSections.cancel">
                            <span style="color: #dc2626;">🚫 Cancelar pedido</span>
                            <span x-text="openSections.cancel ? '−' : '+'" class="text-lg text-ink/40"></span>
                        </button>
                        <div x-show="openSections.cancel" x-collapse class="drawer-section-body">
                            <div class="grid gap-3 items-end" style="grid-template-columns: 1fr auto;">
                                <div>
                                    <label class="form-label">Motivo de cancelación</label>
                                    <input type="text" class="form-input" wire:model.defer="cancellationReason" placeholder="Ej: Cliente solicitó cancelación">
                                </div>
                                <button class="admin-cta admin-cta--danger" wire:click="cancelOrder({{ $selectedOrder->id }})"
                                        wire:confirm="¿Estás seguro de cancelar este pedido? Esta acción no se puede deshacer.">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Fraud factors --}}
                @if(is_array($selectedOrder->fraud_factors) && count($selectedOrder->fraud_factors) > 0)
                    <div class="mt-4 p-3 rounded-lg text-xs" style="background: rgba(245, 158, 11, 0.06); border: 1px solid rgba(245, 158, 11, 0.15);">
                        <p class="font-semibold mb-1" style="color: #b45309;">Señales de fraude</p>
                        <p style="color: rgba(180, 83, 9, 0.7);">{{ implode(', ', $selectedOrder->fraud_factors) }}</p>
                    </div>
                @endif
            </div>
        </aside>
    @endif
</div>
