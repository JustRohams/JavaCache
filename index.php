<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$route = $_GET['page'] ?? 'home';
$route = preg_replace('/[^a-z0-9\-]/', '', strtolower((string)$route));

$routes = require CONFIG_PATH . '/routes.php';

if (!isset($routes[$route])) {
    http_response_code(404);

    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        json_response([
            'ok'    => false,
            'error' => 'route_not_found',
            'route' => $route,
        ], 404);
    }

    require PAGES_PATH . '/404.php';
    exit;
}

$pageFile = PAGES_PATH . '/' . $routes[$route];

if (!file_exists($pageFile) || !is_readable($pageFile)) {
    http_response_code(500);

    if (!empty($app['debug'])) {
        echo "<h1>Page file missing</h1>";
        echo "<p>Expected: <code>" . e($pageFile) . "</code></p>";
    } else {
        require PAGES_PATH . '/404.php';
    }
    exit;
}

try {
    require $pageFile;
} catch (Throwable $e) {
    http_response_code(500);

    if (!empty($app['debug'])) {
        echo "<h1>Application Error</h1>";
        echo "<pre>" . e($e->getMessage()) . "\n\n";
        echo e($e->getTraceAsString()) . "</pre>";
    } else {
        pm_log('Fatal: ' . $e->getMessage(), 'error');
        require PAGES_PATH . '/404.php';
    }
}