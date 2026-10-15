(function () {
    'use strict';

    const Sounds = {
        enabled: true,
        volume: 0.35,
        cache: {},

        init() {
            const saved = localStorage.getItem('pmtools_sounds');
            if (saved !== null) this.enabled = saved === '1';

            const sounds = (window.__APP__ && window.__APP__.sounds) || {};
            Object.entries(sounds).forEach(([key, path]) => {
                const audio = new Audio(window.__APP__.baseUrl + '/' + path);
                audio.preload = 'auto';
                audio.volume = this.volume;
                this.cache[key] = audio;
            });

            this.bind();
        },

        play(name) {
            if (!this.enabled) return;
            const base = this.cache[name];
            if (!base) return;

            try {
                const clone = base.cloneNode();
                clone.volume = this.volume;
                clone.play().catch(() => {});
            } catch (e) {
            }
        },

        bind() {
            document.addEventListener('click', (e) => {
                const el = e.target.closest('[data-sound]');
                if (!el) return;
                this.play(el.dataset.sound || 'click');
            }, true);

            document.addEventListener('mouseover', (e) => {
                const el = e.target.closest('[data-sound="hover"]');
                if (!el) return;
                if (el.dataset.hoverPlayed) return;
                el.dataset.hoverPlayed = '1';
                setTimeout(() => delete el.dataset.hoverPlayed, 300);
                this.play('hover');
            }, true);
        },

        toggle() {
            this.enabled = !this.enabled;
            localStorage.setItem('pmtools_sounds', this.enabled ? '1' : '0');
            return this.enabled;
        }
    };

    window.PMToolsSounds = Sounds;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Sounds.init());
    } else {
        Sounds.init();
    }
})();