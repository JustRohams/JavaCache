<?php
declare(strict_types=1);

return [
    'name'          => 'PocketMine Tools',
    'short_name'    => 'PMTools',
    'version'       => '2.0.0',
    'author'        => 'RohaM',
    'author_url'    => 'https://github.com/JustRohams',
    'github_url'    => 'https://github.com/JustRohams/PocketMineTools',

    'description'   => [
        'en' => 'A modern, bilingual web toolkit for PocketMine-MP plugin developers.',
        'fa' => 'یک جعبه‌ابزار مدرن و دوزبانه برای توسعه‌دهندگان پلاگین PocketMine-MP.',
    ],

    'env'           => 'development',
    'debug'         => true,

    'base_url'      => '/p',

    'timezone'      => 'UTC',

    'default_lang'  => 'en',
    'supported_langs' => ['en', 'fa'],
    
    'meta' => [
        'keywords'    => 'pocketmine, minecraft, bedrock, plugin, formapi, pmmp, tools, developer',
        'og_image'    => 'assets/images/og-image.png',
        'twitter'     => '@RohaM',
    ],

    'features' => [
        'sounds'      => true,
        'loader'      => true,
        'dark_mode'   => true,
        'analytics'   => false,
    ],

    'sounds' => [
        'click'   => 'assets/sounds/click.mp3',
        'hover'   => 'assets/sounds/hover.mp3',
        'success' => 'assets/sounds/success.mp3',
        'error'   => 'assets/sounds/error.mp3',
        'toggle'  => 'assets/sounds/toggle.mp3',
    ],
    
    'cache' => [
        'enabled' => false,
        'path'    => ROOT_PATH . '/data/cache',
        'ttl'     => 3600,
    ],

    'security' => [
        'csrf'           => true,
        'rate_limit'     => true,
        'rate_limit_max' => 60,
    ],
];