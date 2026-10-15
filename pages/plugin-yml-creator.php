<?php
declare(strict_types=1);
$pageScript = 'plugin-yml-creator.js';
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
            <h1 class="tool-head-title"><?= __e('plugin_yml.title') ?></h1>
        </header>

        <div class="tool-layout">

            <section class="editor-panel">
                <div class="panel-head">
                    <span class="panel-title">
                        <span class="material-symbols-rounded">code</span>
                        <?= __e('common.code_editor') ?>
                    </span>
                    <div class="panel-actions">
                        <button class="icon-btn" data-copy-target="#code-output" data-sound="click">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                        <button class="icon-btn" data-download-target="#code-output" data-filename="plugin.yml" data-sound="click">
                            <span class="material-symbols-rounded">download</span>
                        </button>
                    </div>
                </div>
                <pre class="editor-pre"><code id="code-output" class="language-yaml"></code></pre>
            </section>

            <section class="form-panel">

                <div class="field">
                    <label class="field-label"><?= __e('plugin_yml.plugin_name') ?></label>
                    <input type="text" id="py-name" class="input" value="MyPlugin" data-py-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('plugin_yml.version') ?></label>
                    <input type="text" id="py-version" class="input" value="1.0.0" data-py-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('plugin_yml.main_class') ?></label>
                    <input type="text" id="py-main" class="input" value="myplugin\Main" data-py-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('plugin_yml.author') ?></label>
                    <input type="text" id="py-author" class="input" value="RohaM" data-py-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('plugin_yml.description') ?></label>
                    <textarea id="py-desc" class="input" rows="2" data-py-bind>یک پلاگین ساخته شده با ابزارهای PocketMineApi5</textarea>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('plugin_yml.api_version') ?></label>
                    <select id="py-api" class="input" data-py-bind>
                        <?php foreach (PMMP_API_VERSIONS as $val => $label): ?>
                            <option value="<?= e($val) ?>"><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field-group">
                    <div class="field-group-head">
                        <label class="field-label"><?= __e('plugin_yml.commands') ?></label>
                        <button class="btn btn-sm btn-ghost" id="py-add-command" data-sound="click">
                            <span class="material-symbols-rounded">add_circle</span>
                            <?= __e('plugin_yml.add_command') ?>
                        </button>
                    </div>
                    <div id="py-commands-list" class="dynamic-list"></div>
                </div>

                <div class="field-group">
                    <div class="field-group-head">
                        <label class="field-label"><?= __e('plugin_yml.permissions') ?></label>
                        <button class="btn btn-sm btn-ghost" id="py-add-permission" data-sound="click">
                            <span class="material-symbols-rounded">add_circle</span>
                            <?= __e('plugin_yml.add_permission') ?>
                        </button>
                    </div>
                    <div id="py-permissions-list" class="dynamic-list"></div>
                </div>

            </section>
        </div>
    </div>
</main>

<?php partial('footer'); ?>