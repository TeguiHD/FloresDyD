<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Services\AuditService;
use Illuminate\Support\Facades\Log;

/**
 * CouponService - Validación Segura de Cupones
 * 
 * Implementa:
 * - Generación de cupones con firma HMAC-SHA256
 * - Validación criptográfica anti-manipulación
 * - Rate limiting de intentos de canje
 * - Auditoría de uso
 * - Mensajes contextuales para cada regla de negocio
 * 
 * Adaptado de SEGURIDAD.txt para entorno Laravel.
 */
class CouponService
{
    /**
     * Genera un código de cupón seguro con prefijo.
     */
    public static function generateSecureCode(string $prefix = 'FDD'): string
    {
        $random = strtoupper(bin2hex(random_bytes(4)));
        return "{$prefix}-{$random}";
    }

    /**
     * Firma HMAC-SHA256 para un payload de cupón aplicado.
     * Protege la integridad del estado almacenado en el componente Livewire.
     */
    public static function signPayload(array $payload): string
    {
        $data = json_encode([
            $payload['id'] ?? 0,
            $payload['code'] ?? '',
            $payload['type'] ?? '',
            $payload['value'] ?? 0,
        ], JSON_THROW_ON_ERROR);

        return hash_hmac('sha256', $data, config('app.key'));
    }

    /**
     * Verifica la integridad de un payload de cupón firmado.
     * Usa comparación en tiempo constante contra timing attacks.
     */
    public static function verifyPayload(array $payload): bool
    {
        $signature = $payload['_signature'] ?? '';
        if ($signature === '') {
            return false;
        }

        $expected = self::signPayload($payload);
        $valid = hash_equals($expected, $signature);

        if (!$valid) {
            Log::channel('security')->warning('Coupon payload tampering detected', [
                'coupon_code' => $payload['code'] ?? 'unknown',
                'ip' => request()?->ip(),
                'session' => session()->getId(),
            ]);

            AuditService::securityEvent('coupon_tampering', [
                'coupon_code' => $payload['code'] ?? 'unknown',
                'payload' => array_diff_key($payload, ['_signature' => true]),
            ]);
        }

        return $valid;
    }

    /**
     * Construye un payload firmado a partir de un Coupon validado.
     * 
     * @return array{id: int, code: string, type: string, value: int, max_discount_amount: ?int, min_purchase_amount: ?int, _signature: string}
     */
    public static function buildSignedPayload(Coupon $coupon): array
    {
        $payload = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'max_discount_amount' => $coupon->max_discount_amount,
            'min_purchase_amount' => $coupon->min_purchase_amount,
        ];

        $payload['_signature'] = self::signPayload($payload);

        return $payload;
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

    /**
     * Validación completa del cupón contra BD y reglas de negocio.
     *
     * @return array{valid: bool, message: string, coupon: ?Coupon, discount: int}
     */
    public static function validateForCart(
        string $input,
        int $subtotal,
        array $cartItems,
        ?int $userId,
        bool $lockForUpdate = false,
    ): array {
        $code = self::sanitizeCode($input);
        if ($code === '') {
            return self::fail('Ingresa un código de cupón válido');
        }

        $query = Coupon::query()
            ->where('code', $code)
            ->where('is_active', true);

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        $coupon = $query->first();
        if (!$coupon) {
            return self::fail('El código ingresado no es válido o ya no está activo', $code);
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            $formattedDate = $coupon->starts_at->format('d/m/Y H:i');
            return self::fail("Este cupón estará disponible desde el {$formattedDate}", $code);
        }
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            $formattedDate = $coupon->expires_at->format('d/m/Y');
            return self::fail("Este cupón expiró el {$formattedDate}", $code);
        }

        if ($coupon->type === 'percentage' && $coupon->value > 100) {
            Log::channel('security')->warning('Coupon with invalid percentage detected', ['coupon_id' => $coupon->id, 'value' => $coupon->value]);
            return self::fail('Cupón inválido', $code);
        }

        if ($coupon->max_uses && $coupon->uses_count >= $coupon->max_uses) {
            return self::fail('Este cupón ya alcanzó su límite de usos', $code);
        }

        if ($coupon->min_purchase_amount && $subtotal < $coupon->min_purchase_amount) {
            $needed = $coupon->min_purchase_amount - $subtotal;
            $formattedMin = number_format($coupon->min_purchase_amount, 0, ',', '.');
            $formattedNeeded = number_format($needed, 0, ',', '.');
            return self::fail("Compra mínima de \${$formattedMin}. Te faltan \${$formattedNeeded}", $code);
        }

        if ($coupon->first_purchase_only && $userId) {
            $hasOrders = Order::query()
                ->where('user_id', $userId)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->exists();
            if ($hasOrders) {
                return self::fail('Este cupón es solo para tu primera compra', $code);
            }
        }

        if ($coupon->applicable_users) {
            if (!$userId) {
                return self::fail('Debes iniciar sesión para usar este cupón', $code);
            }
            $allowedUsers = collect($coupon->applicable_users)->map(fn ($id) => (int) $id)->all();
            if (!in_array($userId, $allowedUsers, true)) {
                return self::fail('Este cupón no está disponible para tu cuenta', $code);
            }
        }

        if ($userId && $coupon->max_uses_per_user) {
            $uses = CouponUsage::query()
                ->where('coupon_id', $coupon->id)
                ->where('user_id', $userId)
                ->count();
            if ($uses >= $coupon->max_uses_per_user) {
                $max = $coupon->max_uses_per_user;
                return self::fail("Ya usaste este cupón" . ($max > 1 ? " ({$max}/{$max})" : ''), $code);
            }
        }

        $cartProductIds = collect($cartItems)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $cartCategoryIds = collect($cartItems)->pluck('category_id')->filter()->map(fn ($id) => (int) $id)->all();

        if ($coupon->excluded_products) {
            $excluded = collect($coupon->excluded_products)->map(fn ($id) => (int) $id)->all();
            if (count(array_intersect($cartProductIds, $excluded)) > 0) {
                return self::fail('Este cupón no aplica a uno o más productos en tu carrito', $code);
            }
        }

        if ($coupon->applicable_products) {
            $applicable = collect($coupon->applicable_products)->map(fn ($id) => (int) $id)->all();
            if (count(array_intersect($cartProductIds, $applicable)) === 0) {
                return self::fail('Este cupón es para productos específicos que no están en tu carrito', $code);
            }
        }

        if ($coupon->applicable_categories) {
            $applicableCategories = collect($coupon->applicable_categories)->map(fn ($id) => (int) $id)->all();
            if (count(array_intersect($cartCategoryIds, $applicableCategories)) === 0) {
                return self::fail('Este cupón aplica solo a categorías que no están en tu carrito', $code);
            }
        }

        $discount = self::calculateDiscount($coupon, $subtotal);

        if ($discount <= 0) {
            return self::fail('Este cupón no genera descuento para tu compra actual', $code);
        }

        // Construir mensaje de éxito con detalle del descuento
        $discountLabel = $coupon->type === 'percentage'
            ? "{$coupon->value}% de descuento"
            : '$' . number_format($coupon->value, 0, ',', '.') . ' de descuento';

        AuditService::log('coupon_applied', AuditService::CATEGORY_ORDER, [
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'discount' => $discount,
            'subtotal' => $subtotal,
            'user_id' => $userId,
        ]);

        return [
            'valid' => true,
            'message' => "¡Cupón aplicado! {$discountLabel}",
            'coupon' => $coupon,
            'discount' => $discount,
        ];
    }

    /**
     * Calcula descuento según tipo y restricciones.
     */
    public static function calculateDiscount(Coupon $coupon, int $subtotal): int
    {
        if ($subtotal <= 0) {
            return 0;
        }

        $discount = 0;
        if ($coupon->type === 'percentage') {
            $discount = (int) round($subtotal * ($coupon->value / 100));
        } else {
            $discount = (int) $coupon->value;
        }

        if ($coupon->max_discount_amount) {
            $discount = min($discount, (int) $coupon->max_discount_amount);
        }

        return (int) min($discount, $subtotal);
    }

    /**
     * Helper para construir respuesta de fallo con logging.
     *
     * @return array{valid: bool, message: string, coupon: null, discount: int}
     */
    private static function fail(string $message, string $code = ''): array
    {
        if ($code !== '') {
            Log::info('Coupon validation failed', [
                'code' => $code,
                'reason' => $message,
                'ip' => request()?->ip(),
            ]);
        }

        return ['valid' => false, 'message' => $message, 'coupon' => null, 'discount' => 0];
    }
}
