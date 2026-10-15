<?php
declare(strict_types=1);
$langs = I18n::all();
$cur   = I18n::current();
?>
<header class="app-header" id="app-header">
    <div class="container header-inner">

        <a href="<?= e(url('home')) ?>" class="brand" data-sound="click">
            <span class="brand-icon material-symbols-rounded">deployed_code</span>
            <span class="brand-text"><?= __e('common.app_name') ?></span>
        </a>

        <nav class="nav-desktop" aria-label="Main navigation">
            <a href="<?= e(url('home')) ?>"         class="<?= e(nav_class('home')) ?>"     data-sound="hover">
                <span class="material-symbols-rounded">home</span>
                <span><?= __e('nav.home') ?></span>
            </a>
            <a href="<?= e(url('form-creator')) ?>" class="<?= e(nav_class('form-creator')) ?>" data-sound="hover">
                <span class="material-symbols-rounded">check_box</span>
                <span><?= __e('tools.form_creator.title') ?></span>
            </a>
            <a href="<?= e(url('creators')) ?>"     class="<?= e(nav_class('creators')) ?>" data-sound="hover">
                <span class="material-symbols-rounded">person</span>
                <span><?= __e('nav.creators') ?></span>
            </a>
            <a href="<?= e($GLOBALS['app']['github_url']) ?>" target="_blank" rel="noopener" class="nav-link" data-sound="hover">
                <span class="material-symbols-rounded">code</span>
                <span><?= __e('nav.github') ?></span>
            </a>
        </nav>

        <div class="header-actions">

            <div class="lang-switcher" id="lang-switcher">
                <button class="icon-btn" id="lang-toggle" title="<?= __e('nav.switch_lang') ?>" data-sound="click">
                    <span class="material-symbols-rounded">translate</span>
                    <span class="lang-current"><?= e(strtoupper($cur)) ?></span>
                </button>
                <ul class="lang-menu" id="lang-menu">
                    <?php foreach ($langs as $code => $info): ?>
                        <li>
                            <a href="<?= e(I18n::urlWithLang($code)) ?>"
                               class="lang-item <?= $code === $cur ? 'active' : '' ?>"
                               data-sound="click">
                                <span class="lang-flag"><?= e($info['flag']) ?></span>
                                <span class="lang-native"><?= e($info['native']) ?></span>
                                <span class="lang-code"><?= e(strtoupper($code)) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <button class="icon-btn" id="theme-toggle" title="<?= __e('nav.switch_theme') ?>" data-sound="toggle">
                <span class="material-symbols-rounded" id="theme-icon">dark_mode</span>
            </button>

            <button class="icon-btn menu-btn" id="menu-toggle" title="<?= __e('nav.toggle_menu') ?>" data-sound="click">
                <span class="material-symbols-rounded">menu</span>
            </button>

        </div>
    </div>

    <nav class="nav-mobile" id="nav-mobile" aria-label="Mobile navigation">
        <a href="<?= e(url('home')) ?>"         class="<?= e(nav_class('home')) ?>">
            <span class="material-symbols-rounded">home</span> <?= __e('nav.home') ?>
        </a>
        <a href="<?= e(url('form-creator')) ?>" class="<?= e(nav_class('form-creator')) ?>">
            <span class="material-symbols-rounded">check_box</span> <?= __e('tools.form_creator.title') ?>
        </a>
        <a href="<?= e(url('plugin-creator')) ?>" class="<?= e(nav_class('plugin-creator')) ?>">
            <span class="material-symbols-rounded">extension</span> <?= __e('tools.plugin_creator.title') ?>
        </a>
        <a href="<?= e(url('uuid-generator')) ?>" class="<?= e(nav_class('uuid-generator')) ?>">
            <span class="material-symbols-rounded">fingerprint</span> <?= __e('tools.uuid_generator.title') ?>
        </a>
        <a href="<?= e(url('dependencies-builder')) ?>" class="<?= e(nav_class('dependencies-builder')) ?>">
            <span class="material-symbols-rounded">account_tree</span> <?= __e('tools.dependencies_builder.title') ?>
        </a>
        <a href="<?= e(url('creators')) ?>"     class="<?= e(nav_class('creators')) ?>">
            <span class="material-symbols-rounded">person</span> <?= __e('nav.creators') ?>
        </a>
    </nav>
</header>