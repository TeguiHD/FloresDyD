<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    private static bool $whatsappWarned = false;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $setting = self::query()->where('key', $key)->value('value');
        return $setting !== null && $setting !== '' ? $setting : $default;
    }

    public static function getMapEmbedUrl(?string $default = null): ?string
    {
        $raw = self::getValue('contact.map_embed_url', $default);
        return self::normalizeMapEmbedUrl($raw);
    }

    public static function normalizeMapEmbedUrl(?string $raw): ?string
    {
        if (!$raw) {
            return null;
        }

        $raw = trim($raw);
        $parsed = parse_url($raw);
        if (!$parsed || empty($parsed['host']) || empty($parsed['scheme'])) {
            return null;
        }

        $scheme = strtolower($parsed['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower($parsed['host']);
        $allowedHosts = [
            'www.google.com',
            'google.com',
            'maps.google.com',
            'maps.app.goo.gl',
            'goo.gl',
        ];

        if (!in_array($host, $allowedHosts, true)) {
            return null;
        }

        $path = $parsed['path'] ?? '';
        if (str_starts_with($path, '/maps/embed')) {
            return $raw;
        }

        if (in_array($host, ['maps.app.goo.gl', 'goo.gl'], true)) {
            return null;
        }

        $queryParams = [];
        parse_str($parsed['query'] ?? '', $queryParams);
        $query = $queryParams['q'] ?? $queryParams['query'] ?? null;
        $ll = $queryParams['ll'] ?? $queryParams['center'] ?? null;

        if (!$query && str_contains($path, '/maps/place/')) {
            $placePart = substr($path, strpos($path, '/maps/place/') + strlen('/maps/place/'));
            $placePart = preg_split('/\\/|@/', $placePart)[0] ?? '';
            $placePart = trim(urldecode($placePart));
            if ($placePart !== '') {
                $query = str_replace('+', ' ', $placePart);
            }
        }

        if (!$query && str_contains($path, '/maps/search/')) {
            $searchPart = substr($path, strpos($path, '/maps/search/') + strlen('/maps/search/'));
            $searchPart = preg_split('/\\/|@/', $searchPart)[0] ?? '';
            $searchPart = trim(urldecode($searchPart));
            if ($searchPart !== '') {
                $query = str_replace('+', ' ', $searchPart);
            }
        }

        $coords = null;
        $zoom = null;
        if (preg_match('/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)(?:,(\d+(?:\.\d+)?)z)?/i', $path, $matches)) {
            $coords = $matches[1] . ',' . $matches[2];
            $zoom = isset($matches[3]) ? (int) round((float) $matches[3]) : null;
        }

        if (!$coords && $ll && preg_match('/-?\d+(?:\.\d+)?,-?\d+(?:\.\d+)?/', $ll)) {
            $coords = $ll;
        }

        if (!$coords && preg_match('/!3d(-?\d+(?:\.\d+)?)[^!]*!4d(-?\d+(?:\.\d+)?)/i', $path, $matches)) {
            $coords = $matches[1] . ',' . $matches[2];
        }

        if (!$coords && preg_match('/!4d(-?\d+(?:\.\d+)?)[^!]*!3d(-?\d+(?:\.\d+)?)/i', $path, $matches)) {
            $coords = $matches[2] . ',' . $matches[1];
        }

        if ($coords) {
            $embed = 'https://www.google.com/maps?q=' . urlencode($coords) . '&output=embed';
            if ($zoom) {
                $embed .= '&z=' . $zoom;
            }
            return $embed;
        }

        if ($query) {
            return 'https://www.google.com/maps?q=' . urlencode($query) . '&output=embed';
        }

        return null;
    }

    public static function getWhatsappNumber(?string $default = null): string
    {
        $raw = self::getValue('contact.whatsapp', $default ?? config('flores.whatsapp', ''));
        $normalized = self::normalizeWhatsapp((string) $raw);

        if ($normalized === '' && !self::$whatsappWarned) {
            self::$whatsappWarned = true;
            logger()->warning('WhatsApp number inválido en configuración.', [
                'raw' => $raw,
            ]);
        }

        return $normalized;
    }

    // =============================================
    // INSTAGRAM
    // =============================================

    public static function getInstagramUsername(?string $default = null): ?string
    {
        $raw = self::getValue('instagram.username', $default);
        return self::normalizeInstagramUsername($raw);
    }

    public static function getInstagramEmbeds(?string $default = null): array
    {
        $raw = self::getValue('instagram.embed_urls', $default);
        if (!$raw) {
            return [];
        }

        $decoded = json_decode($raw, true);
        $items = is_array($decoded) ? $decoded : preg_split('/\r?\n/', $raw);
        $items = is_array($items) ? $items : [];

        $urls = [];
        foreach ($items as $item) {
            $url = self::normalizeInstagramEmbedUrl($item);
            if ($url) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }

    public static function normalizeInstagramUsername(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim((string) $value);
        $value = ltrim($value, '@');
        if ($value === '') {
            return null;
        }

        if (!preg_match('/^[A-Za-z0-9._]{2,30}$/', $value)) {
            return null;
        }

        return $value;
    }

    public static function normalizeInstagramEmbedList(?string $value): array
    {
        if (!$value) {
            return [];
        }

        $items = preg_split('/[\n,]+/', (string) $value) ?: [];
        $urls = [];
        foreach ($items as $item) {
            $url = self::normalizeInstagramEmbedUrl($item);
            if ($url) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }

    public static function normalizeInstagramEmbedUrl(?string $raw): ?string
    {
        if (!$raw) {
            return null;
        }

        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }

        $parsed = parse_url($raw);
        if (!$parsed || empty($parsed['host']) || empty($parsed['scheme'])) {
            return null;
        }

        $scheme = strtolower($parsed['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower($parsed['host']);
        if (!in_array($host, ['www.instagram.com', 'instagram.com'], true)) {
            return null;
        }

        $path = trim($parsed['path'] ?? '', '/');
        if ($path === '') {
            return null;
        }

        if (str_ends_with($path, 'embed')) {
            return 'https://www.instagram.com/' . $path;
        }

        $parts = explode('/', $path);
        if (count($parts) < 2) {
            return null;
        }

        $type = $parts[0];
        $code = $parts[1];
        if (!in_array($type, ['p', 'reel', 'tv'], true)) {
            return null;
        }

        if (!preg_match('/^[A-Za-z0-9_-]+$/', $code)) {
            return null;
        }

        return "https://www.instagram.com/{$type}/{$code}/embed";
    }

    public static function getWhatsappDisplay(?string $default = null): string
    {
        $number = self::getWhatsappNumber($default);
        return self::formatWhatsappDisplay($number);
    }

    public static function normalizeWhatsapp(string $value, ?string $countryCode = null): string
    {
        $whatsappCountryCode = preg_replace('/\D+/', '', (string) ($countryCode ?? config('flores.whatsapp_country_code', '')));
        $whatsappNumber = preg_replace('/\D+/', '', trim($value));
        if (str_starts_with($whatsappNumber, '00')) {
            $whatsappNumber = substr($whatsappNumber, 2);
        }
        if ($whatsappCountryCode !== '' && $whatsappNumber !== '' && !str_starts_with($whatsappNumber, $whatsappCountryCode)) {
            $whatsappNumber = $whatsappCountryCode . $whatsappNumber;
        }
        if ($whatsappNumber === '' || preg_match('/^0+$/', $whatsappNumber)) {
            return '';
        }
        if (strlen($whatsappNumber) < 9 || strlen($whatsappNumber) > 15) {
            return '';
        }

        return $whatsappNumber;
    }

    public static function formatWhatsappDisplay(string $number, ?string $countryCode = null): string
    {
        if ($number === '') {
            return '';
        }

        $whatsappCountryCode = preg_replace('/\D+/', '', (string) ($countryCode ?? config('flores.whatsapp_country_code', '')));
        if ($whatsappCountryCode !== '' && str_starts_with($number, $whatsappCountryCode)) {
            $rest = substr($number, strlen($whatsappCountryCode));
            $chunks = array_filter(str_split($rest, 3));
            return trim('+' . $whatsappCountryCode . ' ' . implode(' ', $chunks));
        }

        $chunks = array_filter(str_split($number, 3));
        return trim(implode(' ', $chunks));
    }
}
