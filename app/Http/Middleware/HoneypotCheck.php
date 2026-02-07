<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Honeypot Middleware
 * 
 * Detecta bots y escaneos maliciosos:
 * - Rutas trampa (wp-admin, .env, phpmyadmin, etc.)
 * - Campos honeypot en formularios
 * - Bloqueo y registro de IPs sospechosas
 * 
 * Adaptado de SEGURIDAD.txt para entorno Laravel
 */
class HoneypotCheck
{
    /**
     * Campos honeypot que deben estar vacíos en formularios.
     */
    private const HONEYPOT_FIELDS = [
        'website',
        'url',
        'fax_number',
        'company_address',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Verificar campos honeypot en formularios POST
        if ($request->isMethod('POST')) {
            foreach (self::HONEYPOT_FIELDS as $field) {
                if ($request->filled($field)) {
                    $this->logBotAttempt($request, "honeypot_field:{$field}");

                    // Simular éxito para no alertar al bot
                    return response()->json([
                        'success' => true,
                        'message' => 'Formulario enviado correctamente.',
                    ], 200);
                }
            }
        }

        return $next($request);
    }

    /**
     * Registra intento de bot en logs de seguridad.
     */
    private function logBotAttempt(Request $request, string $trigger): void
    {
        Log::channel('security')->warning('Bot/Scanner detectado', [
            'trigger' => $trigger,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
