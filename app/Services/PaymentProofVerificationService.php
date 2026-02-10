<?php

namespace App\Services;

use App\Models\PaymentProof;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * PaymentProofVerificationService
 *
 * Analiza comprobantes de pago usando OCR.space API (gratuito, 25k req/mes).
 * Compatible con hosting compartido (cPanel) — no requiere exec, proc_open ni binarios.
 *
 * Soporta imágenes (JPG, PNG, GIF, BMP, TIFF) y PDFs.
 * Usa HTTP POST con base64 para enviar el archivo a la API.
 *
 * Si OCR está deshabilitado, retorna estado "pending" para revisión manual.
 */
class PaymentProofVerificationService
{
    /**
     * URL del endpoint de OCR.space (gratuito).
     */
    private const API_URL = 'https://api.ocr.space/parse/image';

    /**
     * Tamaño máximo de archivo para plan gratuito (1 MB).
     */
    private const MAX_FILE_SIZE = 1048576;

    /**
     * Retorna metadata de análisis.
     *
     * @return array{status:string, flags:array, ocr_text:?string, ocr_confidence:?float, extracted_amount:?int, extracted_amount_raw:?string, extracted_date:?string, keyword_hits:int, analysis_score:?int}
     */
    public static function analyze(PaymentProof $proof): array
    {
        $emptyResult = [
            'status' => 'pending',
            'flags' => [],
            'ocr_text' => null,
            'ocr_confidence' => null,
            'extracted_amount' => null,
            'extracted_amount_raw' => null,
            'extracted_date' => null,
            'keyword_hits' => 0,
            'analysis_score' => null,
        ];

        // Verificar si OCR está habilitado
        if (!config('ocr.enabled', false)) {
            return array_merge($emptyResult, [
                'flags' => ['ocr_disabled'],
            ]);
        }

        // Verificar API key
        $apiKey = config('ocr.api_key', '');
        if ($apiKey === '' || $apiKey === 'helloworld') {
            Log::warning('OCR: API key no configurada o usando key de prueba.');
            return array_merge($emptyResult, [
                'flags' => ['api_key_missing'],
            ]);
        }

        // Verificar que el archivo existe
        $disk = Storage::disk('local');
        if (!$disk->exists($proof->file_path)) {
            return array_merge($emptyResult, [
                'status' => 'failed',
                'flags' => ['file_missing'],
            ]);
        }

        $path = $disk->path($proof->file_path);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // Para PDFs, enviar directamente (no se pueden comprimir con GD)
        if ($extension === 'pdf') {
            $fileSize = filesize($path);
            if ($fileSize > self::MAX_FILE_SIZE) {
                Log::info("OCR: PDF demasiado grande ({$fileSize} bytes). Máx: " . self::MAX_FILE_SIZE);
                return array_merge($emptyResult, [
                    'flags' => ['file_too_large'],
                ]);
            }

            $fileContent = file_get_contents($path);
            if ($fileContent === false) {
                return array_merge($emptyResult, [
                    'status' => 'failed',
                    'flags' => ['file_read_error'],
                ]);
            }

            $base64 = 'data:application/pdf;base64,' . base64_encode($fileContent);
        } else {
            // Para imágenes: optimizar automáticamente si exceden 1 MB
            $optimized = self::optimizeImageForOcr($path, $extension);
            if ($optimized === null) {
                return array_merge($emptyResult, [
                    'status' => 'failed',
                    'flags' => ['image_optimization_failed'],
                ]);
            }

            $base64 = 'data:image/jpeg;base64,' . base64_encode($optimized);
        }

        // Llamar a OCR.space API
        $ocrResult = self::callOcrApi($apiKey, $base64, $extension);

        if ($ocrResult === null) {
            return array_merge($emptyResult, [
                'flags' => ['ocr_api_error'],
            ]);
        }

        $text = trim($ocrResult['text']);
        $confidence = $ocrResult['confidence'];

        // Analizar el texto extraído
        $flags = [];
        if ($text === '') {
            $flags[] = 'ocr_empty';
        }

        $minConfidence = (float) config('ocr.min_confidence', 60);
        if ($confidence !== null && $confidence < $minConfidence) {
            $flags[] = 'low_confidence';
        }

        $amount = self::extractAmount($text);
        if (!$amount) {
            $flags[] = 'amount_not_found';
        }

        $date = self::extractDate($text);
        if (!$date) {
            $flags[] = 'date_not_found';
        }

        $keywords = (array) config('ocr.keywords', []);
        $keywordHits = self::countKeywordHits($text, $keywords);
        if ($keywordHits < (int) config('ocr.min_keyword_hits', 2)) {
            $flags[] = 'missing_keywords';
        }

        $analysisScore = self::calculateScore($confidence, $keywordHits, $amount !== null, $date !== null);
        $status = $analysisScore >= 70 ? 'likely_proof' : 'needs_review';

        return [
            'status' => $status,
            'flags' => $flags,
            'ocr_text' => $text,
            'ocr_confidence' => $confidence,
            'extracted_amount' => $amount['value'] ?? null,
            'extracted_amount_raw' => $amount['raw'] ?? null,
            'extracted_date' => $date,
            'keyword_hits' => $keywordHits,
            'analysis_score' => $analysisScore,
        ];
    }

    /**
     * Llama a la API de OCR.space y retorna texto + confianza.
     *
     * Usa HTTP POST con base64. Compatible con hosting compartido.
     * No requiere exec, proc_open ni extensiones especiales.
     *
     * @return array{text:string, confidence:?float}|null
     */
    private static function callOcrApi(string $apiKey, string $base64Image, string $fileType): ?array
    {
        $engine = (int) config('ocr.engine', 2);
        $language = (string) config('ocr.language', 'spa');

        // Engine 3 solo acepta language=auto
        if ($engine === 3) {
            $language = 'auto';
        }

        $postData = [
            'base64Image' => $base64Image,
            'language' => $language,
            'isOverlayRequired' => 'false',
            'detectOrientation' => 'true',
            'scale' => 'true',
            'isTable' => 'true',
            'OCREngine' => (string) $engine,
        ];

        // Para PDFs, indicar el tipo explícitamente
        if ($fileType === 'pdf') {
            $postData['filetype'] = 'PDF';
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'apikey' => $apiKey,
                ])
                ->asForm()
                ->post(self::API_URL, $postData);

            if (!$response->successful()) {
                Log::error('OCR API: HTTP error ' . $response->status(), [
                    'body' => $response->body(),
                ]);
                return null;
            }

            $json = $response->json();

            if (empty($json) || !isset($json['OCRExitCode'])) {
                Log::error('OCR API: Respuesta inválida', ['body' => $response->body()]);
                return null;
            }

            $exitCode = (int) $json['OCRExitCode'];

            // ExitCode: 1=OK, 2=Parcial, 3=Falló, 4=Error fatal
            if ($exitCode >= 3) {
                Log::warning('OCR API: Procesamiento falló', [
                    'exit_code' => $exitCode,
                    'error' => $json['ErrorMessage'] ?? 'Unknown',
                    'details' => $json['ErrorDetails'] ?? null,
                ]);
                return null;
            }

            // Extraer texto de todos los resultados parseados
            $fullText = '';
            $totalConfidence = 0;
            $confidenceCount = 0;

            $parsedResults = $json['ParsedResults'] ?? [];
            foreach ($parsedResults as $result) {
                $pageExitCode = (int) ($result['FileParseExitCode'] ?? -1);
                if ($pageExitCode === 1) {
                    $parsedText = $result['ParsedText'] ?? '';
                    $fullText .= $parsedText . "\n";

                    // OCR.space no retorna confianza por palabra en modo sin overlay,
                    // pero el exitCode 1 indica procesamiento exitoso (~alta confianza)
                    if ($parsedText !== '') {
                        $totalConfidence += 85; // Baseline de confianza para exitCode 1
                        $confidenceCount++;
                    }
                }
            }

            $avgConfidence = $confidenceCount > 0
                ? round($totalConfidence / $confidenceCount, 2)
                : null;

            return [
                'text' => trim($fullText),
                'confidence' => $avgConfidence,
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('OCR API: Error de conexión', ['message' => $e->getMessage()]);
            return null;
        } catch (\Exception $e) {
            Log::error('OCR API: Error inesperado', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Calcula un puntaje de análisis (0-100).
     */
    private static function calculateScore(?float $confidence, int $keywordHits, bool $hasAmount, bool $hasDate): int
    {
        $score = 0;
        $score += min(40, $keywordHits * 10);
        if ($confidence !== null) {
            $score += (int) round(min(40, $confidence * 0.4));
        }
        if ($hasAmount) {
            $score += 10;
        }
        if ($hasDate) {
            $score += 10;
        }

        return min(100, max(0, $score));
    }

    /**
     * Extrae montos del texto OCR.
     * Soporta formatos: $15.000, 15000, CLP 15.000, etc.
     */
    private static function extractAmount(string $text): ?array
    {
        if ($text === '') {
            return null;
        }

        $matches = [];
        preg_match_all('/(?:\$|CLP|ARS|MXN|COP|PEN)?\s*([0-9]{1,3}(?:[\.,][0-9]{3})+|[0-9]{3,})/i', $text, $matches);
        if (empty($matches[1])) {
            return null;
        }

        $values = [];
        foreach ($matches[1] as $raw) {
            $numeric = (int) preg_replace('/[^0-9]/', '', $raw);
            if ($numeric > 0) {
                $values[] = ['raw' => $raw, 'value' => $numeric];
            }
        }

        if (empty($values)) {
            return null;
        }

        usort($values, fn ($a, $b) => $b['value'] <=> $a['value']);
        return $values[0];
    }

    /**
     * Extrae fecha del texto OCR.
     * Soporta formatos: dd/mm/yyyy, dd-mm-yyyy, etc.
     */
    private static function extractDate(string $text): ?string
    {
        if ($text === '') {
            return null;
        }

        if (preg_match('/\b(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})\b/', $text, $match)) {
            return $match[1];
        }

        return null;
    }

    /**
     * Cuenta cuántas keywords de comprobantes aparecen en el texto.
     */
    private static function countKeywordHits(string $text, array $keywords): int
    {
        if ($text === '' || empty($keywords)) {
            return 0;
        }

        $textLower = mb_strtolower($text);
        $hits = 0;
        foreach ($keywords as $keyword) {
            if ($keyword !== '' && str_contains($textLower, mb_strtolower($keyword))) {
                $hits++;
            }
        }

        return $hits;
    }

    /**
     * Optimiza una imagen para enviarla a la API de OCR.
     *
     * - Si ya pesa menos de 1 MB, la envía tal cual (como JPEG).
     * - Si pesa más, la redimensiona progresivamente y comprime como JPEG.
     * - Mantiene la calidad suficiente para que el OCR lea el texto.
     * - Usa GD (estándar en PHP / cPanel), no requiere Imagick ni extensiones extra.
     *
     * @return string|null Contenido binario de la imagen optimizada (JPEG), o null si falla.
     */
    private static function optimizeImageForOcr(string $path, string $extension): ?string
    {
        // Intentar leer la imagen con GD
        $image = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'gif' => @imagecreatefromgif($path),
            'bmp' => @imagecreatefrombmp($path),
            'webp' => @imagecreatefromwebp($path),
            default => @imagecreatefromjpeg($path),
        };

        if ($image === false) {
            // Fallback: si GD no puede leer, intentar enviar el archivo crudo
            $raw = file_get_contents($path);
            if ($raw !== false && strlen($raw) <= self::MAX_FILE_SIZE) {
                return $raw;
            }
            Log::warning('OCR: No se pudo leer la imagen con GD ni enviar cruda.', ['path' => $path]);
            return null;
        }

        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Ancho máximo para OCR (el texto sigue siendo legible a 1600px)
        $maxWidth = 1600;

        // Si la imagen es muy grande, redimensionar proporcionalmente
        if ($originalWidth > $maxWidth) {
            $ratio = $maxWidth / $originalWidth;
            $newWidth = $maxWidth;
            $newHeight = (int) round($originalHeight * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            if ($resized === false) {
                imagedestroy($image);
                return null;
            }

            // Fondo blanco (mejor para OCR que transparencia)
            $white = imagecolorallocate($resized, 255, 255, 255);
            imagefill($resized, 0, 0, $white);

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            imagedestroy($image);
            $image = $resized;
        }

        // Comprimir como JPEG con calidad progresivamente menor hasta caber en 1 MB
        // Empezar con calidad alta (buena para OCR) e ir bajando si es necesario
        $qualities = [88, 78, 65, 50];

        foreach ($qualities as $quality) {
            ob_start();
            imagejpeg($image, null, $quality);
            $output = ob_get_clean();

            if ($output !== false && strlen($output) <= self::MAX_FILE_SIZE) {
                $finalSize = strlen($output);
                imagedestroy($image);
                Log::debug('OCR: Imagen optimizada', [
                    'original_size' => filesize($path),
                    'optimized_size' => $finalSize,
                    'quality' => $quality,
                ]);
                return $output;
            }
        }

        // Último recurso: redimensionar mucho más agresivamente
        $currentWidth = imagesx($image);
        $currentHeight = imagesy($image);
        $smallWidth = 1000;
        $smallRatio = $smallWidth / max($currentWidth, 1);
        $smallHeight = (int) round($currentHeight * $smallRatio);

        $small = imagecreatetruecolor($smallWidth, $smallHeight);
        if ($small !== false) {
            $white = imagecolorallocate($small, 255, 255, 255);
            imagefill($small, 0, 0, $white);
            imagecopyresampled($small, $image, 0, 0, 0, 0, $smallWidth, $smallHeight, $currentWidth, $currentHeight);

            ob_start();
            imagejpeg($small, null, 70);
            $output = ob_get_clean();
            imagedestroy($small);

            if ($output !== false && strlen($output) <= self::MAX_FILE_SIZE) {
                imagedestroy($image);
                return $output;
            }
        }

        imagedestroy($image);
        Log::warning('OCR: No se pudo reducir la imagen por debajo de 1 MB.', [
            'original_size' => filesize($path),
        ]);
        return null;
    }
}
