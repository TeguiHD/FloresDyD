<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Custom Throttle Middleware
 * 
 * Rate limiting avanzado por contexto:
 * - Formularios de contacto: 5/min
 * - Búsquedas: 30/min  
 * - API endpoints: 60/min
 * - Login: 5 intentos / 15 min
 * 
 * Adaptado de SEGURIDAD.txt + SEGURIDAD2.TXT para Laravel
 */
class CustomThrottle
{
    /**
     * Configuración de límites por contexto.
     */
    private const LIMITS = [
        'login' => ['attempts' => 5, 'decay_minutes' => 15],
        'contact' => ['attempts' => 5, 'decay_minutes' => 1],
        'search' => ['attempts' => 30, 'decay_minutes' => 1],
        'api' => ['attempts' => 60, 'decay_minutes' => 1],
        'checkout' => ['attempts' => 10, 'decay_minutes' => 5],
        'newsletter' => ['attempts' => 3, 'decay_minutes' => 10],
    ];

    public function handle(Request $request, Closure $next, string $context = 'api'): Response
    {
        $config = self::LIMITS[$context] ?? self::LIMITS['api'];
        $key = $this->resolveKey($request, $context);

        if (RateLimiter::tooManyAttempts($key, $config['attempts'])) {
            $retryAfter = RateLimiter::availableIn($key);

            Log::channel('security')->warning('Rate limit excedido', [
                'context' => $context,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'retry_after' => $retryAfter,
            ]);

            return response()->json([
                'message' => 'Demasiadas solicitudes. Intenta de nuevo en ' . ceil($retryAfter / 60) . ' minutos.',
                'retry_after' => $retryAfter,
            ], 429)->withHeaders([
                'Retry-After' => $retryAfter,
                'X-RateLimit-Limit' => $config['attempts'],
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        RateLimiter::hit($key, $config['decay_minutes'] * 60);

        $response = $next($request);

        // Añadir headers de rate limiting
        $remaining = RateLimiter::remaining($key, $config['attempts']);
        $response->headers->set('X-RateLimit-Limit', (string) $config['attempts']);
        $response->headers->set('X-RateLimit-Remaining', (string) max(0, $remaining));

        return $response;
    }

    /**
     * Genera clave única para el rate limiter.
     */
    private function resolveKey(Request $request, string $context): string
    {
        $identifier = $request->user()?->id ?? $request->ip();
        return "throttle:{$context}:{$identifier}";
    }
}
