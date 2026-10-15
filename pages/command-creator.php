<?php
declare(strict_types=1);
$pageScript = 'command-creator.js';
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
            <h1 class="tool-head-title"><?= __e('command_creator.title') ?></h1>
            <p class="tool-head-sub"><?= __e('command_creator.subtitle') ?></p>
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
                        <button class="icon-btn" data-download-target="#code-output" data-filename="ExampleCommand.php" data-sound="click">
                            <span class="material-symbols-rounded">download</span>
                        </button>
                    </div>
                </div>
                <pre class="editor-pre"><code id="code-output" class="language-php"></code></pre>
            </section>

            <section class="form-panel">

                <div class="field">
                    <label class="field-label"><?= __e('command_creator.command_name') ?></label>
                    <input type="text" id="cmd-name" class="input" value="example" data-cmd-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('command_creator.description') ?></label>
                    <input type="text" id="cmd-desc" class="input" value="دستور نمونه" data-cmd-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('command_creator.permission') ?></label>
                    <input type="text" id="cmd-perm" class="input" value="myplugin.command.example" data-cmd-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('command_creator.aliases') ?></label>
                    <input type="text" id="cmd-aliases" class="input" value="ex,example_cmd" data-cmd-bind>
                </div>

                <div class="field-group">
                    <div class="field-group-head">
                        <label class="field-label"><?= __e('command_creator.arguments') ?></label>
                        <button class="btn btn-sm btn-ghost" id="cmd-add-arg" data-sound="click">
                            <span class="material-symbols-rounded">add_circle</span>
                            <?= __e('common.add') ?>
                        </button>
                    </div>
                    <div id="cmd-args-list" class="dynamic-list"></div>
                </div>

            </section>
        </div>
    </div>
</main>

<?php partial('footer'); ?>