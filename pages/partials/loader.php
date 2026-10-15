<?php
declare(strict_types=1);
?>
<div id="app-loader" class="app-loader" aria-hidden="true">
    <div class="loader-inner">

        <div class="loader-logo">
            <svg viewBox="0 0 100 100" class="loader-svg" aria-hidden="true">
                <defs>
                    <linearGradient id="loaderGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%"  stop-color="var(--accent-1)"/>
                        <stop offset="100%" stop-color="var(--accent-2)"/>
                    </linearGradient>
                </defs>
                <circle cx="50" cy="50" r="42"
                        fill="none"
                        stroke="url(#loaderGrad)"
                        stroke-width="3"
                        stroke-linecap="round"
                        stroke-dasharray="264"
                        stroke-dashoffset="264"
                        class="loader-ring"/>
                <path d="M35 50 L45 60 L65 40"
                      fill="none"
                      stroke="url(#loaderGrad)"
                      stroke-width="4"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="loader-check"/>
            </svg>
        </div>

        <div class="loader-text">
            <div class="loader-title"><?= __e('common.app_name') ?></div>
            <div class="loader-status" id="loader-status"><?= __e('loader.initializing') ?></div>
        </div>

        <div class="loader-bar">
            <div class="loader-bar-fill" id="loader-bar-fill"></div>
        </div>

        <div class="loader-percent" id="loader-percent">0%</div>

    </div>
</div>