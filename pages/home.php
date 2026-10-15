<?php
declare(strict_types=1);
$pageScript = null;
partial('head');
partial('loader');
partial('header');

$tools = [
    ['slug' => 'form-creator',          'icon' => 'check_box',     'color' => 'blue',   'key' => 'form_creator'],
    ['slug' => 'plugin-creator',        'icon' => 'extension',     'color' => 'green',  'key' => 'plugin_creator'],
    ['slug' => 'plugin-yml-creator',    'icon' => 'description',   'color' => 'orange', 'key' => 'plugin_yml_creator'],
    ['slug' => 'manifest-creator',      'icon' => 'inventory_2',   'color' => 'purple', 'key' => 'manifest_creator'],
    ['slug' => 'motd-creator',          'icon' => 'chat_bubble',   'color' => 'pink',   'key' => 'motd_creator'],
    ['slug' => 'coordinates-converter', 'icon' => 'my_location',   'color' => 'cyan',   'key' => 'coordinates_converter'],
    ['slug' => 'command-creator',       'icon' => 'terminal',      'color' => 'red',    'key' => 'command_creator'],
    ['slug' => 'uuid-generator',        'icon' => 'fingerprint',   'color' => 'yellow', 'key' => 'uuid_generator'],
    ['slug' => 'dependencies-builder',  'icon' => 'account_tree',  'color' => 'teal',   'key' => 'dependencies_builder'],
];
?>

<main class="page-home">

    <section class="hero">
        <div class="hero-bg"></div>
        <div class="container hero-inner">
            <div class="hero-badge">
                <span class="material-symbols-rounded">bolt</span>
                v<?= e($GLOBALS['app']['version']) ?>
            </div>

            <h1 class="hero-title">
                <?= __e('home.hero_title') ?>
            </h1>

            <p class="hero-subtitle">
                <?= __e('home.hero_subtitle') ?>
            </p>

            <div class="hero-actions">
                <a href="#tools" class="btn btn-primary btn-lg" data-sound="click">
                    <span class="material-symbols-rounded">rocket_launch</span>
                    <?= __e('home.hero_cta') ?>
                </a>
                <a href="<?= e($GLOBALS['app']['github_url']) ?>"
                   target="_blank" rel="noopener"
                   class="btn btn-ghost btn-lg" data-sound="click">
                    <span class="material-symbols-rounded">code</span>
                    <?= __e('home.hero_cta_alt') ?>
                </a>
            </div>

            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-num">9</span>
                    <span class="stat-label">Tools</span>
                </div>
                <div class="stat">
                    <span class="stat-num">2</span>
                    <span class="stat-label">Languages</span>
                </div>
                <div class="stat">
                    <span class="stat-num">100%</span>
                    <span class="stat-label">Free</span>
                </div>
            </div>
        </div>
    </section>

    <section class="tools-section" id="tools">
        <div class="container">
            <header class="section-head">
                <h2 class="section-title"><?= __e('home.tools_title') ?></h2>
                <p class="section-subtitle"><?= __e('home.tools_subtitle') ?></p>
            </header>

            <div class="tools-grid">
                <?php foreach ($tools as $t): ?>
                    <a href="<?= e(url($t['slug'])) ?>"
                       class="tool-card tool-<?= e($t['color']) ?>"
                       data-sound="click">
                        <div class="tool-icon">
                            <span class="material-symbols-rounded"><?= e($t['icon']) ?></span>
                        </div>
                        <div class="tool-body">
                            <h3 class="tool-title"><?= __e("tools.{$t['key']}.title") ?></h3>
                            <p class="tool-desc"><?= __e("tools.{$t['key']}.description") ?></p>
                        </div>
                        <span class="tool-arrow material-symbols-rounded">arrow_forward</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <span class="material-symbols-rounded cta-icon">star</span>
                <div class="cta-body">
                    <h3 class="cta-title"><?= __e('home.star_github') ?></h3>
                    <p class="cta-text"><?= __e('meta.description') ?></p>
                </div>
                <a href="<?= e($GLOBALS['app']['github_url']) ?>"
                   target="_blank" rel="noopener"
                   class="btn btn-primary" data-sound="click">
                    <span class="material-symbols-rounded">code</span>
                    GitHub
                </a>
            </div>
        </div>
    </section>

</main>

<?php partial('footer'); ?>