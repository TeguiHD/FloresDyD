<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'icon',
        'parent_id',
        'sort_order',
        'is_active',
        'show_in_navbar',
        'show_in_home',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_navbar' => 'boolean',
        'show_in_home' => 'boolean',
        'sort_order' => 'integer',
    ];

    // =========================================
    // RELATIONSHIPS
    // =========================================

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // =========================================
    // SCOPES
    // =========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInNavbar($query)
    {
        return $query->where('show_in_navbar', true);
    }

    public function scopeInHome($query)
    {
        return $query->where('show_in_home', true);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // =========================================
    // ACCESSORS
    // =========================================

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image 
            ? asset('storage/' . $this->image)
            : asset('images/placeholder-category.jpg');
    }

    public function getProductsCountAttribute(): int
    {
        return $this->products()->where('is_active', true)->count();
    }
}
