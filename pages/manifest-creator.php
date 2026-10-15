<?php
declare(strict_types=1);
$pageScript = 'manifest-creator.js';
partial('head');
partial('loader');
partial('header');
?>

<main class="page-tool">
    <div class="container">

        <header class="tool-head">
            <a href="<?= e(url('home')) ?>" class="back-btn" data-sound="hover">
                <span class="material-symbols-rounded">arrow_back</span>
                <?= __e('common.back') ?>
            </a>
            <h1 class="tool-head-title"><?= __e('manifest.title') ?></h1>
        </header>

        <div class="tool-layout">

            <section class="editor-panel">
                <div class="panel-head">
                    <span class="panel-title">
                        <span class="material-symbols-rounded">data_object</span>
                        <?= __e('common.code_editor') ?>
                    </span>
                    <div class="panel-actions">
                        <button class="icon-btn" data-copy-target="#code-output" data-sound="click">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                        <button class="icon-btn" data-download-target="#code-output" data-filename="manifest.json" data-sound="click">
                            <span class="material-symbols-rounded">download</span>
                        </button>
                    </div>
                </div>
                <pre class="editor-pre"><code id="code-output" class="language-json"></code></pre>
            </section>

            <section class="form-panel">

                <div class="field">
                    <label class="field-label"><?= __e('manifest.pack_name') ?></label>
                    <input type="text" id="mc-name" class="input" value="My Addon" data-mc-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('manifest.description') ?></label>
                    <textarea id="mc-desc" class="input" rows="2" data-mc-bind>ساخته شده با PocketMine Tools</textarea>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('manifest.pack_type') ?></label>
                    <select id="mc-type" class="input" data-mc-bind>
                        <?php foreach (MANIFEST_PACK_TYPES as $val => $label): ?>
                            <option value="<?= e($val) ?>"><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('manifest.version') ?></label>
                    <input type="text" id="mc-version" class="input" value="1.0.0" data-mc-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('manifest.min_engine') ?></label>
                    <input type="text" id="mc-minengine" class="input" value="1.20.0" data-mc-bind>
                </div>

                <div class="field checkbox-field">
                    <label class="checkbox-label">
                        <input type="checkbox" id="mc-stable-uuid" checked data-mc-bind>
                        <span class="checkbox-box"></span>
                        <span><?= __e('manifest.uuid_stable') ?></span>
                    </label>
                </div>

                <div class="form-actions">
                    <button class="btn btn-ghost" id="mc-regen" data-sound="click">
                        <span class="material-symbols-rounded">refresh</span>
                        <?= __e('manifest.regen_uuid') ?>
                    </button>
                    <button class="btn btn-primary" id="mc-download-pack" data-sound="click">
                        <span class="material-symbols-rounded">download</span>
                        <?= __e('manifest.download_pack') ?>
                    </button>
                </div>

            </section>
        </div>
    </div>
</main>

<?php partial('footer'); ?>