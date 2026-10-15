(function () {
    'use strict';

    const t = (k) => window.I18n ? window.I18n.t(k) : k;
    const debounce = (fn, d = 200) => {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), d);
        };
    };

    const state = {
        name: 'MyPlugin',
        version: '1.0.0',
        main: 'myplugin\\Main',
        author: 'RohaM',
        description: 'یک پلاگین ساخته شده با ابزارهای PocketMineApi5',
        api: '5.0.0',
        commands: [],
        permissions: []
    };

    function generate() {
        let lines = [];
        lines.push(`name: ${state.name}`);
        lines.push(`main: ${state.main}`);
        lines.push(`version: "${state.version}"`);
        lines.push(`api: ["${state.api}"]`);
        lines.push(`author: ${state.author}`);
        lines.push(`description: "${state.description}"`);

        if (state.commands.length) {
            lines.push('commands:');
            state.commands.forEach((cmd) => {
                lines.push(`  ${cmd.name}:`);
                if (cmd.description) lines.push(`    description: "${cmd.description}"`);
                if (cmd.permission)  lines.push(`    permission: ${cmd.permission}`);
            });
        }

        if (state.permissions.length) {
            lines.push('permissions:');
            state.permissions.forEach((perm) => {
                lines.push(`  ${perm.name}:`);
                lines.push(`    default: ${perm.default || 'op'}`);
            });
        }

        return lines.join('\n');
    }

    function render() {
        const code = generate();
        const out = document.getElementById('code-output');
        if (out && window.PMEditor) {
            out.innerHTML = window.PMEditor.highlight(code, 'yaml');
        } else if (out) {
            out.textContent = code;
        }
    }

    function renderCommands() {
        const list = document.getElementById('py-commands-list');
        if (!list) return;
        list.innerHTML = '';

        state.commands.forEach((cmd, i) => {
            const item = document.createElement('div');
            item.className = 'dynamic-item';
            item.innerHTML = `
                <div class="dynamic-item-head">
                    <span class="dynamic-item-title">Command #${i + 1}</span>
                    <button class="dynamic-item-remove" data-remove-cmd="${i}" data-sound="click">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
                <div class="dynamic-item-row">
                    <input type="text" class="input" placeholder="${t('plugin_yml.command_name')}" value="${cmd.name}" data-cmd-name="${i}">
                    <input type="text" class="input" placeholder="${t('plugin_yml.command_desc')}" value="${cmd.description || ''}" data-cmd-desc="${i}">
                </div>
                <div class="dynamic-item-row single">
                    <input type="text" class="input" placeholder="${t('plugin_yml.command_perm')}" value="${cmd.permission || ''}" data-cmd-perm="${i}">
                </div>
            `;
            list.appendChild(item);
        });

        list.querySelectorAll('[data-cmd-name]').forEach((inp) => {
            inp.addEventListener('input', debounce((e) => {
                state.commands[+e.target.dataset.cmdName].name = e.target.value;
                render();
            }));
        });
        list.querySelectorAll('[data-cmd-desc]').forEach((inp) => {
            inp.addEventListener('input', debounce((e) => {
                state.commands[+e.target.dataset.cmdDesc].description = e.target.value;
                render();
            }));
        });
        list.querySelectorAll('[data-cmd-perm]').forEach((inp) => {
            inp.addEventListener('input', debounce((e) => {
                state.commands[+e.target.dataset.cmdPerm].permission = e.target.value;
                render();
            }));
        });
        list.querySelectorAll('[data-remove-cmd]').forEach((b) => {
            b.addEventListener('click', () => {
                state.commands.splice(+b.dataset.removeCmd, 1);
                renderCommands();
                render();
            });
        });
    }

    function renderPermissions() {
        const list = document.getElementById('py-permissions-list');
        if (!list) return;
        list.innerHTML = '';

        state.permissions.forEach((perm, i) => {
            const item = document.createElement('div');
            item.className = 'dynamic-item';
            item.innerHTML = `
                <div class="dynamic-item-head">
                    <span class="dynamic-item-title">Permission #${i + 1}</span>
                    <button class="dynamic-item-remove" data-remove-perm="${i}" data-sound="click">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
                <div class="dynamic-item-row">
                    <input type="text" class="input" placeholder="${t('plugin_yml.permission_name')}" value="${perm.name}" data-perm-name="${i}">
                    <input type="text" class="input" placeholder="${t('plugin_yml.permission_def')}" value="${perm.default || 'op'}" data-perm-def="${i}">
                </div>
            `;
            list.appendChild(item);
        });

        list.querySelectorAll('[data-perm-name]').forEach((inp) => {
            inp.addEventListener('input', debounce((e) => {
                state.permissions[+e.target.dataset.permName].name = e.target.value;
                render();
            }));
        });
        list.querySelectorAll('[data-perm-def]').forEach((inp) => {
            inp.addEventListener('input', debounce((e) => {
                state.permissions[+e.target.dataset.permDef].default = e.target.value;
                render();
            }));
        });
        list.querySelectorAll('[data-remove-perm]').forEach((b) => {
            b.addEventListener('click', () => {
                state.permissions.splice(+b.dataset.removePerm, 1);
                renderPermissions();
                render();
            });
        });
    }

    function bind() {
        const map = {
            'py-name': 'name',
            'py-version': 'version',
            'py-main': 'main',
            'py-author': 'author',
            'py-desc': 'description',
            'py-api': 'api'
        };

        for (const [id, key] of Object.entries(map)) {
            const el = document.getElementById(id);
            if (!el) continue;
            el.addEventListener('input', debounce((e) => {
                state[key] = e.target.value;
                render();
            }));
            el.addEventListener('change', () => {
                state[key] = el.value;
                render();
            });
        }

        const addCmd = document.getElementById('py-add-command');
        if (addCmd) addCmd.addEventListener('click', () => {
            state.commands.push({ name: 'example', description: '', permission: '' });
            renderCommands();
            render();
        });

        const addPerm = document.getElementById('py-add-permission');
        if (addPerm) addPerm.addEventListener('click', () => {
            state.permissions.push({ name: 'myplugin.use', default: 'op' });
            renderPermissions();
            render();
        });
    }

    function init() {
        bind();
        renderCommands();
        renderPermissions();
        render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();