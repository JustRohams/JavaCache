(function () {
    'use strict';

    const Main = {

        init() {
            this.theme();
            this.language();
            this.mobileMenu();
            this.headerScroll();
            this.autoInit();
        },

        theme() {
            const btn  = document.getElementById('theme-toggle');
            const icon = document.getElementById('theme-icon');
            const saved = localStorage.getItem('pmtools_theme');
            const prefers = window.matchMedia('(prefers-color-scheme: light)').matches;

            const theme = saved || (prefers ? 'light' : 'dark');
            this.setTheme(theme);

            if (btn) {
                btn.addEventListener('click', () => {
                    const cur = document.documentElement.dataset.theme || 'dark';
                    const next = cur === 'dark' ? 'light' : 'dark';
                    this.setTheme(next);
                    localStorage.setItem('pmtools_theme', next);
                    if (window.PMToolsSounds) window.PMToolsSounds.play('toggle');
                });
            }
        },

        setTheme(theme) {
            document.documentElement.dataset.theme = theme;
            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.textContent = theme === 'dark' ? 'dark_mode' : 'light_mode';
            }
        },

        language() {
            const switcher = document.getElementById('lang-switcher');
            const toggle   = document.getElementById('lang-toggle');
            if (!switcher || !toggle) return;

            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                switcher.classList.toggle('open');
            });

            document.addEventListener('click', (e) => {
                if (!switcher.contains(e.target)) {
                    switcher.classList.remove('open');
                }
            });
        },

        mobileMenu() {
            const btn = document.getElementById('menu-toggle');
            const nav = document.getElementById('nav-mobile');
            if (!btn || !nav) return;

            btn.addEventListener('click', () => {
                nav.classList.toggle('open');
            });
        },

        headerScroll() {
            const header = document.getElementById('app-header');
            if (!header) return;

            let lastY = 0;
            window.addEventListener('scroll', () => {
                const y = window.scrollY;
                header.classList.toggle('scrolled', y > 10);
                lastY = y;
            }, { passive: true });
        },

        autoInit() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('[data-animate]').forEach((el) => {
                observer.observe(el);
            });
        }
    };

    window.PMMain = Main;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Main.init());
    } else {
        Main.init();
    }
})();