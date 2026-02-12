<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * FileSecurityService
 *
 * Valida archivos subidos contra ataques comunes:
 * - MIME spoofing (extensión .jpg con contenido PHP)
 * - Archivos polyglot (JPEG válido con PHP/JS embebido)
 * - Metadata EXIF maliciosa
 * - Filenames con path traversal o caracteres peligrosos
 * - Decompression bombs (imágenes enormes)
 *
 * Compatible con hosting compartido (cPanel) — usa finfo + GD estándar.
 */
class FileSecurityService
{
    /**
     * MIMEs permitidos para comprobantes de pago.
     */
    private const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'application/pdf',
    ];

    /**
     * Extensiones permitidas para comprobantes.
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf'];

    /**
     * Dimensión máxima (ancho o alto) para proteger contra decompression bombs.
     * Una imagen de 10000x10000 en RGBA = ~380 MB en memoria.
     */
    private const MAX_IMAGE_DIMENSION = 8000;

    /**
     * Tamaño máximo de archivo (5 MB).
     */
    private const MAX_FILE_SIZE = 5 * 1024 * 1024;

    /**
     * Patrones que indican un archivo polyglot/malicioso.
     * Se buscan tanto en los primeros bytes como en el contenido completo.
     */
    private const DANGEROUS_PATTERNS = [
        '<?php',
        '<?=',
        '<script',
        '<%',
        'eval(',
        'base64_decode(',
        'exec(',
        'system(',
        'passthru(',
        'shell_exec(',
        'proc_open(',
        'popen(',
        '__HALT_COMPILER',
    ];

    /**
     * Valida un archivo subido. Retorna null si es seguro, o un string de error.
     */
    public static function validate(UploadedFile $file): ?string
    {
        // 1. Verificar tamaño
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return 'El archivo excede el tamaño máximo permitido (5 MB).';
        }

        // 2. Verificar extensión
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            self::logSecurityEvent('invalid_extension', $file, ['extension' => $extension]);
            return 'Tipo de archivo no permitido. Usa JPG, PNG o PDF.';
        }

        // 3. Verificar MIME real con finfo (lee bytes mágicos, no la extensión)
        $realMime = self::detectRealMime($file->getRealPath());
        if ($realMime === null || !in_array($realMime, self::ALLOWED_MIMES, true)) {
            self::logSecurityEvent('mime_mismatch', $file, [
                'client_mime' => $file->getClientMimeType(),
                'real_mime' => $realMime,
                'extension' => $extension,
            ]);
            return 'El contenido del archivo no coincide con su extensión.';
        }

        // 4. Verificar consistencia MIME ↔ extensión
        if (!self::mimeMatchesExtension($realMime, $extension)) {
            self::logSecurityEvent('mime_extension_mismatch', $file, [
                'real_mime' => $realMime,
                'extension' => $extension,
            ]);
            return 'El tipo de archivo no coincide con su extensión.';
        }

        // 5. Buscar patrones maliciosos (polyglot detection)
        $polyglotResult = self::detectPolyglot($file->getRealPath());
        if ($polyglotResult !== null) {
            self::logSecurityEvent('polyglot_detected', $file, [
                'pattern' => $polyglotResult,
                'real_mime' => $realMime,
            ]);
            return 'El archivo contiene contenido no permitido.';
        }

        // 6. Para imágenes, verificar dimensiones (decompression bomb)
        if (str_starts_with($realMime, 'image/')) {
            $dimensions = self::getImageDimensions($file->getRealPath());
            if ($dimensions === null) {
                return 'No se pudo leer la imagen. Verifica que no esté corrupta.';
            }
            if ($dimensions['width'] > self::MAX_IMAGE_DIMENSION || $dimensions['height'] > self::MAX_IMAGE_DIMENSION) {
                self::logSecurityEvent('decompression_bomb', $file, $dimensions);
                return 'La imagen es demasiado grande. Máximo ' . self::MAX_IMAGE_DIMENSION . 'px por lado.';
            }
        }

        return null; // Seguro
    }

    /**
     * Sanitiza el nombre original del archivo.
     * Remueve path traversal, caracteres de control, y limita longitud.
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Extraer solo el basename (sin path)
        $filename = basename($filename);

        // Remover caracteres de control y no imprimibles
        $filename = preg_replace('/[\x00-\x1f\x7f]/', '', $filename);

        // Remover caracteres peligrosos para filesystem y HTML
        $filename = preg_replace('/[<>:"\/\\\\|?*&;`${}]/', '_', $filename);

        // Colapsar underscores múltiples
        $filename = preg_replace('/_+/', '_', $filename);

        // Limitar longitud (conservar extensión)
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = mb_substr($name, 0, 100);

        // Si después de sanitizar quedó vacío, usar un nombre genérico
        if (trim($name, '_ ') === '') {
            $name = 'comprobante';
        }

        return $extension !== '' ? "{$name}.{$extension}" : $name;
    }

    /**
     * Limpia metadata EXIF de una imagen reescribiéndola con GD.
     * Retorna la ruta del archivo limpio (sobreescribe el original).
     *
     * Para PDFs no hace nada (GD no maneja PDFs).
     */
    public static function stripExifMetadata(string $path): bool
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // Solo limpiar imágenes (PDFs no tienen EXIF)
        if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            return true;
        }

        $image = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            default => false,
        };

        if ($image === false) {
            return false;
        }

        $result = match ($extension) {
            'jpg', 'jpeg' => imagejpeg($image, $path, 92),
            'png' => imagepng($image, $path, 6),
            default => false,
        };

        imagedestroy($image);

        return $result;
    }

    // ─────────────────────────────────────────────
    //  Métodos privados
    // ─────────────────────────────────────────────

    /**
     * Detecta el MIME real usando finfo (lee los bytes mágicos del archivo).
     */
    private static function detectRealMime(string $path): ?string
    {
        if (!file_exists($path)) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);

        return $mime !== false ? $mime : null;
    }

    /**
     * Verifica que el MIME detectado sea consistente con la extensión del archivo.
     */
    private static function mimeMatchesExtension(string $mime, string $extension): bool
    {
        $map = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'application/pdf' => ['pdf'],
        ];

        $allowedExtensions = $map[$mime] ?? [];

        return in_array($extension, $allowedExtensions, true);
    }

    /**
     * Busca patrones de código ejecutable embebido en el archivo.
     * Detecta archivos polyglot (ej: JPEG válido con PHP embebido).
     */
    private static function detectPolyglot(string $path): ?string
    {
        // Leer el archivo completo (máx 5 MB, ya validado antes)
        $content = file_get_contents($path);
        if ($content === false) {
            return 'file_read_error';
        }

        $contentLower = strtolower($content);

        foreach (self::DANGEROUS_PATTERNS as $pattern) {
            if (str_contains($contentLower, strtolower($pattern))) {
                return $pattern;
            }
        }

        // Buscar pattern adicional: null bytes (técnica común de bypass)
        if (str_contains($content, "\x00")) {
            // Los PDFs legítimamente contienen null bytes, solo verificar imágenes
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
                // PNG tiene null bytes legítimos en su estructura
                if ($extension !== 'png') {
                    return 'null_byte';
                }
            }
        }

        return null;
    }

    /**
     * Obtiene las dimensiones de una imagen sin cargarla completamente en memoria.
     */
    private static function getImageDimensions(string $path): ?array
    {
        $info = @getimagesize($path);
        if ($info === false) {
            return null;
        }

        return [
            'width' => $info[0],
            'height' => $info[1],
        ];
    }

    /**
     * Registra un evento de seguridad en los logs.
     */
    private static function logSecurityEvent(string $type, UploadedFile $file, array $context = []): void
    {
        Log::warning("FileSecurityService: {$type}", array_merge([
            'original_name' => $file->getClientOriginalName(),
            'client_mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'ip' => request()->ip(),
        ], $context));

        // Usar AuditService si está disponible
        if (class_exists(AuditService::class)) {
            AuditService::securityEvent("file_security_{$type}", array_merge([
                'original_name' => $file->getClientOriginalName(),
                'ip' => request()->ip(),
            ], $context));
        }
    }
}
