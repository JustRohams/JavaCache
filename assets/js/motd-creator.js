(function () {
    'use strict';

    const COLOR_MAP = {
        '§0': 'c-black',  '§1': 'c-dblue',  '§2': 'c-dgreen', '§3': 'c-daqua',
        '§4': 'c-dred',   '§5': 'c-dpurple','§6': 'c-gold',   '§7': 'c-gray',
        '§8': 'c-dgray',  '§9': 'c-blue',   '§a': 'c-green',  '§b': 'c-aqua',
        '§c': 'c-red',    '§d': 'c-purple', '§e': 'c-yellow', '§f': 'c-white'
    };

    const state = {
        line1: 'A Minecraft Server',
        line2: 'Powered by PocketMine',
        focused: 1,       
        codes1: [],        
        codes2: []
    };

    function renderPreview() {
        ['1', '2'].forEach((n) => {
            const el = document.getElementById('motd-preview-line' + n);
            if (!el) return;
            const text = state['line' + n] || '';
            el.innerHTML = colorize(text);
        });
    }

    function colorize(text) {
        let out = '';
        let currentClass = '';

        for (let i = 0; i < text.length; i++) {
            const two = text.substr(i, 2);
            if (COLOR_MAP[two]) {
                if (currentClass) out += '</span>';
                currentClass = COLOR_MAP[two];
                out += `<span class="${currentClass}">`;
                i++;
            } else {
                out += escapeHtml(text[i]);
            }
        }
        if (currentClass) out += '</span>';
        return out || ' ';
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        })[c]);
    }

    function renderCode() {
        const l1 = state.line1;
        const l2 = state.line2;
        const code = [
            '# MOTD Preview',
            'line1: "' + l1 + '"',
            'line2: "' + l2 + '"',
            '',
            '# In-Game',
            '§r' + l1,
            '§r' + l2
        ].join('\n');

        const out = document.getElementById('code-output');
        if (out) out.textContent = code;
    }

    function render() {
        renderPreview();
        renderCode();
    }

    function insertCode(code) {
        const key = 'line' + state.focused;
        state[key] = (state[key] || '') + code;
        const input = document.getElementById('motd-line' + state.focused);
        if (input) input.value = state[key];
        render();
    }

    function bind() {
        const l1 = document.getElementById('motd-line1');
        const l2 = document.getElementById('motd-line2');

        if (l1) {
            l1.addEventListener('focus', () => state.focused = 1);
            l1.addEventListener('input', (e) => {
                state.line1 = e.target.value;
                render();
            });
        }
        if (l2) {
            l2.addEventListener('focus', () => state.focused = 2);
            l2.addEventListener('input', (e) => {
                state.line2 = e.target.value;
                render();
            });
        }

        document.querySelectorAll('.color-swatch').forEach((sw) => {
            sw.addEventListener('click', () => {
                insertCode(sw.dataset.code);
            });
        });

        document.querySelectorAll('.format-swatch').forEach((sw) => {
            sw.addEventListener('click', () => {
                insertCode(sw.dataset.code);
            });
        });

        const clear = document.getElementById('motd-clear');
        if (clear) clear.addEventListener('click', () => {
            state.line1 = '';
            state.line2 = '';
            if (l1) l1.value = '';
            if (l2) l2.value = '';
            render();
        });
    }

    function init() {
        bind();
        render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();