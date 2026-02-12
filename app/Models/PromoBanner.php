<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PromoBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'icon',
        'bg_color',
        'text_color',
        'has_countdown',
        'starts_at',
        'ends_at',
        'button_text',
        'button_url',
        'show_on_pages',
        'is_active',
        'is_dismissible',
        'sort_order',
    ];

    protected $casts = [
        'has_countdown' => 'boolean',
        'is_active' => 'boolean',
        'is_dismissible' => 'boolean',
        'show_on_pages' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'sort_order' => 'integer',
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
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('sort_order');
    }

    // =========================================
    // ACCESSORS
    // =========================================

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->ends_at) {
            return false;
        }
        
        return Carbon::now()->greaterThan($this->ends_at);
    }

    public function getTimeRemainingAttribute(): ?array
    {
        if (!$this->ends_at || $this->is_expired) {
            return null;
        }

        $diff = Carbon::now()->diff($this->ends_at);
        
        return [
            'days' => $diff->days,
            'hours' => $diff->h,
            'minutes' => $diff->i,
            'seconds' => $diff->s,
        ];
    }

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
}
