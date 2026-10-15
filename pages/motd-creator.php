<?php
declare(strict_types=1);
$pageScript = 'motd-creator.js';
partial('head');
partial('loader');
partial('header');

$colors = require CONFIG_PATH . '/colors.php';
$palette = array_slice($colors, 0, 16); 
$formats = $colors['formats'] ?? [];
?>

<main class="page-tool">
    <div class="container">

        <header class="tool-head">
            <a href="<?= e(url('home')) ?>" class="back-btn" data-sound="hover">
                <span class="material-symbols-rounded">arrow_back</span>
                <?= __e('common.back') ?>
            </a>
            <h1 class="tool-head-title"><?= __e('motd.title') ?></h1>
            <p class="tool-head-sub"><?= __e('motd.subtitle') ?></p>
        </header>

        <div class="tool-layout">

            <section class="editor-panel">
                <div class="panel-head">
                    <span class="panel-title">
                        <span class="material-symbols-rounded">code</span>
                        <?= __e('motd.editor') ?>
                    </span>
                    <div class="panel-actions">
                        <button class="icon-btn" data-copy-target="#code-output" data-sound="click">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                        <button class="icon-btn" id="motd-clear" data-sound="click">
                            <span class="material-symbols-rounded">delete</span>
                        </button>
                    </div>
                </div>
                <pre class="editor-pre"><code id="code-output" class="language-motd"></code></pre>

                <div class="motd-preview-panel">
                    <span class="panel-title">
                        <span class="material-symbols-rounded">sports_esports</span>
                        <?= __e('motd.preview') ?>
                    </span>
                    <div class="motd-preview" id="motd-preview">
                        <span class="motd-line" id="motd-preview-line1"></span>
                        <span class="motd-line" id="motd-preview-line2"></span>
                    </div>
                </div>
            </section>

            <section class="form-panel">

                <div class="field">
                    <label class="field-label"><?= __e('motd.line1') ?></label>
                    <input type="text" id="motd-line1" class="input" value="A Minecraft Server" data-motd-line="1">
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('motd.line2') ?></label>
                    <input type="text" id="motd-line2" class="input" value="Powered by PocketMine" data-motd-line="2">
                </div>

                <div class="field-group">
                    <label class="field-label"><?= __e('motd.pick_color') ?></label>
                    <div class="color-palette" id="motd-colors">
                        <?php foreach ($palette as $c): ?>
                            <button type="button"
                                    class="color-swatch"
                                    style="--swatch:<?= e($c['hex']) ?>"
                                    data-code="<?= e($c['code']) ?>"
                                    title="<?= e($c['name_en'] . ' — ' . $c['name_fa']) ?>"
                                    data-sound="click">
                                <span class="color-code"><?= e($c['code']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label"><?= __e('motd.formats') ?></label>
                    <div class="format-palette" id="motd-formats">
                        <?php foreach ($formats as $f): ?>
                            <button type="button"
                                    class="format-swatch"
                                    data-code="<?= e($f['code']) ?>"
                                    title="<?= e($f['name_en'] . ' — ' . $f['name_fa']) ?>"
                                    style="<?= e($f['css']) ?>"
                                    data-sound="click">
                                <?= e(str_replace('§', '', $f['code'])) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

            </section>
        </div>
    </div>
</main>

<?php partial('footer'); ?>