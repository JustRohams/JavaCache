<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$action = $_REQUEST['action'] ?? 'generate';

switch ($action) {

    case 'generate':
        $input = json_decode((string)file_get_contents('php://input'), true) ?: $_REQUEST;

        $name        = trim((string)($input['name']        ?? 'My Addon'));
        $description = trim((string)($input['description'] ?? 'Created with PocketMine Tools'));
        $packType    = trim((string)($input['type']        ?? 'data'));   // data | resources
        $version     = trim((string)($input['version']     ?? '1.0.0'));
        $minEngine   = trim((string)($input['min_engine']  ?? '1.20.0'));
        $useStable   = (bool)($input['stable_uuid'] ?? false);

        $versionParts   = array_map('intval', explode('.', $version));
        $minEngineParts = array_map('intval', explode('.', $minEngine));

        if ($useStable) {
            $headerUuid = DeterministicUUID::generate($name, 0);
            $moduleUuid = DeterministicUUID::generate($name, 1);
        } else {
            $headerUuid = random_uuid();
            $moduleUuid = random_uuid();
        }

        $manifest = [
            'format_version' => 2,
            'header' => [
                'name'               => $name,
                'description'        => $description,
                'uuid'               => $headerUuid,
                'version'            => $versionParts,
                'min_engine_version' => $minEngineParts,
            ],
            'modules' => [[
                'type'    => $packType,
                'uuid'    => $moduleUuid,
                'version' => $versionParts,
            ]],
        ];

        json_response([
            'ok'       => true,
            'action'   => 'generate',
            'manifest' => $manifest,
            'json'     => json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);
        break;

    case 'pack':
        if (!class_exists('ZipArchive')) {
            json_response(['ok' => false, 'error' => 'zip_not_available'], 500);
        }

        $input = json_decode((string)file_get_contents('php://input'), true) ?: $_REQUEST;
        $manifestJson = (string)($input['manifest'] ?? '');

        if ($manifestJson === '' || json_decode($manifestJson, true) === null) {
            json_response(['ok' => false, 'error' => 'invalid_manifest'], 400);
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'mcpack_') . '.mcpack';

        $zip = new ZipArchive();
        if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            json_response(['ok' => false, 'error' => 'zip_create_failed'], 500);
        }

        $zip->addFromString('manifest.json', $manifestJson);
        $zip->close();

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="pack.mcpack"');
        header('Content-Length: ' . filesize($tmpFile));
        readfile($tmpFile);
        unlink($tmpFile);
        exit;

    default:
        json_response(['ok' => false, 'error' => 'unknown_action'], 400);
}