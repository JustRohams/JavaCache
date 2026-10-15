<?php
declare(strict_types=1);
partial('head');
partial('loader');
partial('header');
?>

<main class="page-creators">
    <div class="container">

        <header class="tool-head">
            <a href="<?= e(url('home')) ?>" class="back-btn" data-sound="hover">
                <span class="material-symbols-rounded">arrow_back</span>
                <?= __e('common.back') ?>
            </a>
            <h1 class="tool-head-title"><?= __e('creators.title') ?></h1>
            <p class="tool-head-sub"><?= __e('creators.subtitle') ?></p>
        </header>

        <div class="creators-grid">

            <article class="creator-card creator-main">
                <div class="creator-avatar">
                    <img src="<?= e(asset('images/roham.png')) ?>"
                         alt="RohaM"
                         onerror="this.style.display='none';this.parentElement.classList.add('fallback')">
                    <span class="creator-avatar-fallback material-symbols-rounded">person</span>
                </div>

                <div class="creator-body">
                    <h2 class="creator-name">RohaM</h2>
                    <div class="creator-role"><?= __e('creators.roham_role') ?></div>
                    <p class="creator-bio"><?= __e('creators.roham_bio') ?></p>

                    <div class="creator-socials">
                        <a href="https://github.com/JustRohams" target="_blank" rel="noopener"
                           class="social-btn" title="<?= __e('creators.github') ?>" data-sound="hover">
                            <span class="material-symbols-rounded">code</span>
                            <span>GitHub</span>
                        </a>
                        <a href="https://discord.com/users/roham" target="_blank" rel="noopener"
                           class="social-btn" title="<?= __e('creators.discord') ?>" data-sound="hover">
                            <span class="material-symbols-rounded">forum</span>
                            <span>Discord</span>
                        </a>
                        <a href="#" target="_blank" rel="noopener"
                           class="social-btn" title="<?= __e('creators.website') ?>" data-sound="hover">
                            <span class="material-symbols-rounded">public</span>
                            <span>Website</span>
                        </a>
                    </div>
                </div>
            </article>

            <article class="creator-card">
                <div class="creator-avatar fallback">
                    <span class="creator-avatar-fallback material-symbols-rounded">groups</span>
                </div>
                <div class="creator-body">
                    <h2 class="creator-name">Contributors</h2>
                    <div class="creator-role">Open Source Community</div>
                    <p class="creator-bio">
                        Every PR, issue, and suggestion helps make PocketMine Tools better.
                    </p>
                    <a href="<?= e($GLOBALS['app']['github_url']) ?>/graphs/contributors"
                       class="btn btn-ghost" target="_blank" rel="noopener" data-sound="click">
                        <span class="material-symbols-rounded">visibility</span>
                        View on GitHub
                    </a>
                </div>
            </article>

        </div>
    </div>
</main>

<?php partial('footer'); ?>