(function () {
    'use strict';

    const t = (k) => (window.I18n ? window.I18n.t(k) : k);

    const debounce = (fn, d = 200) => {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), d);
        };
    };

    const PRESETS = {
        formapi:      { name: 'FormAPI',      author: 'jojoe77777',   type: 'virion', target: 'depends'     },
        invmenu:      { name: 'InvMenu',      author: 'muqsit',       type: 'virion', target: 'depends'     },
        commando:     { name: 'Commando',     author: 'CortexPE',     type: 'virion', target: 'depends'     },
        scorefactory: { name: 'ScoreFactory', author: 'JackMD',       type: 'virion', target: 'depends'     },
        libpmform:    { name: 'libPMMap',     author: 'dktapps',      type: 'virion', target: 'depends'     },
        economyapi:   { name: 'EconomyAPI',   author: 'onebone',      type: 'plugin', target: 'softdepends' },
        pureperms:    { name: 'PurePerms',    author: 'PEMapModder',  type: 'plugin', target: 'softdepends' },
        rankSystem:   { name: 'RankSystem',   author: 'RohaM',        type: 'plugin', target: 'softdepends' }
    };

    const state = {
        depends:     [],   // [{ name, author, type }]
        softdepends: [],   // [{ name, author, type }]
        virions:     [],   // [{ name, author, type }]
        outputMode:  'yml' // yml | poggit
    };
    
    function yamlEscape(str) {
        return String(str || '').replace(/"/g, '\\"');
    }

    function generateYml() {
        const lines = [];

        if (state.depends.length) {
            lines.push('depend:');
            state.depends.forEach((d) => {
                lines.push(`  - ${yamlEscape(d.name)}`);
            });
        }

        if (state.softdepends.length) {
            if (lines.length) lines.push('');
            lines.push('softdepend:');
            state.softdepends.forEach((d) => {
                lines.push(`  - ${yamlEscape(d.name)}`);
            });
        }

        if (!lines.length) {
            lines.push('# No dependencies configured');
        }

        return lines.join('\n');
    }

    function generatePoggit() {
        const allVirions = [
            ...state.depends.filter((d) => d.type === 'virion'),
            ...state.softdepends.filter((d) => d.type === 'virion'),
            ...state.virions
        ];

        const seen = new Set();
        const uniqueVirions = allVirions.filter((v) => {
            const key = v.name.toLowerCase();
            if (seen.has(key)) return false;
            seen.add(key);
            return true;
        });

        const lines = [];
        lines.push('---');
        lines.push('projects:');
        lines.push('  MyPlugin:');
        lines.push('    path: ""');
        lines.push('    libs:');
        lines.push('      src: src');

        if (uniqueVirions.length) {
            lines.push('    virions:');
            uniqueVirions.forEach((v) => {
                lines.push(`      - ${v.name}`);
            });
        }

        return lines.join('\n');
    }

    function renderOutput() {
        const code = state.outputMode === 'yml' ? generateYml() : generatePoggit();
        const title = document.getElementById('dep-output-title');
        const out = document.getElementById('code-output');

        if (title) {
            title.textContent = state.outputMode === 'yml' ? 'plugin.yml' : '.poggit.yml';
        }

        if (out && window.PMEditor) {
            out.innerHTML = window.PMEditor.highlight(code, 'yaml');
        } else if (out) {
            out.textContent = code;
        }
    }

    function renderList(containerId, list, listKey) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = '';

        if (!list.length) {
            const empty = document.createElement('div');
            empty.className = 'uuid-empty';
            empty.textContent = t('common.empty_state') || 'Nothing here yet';
            container.appendChild(empty);
            return;
        }

        list.forEach((item, i) => {
            const el = document.createElement('div');
            el.className = 'dep-item';

            const name = document.createElement('span');
            name.className = 'dep-item-name';
            name.textContent = item.name || '—';

            const author = document.createElement('span');
            author.className = 'dep-item-author';
            author.textContent = item.author || '';

            const type = document.createElement('span');
            type.className = 'dep-item-type dep-type-' + (item.type || 'plugin');
            type.textContent = item.type || 'plugin';

            const remove = document.createElement('button');
            remove.className = 'dep-item-remove';
            remove.setAttribute('data-remove', String(i));
            remove.setAttribute('data-list', listKey);
            remove.setAttribute('data-sound', 'click');

            const icon = document.createElement('span');
            icon.className = 'material-symbols-rounded';
            icon.textContent = 'delete';
            remove.appendChild(icon);

            el.appendChild(name);
            el.appendChild(author);
            el.appendChild(type);
            el.appendChild(remove);

            container.appendChild(el);
        });
    }

    function renderAll() {
        renderList('dep-depends-list', state.depends, 'depends');
        renderList('dep-soft-list', state.softdepends, 'softdepends');
        renderList('dep-virions-list', state.virions, 'virions');
        renderOutput();
        bindRemoves();
    }

    function bindRemoves() {
        document.querySelectorAll('[data-remove]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const listKey = btn.dataset.list;
                const idx = parseInt(btn.dataset.remove, 10);
                if (!listKey || isNaN(idx)) return;
                if (!Array.isArray(state[listKey])) return;

                state[listKey].splice(idx, 1);
                renderAll();

                if (window.PMToolsSounds) {
                    window.PMToolsSounds.play('click');
                }
            });
        });
    }
    
    function addPreset(key) {
        const preset = PRESETS[key];
        if (!preset) return;

        const target = preset.target;
        const exists = state[target].some(
            (d) => d.name.toLowerCase() === preset.name.toLowerCase()
        );
        if (exists) return;

        state[target].push({
            name: preset.name,
            author: preset.author,
            type: preset.type
        });

        renderAll();

        if (window.PMToolsSounds) {
            window.PMToolsSounds.play('success');
        }
    }

    function bind() {
        document.querySelectorAll('[data-preset]').forEach((chip) => {
            chip.addEventListener('click', () => {
                addPreset(chip.dataset.preset);
            });
        });

        const addDep = document.getElementById('dep-add-depend');
        if (addDep) {
            addDep.addEventListener('click', () => {
                state.depends.push({ name: 'NewDep', author: '', type: 'plugin' });
                renderAll();
            });
        }

        const addSoft = document.getElementById('dep-add-soft');
        if (addSoft) {
            addSoft.addEventListener('click', () => {
                state.softdepends.push({ name: 'NewSoft', author: '', type: 'plugin' });
                renderAll();
            });
        }

        const addVir = document.getElementById('dep-add-virion');
        if (addVir) {
            addVir.addEventListener('click', () => {
                state.virions.push({ name: 'NewVirion', author: '', type: 'virion' });
                renderAll();
            });
        }

        const toggle = document.getElementById('dep-toggle-output');
        if (toggle) {
            toggle.addEventListener('click', () => {
                state.outputMode = state.outputMode === 'yml' ? 'poggit' : 'yml';
                renderOutput();
            });
        }
    }

    function init() {
        bind();
        renderAll();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();