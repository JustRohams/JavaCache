<?php
declare(strict_types=1);

const PMTOOLS_VERSION       = '2.0.0';
const PMTOOLS_BUILD         = '2026.01';
const PMTOOLS_CODENAME      = 'Obsidian';

const PMMP_API_VERSIONS = [
    '4.0.0'  => 'API 4 — PMMP 4.x',
    '5.0.0'  => 'API 5 — PMMP 5.x (Recommended)',
    '5.10.0' => 'API 5.10 — PMMP 5.10+',
];

const BEDROCK_VERSIONS = [
    '1.19.0',
    '1.19.80',
    '1.20.0',
    '1.20.10',
    '1.20.30',
    '1.20.50',
    '1.20.60',
    '1.20.80',
    '1.21.0',
];

const CUSTOM_FORM_FIELDS = [
    'label'    => ['method' => 'addLabel',   'params' => 1, 'icon' => 'label'],
    'input'    => ['method' => 'addInput',   'params' => 4, 'icon' => 'input'],
    'toggle'   => ['method' => 'addToggle',  'params' => 4, 'icon' => 'toggle_on'],
    'dropdown' => ['method' => 'addDropdown','params' => 4, 'icon' => 'arrow_drop_down'],
    'slider'   => ['method' => 'addSlider',  'params' => 6, 'icon' => 'tune'],
    'step'     => ['method' => 'addStepSlider','params'=> 4, 'icon' => 'stairs'],
];

const PLUGIN_FEATURES = [
    'command'  => ['label' => 'Command',       'icon' => 'terminal',       'default' => true],
    'listener' => ['label' => 'Listener',      'icon' => 'sensors',        'default' => true],
    'config'   => ['label' => 'config.yml',    'icon' => 'settings',       'default' => true],
    'manager'  => ['label' => 'Manager',       'icon' => 'account_tree',   'default' => true],
    'sqlite'   => ['label' => 'SQLite Database','icon' => 'storage',       'default' => true],
    'lang'     => ['label' => 'Lang files',    'icon' => 'translate',      'default' => true],
    'task'     => ['label' => 'Scheduler Task','icon' => 'schedule',       'default' => false],
    'form'     => ['label' => 'Form UI',       'icon' => 'check_box',      'default' => false],
];

const MANIFEST_PACK_TYPES = [
    'data'      => 'Behavior / Data Pack',
    'resources' => 'Resource Pack',
    'script'    => 'Script Pack',
];

const EXPORT_FORMATS = [
    'php'  => 'PHP',
    'yml'  => 'YAML',
    'json' => 'JSON',
    'md'   => 'Markdown',
    'txt'  => 'Plain Text',
];

const SOUND_CLICK   = 'assets/sounds/click.mp3';
const SOUND_HOVER   = 'assets/sounds/hover.mp3';
const SOUND_SUCCESS = 'assets/sounds/success.mp3';
const SOUND_ERROR   = 'assets/sounds/error.mp3';
const SOUND_TOGGLE  = 'assets/sounds/toggle.mp3';

const MATERIAL_ICONS = [
    'home'         => 'home',
    'form'         => 'check_box',
    'plugin'       => 'extension',
    'file'         => 'description',
    'manifest'     => 'inventory_2',
    'motd'         => 'chat_bubble',
    'coords'       => 'my_location',
    'command'      => 'terminal',
    'uuid'         => 'fingerprint',
    'dependency'   => 'account_tree',
    'creator'      => 'person',
    'copy'         => 'content_copy',
    'download'     => 'download',
    'add'          => 'add_circle',
    'remove'       => 'remove_circle',
    'check'        => 'check_circle',
    'close'        => 'cancel',
    'edit'         => 'edit',
    'save'         => 'save',
    'refresh'      => 'refresh',
    'translate'    => 'translate',
    'theme'        => 'dark_mode',
];