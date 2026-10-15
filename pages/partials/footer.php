<?php
declare(strict_types=1);
$base = rtrim($GLOBALS['app']['base_url'] ?? '/', '/');

$pageScript = $pageScript ?? ($GLOBALS['pageScript'] ?? null);
?>
<footer class="app-footer">
    <div class="container footer-inner">

        <div class="footer-col">
            <div class="footer-brand">
                <span class="material-symbols-rounded">deployed_code</span>
                <span><?= __e('common.app_name') ?></span>
            </div>
            <p class="footer-desc"><?= __e('meta.description') ?></p>
        </div>

        <div class="footer-col">
            <h4 class="footer-title"><?= __e('footer.links') ?></h4>
            <ul class="footer-list">
                <li><a href="<?= e(url('home')) ?>" data-sound="hover"><?= __e('nav.home') ?></a></li>
                <li><a href="<?= e(url('form-creator')) ?>" data-sound="hover"><?= __e('tools.form_creator.title') ?></a></li>
                <li><a href="<?= e(url('creators')) ?>" data-sound="hover"><?= __e('nav.creators') ?></a></li>
                <li><a href="<?= e($GLOBALS['app']['github_url']) ?>" target="_blank" rel="noopener" data-sound="hover"><?= __e('nav.github') ?></a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4 class="footer-title"><?= __e('footer.legal') ?></h4>
            <ul class="footer-list">
                <li><a href="#" data-sound="hover"><?= __e('footer.privacy') ?></a></li>
                <li><a href="#" data-sound="hover"><?= __e('footer.terms') ?></a></li>
                <li><a href="#" data-sound="hover"><?= __e('footer.license') ?></a></li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <span>
                <?= __e('footer.made_with') ?>
                <span class="material-symbols-rounded heart">favorite</span>
                <?= __e('footer.by') ?>
                <a href="<?= e($GLOBALS['app']['author_url']) ?>" target="_blank" rel="noopener" data-sound="hover">
                    <?= e($GLOBALS['app']['author']) ?>
                </a>
                &copy; <?= date('Y') ?>
            </span>
            <span class="footer-version">
                <?= __e('footer.version') ?> <?= e($GLOBALS['app']['version']) ?>
            </span>
        </div>
    </div>
</footer>

<script src="<?= e($base) ?>/assets/js/sounds.js" defer></script>
<script src="<?= e($base) ?>/assets/js/i18n.js" defer></script>
<script src="<?= e($base) ?>/assets/js/loader.js" defer></script>
<script src="<?= e($base) ?>/assets/js/router.js" defer></script>
<script src="<?= e($base) ?>/assets/js/editor.js" defer></script>
<script src="<?= e($base) ?>/assets/js/main.js" defer></script>

<?php if (!empty($pageScript)): ?>
    <script src="<?= e($base) ?>/assets/js/<?= e($pageScript) ?>" defer></script>
<?php endif; ?>

</body>
</html>