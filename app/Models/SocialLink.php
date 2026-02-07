<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'platform',
        'url',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Plataformas soportadas
    public const PLATFORMS = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'tiktok' => 'TikTok',
        'whatsapp' => 'WhatsApp',
        'youtube' => 'YouTube',
        'twitter' => 'X (Twitter)',
        'pinterest' => 'Pinterest',
        'linkedin' => 'LinkedIn',
        'other' => 'Otro',
    ];

    // =========================================
    // SCOPES
    // =========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // =========================================
    // ACCESSORS
    // =========================================

    public function getPlatformNameAttribute(): string
    {
        return self::PLATFORMS[$this->platform] ?? $this->platform;
    }
}
