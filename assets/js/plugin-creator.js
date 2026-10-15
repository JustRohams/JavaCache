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
        name: 'MyPlugin',
        author: 'RohaM',
        namespace: 'myplugin',
        features: {
            command: true,
            listener: true,
            config: true,
            manager: true,
            sqlite: true,
            lang: true,
            task: false,
            form: false
        }
    };

    function values() {
        const name   = (state.name || '').replace(/[^A-Za-z0-9_.\-]/g, '') || 'MyPlugin';
        const author = (state.author || '').replace(/[^A-Za-z0-9_]/g, '') || 'RohaM';
        const ns     = (state.namespace || '').replace(/[^A-Za-z0-9_]/g, '') || 'myplugin';
        return { name, author, ns };
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function srcDirList() {
        const dirs = [];
        if (state.features.command)  dirs.push({ dir: 'command',  file: 'ExampleCommand.php' });
        if (state.features.listener) dirs.push({ dir: 'listener', file: 'ExampleListener.php' });
        if (state.features.manager)  dirs.push({ dir: 'manager',  file: 'DataManager.php' });
        if (state.features.task)     dirs.push({ dir: 'task',     file: 'ExampleTask.php' });
        if (state.features.form)     dirs.push({ dir: 'form',     file: null });
        return dirs;
    }

    function resourceList() {
        const items = [];
        if (state.features.config) items.push({ name: 'config.yml', isDir: false });
        if (state.features.sqlite) items.push({ name: 'data.sql',   isDir: false });
        if (state.features.lang)   items.push({ name: 'lang',       isDir: true  });
        return items;
    }

    function generateTree() {
        const { name, author, ns } = values();
        const srcDirs  = srcDirList();
        const resItems = resourceList();

        const lines = [];
        lines.push(`${name}/`);
        lines.push('├── plugin.yml');
        lines.push('├── src/');
        lines.push(`│   └── ${author}/`);
        lines.push(`│       └── ${ns}/`);

        if (srcDirs.length === 0) {
            lines.push('│           └── Main.php');
        } else {
            lines.push('│           ├── Main.php');
            srcDirs.forEach((item, i) => {
                const isLast = i === srcDirs.length - 1;
                const branch = isLast ? '└──' : '├──';
                const pipe   = isLast ? '    ' : '│   ';
                lines.push(`│           ${branch} ${item.dir}/`);
                if (item.file) {
                    lines.push(`│           ${pipe}└── ${item.file}`);
                }
            });
        }

        lines.push('└── resources/');
        resItems.forEach((item, i) => {
            const isLast = i === resItems.length - 1;
            const branch = isLast ? '└──' : '├──';
            if (item.isDir) {
                lines.push(`    ${branch} ${item.name}/`);
                const pipe = isLast ? '    ' : '│   ';
                lines.push(`    ${pipe}├── en.yml`);
                lines.push(`    ${pipe}└── fa.yml`);
            } else {
                lines.push(`    ${branch} ${item.name}`);
            }
        });

        return lines.join('\n');
    }

    function highlightTree(text) {
        return text.split('\n').map((line) => {
            const m = line.match(/^([│├└─ ]*)(.*)$/);
            const prefix = m ? m[1] : '';
            const label  = m ? m[2] : line;
            const cls = label.endsWith('/') ? 'dir' : 'file';
            return escapeHtml(prefix) + '<span class="' + cls + '">' + escapeHtml(label) + '</span>';
        }).join('\n');
    }

    function render() {
        const out = document.getElementById('code-output');
        if (out) out.innerHTML = highlightTree(generateTree());
    }

    function buildProject() {
        const { name, author, ns } = values();
        const root = name;
        const base = `${author}\\${ns}`;
        const srcDirs  = srcDirList();
        const resItems = resourceList();

        const dirs  = [];
        const files = [];

        const addDir  = (path) => dirs.push(path + '/');
        const addFile = (path, content) => files.push({ path, content });

        addDir(root);
        addDir(`${root}/src`);
        addDir(`${root}/src/${author}`);
        addDir(`${root}/src/${author}/${ns}`);
        addDir(`${root}/resources`);

        addFile(`${root}/plugin.yml`, [
            `name: ${name}`,
            `main: ${base}\\Main`,
            'version: 1.0.0',
            'api: 5.0.0',
            `author: ${author}`,
            ''
        ].join('\n'));

        const uses = ['use pocketmine\\plugin\\PluginBase;'];
        const enable = [];
        if (state.features.config) enable.push('        $this->saveDefaultConfig();');
        if (state.features.command) {
            uses.push(`use ${base}\\command\\ExampleCommand;`);
            enable.push(`        $this->getServer()->getCommandMap()->register("${name}", new ExampleCommand());`);
        }
        if (state.features.listener) {
            uses.push(`use ${base}\\listener\\ExampleListener;`);
            enable.push('        $this->getServer()->getPluginManager()->registerEvents(new ExampleListener(), $this);');
        }
        enable.push(`        $this->getLogger()->info("${name} enabled!");`);

        addFile(`${root}/src/${author}/${ns}/Main.php`, [
            '<?php',
            '',
            'declare(strict_types=1);',
            '',
            `namespace ${base};`,
            '',
            ...uses.sort(),
            '',
            'class Main extends PluginBase {',
            '',
            '    protected function onEnable(): void {',
            ...enable,
            '    }',
            '}',
            ''
        ].join('\n'));

        srcDirs.forEach((item) => {
            const dirPath = `${root}/src/${author}/${ns}/${item.dir}`;
            addDir(dirPath);
            if (!item.file) return;

            let body = '';
            switch (item.dir) {
                case 'command':
                    body = [
                        `namespace ${base}\\command;`,
                        '',
                        'use pocketmine\\command\\Command;',
                        'use pocketmine\\command\\CommandSender;',
                        '',
                        'class ExampleCommand extends Command {',
                        '',
                        '    public function __construct() {',
                        '        parent::__construct("example", "Example command", "/example");',
                        '    }',
                        '',
                        '    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {',
                        '        $sender->sendMessage("Hello!");',
                        '        return true;',
                        '    }',
                        '}'
                    ].join('\n');
                    break;
                case 'listener':
                    body = [
                        `namespace ${base}\\listener;`,
                        '',
                        'use pocketmine\\event\\Listener;',
                        'use pocketmine\\event\\player\\PlayerJoinEvent;',
                        '',
                        'class ExampleListener implements Listener {',
                        '',
                        '    public function onJoin(PlayerJoinEvent $event): void {',
                        '        $event->getPlayer()->sendMessage("Welcome!");',
                        '    }',
                        '}'
                    ].join('\n');
                    break;
                case 'manager':
                    body = [
                        `namespace ${base}\\manager;`,
                        '',
                        'class DataManager {',
                        '',
                        '    private array $data = [];',
                        '',
                        '    public function get(string $key, mixed $default = null): mixed {',
                        '        return $this->data[$key] ?? $default;',
                        '    }',
                        '',
                        '    public function set(string $key, mixed $value): void {',
                        '        $this->data[$key] = $value;',
                        '    }',
                        '}'
                    ].join('\n');
                    break;
                case 'task':
                    body = [
                        `namespace ${base}\\task;`,
                        '',
                        'use pocketmine\\scheduler\\Task;',
                        '',
                        'class ExampleTask extends Task {',
                        '',
                        '    public function onRun(): void {',
                        '        // TODO: task logic',
                        '    }',
                        '}'
                    ].join('\n');
                    break;
            }

            addFile(`${dirPath}/${item.file}`, [
                '<?php',
                '',
                'declare(strict_types=1);',
                '',
                body,
                ''
            ].join('\n'));
        });

        resItems.forEach((item) => {
            if (item.name === 'config.yml') {
                addFile(`${root}/resources/config.yml`, [
                    `# ${name} configuration`,
                    `prefix: "[${name}]"`,
                    ''
                ].join('\n'));
            } else if (item.name === 'data.sql') {
                addFile(`${root}/resources/data.sql`, [
                    '-- SQLite schema',
                    'CREATE TABLE IF NOT EXISTS players (',
                    '    uuid TEXT PRIMARY KEY,',
                    '    name TEXT NOT NULL',
                    ');',
                    ''
                ].join('\n'));
            } else if (item.name === 'lang') {
                addDir(`${root}/resources/lang`);
                addFile(`${root}/resources/lang/en.yml`, 'welcome: "Welcome!"\n');
                addFile(`${root}/resources/lang/fa.yml`, 'welcome: "خوش آمدید!"\n');
            }
        });

        return { root, dirs, files };
    }

    const CRC_TABLE = (() => {
        const table = new Uint32Array(256);
        for (let n = 0; n < 256; n++) {
            let c = n;
            for (let k = 0; k < 8; k++) {
                c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
            }
            table[n] = c >>> 0;
        }
        return table;
    })();

    function crc32(bytes) {
        let c = 0xFFFFFFFF;
        for (let i = 0; i < bytes.length; i++) {
            c = CRC_TABLE[(c ^ bytes[i]) & 0xFF] ^ (c >>> 8);
        }
        return (c ^ 0xFFFFFFFF) >>> 0;
    }

    function buildZip(dirs, files) {
        const enc = new TextEncoder();
        const now = new Date();
        const dosTime = (now.getHours() << 11) | (now.getMinutes() << 5) | (now.getSeconds() >> 1);
        const dosDate = ((now.getFullYear() - 1980) << 9) | ((now.getMonth() + 1) << 5) | now.getDate();

        const entries = [
            ...dirs.map((path) => ({ path, data: new Uint8Array(0), dir: true })),
            ...files.map((f) => ({ path: f.path, data: enc.encode(f.content), dir: false }))
        ];

        const chunks  = [];
        const central = [];
        let offset = 0;

        entries.forEach((e) => {
            const nameBytes = enc.encode(e.path);
            const crc = e.dir ? 0 : crc32(e.data);
            const size = e.data.length;

            const local = new Uint8Array(30 + nameBytes.length);
            const lv = new DataView(local.buffer);
            lv.setUint32(0, 0x04034b50, true);
            lv.setUint16(4, 20, true);
            lv.setUint16(6, 0x0800, true);          
            lv.setUint16(8, 0, true);               
            lv.setUint16(10, dosTime, true);
            lv.setUint16(12, dosDate, true);
            lv.setUint32(14, crc, true);
            lv.setUint32(18, size, true);
            lv.setUint32(22, size, true);
            lv.setUint16(26, nameBytes.length, true);
            lv.setUint16(28, 0, true);
            local.set(nameBytes, 30);

            const cen = new Uint8Array(46 + nameBytes.length);
            const cv = new DataView(cen.buffer);
            cv.setUint32(0, 0x02014b50, true);
            cv.setUint16(4, (3 << 8) | 20, true);   
            cv.setUint16(6, 20, true);
            cv.setUint16(8, 0x0800, true);
            cv.setUint16(10, 0, true);
            cv.setUint16(12, dosTime, true);
            cv.setUint16(14, dosDate, true);
            cv.setUint32(16, crc, true);
            cv.setUint32(20, size, true);
            cv.setUint32(24, size, true);
            cv.setUint16(28, nameBytes.length, true);
            cv.setUint16(30, 0, true);
            cv.setUint16(32, 0, true);
            cv.setUint16(34, 0, true);
            cv.setUint16(36, 0, true);
            cv.setUint32(38, e.dir ? ((0o40755 << 16) | 0x10) >>> 0 : (0o100644 << 16) >>> 0, true);
            cv.setUint32(42, offset, true);
            cen.set(nameBytes, 46);

            chunks.push(local, e.data);
            central.push(cen);
            offset += local.length + size;
        });

        const cdSize = central.reduce((sum, c) => sum + c.length, 0);
        const eocd = new Uint8Array(22);
        const ev = new DataView(eocd.buffer);
        ev.setUint32(0, 0x06054b50, true);
        ev.setUint16(4, 0, true);
        ev.setUint16(6, 0, true);
        ev.setUint16(8, entries.length, true);
        ev.setUint16(10, entries.length, true);
        ev.setUint32(12, cdSize, true);
        ev.setUint32(16, offset, true);
        ev.setUint16(20, 0, true);

        return new Blob([...chunks, ...central, eocd], { type: 'application/zip' });
    }

    function downloadBlob(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    }

    function downloadProject() {
        try {
            const project = buildProject();
            const blob = buildZip(project.dirs, project.files);
            downloadBlob(blob, project.root + '.zip');
            if (window.PMToolsSounds) window.PMToolsSounds.play('success');
        } catch (err) {
            console.error('ZIP build failed:', err);
            if (window.PMToolsSounds) window.PMToolsSounds.play('error');
        }
    }

    function bind() {
        const name   = document.getElementById('pc-name');
        const author = document.getElementById('pc-author');
        const ns     = document.getElementById('pc-namespace');

        if (name)   name.addEventListener('input',   debounce((e) => { state.name = e.target.value; render(); }));
        if (author) author.addEventListener('input', debounce((e) => { state.author = e.target.value; render(); }));
        if (ns)     ns.addEventListener('input',     debounce((e) => { state.namespace = e.target.value; render(); }));

        document.querySelectorAll('[data-pc-feature]').forEach((cb) => {
            if (cb.value in state.features) state.features[cb.value] = cb.checked;
            cb.addEventListener('change', () => {
                state.features[cb.value] = cb.checked;
                render();
            });
        });

        const dl = document.getElementById('pc-download');
        if (dl) dl.addEventListener('click', (e) => {
            e.preventDefault();
            downloadProject();
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
