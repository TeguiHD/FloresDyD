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
        'product_variant_id',
        'product_name',
        'variant_label',
        'variant_type',
        'custom_value',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'product_variant_id' => 'integer',
        'quantity' => 'integer',
        'custom_value' => 'integer',
        'unit_price' => 'integer',
        'total_price' => 'integer',
    ];

    /**
     * Campos que se cifran en la base de datos
     */
    protected array $encryptedAttributes = [
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

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // =========================================
    // ACCESSORS
    // =========================================

    public function getTotalAttribute(): float
    {
        return $this->total_price;
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return '$' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return '$' . number_format($this->total_price, 0, ',', '.');
    }
}
