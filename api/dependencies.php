<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$action = $_REQUEST['action'] ?? 'list';
$dataFile = ROOT_PATH . '/data/dependencies.json';

switch ($action) {

    case 'list':
        $default = [
            'depends' => [
                ['name' => 'FormAPI',      'author' => 'jojoe77777', 'type' => 'virion'],
                ['name' => 'InvMenu',      'author' => 'muqsit',     'type' => 'virion'],
                ['name' => 'Commando',     'author' => 'CortexPE',   'type' => 'virion'],
                ['name' => 'ScoreFactory', 'author' => 'JackMD',     'type' => 'virion'],
            ],
            'softdepends' => [
                ['name' => 'EconomyAPI',   'author' => 'onebone',    'type' => 'plugin'],
                ['name' => 'PurePerms',    'author' => 'PEMapModder', 'type' => 'plugin'],
                ['name' => 'RankSystem',   'author' => 'RohaM',      'type' => 'plugin'],
            ],
        ];

        json_response(['ok' => true, 'action' => 'list', 'data' => $default]);
        break;

    case 'validate':
        $input = json_decode((string)($_REQUEST['data'] ?? '[]'), true);
        if (!is_array($input)) {
            json_response(['ok' => false, 'error' => 'invalid_json'], 400);
        }

        $errors = [];
        foreach (['depends', 'softdepends'] as $type) {
            if (!isset($input[$type]) || !is_array($input[$type])) {
                $errors[] = "missing_field:$type";
                continue;
            }
            foreach ($input[$type] as $i => $dep) {
                if (empty($dep['name'])) {
                    $errors[] = "$type.$i.name_required";
                }
            }
        }

        json_response([
            'ok'     => empty($errors),
            'errors' => $errors,
        ]);
        break;

    case 'save':
        $input = json_decode((string)($_REQUEST['data'] ?? '[]'), true);
        if (!is_array($input)) {
            json_response(['ok' => false, 'error' => 'invalid_json'], 400);
        }

        if (!is_dir(dirname($dataFile))) {
            mkdir(dirname($dataFile), 0755, true);
        }

        file_put_contents($dataFile, json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        json_response(['ok' => true, 'action' => 'save']);
        break;

    default:
        json_response(['ok' => false, 'error' => 'unknown_action'], 400);
}