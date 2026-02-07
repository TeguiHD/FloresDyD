<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * AuditService - Servicio de Auditoría Inmutable
 * 
 * Implementa:
 * - Registro de acciones administrativas
 * - Hash chain para integridad (blockchain-like)
 * - Separación de logs por categoría
 * - Contexto enriquecido (IP, User-Agent, usuario)
 * 
 * Adaptado de SEGURIDAD3.TXT (hash chain audit logs)
 * para entorno Laravel.
 */
class AuditService
{
    /**
     * Hash del último registro para crear cadena.
     */
    private static ?string $lastHash = null;

    /**
     * Categorías de auditoría.
     */
    public const CATEGORY_AUTH = 'auth';
    public const CATEGORY_ADMIN = 'admin';
    public const CATEGORY_ORDER = 'order';
    public const CATEGORY_PRODUCT = 'product';
    public const CATEGORY_USER = 'user';
    public const CATEGORY_SECURITY = 'security';
    public const CATEGORY_SYSTEM = 'system';

    /**
     * Registra una acción en el log de auditoría con hash chain.
     */
    public static function log(
        string $action,
        string $category = self::CATEGORY_SYSTEM,
        array $details = [],
        ?string $entityType = null,
        ?int $entityId = null,
    ): void {
        $request = request();
        $user = Auth::user();

        $entry = [
            'action' => $action,
            'category' => $category,
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'user_role' => $user?->roles?->first()?->name ?? 'guest',
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => $details,
            'timestamp' => now()->toIso8601String(),
        ];

        // Crear hash chain para inmutabilidad
        $entry['previous_hash'] = self::$lastHash ?? 'GENESIS';
        $entry['hash'] = hash('sha256', json_encode($entry));
        self::$lastHash = $entry['hash'];

        Log::channel('audit')->info($action, $entry);
    }

    /**
     * Registra un intento de login.
     */
    public static function loginAttempt(string $email, bool $successful): void
    {
        self::log(
            action: $successful ? 'login_success' : 'login_failed',
            category: self::CATEGORY_AUTH,
            details: [
                'email' => $email,
                'successful' => $successful,
            ]
        );
    }

    /**
     * Registra un logout.
     */
    public static function logout(): void
    {
        self::log(
            action: 'logout',
            category: self::CATEGORY_AUTH,
        );
    }

    /**
     * Registra una acción administrativa.
     */
    public static function adminAction(
        string $action,
        string $entityType,
        int $entityId,
        array $changes = [],
    ): void {
        self::log(
            action: "admin:{$action}",
            category: self::CATEGORY_ADMIN,
            details: ['changes' => $changes],
            entityType: $entityType,
            entityId: $entityId,
        );
    }

    /**
     * Registra un cambio en un pedido.
     */
    public static function orderAction(
        string $action,
        int $orderId,
        array $details = [],
    ): void {
        self::log(
            action: "order:{$action}",
            category: self::CATEGORY_ORDER,
            details: $details,
            entityType: 'Order',
            entityId: $orderId,
        );
    }

    /**
     * Registra un evento de seguridad.
     */
    public static function securityEvent(
        string $event,
        array $details = [],
    ): void {
        self::log(
            action: "security:{$event}",
            category: self::CATEGORY_SECURITY,
            details: $details,
        );

        // También enviar al canal de seguridad
        Log::channel('security')->warning("Audit: {$event}", $details);
    }

    /**
     * Registra un cambio de rol/permisos.
     */
    public static function roleChange(
        int $userId,
        string $action,
        array $roles = [],
    ): void {
        self::log(
            action: "role:{$action}",
            category: self::CATEGORY_USER,
            details: ['roles' => $roles],
            entityType: 'User',
            entityId: $userId,
        );
    }
}
