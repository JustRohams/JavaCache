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
        type: 'simple',            // simple | modal | custom
        title: 'My Form',
        content: 'Welcome!',
        buttons: [{ text: 'Yes', icon: '' }, { text: 'No', icon: '' }],
        button1: 'Yes',
        button2: 'No',
        fields: []                
    };

    function render() {
        const code = generateCode();
        const out = document.getElementById('code-output');
        if (out && window.PMEditor) {
            out.innerHTML = window.PMEditor.highlight(code, 'php');
        } else if (out) {
            out.textContent = code;
        }
    }

    function generateCode() {
        if (state.type === 'simple')  return generateSimple();
        if (state.type === 'modal')   return generateModal();
        return generateCustom();
    }

    function generateSimple() {
        let lines = [];
        lines.push('use jojoe77777\\FormAPI\\SimpleForm;');
        lines.push('use pocketmine\\player\\Player;');
        lines.push('');
        lines.push('$form = new SimpleForm(function (Player $player, $data) {');
        lines.push('    if ($data === null) return;');
        lines.push('    switch ($data) {');

        state.buttons.forEach((btn, i) => {
            lines.push(`        case ${i}: // ${btn.text}`);
            lines.push('            break;');
        });

        lines.push('    }');
        lines.push('});');
        lines.push(`$form->setTitle("${escape(state.title)}");`);
        lines.push(`$form->setContent("${escape(state.content)}");`);

        state.buttons.forEach((btn) => {
            // FormAPI: addButton(string $text, int $imageType = -1, string $imagePath = "")
            if (btn.icon) {
                lines.push(`$form->addButton("${escape(btn.text)}", 0, "${escape(btn.icon)}");`);
            } else {
                lines.push(`$form->addButton("${escape(btn.text)}");`);
            }
        });

        lines.push('$player->sendForm($form);');

        return lines.join('\n');
    }

    function generateModal() {
        return [
            'use jojoe77777\\FormAPI\\ModalForm;',
            'use pocketmine\\player\\Player;',
            '',
            '$form = new ModalForm(function (Player $player, $data) {',
            '    if ($data === null) return;',
            '    if ($data === true) {',
            `        // ${state.button1}`,
            '    } else {',
            `        // ${state.button2}`,
            '    }',
            '});',
            `$form->setTitle("${escape(state.title)}");`,
            `$form->setContent("${escape(state.content)}");`,
            `$form->setButton1("${escape(state.button1)}");`,
            `$form->setButton2("${escape(state.button2)}");`,
            '$player->sendForm($form);'
        ].join('\n');
    }

    function generateCustom() {
        let lines = [];
        lines.push('use jojoe77777\\FormAPI\\CustomForm;');
        lines.push('use pocketmine\\player\\Player;');
        lines.push('');
        lines.push('$form = new CustomForm(function (Player $player, $data) {');
        lines.push('    if ($data === null) return;');

        state.fields.forEach((field, i) => {
            lines.push(`    // $data[${i}] -> ${field.label || field.type}`);
        });

        lines.push('});');
        lines.push(`$form->setTitle("${escape(state.title)}");`);

        state.fields.forEach((field) => {
            const label = escape(field.label || '');
            const def = escape(field.default || '');

            switch (field.type) {
                case 'label':
                    lines.push(`$form->addLabel("${label}");`);
                    break;
                case 'input':
                    lines.push(`$form->addInput("${label}", "", "${def}");`);
                    break;
                case 'toggle':
                    lines.push(`$form->addToggle("${label}", ${field.default === 'true'});`);
                    break;
                case 'dropdown':
                    const opts = (field.options || '').split(',').map(o => `"${escape(o.trim())}"`).join(', ');
                    lines.push(`$form->addDropdown("${label}", [${opts}]);`);
                    break;
                case 'slider':
                    lines.push(`$form->addSlider("${label}", ${field.min || 0}, ${field.max || 100}, ${field.step || 1}, ${field.default || 0});`);
                    break;
            }
        });

        lines.push('$player->sendForm($form);');
        return lines.join('\n');
    }

    // Escape text for a double-quoted PHP string
    function escape(str) {
        return String(str)
            .replace(/\\/g, '\\\\')
            .replace(/"/g, '\\"')
            .replace(/\$/g, '\\$');
    }

    // Escape text for an HTML attribute value
    function attr(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
            .replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function bindTabs() {
        const tabs = document.querySelectorAll('[data-form-type]');
        const select = (tab) => {
            tabs.forEach((el) => {
                const on = el === tab;
                el.classList.toggle('active', on);
                el.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            state.type = tab.dataset.formType;
            updateVisibility();
            render();
        };

        tabs.forEach((tab) => {
            tab.setAttribute('type', 'button');
            tab.addEventListener('click', () => select(tab));
        });
    }

    function updateVisibility() {
        const bg = document.getElementById('fc-buttons-group');
        const mg = document.getElementById('fc-modal-group');
        const fg = document.getElementById('fc-fields-group');

        if (bg) bg.hidden = state.type !== 'simple';
        if (mg) mg.hidden = state.type !== 'modal';
        if (fg) fg.hidden = state.type !== 'custom';
    }

    function bindSharedInputs() {
        const title = document.getElementById('fc-title');
        const content = document.getElementById('fc-content');
        const btn1 = document.getElementById('fc-btn1');
        const btn2 = document.getElementById('fc-btn2');

        if (title) title.addEventListener('input', debounce((e) => {
            state.title = e.target.value;
            render();
        }));
        if (content) content.addEventListener('input', debounce((e) => {
            state.content = e.target.value;
            render();
        }));
        if (btn1) btn1.addEventListener('input', debounce((e) => {
            state.button1 = e.target.value;
            render();
        }));
        if (btn2) btn2.addEventListener('input', debounce((e) => {
            state.button2 = e.target.value;
            render();
        }));
    }

    function bindButtons() {
        const addBtn = document.getElementById('fc-add-button');
        const addField = document.getElementById('fc-add-field');

        if (addBtn) {
            addBtn.addEventListener('click', () => {
                state.buttons.push({ text: 'Button', icon: '' });
                renderButtons();
                render();
            });
        }

        if (addField) {
            addField.addEventListener('click', () => {
                state.fields.push({ type: 'input', label: 'Field', default: '' });
                renderFields();
                render();
            });
        }
    }

    function renderButtons() {
        const list = document.getElementById('fc-buttons-list');
        if (!list) return;
        list.innerHTML = '';

        state.buttons.forEach((btn, i) => {
            const item = document.createElement('div');
            item.className = 'dynamic-item';
            item.innerHTML = `
                <div class="dynamic-item-head">
                    <span class="dynamic-item-title">${t('form_creator.buttons')} #${i + 1}</span>
                    <button class="dynamic-item-remove" data-remove="${i}" data-sound="click">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
                <div class="dynamic-item-row">
                    <input type="text" class="input" placeholder="${t('form_creator.button_text')}" value="${attr(btn.text)}" data-btn-text="${i}">
                    <input type="text" class="input" placeholder="${t('form_creator.button_icon')}" value="${attr(btn.icon)}" data-btn-icon="${i}">
                </div>
            `;
            list.appendChild(item);
        });

        list.querySelectorAll('[data-btn-text]').forEach((inp) => {
            inp.addEventListener('input', debounce((e) => {
                state.buttons[+e.target.dataset.btnText].text = e.target.value;
                render();
            }));
        });
        list.querySelectorAll('[data-btn-icon]').forEach((inp) => {
            inp.addEventListener('input', debounce((e) => {
                state.buttons[+e.target.dataset.btnIcon].icon = e.target.value;
                render();
            }));
        });
        list.querySelectorAll('[data-remove]').forEach((b) => {
            b.addEventListener('click', () => {
                state.buttons.splice(+b.dataset.remove, 1);
                renderButtons();
                render();
            });
        });
    }

    function renderFields() {
        const list = document.getElementById('fc-fields-list');
        if (!list) return;
        list.innerHTML = '';

        const types = ['label', 'input', 'toggle', 'dropdown', 'slider'];

        state.fields.forEach((field, i) => {
            const item = document.createElement('div');
            item.className = 'dynamic-item';

            const typeOpts = types.map((type) =>
                `<option value="${type}" ${field.type === type ? 'selected' : ''}>${t('form_creator.types.' + type)}</option>`
            ).join('');

            item.innerHTML = `
                <div class="dynamic-item-head">
                    <span class="dynamic-item-title">${t('form_creator.fields')} #${i + 1}</span>
                    <button class="dynamic-item-remove" data-remove-field="${i}" data-sound="click">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
                <div class="dynamic-item-row">
                    <select class="input" data-field-type="${i}">${typeOpts}</select>
                    <input type="text" class="input" placeholder="${t('form_creator.field_label')}" value="${attr(field.label)}" data-field-label="${i}">
                </div>
                <div class="dynamic-item-row single">
                    <input type="text" class="input" placeholder="${t('form_creator.field_default')}" value="${attr(field.default)}" data-field-default="${i}">
                </div>
            `;
            list.appendChild(item);
        });

        list.querySelectorAll('[data-field-type]').forEach((sel) => {
            sel.addEventListener('change', (e) => {
                state.fields[+e.target.dataset.fieldType].type = e.target.value;
                render();
            });
        });
        list.querySelectorAll('[data-field-label]').forEach((inp) => {
            inp.addEventListener('input', debounce((e) => {
                state.fields[+e.target.dataset.fieldLabel].label = e.target.value;
                render();
            }));
        });
        list.querySelectorAll('[data-field-default]').forEach((inp) => {
            inp.addEventListener('input', debounce((e) => {
                state.fields[+e.target.dataset.fieldDefault].default = e.target.value;
                render();
            }));
        });
        list.querySelectorAll('[data-remove-field]').forEach((b) => {
            b.addEventListener('click', () => {
                state.fields.splice(+b.dataset.removeField, 1);
                renderFields();
                render();
            });
        });
    }

    function init() {
        bindTabs();
        bindSharedInputs();
        bindButtons();
        renderButtons();
        renderFields();
        updateVisibility();
        render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();