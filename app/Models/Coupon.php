<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_purchase_amount',
        'max_discount_amount',
        'max_uses',
        'max_uses_per_user',
        'uses_count',
        'starts_at',
        'expires_at',
        'is_active',
        'applicable_products',
        'applicable_categories',
        'excluded_products',
        'first_purchase_only',
        'applicable_users',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'min_purchase_amount' => 'integer',
            'max_discount_amount' => 'integer',
            'max_uses' => 'integer',
            'max_uses_per_user' => 'integer',
            'uses_count' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'first_purchase_only' => 'boolean',
            'applicable_products' => 'array',
            'applicable_categories' => 'array',
            'excluded_products' => 'array',
            'applicable_users' => 'array',
        ];
    }
}
