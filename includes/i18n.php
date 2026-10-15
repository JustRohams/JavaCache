<?php
declare(strict_types=1);

final class I18n
{
    private static string $current = 'en';
    private static array  $supported = ['en'];
    private static array  $strings = [];
    private static array  $meta = [];
    private static bool   $initialized = false;

    public static function init(string $default, array $supported = ['en']): void
    {
        if (self::$initialized) return;
        self::$supported = $supported ?: ['en'];
        self::$meta = require CONFIG_PATH . '/languages.php';

        $lang = self::detect();

        if (!in_array($lang, self::$supported, true)) {
            $lang = in_array($default, self::$supported, true) ? $default : self::$supported[0];
        }

        self::load($lang);
        self::$initialized = true;
    }

    private static function detect(): string
    {
        if (!empty($_GET['lang']) && in_array($_GET['lang'], self::$supported, true)) {
            setcookie('lang', $_GET['lang'], time() + 60 * 60 * 24 * 365, '/');
            return $_GET['lang'];
        }

        if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], self::$supported, true)) {
            return $_COOKIE['lang'];
        }

        $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if ($accept !== '') {
            $preferred = substr($accept, 0, 2);
            if (in_array($preferred, self::$supported, true)) {
                return $preferred;
            }
        }

        return 'en';
    }

    public static function load(string $lang): void
    {
        $file = LANG_PATH . '/' . safe_slug($lang) . '.php';

        if (!file_exists($file)) {
            $file = LANG_PATH . '/en.php';
        }

        $strings = require $file;
        self::$strings = is_array($strings) ? $strings : [];
        self::$current = $lang;
    }

    public static function t(string $key, array $replace = []): string
    {
        $value = self::$strings;

        foreach (explode('.', $key) as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $key;
            }
        }

        if (!is_string($value)) {
            return $key;
        }

        foreach ($replace as $k => $v) {
            $value = str_replace('{' . $k . '}', (string)$v, $value);
        }

        return $value;
    }

    public static function has(string $key): bool
    {
        $value = self::$strings;
        foreach (explode('.', $key) as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return false;
            }
        }
        return true;
    }

    public static function current(): string
    {
        return self::$current;
    }

    public static function dir(): string
    {
        return self::$meta[self::$current]['dir'] ?? 'ltr';
    }

    public static function isRtl(): bool
    {
        return self::dir() === 'rtl';
    }

    public static function info(?string $lang = null): array
    {
        $lang = $lang ?? self::$current;
        return self::$meta[$lang] ?? [];
    }

    public static function supported(): array
    {
        return self::$supported;
    }

    public static function all(): array
    {
        $result = [];
        foreach (self::$supported as $code) {
            $result[$code] = self::$meta[$code] ?? [
                'code'   => $code,
                'name'   => $code,
                'native' => $code,
                'flag'   => '',
                'dir'    => 'ltr',
            ];
        }
        return $result;
    }

    public static function toJs(): string
    {
        $payload = [
            'current'   => self::$current,
            'dir'       => self::dir(),
            'isRtl'     => self::isRtl(),
            'supported' => self::all(),
            'strings'   => self::$strings,
        ];
        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function urlWithLang(string $lang, ?string $page = null): string
    {
        $page = $page ?? ($_GET['page'] ?? 'home');
        return url($page, ['lang' => $lang]);
    }
}