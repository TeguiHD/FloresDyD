<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_price',
        'discount_percentage',
        'promo_starts_at',
        'promo_ends_at',
        'stock',
        'reserved_stock',
        'low_stock_threshold',
        'track_stock',
        'is_active',
        'is_featured',
        'is_new',
        'has_fresh_guarantee',
        'has_free_delivery',
        'free_delivery_city',
        'has_personalized_card',
        'custom_badges',
        'badge_overrides',
        'main_image',
        'gallery_images',
        'meta_title',
        'meta_description',
        'views_count',
        'sales_count',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'compare_price' => 'integer',
            'discount_percentage' => 'integer',
            'promo_starts_at' => 'datetime',
            'promo_ends_at' => 'datetime',
            'stock' => 'integer',
            'reserved_stock' => 'integer',
            'low_stock_threshold' => 'integer',
            'track_stock' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'has_fresh_guarantee' => 'boolean',
            'has_free_delivery' => 'boolean',
            'has_personalized_card' => 'boolean',
            'custom_badges' => 'array',
            'badge_overrides' => 'array',
            'gallery_images' => 'array',
            'views_count' => 'integer',
            'sales_count' => 'integer',
        ];
    }

    // =============================================
    // RELACIONES
    // =============================================

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockReservations()
    {
        return $this->hasMany(StockReservation::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function activeVariants()
    {
        return $this->variants()->where('is_active', true);
    }

    public function stockLogs()
    {
        return $this->hasMany(ProductStockLog::class);
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists')
            ->withTimestamps();
    }

    // =============================================
    // ACCESSORS
    // =============================================

    /**
     * Precio formateado
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Precio actual (alias para compatibilidad en vistas)
     */
    public function getCurrentPriceAttribute(): int
    {
        return $this->price;
    }

    /**
     * Imagen principal (alias para compatibilidad en vistas)
     */
    public function getImageAttribute(): string
    {
        return $this->main_image;
    }

    public function getImageUrlsAttribute(): array
    {
        $images = array_merge([$this->main_image], $this->gallery_images ?? []);
        $images = array_values(array_unique(array_filter($images)));

        return array_map(function (string $image): string {
            if (str_starts_with($image, 'http')) {
                return $image;
            }
            return asset('storage/' . $image);
        }, $images);
    }

    /**
     * Precio anterior formateado
     */
    public function getFormattedComparePriceAttribute(): ?string
    {
        if (!$this->compare_price) {
            return null;
        }
        return '$' . number_format($this->compare_price, 0, ',', '.');
    }

    /**
     * Stock disponible (total - reservado)
     */
    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock - $this->reserved_stock);
    }

    /**
     * ¿Tiene stock disponible?
     */
    public function getInStockAttribute(): bool
    {
        if (!$this->track_stock) {
            return true;
        }
        return $this->available_stock > 0;
    }

    /**
     * ¿Stock bajo?
     */
    public function getLowStockAttribute(): bool
    {
        if (!$this->track_stock) {
            return false;
        }
        return $this->available_stock <= $this->low_stock_threshold;
    }

    /**
     * Rating promedio
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Cantidad de reseñas
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * ¿Tiene descuento?
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    /**
     * Calcular porcentaje de descuento
     */
    public function getCalculatedDiscountAttribute(): ?int
    {
        if (!$this->has_discount) {
            return null;
        }
        return (int) round((1 - $this->price / $this->compare_price) * 100);
    }

    /**
     * URL de imagen principal
     */
    public function getMainImageUrlAttribute(): string
    {
        if (str_starts_with($this->main_image, 'http')) {
            return $this->main_image;
        }
        return asset('storage/' . $this->main_image);
    }

    /**
     * ¿Promoción activa?
     */
    public function getPromoActiveAttribute(): bool
    {
        if (!$this->compare_price || $this->compare_price <= $this->price) {
            return false;
        }
        if ($this->promo_starts_at && $this->promo_starts_at->isFuture()) {
            return false;
        }
        if ($this->promo_ends_at && $this->promo_ends_at->isPast()) {
            return false;
        }
        return true;
    }

    /**
     * Precio mínimo y máximo considerando variantes activas.
     *
     * @return array{min:int,max:int}
     */
    public function getVariantPriceRangeAttribute(): array
    {
        $variants = $this->activeVariants()->get();
        if ($variants->isEmpty()) {
            return ['min' => $this->price, 'max' => $this->price];
        }

        $prices = $variants->flatMap(function (ProductVariant $variant) {
            $basePrice = $variant->price_override !== null ? (int) $variant->price_override : (int) $this->price;

            if ($variant->type === 'range') {
                $min = max(1, (int) ($variant->min_value ?? 1));
                $max = (int) ($variant->max_value ?? $min);
                $pricePerUnit = (int) ($variant->price_per_unit ?? 0);
                $minPrice = $basePrice;
                $maxExtra = max(0, $max - $min) * $pricePerUnit;
                $maxPrice = $basePrice + $maxExtra;
                return [$minPrice, $maxPrice];
            }

            if ($variant->price_override !== null) {
                return [$basePrice];
            }
            return [(int) $basePrice + (int) $variant->price_modifier];
        });

        return [
            'min' => $prices->min(),
            'max' => $prices->max(),
        ];
    }

    public function getIsBestsellerAttribute(): bool
    {
        return $this->sales_count >= 20;
    }

    public function getBadgeLabel(string $key, string $fallback, array $replacements = []): string
    {
        $label = $fallback;
        $overrides = is_array($this->badge_overrides) ? $this->badge_overrides : [];

        if (array_key_exists($key, $overrides)) {
            $override = trim((string) $overrides[$key]);
            if ($override !== '') {
                $label = $override;
            }
        }

        foreach ($replacements as $token => $value) {
            $label = str_replace('{' . $token . '}', (string) $value, $label);
        }

        return $label;
    }

    // =============================================
    // MÉTODOS
    // =============================================

    /**
     * Incrementar contador de vistas
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Reservar stock temporalmente
     */
    public function reserveStock(int $quantity, ?int $userId = null, ?string $sessionId = null): ?StockReservation
    {
        if (!$this->track_stock) {
            return null;
        }

        if ($this->available_stock < $quantity) {
            return null;
        }

        // Crear reserva
        $reservation = $this->stockReservations()->create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'quantity' => $quantity,
            'expires_at' => now()->addMinutes(config('flores.stock.reservation_time', 30)),
        ]);

        // Incrementar stock reservado
        $this->increment('reserved_stock', $quantity);

        // Log
        $this->logStockChange('reserve', $quantity, "Reserva #{$reservation->id}");

        return $reservation;
    }

    /**
     * Liberar reserva de stock
     */
    public function releaseReservation(StockReservation $reservation): void
    {
        $this->decrement('reserved_stock', $reservation->quantity);
        $this->logStockChange('release', $reservation->quantity, "Liberación reserva #{$reservation->id}");
        $reservation->delete();
    }

    /**
     * Confirmar venta (descontar stock real)
     */
    public function confirmSale(int $quantity, ?StockReservation $reservation = null): void
    {
        if ($reservation) {
            $this->decrement('reserved_stock', $reservation->quantity);
            $reservation->delete();
        }

        $this->decrement('stock', $quantity);
        $this->increment('sales_count');
        $this->logStockChange('sale', -$quantity, "Venta confirmada");
    }

    /**
     * Confirmar venta desde un pedido sin reserva asociada.
     */
    public function confirmSaleForOrder(int $quantity, ?string $reason = null): void
    {
        if (!$this->track_stock) {
            return;
        }

        $release = min($quantity, $this->reserved_stock);
        if ($release > 0) {
            $this->decrement('reserved_stock', $release);
        }

        $this->decrement('stock', $quantity);
        $this->increment('sales_count');
        $this->logStockChange('sale', -$quantity, $reason ?? 'Venta confirmada (pedido)');
    }

    /**
     * Liberar stock reservado desde un pedido cancelado.
     */
    public function releaseReservedStock(int $quantity, ?string $reason = null): void
    {
        if (!$this->track_stock) {
            return;
        }

        $release = min($quantity, $this->reserved_stock);
        if ($release <= 0) {
            return;
        }

        $this->decrement('reserved_stock', $release);
        $this->logStockChange('release', $release, $reason ?? 'Liberación por cancelación');
    }

    /**
     * Registrar cambio de stock
     */
    public function logStockChange(string $type, int $quantity, ?string $reason = null): void
    {
        $this->stockLogs()->create([
            'user_id' => auth()->id(),
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $this->stock + ($type === 'sale' ? $quantity : 0),
            'stock_after' => $this->stock,
            'reason' => $reason,
        ]);
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('track_stock', false)
                ->orWhereRaw('stock - reserved_stock > 0');
        });
    }

    public function scopeWithDiscount($query)
    {
        return $query->whereNotNull('compare_price')
            ->whereColumn('compare_price', '>', 'price');
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('short_description', 'like', "%{$term}%");
        });
    }
}
