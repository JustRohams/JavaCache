(function () {
    'use strict';

    const steps = [
        { p: 15,  key: 'loader.initializing' },
        { p: 40,  key: 'loader.loading_assets' },
        { p: 65,  key: 'loader.preparing' },
        { p: 88,  key: 'loader.almost' },
        { p: 100, key: 'loader.done' }
    ];

    function run() {
        const loader = document.getElementById('app-loader');
        const bar    = document.getElementById('loader-bar-fill');
        const pct    = document.getElementById('loader-percent');
        const status = document.getElementById('loader-status');

        if (!loader) return;

        const seen = sessionStorage.getItem('pmtools_loader_seen');
        if (seen) {
            loader.classList.add('loaded');
            loader.setAttribute('aria-hidden', 'true');
            document.dispatchEvent(new CustomEvent('loader:done'));
            return;
        }

        const speed = 1;

        let index = 0;
        let progress = 0;

        function tick() {
            if (index >= steps.length) {
                finish();
                return;
            }

            const step = steps[index];
            const target = step.p;

            status.textContent = window.I18n ? window.I18n.t(step.key) : step.key;

            const interval = setInterval(() => {
                progress += 2;
                if (progress >= target) {
                    progress = target;
                    clearInterval(interval);
                    index++;
                    setTimeout(tick, 200 * speed);
                }
                if (bar) bar.style.width = progress + '%';
                if (pct) pct.textContent = progress + '%';
            }, 30 * speed);
        }

        function finish() {
            setTimeout(() => {
                loader.classList.add('loaded');
                loader.setAttribute('aria-hidden', 'true');
                sessionStorage.setItem('pmtools_loader_seen', '1');
                document.dispatchEvent(new CustomEvent('loader:done'));
            }, 300 * speed);
        }

        setTimeout(tick, 100);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();