<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasEncryptedAttributes;
use App\Services\FraudDetectionService;

class Order extends Model
{
    use HasFactory, SoftDeletes, HasEncryptedAttributes;

    protected $fillable = [
        'order_number',
        'tracking_code',
        'user_id',
        'status',
        'customer_name_encrypted',
        'customer_email_encrypted',
        'customer_phone_encrypted',
        'delivery_address_encrypted',
        'delivery_city_encrypted',
        'delivery_zip_encrypted',
        'delivery_notes_encrypted',
        'delivery_date',
        'delivery_time_slot',
        'delivery_method',
        'card_message_encrypted',
        'card_recipient',
        'card_sender',
        'subtotal',
        'discount_amount',
        'delivery_fee',
        'total',
        'coupon_id',
        'coupon_code',
        'fraud_score',
        'fraud_factors',
        'ip_address',
        'user_agent',
        'fingerprint_hash',
        'admin_notes',
        'cancellation_reason',
    ];

    // Atributos cifrados
    protected array $encryptedAttributes = [
        'customer_name_encrypted',
        'customer_email_encrypted',
        'customer_phone_encrypted',
        'delivery_address_encrypted',
        'delivery_city_encrypted',
        'delivery_zip_encrypted',
        'delivery_notes_encrypted',
        'card_message_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'fraud_factors' => 'array',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
            'prepared_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'subtotal' => 'integer',
            'discount_amount' => 'integer',
            'delivery_fee' => 'integer',
            'total' => 'integer',
            'fraud_score' => 'integer',
        ];
    }

    // =============================================
    // ACCESSORS PARA DATOS CIFRADOS
    // =============================================

    public function getCustomerNameAttribute(): ?string
    {
        return $this->decryptAttribute($this->attributes['customer_name_encrypted'] ?? null);
    }

    public function setCustomerNameAttribute(?string $value): void
    {
        $this->attributes['customer_name_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->decryptAttribute($this->attributes['customer_email_encrypted'] ?? null);
    }

    public function setCustomerEmailAttribute(?string $value): void
    {
        $this->attributes['customer_email_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->decryptAttribute($this->attributes['customer_phone_encrypted'] ?? null);
    }

    public function setCustomerPhoneAttribute(?string $value): void
    {
        $this->attributes['customer_phone_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    public function getDeliveryAddressAttribute(): ?string
    {
        return $this->decryptAttribute($this->attributes['delivery_address_encrypted'] ?? null);
    }

    public function setDeliveryAddressAttribute(?string $value): void
    {
        $this->attributes['delivery_address_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    public function getDeliveryCityAttribute(): ?string
    {
        return $this->decryptAttribute($this->attributes['delivery_city_encrypted'] ?? null);
    }

    public function setDeliveryCityAttribute(?string $value): void
    {
        $this->attributes['delivery_city_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    public function getDeliveryZipAttribute(): ?string
    {
        return $this->decryptAttribute($this->attributes['delivery_zip_encrypted'] ?? null);
    }

    public function setDeliveryZipAttribute(?string $value): void
    {
        $this->attributes['delivery_zip_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    public function getDeliveryNotesAttribute(): ?string
    {
        return $this->decryptAttribute($this->attributes['delivery_notes_encrypted'] ?? null);
    }

    public function setDeliveryNotesAttribute(?string $value): void
    {
        $this->attributes['delivery_notes_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    public function getCardMessageAttribute(): ?string
    {
        return $this->decryptAttribute($this->attributes['card_message_encrypted'] ?? null);
    }

    public function setCardMessageAttribute(?string $value): void
    {
        $this->attributes['card_message_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    /**
     * Alias para compatibilidad en vistas
     */
    public function getDeliveryTimeAttribute(): ?string
    {
        return $this->delivery_time_slot;
    }

    // =============================================
    // RELACIONES
    // =============================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentProofs()
    {
        return $this->hasMany(PaymentProof::class);
    }

    public function latestPaymentProof()
    {
        return $this->hasOne(PaymentProof::class)->latestOfMany();
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    // =============================================
    // MÉTODOS
    // =============================================

    /**
     * Generar número de pedido único
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastOrder ? (int) substr($lastOrder->order_number, -5) + 1 : 1;
        
        return sprintf('FDD-%s-%05d', $year, $sequence);
    }

    /**
     * Generar tracking code público para seguimiento.
     */
    public static function generateTrackingCode(): string
    {
        return 'TRK-' . strtoupper(bin2hex(random_bytes(4)));
    }

    /**
     * Calcular fraud score
     */
    public function calculateFraudScore(): int
    {
        return app(FraudDetectionService::class)->calculateScore($this);
    }

    /**
     * Obtener nivel de riesgo
     */
    public function getRiskLevel(): string
    {
        return match (true) {
            $this->fraud_score >= 75 => 'high',
            $this->fraud_score >= 50 => 'medium',
            default => 'low',
        };
    }

    /**
     * Obtener color del semáforo de riesgo
     */
    public function getRiskColor(): string
    {
        return match ($this->getRiskLevel()) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
        };
    }

    /**
     * Cambiar estado con registro en historial
     */
    public function changeStatus(string $newStatus, ?int $changedBy = null, ?string $notes = null): void
    {
        $previousStatus = $this->status;
        
        // Actualizar estado
        $this->status = $newStatus;
        
        // Actualizar timestamp según estado
        match ($newStatus) {
            'payment_verified' => $this->verified_at = now(),
            'preparing' => $this->prepared_at = now(),
            'in_delivery' => $this->shipped_at = now(),
            'delivered' => $this->delivered_at = now(),
            'cancelled' => $this->cancelled_at = now(),
            default => null,
        };
        
        $this->save();
        
        // Registrar en historial
        $this->statusHistory()->create([
            'from_status' => $previousStatus,
            'to_status' => $newStatus,
            'changed_by' => $changedBy,
            'notes' => $notes,
        ]);
    }

    /**
     * Formatear total para mostrar
     */
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total, 0, ',', '.');
    }

    /**
     * Obtener etiqueta del estado
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending_payment' => 'Esperando comprobante',
            'pending_review' => 'En revisión',
            'payment_verified' => 'Pago verificado',
            'preparing' => 'En preparación',
            'ready_for_delivery' => 'Listo para envío',
            'in_delivery' => 'En camino',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending_payment', 'pending_review']);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'refunded', 'delivered']);
    }

    public function scopeHighRisk($query)
    {
        return $query->where('fraud_score', '>=', 75);
    }

    public function scopeMediumRisk($query)
    {
        return $query->whereBetween('fraud_score', [50, 74]);
    }

    public function scopeLowRisk($query)
    {
        return $query->where('fraud_score', '<', 50);
    }
}
