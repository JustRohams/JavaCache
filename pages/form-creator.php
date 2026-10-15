<?php
declare(strict_types=1);
$pageScript = 'form-creator.js';
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
            <h1 class="tool-head-title"><?= __e('form_creator.title') ?></h1>
            <p class="tool-head-sub"><?= __e('form_creator.subtitle') ?></p>
        </header>

        <div class="tool-layout">

            <section class="editor-panel">
                <div class="panel-head">
                    <span class="panel-title">
                        <span class="material-symbols-rounded">code</span>
                        <?= __e('common.code_editor') ?>
                    </span>
                    <div class="panel-actions">
                        <button class="icon-btn" data-copy-target="#code-output" title="<?= __e('common.copy') ?>" data-sound="click">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                        <button class="icon-btn" data-download-target="#code-output" data-filename="form.php" title="<?= __e('common.download') ?>" data-sound="click">
                            <span class="material-symbols-rounded">download</span>
                        </button>
                    </div>
                </div>
                <pre class="editor-pre"><code id="code-output" class="language-php"></code></pre>
            </section>

            <section class="form-panel">

                <div class="tabs" role="tablist">
                    <button class="tab active" data-form-type="simple" role="tab" data-sound="click">
                        <span class="material-symbols-rounded">check_box</span>
                        <?= __e('form_creator.type_simple') ?>
                    </button>
                    <button class="tab" data-form-type="modal" role="tab" data-sound="click">
                        <span class="material-symbols-rounded">web_asset</span>
                        <?= __e('form_creator.type_modal') ?>
                    </button>
                    <button class="tab" data-form-type="custom" role="tab" data-sound="click">
                        <span class="material-symbols-rounded">tune</span>
                        <?= __e('form_creator.type_custom') ?>
                    </button>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('form_creator.form_title') ?></label>
                    <input type="text" id="fc-title" class="input" value="<?= __e('form_creator.title') ?>" data-fc-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('form_creator.form_content') ?></label>
                    <textarea id="fc-content" class="input" rows="2" data-fc-bind>Welcome!</textarea>
                </div>

                <div class="field-group" id="fc-buttons-group">
                    <div class="field-group-head">
                        <label class="field-label"><?= __e('form_creator.buttons') ?></label>
                        <button class="btn btn-sm btn-ghost" id="fc-add-button" data-sound="click">
                            <span class="material-symbols-rounded">add_circle</span>
                            <?= __e('form_creator.add_button') ?>
                        </button>
                    </div>
                    <div id="fc-buttons-list" class="dynamic-list"></div>
                </div>

                <div class="field-group" id="fc-modal-group" hidden>
                    <div class="field">
                        <label class="field-label"><?= __e('form_creator.button_1') ?></label>
                        <input type="text" id="fc-btn1" class="input" value="Yes" data-fc-bind>
                    </div>
                    <div class="field">
                        <label class="field-label"><?= __e('form_creator.button_2') ?></label>
                        <input type="text" id="fc-btn2" class="input" value="No" data-fc-bind>
                    </div>
                </div>

                <div class="field-group" id="fc-fields-group" hidden>
                    <div class="field-group-head">
                        <label class="field-label"><?= __e('form_creator.fields') ?></label>
                        <button class="btn btn-sm btn-ghost" id="fc-add-field" data-sound="click">
                            <span class="material-symbols-rounded">add_circle</span>
                            <?= __e('form_creator.add_field') ?>
                        </button>
                    </div>
                    <div id="fc-fields-list" class="dynamic-list"></div>
                </div>

            </section>
        </div>
    </div>
</main>

<?php partial('footer'); ?>