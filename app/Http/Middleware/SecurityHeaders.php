<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Headers Middleware
 * 
 * Headers de seguridad según OWASP Top 10:2025
 * Adaptado de SEGURIDAD.txt, SEGURIDAD2.TXT, SEGURIDAD3.TXT
 * 
 * Incluye:
 * - CSP con nonces para scripts/styles inline
 * - HSTS con preload
 * - Permissions Policy restrictiva
 * - Cache-Control para datos sensibles
 * - Cross-Origin policies
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Generar nonce para CSP
        $nonce = Str::random(32);
        app()->instance('csp-nonce', $nonce);
        
        $response = $next($request);
        
        // === Headers base (siempre activos) ===
        
        // Prevenir clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Prevenir MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // XSS Protection (legacy browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions Policy (restrictiva según SEGURIDAD2.TXT)
        $response->headers->set('Permissions-Policy', implode(', ', [
            'geolocation=()',
            'microphone=()',
            'camera=()',
            'payment=()',
            'usb=()',
            'magnetometer=()',
            'gyroscope=()',
            'accelerometer=()',
            'autoplay=(self)',
            'fullscreen=(self)',
        ]));
        
        // Cross-Origin policies
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        
        // Prevenir DNS prefetch abuse
        $response->headers->set('X-DNS-Prefetch-Control', 'off');
        
        // No permitir que se sirva en marcos de descargas
        $response->headers->set('X-Download-Options', 'noopen');
        
        // Deshabilitar client-side caching en rutas sensibles
        if ($this->isSensitiveRoute($request)) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
        
        // === Content Security Policy ===
        $csp = $this->buildCsp($nonce, $request);
        $response->headers->set('Content-Security-Policy', $csp);
        
        // === HSTS (solo en HTTPS) ===
        if ($request->secure() || app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security', 
                'max-age=63072000; includeSubDomains; preload'
            );
        }
        
        return $response;
    }
    
    /**
     * Construye la política CSP según el entorno.
     */
    private function buildCsp(string $nonce, Request $request): string
    {
        if (app()->environment('local', 'development', 'testing')) {
            // CSP permisiva para desarrollo (Vite HMR necesita unsafe-inline)
            return implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://127.0.0.1:5173",
                "style-src 'self' 'unsafe-inline' http://127.0.0.1:5173",
                "img-src 'self' data: https: blob: http://127.0.0.1:5173",
                "media-src 'self' data: blob: http://127.0.0.1:5173",
                "font-src 'self' data: http://127.0.0.1:5173",
                "connect-src 'self' ws://127.0.0.1:5173 http://127.0.0.1:5173",
                "frame-src 'self'",
                "frame-ancestors 'self'",
                "form-action 'self'",
                "base-uri 'self'",
                "object-src 'none'",
            ]);
        }
        
        // CSP restrictiva para producción
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' https://www.googletagmanager.com https://connect.facebook.net",
            "style-src 'self' 'nonce-{$nonce}'",
            "img-src 'self' data: https: blob:",
            "media-src 'self' data: blob:",
            "font-src 'self' data:",
            "connect-src 'self' https://www.google-analytics.com https://api.whatsapp.com",
            "frame-src 'self' https://www.google.com https://www.facebook.com",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "upgrade-insecure-requests",
        ]);
    }
    
    /**
     * Verifica si la ruta es sensible (admin, checkout, auth).
     */
    private function isSensitiveRoute(Request $request): bool
    {
        $sensitivePrefixes = ['admin', 'checkout', 'login', 'register', 'password'];
        
        foreach ($sensitivePrefixes as $prefix) {
            if ($request->is("{$prefix}*")) {
                return true;
            }
        }
        
        return false;
    }
}
