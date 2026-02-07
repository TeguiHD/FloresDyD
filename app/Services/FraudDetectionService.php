<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * FraudDetectionService - Detección de Fraude en Pedidos
 * 
 * Sistema de puntuación de riesgo para pedidos:
 * - Análisis de velocidad (pedidos por IP en período)
 * - Detección de datos sospechosos
 * - Validación de patrones de compra
 * - Bloqueo proactivo
 * 
 * Adaptado de considerar.txt y SEGURIDAD.txt
 * para entorno Laravel con pagos manuales.
 */
class FraudDetectionService
{
    /**
     * Umbral de riesgo para bloquear pedido (0-100).
     */
    private const BLOCK_THRESHOLD = 75;

    /**
     * Umbral para marcar para revisión manual.
     */
    private const REVIEW_THRESHOLD = 50;

    /**
     * Evalúa el riesgo de fraude de un pedido.
     * 
     * @return array{score: int, level: string, reasons: array, action: string}
     */
    public static function evaluate(array $orderData, Request $request): array
    {
        $score = 0;
        $reasons = [];

        // 1. Velocidad de pedidos por IP (máx +30)
        $ipKey = "fraud:orders:{$request->ip()}";
        $recentOrders = (int) Cache::get($ipKey, 0);
        if ($recentOrders >= 5) {
            $score += 30;
            $reasons[] = "Alta frecuencia: {$recentOrders} pedidos recientes desde esta IP";
        } elseif ($recentOrders >= 3) {
            $score += 15;
            $reasons[] = "Frecuencia moderada: {$recentOrders} pedidos recientes";
        }

        // 2. Monto inusual (máx +20)
        $total = $orderData['total'] ?? 0;
        if ($total > 50000) { // MXN
            $score += 20;
            $reasons[] = "Monto muy alto: $" . number_format($total, 2);
        } elseif ($total > 20000) {
            $score += 10;
            $reasons[] = "Monto alto: $" . number_format($total, 2);
        }

        // 3. Email sospechoso (máx +15)
        $email = $orderData['email'] ?? '';
        if (self::isSuspiciousEmail($email)) {
            $score += 15;
            $reasons[] = "Email con patrón sospechoso: {$email}";
        }

        // 4. Teléfono inválido (máx +10)
        $phone = $orderData['phone'] ?? '';
        if (!empty($phone) && !preg_match('/^\+?[0-9]{10,15}$/', preg_replace('/[\s\-\(\)]/', '', $phone))) {
            $score += 10;
            $reasons[] = 'Teléfono con formato inválido';
        }

        // 5. Dirección de entrega sospechosa (máx +10)
        $address = $orderData['address'] ?? '';
        if (strlen($address) < 10 || strlen($address) > 500) {
            $score += 10;
            $reasons[] = 'Dirección de entrega sospechosa (longitud)';
        }

        // 6. User-Agent sospechoso (máx +15)
        $ua = $request->userAgent() ?? '';
        if (empty($ua) || self::isBotUserAgent($ua)) {
            $score += 15;
            $reasons[] = 'User-Agent ausente o de bot';
        }

        // Determinar acción
        $level = match (true) {
            $score >= self::BLOCK_THRESHOLD => 'high',
            $score >= self::REVIEW_THRESHOLD => 'medium',
            default => 'low',
        };

        $action = match ($level) {
            'high' => 'block',
            'medium' => 'review',
            'low' => 'allow',
        };

        // Incrementar contador de pedidos por IP
        Cache::put($ipKey, $recentOrders + 1, now()->addHours(24));

        // Log si riesgo medio o alto
        if ($score >= self::REVIEW_THRESHOLD) {
            Log::channel('security')->warning('Fraud detection alert', [
                'score' => $score,
                'level' => $level,
                'action' => $action,
                'reasons' => $reasons,
                'ip' => $request->ip(),
                'email' => $email,
                'total' => $total,
            ]);
        }

        return [
            'score' => min($score, 100),
            'level' => $level,
            'reasons' => $reasons,
            'action' => $action,
        ];
    }

    /**
     * Verifica si un email tiene patrones sospechosos.
     */
    private static function isSuspiciousEmail(string $email): bool
    {
        if (empty($email)) {
            return true;
        }

        $suspiciousPatterns = [
            '/^test@/',
            '/^admin@/',
            '/^info@/',
            '/\+.*@/',                      // email con alias
            '/^[a-z]{1,3}[0-9]{5,}@/',     // letras cortas + muchos números
            '/@(tempmail|guerrillamail|throwaway|mailinator|yopmail)\./i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $email)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica si el User-Agent es de un bot conocido.
     */
    private static function isBotUserAgent(string $ua): bool
    {
        $botPatterns = [
            'curl', 'wget', 'python', 'scrapy', 'httpie',
            'postman', 'insomnia', 'phantomjs', 'headless',
            'selenium', 'puppeteer', 'playwright',
        ];

        $uaLower = strtolower($ua);
        foreach ($botPatterns as $pattern) {
            if (str_contains($uaLower, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
