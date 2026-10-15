(function () {
    'use strict';

    const data = window.__I18N__ || {
        current: 'en',
        dir: 'ltr',
        isRtl: false,
        supported: {},
        strings: {}
    };

    function t(key, replace = {}) {
        let value = data.strings;

        for (const segment of key.split('.')) {
            if (value && typeof value === 'object' && segment in value) {
                value = value[segment];
            } else {
                return key;
            }
        }

        if (typeof value !== 'string') return key;

        for (const [k, v] of Object.entries(replace)) {
            value = value.replace(new RegExp(`\\{${k}\\}`, 'g'), String(v));
        }

        return value;
    }

    function has(key) {
        let value = data.strings;
        for (const segment of key.split('.')) {
            if (value && typeof value === 'object' && segment in value) {
                value = value[segment];
            } else {
                return false;
            }
        }
        return true;
    }

    window.I18n = {
        t,
        has,
        current: () => data.current,
        dir: () => data.dir,
        isRtl: () => data.isRtl,
        supported: () => data.supported,
        all: () => data.strings,

        apply(root = document) {
            root.querySelectorAll('[data-i18n]').forEach((el) => {
                const key = el.dataset.i18n;
                const translated = t(key);
                if (translated !== key) el.textContent = translated;
            });

            root.querySelectorAll('[data-i18n-html]').forEach((el) => {
                const key = el.dataset.i18nHtml;
                const translated = t(key);
                if (translated !== key) el.innerHTML = translated;
            });

            root.querySelectorAll('[data-i18n-placeholder]').forEach((el) => {
                const key = el.dataset.i18nPlaceholder;
                const translated = t(key);
                if (translated !== key) el.placeholder = translated;
            });

            root.querySelectorAll('[data-i18n-title]').forEach((el) => {
                const key = el.dataset.i18nTitle;
                const translated = t(key);
                if (translated !== key) el.title = translated;
            });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => I18n.apply());
    } else {
        I18n.apply();
    }
})();