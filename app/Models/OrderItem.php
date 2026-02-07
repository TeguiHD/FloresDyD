<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasEncryptedAttributes;

/**
 * OrderItem Model
 * 
 * Representa un producto individual dentro de una orden.
 */
class OrderItem extends Model
{
    use HasFactory, HasEncryptedAttributes;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'unit_price',
        'subtotal',
        'discount_amount',
        'card_message',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    /**
     * Campos que se cifran en la base de datos
     */
    protected array $encryptedAttributes = [
        'card_message', // Mensaje personal, datos sensibles
    ];

    // =========================================
    // RELATIONSHIPS
    // =========================================

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // =========================================
    // ACCESSORS
    // =========================================

    public function getTotalAttribute(): float
    {
        return $this->subtotal - $this->discount_amount;
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return '$' . number_format($this->unit_price, 2);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '$' . number_format($this->subtotal, 2);
    }
}
