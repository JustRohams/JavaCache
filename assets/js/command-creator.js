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
        name: 'example',
        description: 'دستور نمونه',
        permission: 'myplugin.command.example',
        aliases: 'ex,example_cmd',
        args: []
    };

    function generate() {
        function phpEscape(str) {
            return String(str ?? '')
                .replace(/\\/g, '\\\\')
                .replace(/"/g, '\\"')
                .replace(/\$/g, '\\$');
        }
        const rawName = state.name || 'example';
        const cleaned = rawName.replace(/[^a-zA-Z0-9_]/g, '') || 'example';
        const classBase = cleaned.charAt(0).toUpperCase() + cleaned.slice(1);
        const aliasList = state.aliases
            .split(',')
            .map(a => a.trim())
            .filter(Boolean)
            .map(a => `"${phpEscape(a)}"`)
            .join(', ');

        let lines = [];
        lines.push('<?php');
        lines.push('declare(strict_types=1);');
        lines.push('');
        lines.push('namespace myplugin\\command;');
        lines.push('');
        lines.push('use pocketmine\\command\\Command;');
        lines.push('use pocketmine\\command\\CommandSender;');
        lines.push('use pocketmine\\player\\Player;');
        lines.push('');
        lines.push(`class ${classBase}Command extends Command {`);
        lines.push('    public function __construct() {');
        lines.push(`        parent::__construct("${phpEscape(state.name)}", "${phpEscape(state.description)}", "/${phpEscape(state.name)}", [${aliasList}]);`);
        if (state.permission) {
            lines.push(`        $this->setPermission("${phpEscape(state.permission)}");`);
        }
        lines.push('    }');
        lines.push('');
        lines.push('    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {');
        lines.push('        if (!$this->testPermission($sender)) {');
        lines.push('            return false;');
        lines.push('        }');
        lines.push('');

        if (state.args.length) {
            lines.push(`        if (count($args) < ${state.args.length}) {`);
            const usage = state.args.map(a => '<' + (a.name || 'arg') + '>').join(' ');
            lines.push(`            $sender->sendMessage("Usage: /${phpEscape(state.name)} ${phpEscape(usage)}");`);
            lines.push('            return false;');
            lines.push('        }');
            lines.push('');

            state.args.forEach((arg, i) => {
                const argName = (arg.name || ('arg' + i)).replace(/[^a-zA-Z0-9_]/g, '') || ('arg' + i);
                switch (arg.type) {
                    case 'int':
                        lines.push(`        $${argName} = (int) $args[${i}];`);
                        break;
                    case 'float':
                        lines.push(`        $${argName} = (float) $args[${i}];`);
                        break;
                    case 'bool':
                        lines.push(`        $${argName} = filter_var($args[${i}], FILTER_VALIDATE_BOOLEAN);`);
                        break;
                    case 'Player':
                        lines.push(`        $${argName} = $sender->getServer()->getPlayerByPrefix($args[${i}]);`);
                        lines.push(`        if ($${argName} === null) {`);
                        lines.push(`            $sender->sendMessage("Player not found: " . $args[${i}]);`);
                        lines.push('            return false;');
                        lines.push('        }');
                        break;
                    default:
                        lines.push(`        $${argName} = $args[${i}];`);
                }
            });
            lines.push('');
        }

        lines.push('        // TODO: implement command logic');
        lines.push('        $sender->sendMessage("Command executed!");');
        lines.push('        return true;');
        lines.push('    }');
        lines.push('}');

        return lines.join('\n');
    }

function render() {
        const code = generate();
        const out = document.getElementById('code-output');
        if (out && window.PMEditor) {
            out.innerHTML = window.PMEditor.highlight(code, 'php');
        } else if (out) {
            out.textContent = code;
        }
    }

    function renderArgs() {
        const list = document.getElementById('cmd-args-list');
        if (!list) return;
        list.innerHTML = '';

        state.args.forEach((arg, i) => {
            const item = document.createElement('div');
            item.className = 'dynamic-item';
            item.innerHTML = `
                <div class="dynamic-item-head">
                    <span class="dynamic-item-title">Arg #${i + 1}</span>
                    <button class="dynamic-item-remove" data-remove-arg="${i}" data-sound="click">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
                <div class="dynamic-item-row">
                    <input type="text" class="input" placeholder="${t('command_creator.arg_name')}" value="${arg.name}" data-arg-name="${i}">
                    <select class="input" data-arg-type="${i}">
                        <option value="string"  ${arg.type === 'string'  ? 'selected' : ''}>string</option>
                        <option value="int"     ${arg.type === 'int'     ? 'selected' : ''}>int</option>
                        <option value="float"   ${arg.type === 'float'   ? 'selected' : ''}>float</option>
                        <option value="bool"    ${arg.type === 'bool'    ? 'selected' : ''}>bool</option>
                        <option value="Player"  ${arg.type === 'Player'  ? 'selected' : ''}>Player</option>
                    </select>
                </div>
            `;
            list.appendChild(item);
        });

        list.querySelectorAll('[data-arg-name]').forEach((inp) => {
            inp.addEventListener('input', debounce((e) => {
                state.args[+e.target.dataset.argName].name = e.target.value;
                render();
            }));
        });
        list.querySelectorAll('[data-arg-type]').forEach((sel) => {
            sel.addEventListener('change', (e) => {
                state.args[+e.target.dataset.argType].type = e.target.value;
                render();
            });
        });
        list.querySelectorAll('[data-remove-arg]').forEach((b) => {
            b.addEventListener('click', () => {
                state.args.splice(+b.dataset.removeArg, 1);
                renderArgs();
                render();
            });
        });
    }

    function bind() {
        const map = {
            'cmd-name': 'name',
            'cmd-desc': 'description',
            'cmd-perm': 'permission',
            'cmd-aliases': 'aliases'
        };

        for (const [id, key] of Object.entries(map)) {
            const el = document.getElementById(id);
            if (!el) continue;
            el.addEventListener('input', debounce((e) => {
                state[key] = e.target.value;
                render();
            }));
        }

        const addArg = document.getElementById('cmd-add-arg');
        if (addArg) addArg.addEventListener('click', () => {
            state.args.push({ name: 'arg', type: 'string' });
            renderArgs();
            render();
        });
    }

    function init() {
        bind();
        renderArgs();
        render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();