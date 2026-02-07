<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

/**
 * Servicio de hashing de passwords con Argon2id + Peppering
 * Basado en OWASP Password Storage Cheat Sheet 2025
 */
class PasswordService
{
    private string $pepper;

    public function __construct()
    {
        $this->pepper = config('app.password_pepper', env('PASSWORD_PEPPER', ''));
        
        if (empty($this->pepper) && app()->environment('production')) {
            throw new \RuntimeException('PASSWORD_PEPPER must be set in production');
        }
    }

    /**
     * Hash password con Argon2id + Pepper
     * 
     * Parámetros OWASP 2025:
     * - memory: 19456 KiB (19 MiB) - Mínimo recomendado
     * - time: 2 iteraciones
     * - threads: 1
     */
    public function hash(string $password): string
    {
        // Aplicar pepper antes del hash
        $pepperedPassword = $this->applyPepper($password);
        
        // Hash con Argon2id
        return password_hash($pepperedPassword, PASSWORD_ARGON2ID, [
            'memory_cost' => 19456,  // 19 MiB
            'time_cost' => 2,        // 2 iteraciones
            'threads' => 1,          // 1 thread
        ]);
    }

    /**
     * Verificar password
     */
    public function verify(string $password, string $hash): bool
    {
        $pepperedPassword = $this->applyPepper($password);
        
        return password_verify($pepperedPassword, $hash);
    }

    /**
     * Verificar si el hash necesita rehash (parámetros actualizados)
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_ARGON2ID, [
            'memory_cost' => 19456,
            'time_cost' => 2,
            'threads' => 1,
        ]);
    }

    /**
     * Aplicar pepper al password usando HMAC-SHA256
     */
    private function applyPepper(string $password): string
    {
        if (empty($this->pepper)) {
            return $password;
        }
        
        // Usar HMAC para aplicar el pepper de forma segura
        return hash_hmac('sha256', $password, $this->pepper);
    }

    /**
     * Generar un hash seguro para tokens (reset password, etc.)
     */
    public function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Generar token aleatorio seguro
     */
    public function generateSecureToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}
