<?php
declare(strict_types=1);

final class DeterministicUUID
{
    private const NAMESPACE_SALT = 'PMTOOLS_v2_ROHAM_2026';
    private const HASH_ALGO      = 'sha256';
    private const UUID_LEN       = 36;
    private const HEX_LEN        = 32;

    /**
     * @param string $key  
     * @param int    $seed 
     */
    public static function generate(string $key, int $seed = 0): string
    {
        $key = self::normalize($key);
        $payload = self::NAMESPACE_SALT . '|' . $key . '|' . $seed;

        $hash = hash(self::HASH_ALGO, $payload, true);

        $second = hash(self::HASH_ALGO, $payload . '|second', true);

        $bytes = substr($hash, 0, 16);

        for ($i = 0; $i < 16; $i++) {
            $bytes[$i] = chr(ord($bytes[$i]) ^ ord($second[$i]));
        }

        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return self::format($bytes);
    }

    public static function reverse(string $uuid): ?string
    {
        if (!self::isValid($uuid)) {
            return null;
        }

        $mapFile = DATA_PATH . '/uuid-map.json';
        if (!file_exists($mapFile)) {
            return null;
        }

        $map = json_decode((string)file_get_contents($mapFile), true);
        if (!is_array($map)) {
            return null;
        }

        return $map[strtolower($uuid)] ?? null;
    }

    public static function generateAndStore(string $key, int $seed = 0): string
    {
        $uuid = self::generate($key, $seed);
        self::storeMapping($uuid, $key);

        if ($seed === 0) {
            $moduleUuid = self::generate($key, 1);
            self::storeMapping($moduleUuid, $key);
        }

        return $uuid;
    }

    private static function storeMapping(string $uuid, string $key): void
    {
        if (!is_dir(DATA_PATH)) {
            @mkdir(DATA_PATH, 0755, true);
        }

        $mapFile = DATA_PATH . '/uuid-map.json';
        $map = [];

        if (file_exists($mapFile)) {
            $decoded = json_decode((string)file_get_contents($mapFile), true);
            if (is_array($decoded)) $map = $decoded;
        }

        $map[strtolower($uuid)] = $key;

        if (count($map) > 5000) {
            $map = array_slice($map, -4000, null, true);
        }

        @file_put_contents($mapFile, json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public static function pair(string $key): array
    {
        return [
            'header' => self::generate($key, 0),
            'module' => self::generate($key, 1),
        ];
    }

    public static function isValid(string $uuid): bool
    {
        return (bool)preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $uuid
        );
    }

    public static function fingerprint(string $key, int $seed = 0): string
    {
        return substr(hash(self::HASH_ALGO, self::NAMESPACE_SALT . '|' . self::normalize($key) . '|' . $seed), 0, 8);
    }

    private static function normalize(string $key): string
    {
        $key = trim($key);
        if (function_exists('mb_strtolower')) {
            $key = mb_strtolower($key, 'UTF-8');
        } else {
            $key = strtolower($key);
        }
        $key = preg_replace('/\s+/', ' ', $key) ?? $key;
        return $key;
    }

    private static function format(string $bytes): string
    {
        $hex = bin2hex($bytes);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }
}