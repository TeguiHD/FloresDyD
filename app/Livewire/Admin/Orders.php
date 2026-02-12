<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Services\AuditService;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Pedidos - Flores D&D')]
class Orders extends Component
{
    use WithPagination;

    public string $segment = 'all';
    public string $status = 'all';
    public string $search = '';
    public string $risk = 'all';
    public string $proof = 'all';
    public string $sort = 'newest';
    public string $perPage = '12';
    public string $createdFrom = '';
    public string $createdTo = '';
    public string $deliveryFrom = '';
    public string $deliveryTo = '';
    public string $minTotal = '';
    public string $maxTotal = '';
    public ?Order $selectedOrder = null;
    public array $rejectionReasons = [];
    public bool $showDetails = false;
    public bool $maskSensitive = true;
    public string $statusNote = '';
    public string $cancellationReason = '';

    protected $queryString = [
        'segment' => ['except' => 'all'],
        'status' => ['except' => 'all'],
        'search' => ['except' => ''],
        'risk' => ['except' => 'all'],
        'proof' => ['except' => 'all'],
        'sort' => ['except' => 'newest'],
        'perPage' => ['except' => '12'],
        'createdFrom' => ['except' => ''],
        'createdTo' => ['except' => ''],
        'deliveryFrom' => ['except' => ''],
        'deliveryTo' => ['except' => ''],
        'minTotal' => ['except' => ''],
        'maxTotal' => ['except' => ''],
    ];

    public array $segmentOptions = [
        'all' => 'Todos',
        'pending' => 'Pendientes',
        'pending_validation' => 'Pendientes de validacion',
        'in_process' => 'En proceso',
        'completed' => 'Entregados',
        'cancelled' => 'Cancelados',
    ];

    public array $statusOptions = [
        'all' => 'Todos',
        'pending_payment' => 'Esperando comprobante',
        'pending_review' => 'En revision',
        'payment_verified' => 'Pago verificado',
        'preparing' => 'En preparacion',
        'ready_for_delivery' => 'Listo para envio',
        'in_delivery' => 'En camino',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
        'refunded' => 'Reembolsado',
    ];

    public array $riskOptions = [
        'all' => 'Todos',
        'low' => 'Bajo',
        'medium' => 'Medio',
        'high' => 'Alto',
    ];

    public array $proofOptions = [
        'all' => 'Todos',
        'missing' => 'Sin comprobante',
        'pending' => 'Comprobante pendiente',
        'approved' => 'Comprobante aprobado',
        'rejected' => 'Comprobante rechazado',
        'needs_review' => 'OCR en revision',
        'likely_proof' => 'OCR probable',
    ];

    public array $sortOptions = [
        'newest' => 'Mas recientes',
        'oldest' => 'Mas antiguos',
        'total_desc' => 'Total mayor',
        'total_asc' => 'Total menor',
        'delivery_asc' => 'Entrega proxima',
        'delivery_desc' => 'Entrega lejana',
        'risk_desc' => 'Riesgo alto',
    ];

    public array $perPageOptions = [
        '12' => '12',
        '25' => '25',
        '50' => '50',
        '100' => '100',
    ];

    private array $statusTransitions = [
        'payment_verified' => ['preparing'],
        'preparing' => ['ready_for_delivery'],
        'ready_for_delivery' => ['in_delivery'],
        'in_delivery' => ['delivered'],
    ];

    private array $paidStatuses = [
        'payment_verified',
        'preparing',
        'ready_for_delivery',
        'in_delivery',
        'delivered',
    ];

    private array $terminalStatuses = [
        'cancelled',
        'refunded',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSegment(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(string $value): void
    {
        if ($value !== 'all') {
            $this->segment = 'all';
        }
    }

    public function updatingRisk(): void
    {
        $this->resetPage();
    }

    public function updatingProof(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatingCreatedFrom(): void
    {
        $this->resetPage();
    }

    public function updatingCreatedTo(): void
    {
        $this->resetPage();
    }

    public function updatingDeliveryFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDeliveryTo(): void
    {
        $this->resetPage();
    }

    public function updatingMinTotal(): void
    {
        $this->resetPage();
    }

    public function updatingMaxTotal(): void
    {
        $this->resetPage();
    }

    public function selectOrder(int $orderId): void
    {
        $this->selectedOrder = Order::query()
            ->with([
                'paymentProofs' => fn($query) => $query->latest(),
                'items.product',
                'statusHistory.changer',
                'coupon',
                'user',
            ])
            ->findOrFail($orderId);

        $this->showDetails = true;
        $this->maskSensitive = true;
        $this->statusNote = '';
        $this->cancellationReason = '';
    }

    public function setSegment(string $segment): void
    {
        $this->segment = $segment;
        $this->status = 'all';
        $this->resetPage();
    }

    public function closeDetails(): void
    {
        $this->selectedOrder = null;
        $this->rejectionReasons = [];
        $this->showDetails = false;
        $this->maskSensitive = true;
        $this->statusNote = '';
        $this->cancellationReason = '';
    }

    public function approveProof(int $proofId): void
    {
        $blocked = false;

        DB::transaction(function () use ($proofId, &$blocked) {
            $proof = PaymentProof::query()->lockForUpdate()->findOrFail($proofId);
            $order = Order::query()->lockForUpdate()->findOrFail($proof->order_id);

            if ($proof->status !== 'pending') {
                return;
            }

            if (in_array($order->status, $this->terminalStatuses, true)) {
                $blocked = true;
                return;
            }

            $proof->status = 'approved';
            $proof->verified_by = auth()->id();
            $proof->verified_at = now();
            $proof->save();

            $alreadyPaid = in_array($order->status, $this->paidStatuses, true);

            if (!$alreadyPaid) {
                $order->paid_at = $order->paid_at ?? now();
                $order->changeStatus('payment_verified', auth()->id(), 'Pago verificado');

                $order->loadMissing('items');
                foreach ($order->items as $item) {
                    $product = Product::query()->find($item->product_id);
                    if ($product) {
                        $product->confirmSaleForOrder((int) $item->quantity, "Confirmacion pago {$order->order_number}");
                    }
                }
            }

            AuditService::orderAction('payment_proof_approved', $order->id, [
                'proof_id' => $proof->id,
                'already_paid' => $alreadyPaid,
            ]);
        });

        if ($blocked) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'No puedes aprobar comprobantes de pedidos cancelados o reembolsados.',
            ]);
            return;
        }

        if ($this->selectedOrder) {
            $this->selectOrder($this->selectedOrder->id);
        }
    }

    public function rejectProof(int $proofId): void
    {
        $reason = trim($this->rejectionReasons[$proofId] ?? '');
        if ($reason === '') {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Debes indicar un motivo de rechazo',
            ]);
            return;
        }

        $blocked = false;

        DB::transaction(function () use ($proofId, $reason, &$blocked) {
            $proof = PaymentProof::query()->lockForUpdate()->findOrFail($proofId);
            $order = Order::query()->lockForUpdate()->findOrFail($proof->order_id);

            if ($proof->status !== 'pending') {
                return;
            }

            if (in_array($order->status, $this->paidStatuses, true) || in_array($order->status, $this->terminalStatuses, true)) {
                $blocked = true;
                return;
            }

            $proof->status = 'rejected';
            $proof->verified_by = auth()->id();
            $proof->verified_at = now();
            $proof->rejection_reason = $reason;
            $proof->save();

            if ($order->status !== 'pending_payment') {
                $order->changeStatus('pending_payment', auth()->id(), 'Comprobante rechazado');
            }

            AuditService::orderAction('payment_rejected', $order->id, [
                'proof_id' => $proof->id,
                'reason' => $reason,
            ]);

            // Notificar al cliente del rechazo
            try {
                app(EmailService::class)->sendProofRejected($order, $reason);
            } catch (\Throwable $e) {
                report($e);
            }
        });

        if ($blocked) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Este pedido ya esta confirmado o cerrado y no puede rechazar el comprobante.',
            ]);
            return;
        }

        if ($this->selectedOrder) {
            $this->selectOrder($this->selectedOrder->id);
        }
    }

    public function cancelOrder(int $orderId): void
    {
        $reason = trim($this->cancellationReason);
        if ($reason === '') {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Debes indicar un motivo de cancelacion',
            ]);
            return;
        }

        $blocked = false;

        DB::transaction(function () use ($orderId, $reason, &$blocked) {
            $order = Order::query()->lockForUpdate()->findOrFail($orderId);
            $order->loadMissing('items');

            if (in_array($order->status, ['cancelled', 'refunded', 'delivered'], true)) {
                $blocked = true;
                return;
            }

            foreach ($order->items as $item) {
                $product = Product::query()->find($item->product_id);
                if ($product) {
                    $product->releaseReservedStock((int) $item->quantity, "Cancelacion {$order->order_number}");
                }
            }

            if ($order->status !== 'cancelled') {
                $order->cancellation_reason = $reason;
                $order->changeStatus('cancelled', auth()->id(), 'Pedido cancelado por admin');
            }

            AuditService::orderAction('order_cancelled', $order->id);
        });

        if ($blocked) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'No puedes cancelar un pedido ya cerrado.',
            ]);
            return;
        }

        if ($this->selectedOrder) {
            $this->selectOrder($this->selectedOrder->id);
        }
    }

    public function updateStatus(int $orderId, string $newStatus): void
    {
        $blockedReason = null;

        DB::transaction(function () use ($orderId, $newStatus, &$blockedReason) {
            $order = Order::query()->lockForUpdate()->findOrFail($orderId);
            $allowed = $this->statusTransitions[$order->status] ?? [];

            if (!in_array($newStatus, $allowed, true)) {
                $blockedReason = 'transition';
                return;
            }

            if ($newStatus === 'preparing') {
                $hasApprovedProof = $order->paymentProofs()
                    ->where('status', 'approved')
                    ->exists();
                if (!$hasApprovedProof) {
                    $blockedReason = 'proof';
                    return;
                }
            }

            $note = trim($this->statusNote);
            $previousStatus = $order->status;
            $order->changeStatus($newStatus, auth()->id(), $note !== '' ? $note : null);

            AuditService::orderAction('status_updated', $order->id, [
                'from' => $previousStatus,
                'to' => $newStatus,
                'note' => $note !== '' ? $note : null,
            ]);

            // Enviar email de cambio de estado al cliente
            try {
                app(EmailService::class)->sendOrderStatusChanged($order, $previousStatus, $newStatus);
            } catch (\Throwable $e) {
                report($e);
            }
        });

        if ($blockedReason === 'transition') {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Esta transicion no es valida para el estado actual.',
            ]);
            return;
        }

        if ($blockedReason === 'proof') {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'No puedes preparar un pedido sin comprobante aprobado.',
            ]);
            return;
        }

        $this->statusNote = '';

        if ($this->selectedOrder) {
            $this->selectOrder($this->selectedOrder->id);
        }
    }

    public function toggleSensitive(): void
    {
        $this->maskSensitive = !$this->maskSensitive;
    }

    public function clearFilters(): void
    {
        $this->segment = 'all';
        $this->status = 'all';
        $this->search = '';
        $this->risk = 'all';
        $this->proof = 'all';
        $this->sort = 'newest';
        $this->perPage = '12';
        $this->createdFrom = '';
        $this->createdTo = '';
        $this->deliveryFrom = '';
        $this->deliveryTo = '';
        $this->minTotal = '';
        $this->maxTotal = '';

        $this->resetPage();
    }

    public function render()
    {
        $countsByStatus = Order::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $segmentCounts = [
            'all' => array_sum($countsByStatus),
            'pending' => (int) ($countsByStatus['pending_payment'] ?? 0),
            'pending_validation' => (int) ($countsByStatus['pending_review'] ?? 0),
            'in_process' => (int) ($countsByStatus['payment_verified'] ?? 0)
                + (int) ($countsByStatus['preparing'] ?? 0)
                + (int) ($countsByStatus['ready_for_delivery'] ?? 0)
                + (int) ($countsByStatus['in_delivery'] ?? 0),
            'completed' => (int) ($countsByStatus['delivered'] ?? 0),
            'cancelled' => (int) ($countsByStatus['cancelled'] ?? 0) + (int) ($countsByStatus['refunded'] ?? 0),
        ];

        $query = Order::query()
            ->with('latestPaymentProof')
            ->select([
                'id',
                'order_number',
                'tracking_code',
                'customer_name_encrypted',
                'status',
                'total',
                'fraud_score',
                'delivery_date',
                'delivery_time_slot',
                'created_at',
            ]);

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        } elseif ($this->segment !== 'all') {
            $segments = [
                'pending' => ['pending_payment'],
                'pending_validation' => ['pending_review'],
                'in_process' => ['payment_verified', 'preparing', 'ready_for_delivery', 'in_delivery'],
                'completed' => ['delivered'],
                'cancelled' => ['cancelled', 'refunded'],
            ];

            $segmentStatuses = $segments[$this->segment] ?? [];
            if (!empty($segmentStatuses)) {
                $query->whereIn('status', $segmentStatuses);
            }
        }

        if ($this->risk === 'high') {
            $query->highRisk();
        } elseif ($this->risk === 'medium') {
            $query->mediumRisk();
        } elseif ($this->risk === 'low') {
            $query->lowRisk();
        }

        if ($this->proof === 'missing') {
            $query->doesntHave('paymentProofs');
        } elseif ($this->proof === 'pending') {
            $query->whereHas('latestPaymentProof', fn($proof) => $proof->where('status', 'pending'));
        } elseif ($this->proof === 'approved') {
            $query->whereHas('latestPaymentProof', fn($proof) => $proof->where('status', 'approved'));
        } elseif ($this->proof === 'rejected') {
            $query->whereHas('latestPaymentProof', fn($proof) => $proof->where('status', 'rejected'));
        } elseif ($this->proof === 'needs_review') {
            $query->whereHas('latestPaymentProof', fn($proof) => $proof->where('file_metadata->analysis_status', 'needs_review'));
        } elseif ($this->proof === 'likely_proof') {
            $query->whereHas('latestPaymentProof', fn($proof) => $proof->where('file_metadata->analysis_status', 'likely_proof'));
        }

        $search = trim($this->search);
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('order_number', 'like', '%' . $search . '%')
                    ->orWhere('tracking_code', 'like', '%' . $search . '%');
            });
        }

        if ($this->createdFrom !== '') {
            $query->whereDate('created_at', '>=', $this->createdFrom);
        }

        if ($this->createdTo !== '') {
            $query->whereDate('created_at', '<=', $this->createdTo);
        }

        if ($this->deliveryFrom !== '') {
            $query->whereDate('delivery_date', '>=', $this->deliveryFrom);
        }

        if ($this->deliveryTo !== '') {
            $query->whereDate('delivery_date', '<=', $this->deliveryTo);
        }

        $minTotal = $this->minTotal !== '' ? (int) $this->minTotal : null;
        $maxTotal = $this->maxTotal !== '' ? (int) $this->maxTotal : null;
        if ($minTotal !== null && $maxTotal !== null && $minTotal > $maxTotal) {
            [$minTotal, $maxTotal] = [$maxTotal, $minTotal];
        }

        if ($minTotal !== null) {
            $query->where('total', '>=', $minTotal);
        }

        if ($maxTotal !== null) {
            $query->where('total', '<=', $maxTotal);
        }

        $sortMap = [
            'newest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'total_desc' => ['total', 'desc'],
            'total_asc' => ['total', 'asc'],
            'delivery_asc' => ['delivery_date', 'asc'],
            'delivery_desc' => ['delivery_date', 'desc'],
            'risk_desc' => ['fraud_score', 'desc'],
        ];

        [$sortColumn, $sortDirection] = $sortMap[$this->sort] ?? $sortMap['newest'];
        $query->orderBy($sortColumn, $sortDirection);
        if ($sortColumn !== 'created_at') {
            $query->orderByDesc('created_at');
        }

        return view('livewire.admin.orders', [
            'orders' => $query->paginate((int) $this->perPage),
            'segmentOptions' => $this->segmentOptions,
            'segmentCounts' => $segmentCounts,
            'statusOptions' => $this->statusOptions,
            'riskOptions' => $this->riskOptions,
            'proofOptions' => $this->proofOptions,
            'sortOptions' => $this->sortOptions,
            'perPageOptions' => $this->perPageOptions,
            'statusTransitions' => $this->statusTransitions,
        ]);
    }
}
