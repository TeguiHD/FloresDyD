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
        if (!$raw) {
            return null;
        }

        $raw = trim($raw);
        $parsed = parse_url($raw);
        if (!$parsed || empty($parsed['host'])) {
            return null;
        }

        $host = strtolower($parsed['host']);
        $allowedHosts = [
            'www.google.com',
            'google.com',
            'maps.google.com',
        ];

        if (!in_array($host, $allowedHosts, true)) {
            return null;
        }

        if (!isset($parsed['path']) || !str_starts_with($parsed['path'], '/maps/embed')) {
            return null;
        }

        return $raw;
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
