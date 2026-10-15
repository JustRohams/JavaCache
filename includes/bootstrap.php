<?php
declare(strict_types=1);

define('ROOT_PATH',     dirname(__DIR__));
define('INCLUDES_PATH', __DIR__);
define('PAGES_PATH',    ROOT_PATH . '/pages');
define('CONFIG_PATH',   ROOT_PATH . '/config');
define('LANG_PATH',     ROOT_PATH . '/languages');
define('DATA_PATH',     ROOT_PATH . '/data');
define('ASSETS_PATH',   ROOT_PATH . '/assets');
define('DOCS_PATH',     ROOT_PATH . '/docs');

define('APP_START', microtime(true));

$app = require CONFIG_PATH . '/app.php';

if (!empty($app['debug'])) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
}

if (!empty($app['timezone'])) {
    date_default_timezone_set($app['timezone']);
}

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}

require_once INCLUDES_PATH . '/constants.php';
require_once INCLUDES_PATH . '/helpers.php';
require_once INCLUDES_PATH . '/i18n.php';
require_once INCLUDES_PATH . '/uuid.php';

$GLOBALS['app']   = $app;
$GLOBALS['start'] = APP_START;

I18n::init($app['default_lang'] ?? 'en', $app['supported_langs'] ?? ['en']);

$GLOBALS['request'] = [
    'page'   => $_GET['page']  ?? 'home',
    'lang'   => I18n::current(),
    'dir'    => I18n::dir(),
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
    'ip'     => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
    'ua'     => $_SERVER['HTTP_USER_AGENT'] ?? '',
];