<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasEncryptedAttributes;
use App\Services\PasswordService;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, HasEncryptedAttributes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_encrypted',
        'address_encrypted',
        'trust_score',
        'successful_orders',
        'is_whitelisted',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'phone_encrypted',
        'address_encrypted',
    ];

    // Atributos que se cifran automáticamente en BD
    protected array $encryptedAttributes = [
        'phone_encrypted',
        'address_encrypted',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked_until' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'is_whitelisted' => 'boolean',
            'trust_score' => 'integer',
            'successful_orders' => 'integer',
            'failed_login_attempts' => 'integer',
        ];
    }

    // =============================================
    // ACCESSORS Y MUTATORS PARA DATOS CIFRADOS
    // =============================================

    /**
     * Obtener teléfono descifrado
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->decryptAttribute('phone_encrypted');
    }

    /**
     * Establecer teléfono (se cifra automáticamente)
     */
    public function setPhoneAttribute(?string $value): void
    {
        $this->attributes['phone_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    /**
     * Obtener dirección descifrada
     */
    public function getAddressAttribute(): ?string
    {
        return $this->decryptAttribute('address_encrypted');
    }

    /**
     * Establecer dirección (se cifra automáticamente)
     */
    public function setAddressAttribute(?string $value): void
    {
        $this->attributes['address_encrypted'] = $value ? $this->encryptAttribute($value) : null;
    }

    // =============================================
    // PASSWORD CON ARGON2ID + PEPPERING
    // =============================================

    /**
     * Hashear password con Argon2id + Pepper
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = app(PasswordService::class)->hash($value);
    }

    /**
     * Verificar password
     */
    public function verifyPassword(string $password): bool
    {
        return app(PasswordService::class)->verify($password, $this->password);
    }

    // =============================================
    // RELACIONES
    // =============================================

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlist()
    {
        return $this->belongsToMany(Product::class, 'wishlists')
            ->withTimestamps();
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    // =============================================
    // TRUST SCORE Y ANTI-FRAUDE
    // =============================================

    /**
     * Incrementar trust score después de compra exitosa
     */
    public function incrementTrustScore(int $amount = 10): void
    {
        $this->increment('successful_orders');
        $this->trust_score = min(100, $this->trust_score + $amount);
        
        // Auto-whitelist después de 2 compras exitosas
        if ($this->successful_orders >= 2 && !$this->is_whitelisted) {
            $this->is_whitelisted = true;
        }
        
        $this->save();
    }

    /**
     * Decrementar trust score por comportamiento sospechoso
     */
    public function decrementTrustScore(int $amount = 15): void
    {
        $this->trust_score = max(0, $this->trust_score - $amount);
        $this->save();
    }

    /**
     * Verificar si la cuenta está bloqueada
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Bloquear cuenta por intentos fallidos
     */
    public function lockAccount(int $minutes = 15): void
    {
        $this->locked_until = now()->addMinutes($minutes);
        $this->save();
    }

    /**
     * Registrar intento de login fallido
     */
    public function recordFailedLogin(): void
    {
        $this->increment('failed_login_attempts');
        
        if ($this->failed_login_attempts >= config('flores.rate_limit_login_attempts', 5)) {
            $this->lockAccount();
        }
    }

    /**
     * Resetear intentos fallidos después de login exitoso
     */
    public function resetFailedLogins(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_ip' => request()->ip(),
        ]);
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopeWhitelisted($query)
    {
        return $query->where('is_whitelisted', true);
    }

    public function scopeTrusted($query, int $minScore = 70)
    {
        return $query->where('trust_score', '>=', $minScore);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('locked_until')
                    ->orWhere('locked_until', '<', now());
            });
    }
}
