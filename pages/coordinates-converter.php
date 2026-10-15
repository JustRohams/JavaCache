<?php
declare(strict_types=1);
$pageScript = 'coordinates-converter.js';
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
            <h1 class="tool-head-title"><?= __e('coords.title') ?></h1>
            <p class="tool-head-sub"><?= __e('coords.subtitle') ?></p>
        </header>

        <div class="coords-card">
            <div class="coords-inputs">

                <div class="field">
                    <label class="field-label"><?= __e('coords.coord_x') ?></label>
                    <input type="number" id="cc-x" class="input" value="0" step="any" data-cc-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('coords.coord_y') ?></label>
                    <input type="number" id="cc-y" class="input" value="64" step="any" data-cc-bind>
                </div>

                <div class="field">
                    <label class="field-label"><?= __e('coords.coord_z') ?></label>
                    <input type="number" id="cc-z" class="input" value="0" step="any" data-cc-bind>
                </div>

            </div>

            <div class="coords-actions">
                <button class="btn btn-primary" id="cc-to-nether" data-sound="click">
                    <span class="material-symbols-rounded">south</span>
                    <?= __e('coords.to_nether') ?>
                </button>
                <button class="btn btn-primary" id="cc-to-overworld" data-sound="click">
                    <span class="material-symbols-rounded">north</span>
                    <?= __e('coords.to_overworld') ?>
                </button>
            </div>

            <div class="coords-result" id="cc-result">
                <div class="coords-result-head">
                    <span class="material-symbols-rounded">my_location</span>
                    <?= __e('coords.result') ?>
                </div>
                <div class="coords-result-body">
                    <div class="coord-box">
                        <span class="coord-label">X</span>
                        <span class="coord-value" id="cc-out-x">—</span>
                    </div>
                    <div class="coord-box">
                        <span class="coord-label">Y</span>
                        <span class="coord-value" id="cc-out-y">—</span>
                    </div>
                    <div class="coord-box">
                        <span class="coord-label">Z</span>
                        <span class="coord-value" id="cc-out-z">—</span>
                    </div>
                </div>
                <p class="coords-note">
                    <span class="material-symbols-rounded">info</span>
                    <?= __e('coords.note') ?>
                </p>
            </div>
        </div>

    </div>
</main>

<?php partial('footer'); ?>