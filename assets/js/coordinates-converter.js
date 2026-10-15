(function () {
    'use strict';

    const state = { x: 0, y: 64, z: 0 };

    function convert(mode) {
        const { x, y, z } = state;
        let outX, outZ;

        if (mode === 'o2n') {
            outX = x / 8;
            outZ = z / 8;
        } else {
            outX = x * 8;
            outZ = z * 8;
        }

        const outElX = document.getElementById('cc-out-x');
        const outElY = document.getElementById('cc-out-y');
        const outElZ = document.getElementById('cc-out-z');

        if (outElX) outElX.textContent = round(outX);
        if (outElY) outElY.textContent = round(y);
        if (outElZ) outElZ.textContent = round(outZ);

        if (window.PMToolsSounds) window.PMToolsSounds.play('success');
    }

    function round(n) {
        return Math.round(n * 100) / 100;
    }

    function bind() {
        const ids = { x: 'cc-x', y: 'cc-y', z: 'cc-z' };

        Object.entries(ids).forEach(([key, id]) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('input', () => {
                state[key] = parseFloat(el.value) || 0;
            });
        });

        const toNether = document.getElementById('cc-to-nether');
        const toOver   = document.getElementById('cc-to-overworld');

        if (toNether) toNether.addEventListener('click', () => convert('o2n'));
        if (toOver)   toOver.addEventListener('click', () => convert('n2o'));

        document.querySelectorAll('[data-cc-bind]').forEach((inp) => {
            inp.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') convert('o2n');
            });
        });
    }

    function init() {
        bind();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();