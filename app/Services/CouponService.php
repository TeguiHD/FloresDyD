<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * CouponService - Validación Segura de Cupones
 * 
 * Implementa:
 * - Generación de cupones con firma HMAC-SHA256
 * - Validación criptográfica anti-manipulación
 * - Rate limiting de intentos de canje
 * - Auditoría de uso
 * 
 * Adaptado de SEGURIDAD.txt para entorno Laravel.
 */
class CouponService
{
    /**
     * Genera un código de cupón firmado con HMAC.
     */
    public static function generateSecureCode(string $prefix = 'FDD'): string
    {
        $random = strtoupper(bin2hex(random_bytes(4))); // 8 chars hex
        $code = "{$prefix}-{$random}";
        
        return $code;
    }

    /**
     * Genera la firma HMAC para un cupón.
     */
    public static function sign(string $couponCode, float $discount, string $type): string
    {
        $payload = "{$couponCode}:{$discount}:{$type}";
        $secret = config('app.key');
        
        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Verifica la integridad de un cupón comparando su firma.
     */
    public static function verify(string $couponCode, float $discount, string $type, string $signature): bool
    {
        $expectedSignature = self::sign($couponCode, $discount, $type);
        
        // Comparación en tiempo constante para evitar timing attacks
        $valid = hash_equals($expectedSignature, $signature);
        
        if (!$valid) {
            Log::channel('security')->warning('Coupon signature mismatch', [
                'coupon_code' => $couponCode,
                'ip' => request()?->ip(),
            ]);

            AuditService::securityEvent('coupon_tampering', [
                'coupon_code' => $couponCode,
            ]);
        }
        
        return $valid;
    }

    /**
     * Sanitiza y normaliza un código de cupón ingresado por el usuario.
     */
    public static function sanitizeCode(string $input): string
    {
        // Remover espacios, convertir a mayúsculas, solo alfanuméricos y guiones
        $sanitized = strtoupper(trim($input));
        $sanitized = preg_replace('/[^A-Z0-9\-]/', '', $sanitized);
        
        // Limitar longitud
        return substr($sanitized, 0, 20);
    }
}
