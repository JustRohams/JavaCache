<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$action = $_REQUEST['action'] ?? 'random';

try {
    switch ($action) {

        case 'generate':
            $key  = trim((string)($_REQUEST['key'] ?? ''));
            $seed = (int)($_REQUEST['seed'] ?? 0);

            if ($key === '') {
                json_response(['ok' => false, 'error' => 'key_required'], 400);
            }
            if (strlen($key) > 256) {
                json_response(['ok' => false, 'error' => 'key_too_long'], 400);
            }

            $uuid = DeterministicUUID::generate($key, $seed);

            json_response([
                'ok'       => true,
                'action'   => 'generate',
                'uuid'     => $uuid,
                'key'      => $key,
                'seed'     => $seed,
            ]);
            break;

        case 'reverse':
            $uuid = trim((string)($_REQUEST['uuid'] ?? ''));

            if (!DeterministicUUID::isValid($uuid)) {
                json_response(['ok' => false, 'error' => 'invalid_uuid'], 400);
            }

            $key = DeterministicUUID::reverse($uuid);

            json_response([
                'ok'   => true,
                'action' => 'reverse',
                'uuid' => $uuid,
                'key'  => $key,
            ]);
            break;

        case 'random':
            json_response([
                'ok'   => true,
                'action' => 'random',
                'uuid' => random_uuid(),
            ]);
            break;

        case 'pair':
            $key = trim((string)($_REQUEST['key'] ?? ''));

            if ($key === '') {
                json_response(['ok' => false, 'error' => 'key_required'], 400);
            }

            json_response([
                'ok'      => true,
                'action'  => 'pair',
                'key'     => $key,
                'header'  => DeterministicUUID::generate($key, 0),
                'module'  => DeterministicUUID::generate($key, 1),
            ]);
            break;

        default:
            json_response(['ok' => false, 'error' => 'unknown_action'], 400);
    }
} catch (Throwable $e) {
    json_response([
        'ok'    => false,
        'error' => 'server_error',
        'msg'   => $GLOBALS['app']['debug'] ? $e->getMessage() : null,
    ], 500);
}