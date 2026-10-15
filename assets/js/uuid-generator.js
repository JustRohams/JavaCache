(function () {
    'use strict';

    const state = {
        mode: 'stable',
        key: 'MyPlugin',
        seed: 0,
        output: '',
        busy: false
    };

    function apiBase() {
        const base = (window.__APP__ && window.__APP__.baseUrl) || '';
        return base.replace(/\/$/, '') + '/api/uuid.php';
    }

    function randomUUID() {
        if (typeof crypto !== 'undefined' && crypto.randomUUID) {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
            const r = Math.random() * 16 | 0;
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    }

    async function callApi(action, params = {}) {
        const qs = new URLSearchParams({ action, ...params });
        const res = await fetch(apiBase() + '?' + qs.toString(), {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.error || ('HTTP ' + res.status));
        }
        return res.json();
    }

    async function generate() {
        if (state.busy) return;
        state.busy = true;

        try {
            if (state.mode === 'stable') {
                try {
                    const data = await callApi('generate', {
                        key: state.key,
                        seed: String(state.seed)
                    });
                    state.output = JSON.stringify({
                        key: data.key,
                        seed: data.seed,
                        uuid: data.uuid
                    }, null, 2);
                } catch (e) {
                    state.output = JSON.stringify({
                        error: 'API unavailable',
                        message: e.message
                    }, null, 2);
                }
            } else if (state.mode === 'random') {
                try {
                    const data = await callApi('random');
                    state.output = JSON.stringify({ uuid: data.uuid }, null, 2);
                } catch (e) {
                    state.output = JSON.stringify({ uuid: randomUUID() }, null, 2);
                }
            } else if (state.mode === 'pair') {
                try {
                    const data = await callApi('pair', { key: state.key });
                    state.output = JSON.stringify({
                        key: data.key,
                        header: data.header,
                        module: data.module
                    }, null, 2);
                } catch (e) {
                    state.output = JSON.stringify({
                        error: 'API unavailable',
                        message: e.message
                    }, null, 2);
                }
            } else if (state.mode === 'reverse') {
                const input = document.getElementById('uuid-reverse-input');
                const uuid = input ? input.value.trim() : '';
                if (!uuid) {
                    state.output = JSON.stringify({ error: 'uuid_required' }, null, 2);
                } else {
                    try {
                        const data = await callApi('reverse', { uuid });
                        state.output = JSON.stringify({
                            uuid: data.uuid,
                            key: data.key
                        }, null, 2);
                    } catch (e) {
                        state.output = JSON.stringify({
                            uuid,
                            key: null,
                            error: e.message
                        }, null, 2);
                    }
                }
            }
        } finally {
            state.busy = false;
            renderOutput();
            if (window.PMToolsSounds) window.PMToolsSounds.play('success');
        }
    }

    function renderOutput() {
        const out = document.getElementById('code-output');
        if (out && window.PMEditor) {
            out.innerHTML = window.PMEditor.highlight(state.output, 'json');
        } else if (out) {
            out.textContent = state.output;
        }
    }

    function bind() {
        document.querySelectorAll('[data-uuid-mode]').forEach((tab) => {
            tab.setAttribute('type', 'button');
            tab.addEventListener('click', () => {
                document.querySelectorAll('[data-uuid-mode]').forEach((el) => el.classList.remove('active'));
                tab.classList.add('active');
                state.mode = tab.dataset.uuidMode;

                const stable = document.getElementById('uuid-stable-group');
                const rev = document.getElementById('uuid-reverse-group');

                if (stable) stable.hidden = (state.mode === 'reverse');
                if (rev) rev.hidden = (state.mode !== 'reverse');

                generate();
            });
        });

        const key = document.getElementById('uuid-key');
        const seed = document.getElementById('uuid-seed');

        if (key) key.addEventListener('input', (e) => { state.key = e.target.value; });
        if (seed) seed.addEventListener('input', (e) => { state.seed = parseInt(e.target.value, 10) || 0; });

        const gen = document.getElementById('uuid-generate');
        if (gen) gen.addEventListener('click', generate);
    }

    function init() {
        bind();
        generate();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
