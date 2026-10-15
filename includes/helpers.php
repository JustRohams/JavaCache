<?php
declare(strict_types=1);

if (!function_exists('e')) {
    function e(?string $value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        $base = $GLOBALS['app']['base_url'] ?? '/';
        $base = rtrim($base, '/');
        return $base . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $page = 'home', array $params = []): string {
        $base = rtrim($GLOBALS['app']['base_url'] ?? '/', '/');
        $params = array_merge(['page' => $page], $params);
        return $base . '/?' . http_build_query($params);
    }
}

if (!function_exists('is_active')) {
    function is_active(string $page): bool {
        return ($_GET['page'] ?? 'home') === $page;
    }
}

if (!function_exists('nav_class')) {
    function nav_class(string $page, string $base = 'nav-link'): string {
        return is_active($page) ? $base . ' active' : $base;
    }
}

if (!function_exists('json_response')) {
    function json_response(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }
}

if (!function_exists('random_uuid')) {
    function random_uuid(): string {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists('json_body')) {
    function json_body(): array {
        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') return [];
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
}

if (!function_exists('safe_slug')) {
    function safe_slug(string $input): string {
        $input = preg_replace('/[^a-z0-9\-]/', '', strtolower($input)) ?? '';
        return trim($input, '-');
    }
}

if (!function_exists('safe_filename')) {
    function safe_filename(string $name): string {
        return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $name);
    }
}

if (!function_exists('is_valid_uuid')) {
    function is_valid_uuid(string $uuid): bool {
        return (bool)preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $uuid
        );
    }
}

if (!function_exists('format_number')) {
    function format_number(float|int $num, int $decimals = 2): string {
        return number_format((float)$num, $decimals, '.', '');
    }
}

if (!function_exists('pm_log')) {
    function pm_log(string $message, string $level = 'info'): void {
        if (empty($GLOBALS['app']['debug'])) return;
        $logDir = DATA_PATH . '/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
        $line = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), strtoupper($level), $message);
        @file_put_contents($logDir . '/app.log', $line, FILE_APPEND);
    }
}

if (!function_exists('partial')) {
    function partial(string $name, array $vars = []): void {
        $file = PAGES_PATH . '/partials/' . $name . '.php';
        if (!file_exists($file)) {
            if (!empty($GLOBALS['app']['debug'])) {
                echo "<!-- partial not found: {$name} -->";
            }
            return;
        }
        extract($vars, EXTR_SKIP);
        require $file;
    }
}

if (!function_exists('__')) {
    function __(string $key, array $replace = []): string {
        return I18n::t($key, $replace);
    }
}

if (!function_exists('__e')) {
    function __e(string $key, array $replace = []): string {
        return e(I18n::t($key, $replace));
    }
}