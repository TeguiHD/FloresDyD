<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEncryptedAttributes;

class UserNameHistory extends Model
{
    use HasFactory, HasEncryptedAttributes;

    protected $fillable = [
        'user_id',
        'old_name',
        'new_name',
        'changed_by',
        'ip_address',
        'user_agent',
    ];

    protected array $encryptedAttributes = [
        'old_name_encrypted',
        'new_name_encrypted',
    ];

    public function getOldNameAttribute(): ?string
    {
        return $this->decryptAttribute($this->attributes['old_name_encrypted'] ?? null);
    }

    public function setOldNameAttribute(?string $value): void
    {
        $this->attributes['old_name_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    public function getNewNameAttribute(): ?string
    {
        return $this->decryptAttribute($this->attributes['new_name_encrypted'] ?? null);
    }

    public function setNewNameAttribute(?string $value): void
    {
        $this->attributes['new_name_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
