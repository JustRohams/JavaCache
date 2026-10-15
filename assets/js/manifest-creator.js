(function () {
    'use strict';

    const debounce = (fn, d = 200) => {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), d);
        };
    };

    const state = {
        name: 'My Addon',
        description: 'ساخته شده با PocketMine Tools',
        type: 'data',
        version: '1.0.0',
        minEngine: '1.20.0',
        stableUuid: true,
        headerUuid: '',
        moduleUuid: ''
    };

    function deterministicUUID(key, seed) {
        let hash = 0;
        const str = 'PMTOOLS_v2_ROHAM_2026|' + key.toLowerCase() + '|' + seed;
        for (let i = 0; i < str.length; i++) {
            hash = ((hash << 5) - hash) + str.charCodeAt(i);
            hash |= 0;
        }

        let hex = '';
        for (let s = 0; s < 4; s++) {
            let h = (hash * (s + 1) * 9301 + 49297) % 233280;
            hex += Math.abs(h).toString(16).padStart(8, '0').slice(0, 8);
        }
        hex = hex.slice(0, 32);

        hex = hex.slice(0, 12) + '4' + hex.slice(13);
        const v = parseInt(hex[16], 16);
        hex = hex.slice(0, 16) + ((v & 0x3) | 0x8).toString(16) + hex.slice(17);

        return [
            hex.slice(0, 8),
            hex.slice(8, 12),
            hex.slice(12, 16),
            hex.slice(16, 20),
            hex.slice(20, 32)
        ].join('-');
    }

    function randomUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    function refreshUUIDs() {
        if (state.stableUuid) {
            state.headerUuid = deterministicUUID(state.name, 0);
            state.moduleUuid = deterministicUUID(state.name, 1);
        } else {
            state.headerUuid = randomUUID();
            state.moduleUuid = randomUUID();
        }
    }

    function generate() {
        const v = state.version.split('.').map(Number);
        const me = state.minEngine.split('.').map(Number);

        const manifest = {
            format_version: 2,
            header: {
                name: state.name,
                description: state.description,
                uuid: state.headerUuid,
                version: v,
                min_engine_version: me
            },
            modules: [{
                type: state.type,
                uuid: state.moduleUuid,
                version: v
            }]
        };

        return JSON.stringify(manifest, null, 2);
    }

    function render() {
        const code = generate();
        const out = document.getElementById('code-output');
        if (out && window.PMEditor) {
            out.innerHTML = window.PMEditor.highlight(code, 'json');
        } else if (out) {
            out.textContent = code;
        }
    }

    function bind() {
        const map = {
            'mc-name': 'name',
            'mc-desc': 'description',
            'mc-type': 'type',
            'mc-version': 'version',
            'mc-minengine': 'minEngine'
        };

        for (const [id, key] of Object.entries(map)) {
            const el = document.getElementById(id);
            if (!el) continue;
            el.addEventListener('input', debounce((e) => {
                state[key] = e.target.value;
                if (key === 'name') refreshUUIDs();
                render();
            }));
            el.addEventListener('change', () => {
                state[key] = el.value;
                if (key === 'name') refreshUUIDs();
                render();
            });
        }

        const stable = document.getElementById('mc-stable-uuid');
        if (stable) stable.addEventListener('change', () => {
            state.stableUuid = stable.checked;
            refreshUUIDs();
            render();
        });

        const regen = document.getElementById('mc-regen');
        if (regen) regen.addEventListener('click', () => {
            if (state.stableUuid) {
                state.moduleUuid = deterministicUUID(state.name, Math.floor(Math.random() * 100) + 2);
            } else {
                refreshUUIDs();
            }
            render();
        });

        const dl = document.getElementById('mc-download-pack');
        if (dl) dl.addEventListener('click', async () => {
            const json = generate();
            try {
                const res = await fetch((window.__APP__.baseUrl || '') + '/api/manifest.php?action=pack', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ manifest: json })
                });

                if (!res.ok) throw new Error('failed');

                const blob = await res.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'pack.mcpack';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                setTimeout(() => URL.revokeObjectURL(url), 1000);
            } catch (err) {
                if (window.PMEditor) {
                    window.PMEditor.download(json, 'manifest.json');
                }
            }
        });
    }

    function init() {
        refreshUUIDs();
        bind();
        render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();