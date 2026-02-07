<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'order_id',
        'customer_name',
        'customer_email',
        'rating',
        'title',
        'comment',
        'images',
        'is_verified_purchase',
        'is_approved',
        'approved_at',
        'approved_by',
        'admin_response',
        'admin_response_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'images' => 'array',
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'admin_response_at' => 'datetime',
    ];

    // =========================================
    // RELATIONSHIPS
    // =========================================

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // =========================================
    // SCOPES
    // =========================================

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    // =========================================
    // METHODS
    // =========================================

    public function approve(int $userId): void
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);
    }

    public function addAdminResponse(string $response): void
    {
        $this->update([
            'admin_response' => $response,
            'admin_response_at' => now(),
        ]);
    }

    // =========================================
    // ACCESSORS
    // =========================================

    public function getStarsHtmlAttribute(): string
    {
        $filled = str_repeat('★', $this->rating);
        $empty = str_repeat('☆', 5 - $this->rating);
        return $filled . $empty;
    }
}
