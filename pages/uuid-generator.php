<?php
declare(strict_types=1);
$pageScript = 'uuid-generator.js';
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
            <h1 class="tool-head-title"><?= __e('uuid.title') ?></h1>
            <p class="tool-head-sub"><?= __e('uuid.subtitle') ?></p>
        </header>

        <div class="tool-layout">

            <section class="editor-panel">
                <div class="panel-head">
                    <span class="panel-title">
                        <span class="material-symbols-rounded">code</span>
                        <?= __e('common.output') ?>
                    </span>
                    <div class="panel-actions">
                        <button class="icon-btn" data-copy-target="#code-output" data-sound="click">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                    </div>
                </div>
                <pre class="editor-pre"><code id="code-output" class="language-json"></code></pre>
            </section>

            <section class="form-panel">

                <div class="tabs">
                    <button class="tab active" data-uuid-mode="stable" data-sound="click">
                        <span class="material-symbols-rounded">fingerprint</span>
                        <?= __e('uuid.mode_stable') ?>
                    </button>
                    <button class="tab" data-uuid-mode="random" data-sound="click">
                        <span class="material-symbols-rounded">casino</span>
                        <?= __e('uuid.mode_random') ?>
                    </button>
                    <button class="tab" data-uuid-mode="pair" data-sound="click">
                        <span class="material-symbols-rounded">join_inner</span>
                        <?= __e('uuid.mode_pair') ?>
                    </button>
                    <button class="tab" data-uuid-mode="reverse" data-sound="click">
                        <span class="material-symbols-rounded">undo</span>
                        <?= __e('uuid.mode_reverse') ?>
                    </button>
                </div>

                <div id="uuid-stable-group">
                    <div class="field">
                        <label class="field-label"><?= __e('uuid.key_label') ?></label>
                        <input type="text" id="uuid-key" class="input" value="MyPlugin" data-uuid-bind>
                    </div>
                    <div class="field">
                        <label class="field-label"><?= __e('uuid.seed_label') ?></label>
                        <input type="number" id="uuid-seed" class="input" value="0" min="0" data-uuid-bind>
                    </div>
                </div>

                <!-- حالت reverse -->
                <div id="uuid-reverse-group" hidden>
                    <div class="field">
                        <label class="field-label"><?= __e('uuid.uuid_output') ?></label>
                        <input type="text" id="uuid-reverse-input" class="input" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                    </div>
                </div>

                <button class="btn btn-primary btn-lg" id="uuid-generate" data-sound="click">
                    <span class="material-symbols-rounded">bolt</span>
                    <?= __e('common.generate') ?>
                </button>

            </section>
        </div>
    </div>
</main>

<?php partial('footer'); ?>