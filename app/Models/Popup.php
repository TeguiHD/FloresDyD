<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Popup extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'content',
        'image',
        'button_text',
        'button_url',
        'trigger',
        'trigger_value',
        'show_on_pages',
        'show_once_per_session',
        'show_once_per_user',
        'is_active',
        'starts_at',
        'expires_at',
        'views_count',
        'clicks_count',
    ];

    protected $casts = [
        'trigger_value' => 'integer',
        'show_on_pages' => 'array',
        'show_once_per_session' => 'boolean',
        'show_once_per_user' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'views_count' => 'integer',
        'clicks_count' => 'integer',
    ];

    // =========================================
    // SCOPES
    // =========================================

    public function scopeActive($query)
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', $now);
            });
    }

    // =========================================
    // HELPERS
    // =========================================

    public function isVisibleOnPath(string $path): bool
    {
        $path = '/' . ltrim($path, '/');
        $pages = $this->show_on_pages ?? [];

        if (empty($pages)) {
            return true;
        }

        $normalized = array_values(array_filter(array_map(function ($page) {
            $page = trim((string) $page);
            if ($page === '') {
                return null;
            }
            if ($page === '*') {
                return '*';
            }
            return '/' . ltrim($page, '/');
        }, $pages)));

        foreach ($normalized as $page) {
            if ($page === '*') {
                return true;
            }
            if ($page === '/' && ($path === '/' || $path === '')) {
                return true;
            }
            if (Str::is($page, $path)) {
                return true;
            }
        }

        return false;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }
}
