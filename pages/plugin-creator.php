<?php
declare(strict_types=1);
$pageScript = 'plugin-creator.js';
partial('head');
partial('loader');
partial('header');

$features = PLUGIN_FEATURES;
?>

<main class="page-tool">
    <div class="container">

        <header class="tool-head">
            <a href="<?= e(url('home')) ?>" class="back-btn" data-sound="hover">
                <span class="material-symbols-rounded">arrow_back</span>
                <?= __e('common.back') ?>
            </a>
            <h1 class="tool-head-title"><?= __e('plugin_creator.title') ?></h1>
            <p class="tool-head-sub"><?= __e('plugin_creator.subtitle') ?></p>
        </header>

        <div class="tool-layout">

            <section class="editor-panel">
                <div class="panel-head">
                    <span class="panel-title">
                        <span class="material-symbols-rounded">folder</span>
                        <?= __e('plugin_creator.structure') ?>
                    </span>
                    <div class="panel-actions">
                        <button class="icon-btn" data-copy-target="#code-output" data-sound="click">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                        <button class="icon-btn" id="pc-download" data-sound="click" title="<?= __e('plugin_creator.generate_zip') ?>">
                            <span class="material-symbols-rounded">download</span>
                        </button>
                    </div>
                </div>
                <pre class="editor-pre"><code id="code-output" class="language-tree"></code></pre>
            </section>

            <section class="form-panel">

                <div class="field">
                    <label class="field-label"><?= __e('plugin_creator.plugin_name') ?></label>
                    <input type="text" id="pc-name" class="input" value="MyPlugin" data-pc-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('plugin_creator.author') ?></label>
                    <input type="text" id="pc-author" class="input" value="RohaM" data-pc-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('plugin_creator.namespace') ?></label>
                    <input type="text" id="pc-namespace" class="input" value="myplugin" data-pc-bind>
                </div>

                <div class="field-group">
                    <label class="field-label"><?= __e('plugin_creator.features') ?></label>
                    <div class="feature-list" id="pc-features">
                        <?php foreach ($features as $key => $f): ?>
                            <label class="feature-item">
                                <input type="checkbox" value="<?= e($key) ?>"
                                       <?= !empty($f['default']) ? 'checked' : '' ?>
                                       data-pc-feature>
                                <span class="feature-box">
                                    <span class="material-symbols-rounded feature-icon"><?= e($f['icon']) ?></span>
                                    <span class="feature-label"><?= e($f['label']) ?></span>
                                    <span class="feature-check material-symbols-rounded">check</span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

            </section>
        </div>
    </div>
</main>

<?php partial('footer'); ?>