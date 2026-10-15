<?php
declare(strict_types=1);
$meta = I18n::t('meta.title');
$desc = I18n::t('meta.description');
$kw   = I18n::t('meta.keywords');
$dir  = I18n::dir();
$lang = I18n::current();

$base = rtrim($GLOBALS['app']['base_url'] ?? '/', '/');
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>" dir="<?= e($dir) ?>" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= e($meta) ?></title>
    <meta name="description" content="<?= e($desc) ?>">
    <meta name="keywords" content="<?= e($kw) ?>">
    <meta name="author" content="<?= e($GLOBALS['app']['author']) ?>">
    <meta name="robots" content="index, follow">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($meta) ?>">
    <meta property="og:description" content="<?= e($desc) ?>">
    <meta property="og:image" content="<?= e($base) ?>/assets/images/og-image.png">
    <meta property="og:url" content="<?= e($base) ?>/">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($meta) ?>">
    <meta name="twitter:description" content="<?= e($desc) ?>">

    <link rel="icon" type="image/x-icon" href="<?= e($base) ?>/assets/images/favicon.ico">
    <link rel="apple-touch-icon" href="<?= e($base) ?>/assets/images/logo.svg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap">

    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/loader.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/nav.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/cards.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/editor.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/forms.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/buttons.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/uuid.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/motd.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/dependencies.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/home.css">
    <link rel="stylesheet" href="<?= e($base) ?>/assets/css/responsive.css">

    <script>
        window.__I18N__ = <?= I18n::toJs() ?>;
        window.__APP__ = {
            baseUrl:  '<?= e($base) ?>',
            version:  '<?= e($GLOBALS['app']['version']) ?>',
            sounds:   <?= json_encode($GLOBALS['app']['sounds'] ?? [], JSON_UNESCAPED_SLASHES) ?>,
            features: <?= json_encode($GLOBALS['app']['features'] ?? [], JSON_UNESCAPED_SLASHES) ?>
        };
    </script>
</head>
<body class="app-body lang-<?= e($lang) ?> dir-<?= e($dir) ?>">