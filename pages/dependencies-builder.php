<?php
declare(strict_types=1);
$pageScript = 'dependencies-builder.js';
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
            <h1 class="tool-head-title"><?= __e('dependencies.title') ?></h1>
            <p class="tool-head-sub"><?= __e('dependencies.subtitle') ?></p>
        </header>

        <div class="tool-layout">

            <section class="editor-panel">
                <div class="panel-head">
                    <span class="panel-title">
                        <span class="material-symbols-rounded">code</span>
                        <span id="dep-output-title">plugin.yml</span>
                    </span>
                    <div class="panel-actions">
                        <button class="icon-btn" id="dep-toggle-output" title="Toggle output" data-sound="click">
                            <span class="material-symbols-rounded">swap_horiz</span>
                        </button>
                        <button class="icon-btn" data-copy-target="#code-output" data-sound="click">
                            <span class="material-symbols-rounded">content_copy</span>
                        </button>
                        <button class="icon-btn" data-download-target="#code-output" data-filename="dependencies.txt" data-sound="click">
                            <span class="material-symbols-rounded">download</span>
                        </button>
                    </div>
                </div>
                <pre class="editor-pre"><code id="code-output" class="language-yaml"></code></pre>
            </section>

            <section class="form-panel">

                <div class="field-group">
                    <label class="field-label"><?= __e('dependencies.presets') ?></label>
                    <div class="preset-list" id="dep-presets">
                        <button class="preset-chip" data-preset="formapi" data-sound="click">FormAPI</button>
                        <button class="preset-chip" data-preset="invmenu" data-sound="click">InvMenu</button>
                        <button class="preset-chip" data-preset="commando" data-sound="click">Commando</button>
                        <button class="preset-chip" data-preset="scorefactory" data-sound="click">ScoreFactory</button>
                        <button class="preset-chip" data-preset="economyapi" data-sound="click">EconomyAPI</button>
                        <button class="preset-chip" data-preset="pureperms" data-sound="click">PurePerms</button>
                    </div>
                </div>

                <div class="field-group">
                    <div class="field-group-head">
                        <label class="field-label">
                            <span class="material-symbols-rounded">link</span>
                            <?= __e('dependencies.depends') ?>
                        </label>
                        <button class="btn btn-sm btn-ghost" id="dep-add-depend" data-sound="click">
                            <span class="material-symbols-rounded">add_circle</span>
                            <?= __e('common.add') ?>
                        </button>
                    </div>
                    <div id="dep-depends-list" class="dynamic-list"></div>
                </div>

                <div class="field-group">
                    <div class="field-group-head">
                        <label class="field-label">
                            <span class="material-symbols-rounded">link_off</span>
                            <?= __e('dependencies.softdepends') ?>
                        </label>
                        <button class="btn btn-sm btn-ghost" id="dep-add-soft" data-sound="click">
                            <span class="material-symbols-rounded">add_circle</span>
                            <?= __e('common.add') ?>
                        </button>
                    </div>
                    <div id="dep-soft-list" class="dynamic-list"></div>
                </div>

                <div class="field-group">
                    <div class="field-group-head">
                        <label class="field-label">
                            <span class="material-symbols-rounded">hub</span>
                            <?= __e('dependencies.virions') ?>
                        </label>
                        <button class="btn btn-sm btn-ghost" id="dep-add-virion" data-sound="click">
                            <span class="material-symbols-rounded">add_circle</span>
                            <?= __e('common.add') ?>
                        </button>
                    </div>
                    <div id="dep-virions-list" class="dynamic-list"></div>
                </div>

            </section>
        </div>
    </div>
</main>

<?php partial('footer'); ?>