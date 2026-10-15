<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$action = $_REQUEST['action'] ?? 'o2n'; // o2n | n2o
$x      = (float)($_REQUEST['x'] ?? 0);
$y      = (float)($_REQUEST['y'] ?? 0);
$z      = (float)($_REQUEST['z'] ?? 0);

$result = [
    'ok'     => true,
    'action' => $action,
    'input'  => ['x' => $x, 'y' => $y, 'z' => $z],
];

switch ($action) {
    case 'o2n': // Overworld to Nether
        $result['output'] = [
            'x' => round($x / 8, 2),
            'y' => $y,
            'z' => round($z / 8, 2),
        ];
        $result['label'] = 'Overworld → Nether';
        break;

    case 'n2o': // Nether to Overworld
        $result['output'] = [
            'x' => round($x * 8, 2),
            'y' => $y,
            'z' => round($z * 8, 2),
        ];
        $result['label'] = 'Nether → Overworld';
        break;

    default:
        json_response(['ok' => false, 'error' => 'invalid_action'], 400);
}

json_response($result);