<?php
declare(strict_types=1);
$pageScript = null;
partial('head');
partial('loader');
partial('header');
?>

<main class="page-404">
    <div class="container">
        <div class="error-card">
            <div class="error-code">404</div>
            <h1 class="error-title"><?= __e('errors.404_title') ?></h1>
            <p class="error-message"><?= __e('errors.404_message') ?></p>
            <a href="<?= e(url('home')) ?>" class="btn btn-primary" data-sound="click">
                <span class="material-symbols-rounded">home</span>
                <?= __e('errors.go_home') ?>
            </a>
        </div>
    </div>
</main>

<?php partial('footer'); ?>