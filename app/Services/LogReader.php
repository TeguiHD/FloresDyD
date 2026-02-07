<?php

namespace App\Services;

use SplFileObject;

class LogReader
{
    /**
     * Leer últimas líneas de un archivo de log.
     */
    public static function tail(string $path, int $lines = 120): array
    {
        if (!is_file($path)) {
            return [];
        }

        $file = new SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        $start = max(0, $lastLine - $lines);

        $file->seek($start);
        $results = [];

        while (!$file->eof()) {
            $line = trim((string) $file->current());
            if ($line !== '') {
                $results[] = $line;
            }
            $file->next();
        }

        return $results;
    }

    /**
     * Parsear líneas de log con contexto JSON opcional.
     */
    public static function parse(array $lines): array
    {
        $entries = [];

        foreach ($lines as $line) {
            $parsed = self::parseLine($line);
            if ($parsed) {
                $entries[] = $parsed;
            }
        }

        return array_reverse($entries);
    }

    private static function parseLine(string $line): ?array
    {
        $pattern = '/^\[(.*?)\]\s+([\w-]+)\.(\w+):\s(.*?)(\s\{.*\})?$/';
        if (!preg_match($pattern, $line, $matches)) {
            return [
                'timestamp' => null,
                'env' => null,
                'level' => 'info',
                'message' => $line,
                'context' => null,
            ];
        }

        $context = null;
        if (!empty($matches[5])) {
            $json = trim($matches[5]);
            $decoded = json_decode($json, true);
            $context = is_array($decoded) ? $decoded : null;
        }

        return [
            'timestamp' => $matches[1],
            'env' => $matches[2],
            'level' => strtolower($matches[3]),
            'message' => trim($matches[4]),
            'context' => $context,
        ];
    }
}
