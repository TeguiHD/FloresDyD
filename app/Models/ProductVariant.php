<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'label',
        'type',
        'price_modifier',
        'price_override',
        'min_value',
        'max_value',
        'step_value',
        'price_per_unit',
        'unit_label',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_modifier' => 'integer',
            'price_override' => 'integer',
            'min_value' => 'integer',
            'max_value' => 'integer',
            'step_value' => 'integer',
            'price_per_unit' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
