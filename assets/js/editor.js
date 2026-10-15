(function () {
    'use strict';

    const Editor = {

        init() {
            this.bindCopy();
            this.bindDownload();
        },

        bindCopy() {
            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('[data-copy-target]');
                if (!btn) return;

                e.preventDefault();
                const selector = btn.dataset.copyTarget;
                const target = document.querySelector(selector);
                if (!target) return;

                const text = target.textContent || '';
                const ok = await this.copy(text);

                if (ok) {
                    this.flash(btn, 'check', 'success');
                    if (window.PMToolsSounds) window.PMToolsSounds.play('success');
                } else {
                    this.flash(btn, 'error', 'danger');
                    if (window.PMToolsSounds) window.PMToolsSounds.play('error');
                }
            });
        },

        bindDownload() {
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-download-target]');
                if (!btn) return;

                e.preventDefault();
                const selector = btn.dataset.downloadTarget;
                const filename = btn.dataset.filename || 'output.txt';
                const target = document.querySelector(selector);
                if (!target) return;

                const text = target.textContent || '';
                this.download(text, filename);
                if (window.PMToolsSounds) window.PMToolsSounds.play('success');
            });
        },

        async copy(text) {
            try {
                if (navigator.clipboard && (location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1')) {
                    await navigator.clipboard.writeText(text);
                    return true;
                }
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                const ok = document.execCommand('copy');
                document.body.removeChild(ta);
                return ok;
            } catch (err) {
                return false;
            }
        },

        download(text, filename) {
            const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        },

        flash(btn, icon, cls = 'success') {
            const originalIcon = btn.querySelector('.material-symbols-rounded');
            if (!originalIcon) return;

            const originalText = originalIcon.textContent;
            originalIcon.textContent = icon;
            btn.classList.add(cls);

            setTimeout(() => {
                originalIcon.textContent = originalText;
                btn.classList.remove(cls);
            }, 1400);
        },

        debounce(fn, delay = 250) {
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => fn.apply(this, args), delay);
            };
        },

        highlight(code, lang = 'php') {
            if (!code) return '';

            // Single-pass tokenizer: the text is scanned once and every piece is
            // escaped exactly once, so rules can never match inside generated markup.
            const RULES = {
                php: [
                    [/\/\*[\s\S]*?\*\/|\/\/[^\n]*|#[^\n]*/g, 'cmt'],
                    [/'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*"/g, 'str'],
                    [/\b(?:use|namespace|class|function|return|new|public|private|protected|static|if|else|elseif|foreach|for|while|switch|case|break|continue|try|catch|throw|extends|implements|interface|abstract|final|const|var|echo|print|null|true|false|array|as|instanceof|match|default)\b/g, 'kw'],
                    [/\$[A-Za-z_][A-Za-z0-9_]*/g, 'var'],
                    [/\b\d+(?:\.\d+)?\b/g, 'num'],
                    [/\b[A-Z][A-Za-z0-9_]*\b/g, 'cls'],
                    [/\b[a-zA-Z_][a-zA-Z0-9_]*(?=\s*\()/g, 'fn']
                ],
                js: [
                    [/\/\*[\s\S]*?\*\/|\/\/[^\n]*/g, 'cmt'],
                    [/'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*"|`(?:[^`\\]|\\.)*`/g, 'str'],
                    [/\b(?:const|let|var|function|return|new|class|extends|if|else|for|while|switch|case|break|continue|try|catch|throw|async|await|import|export|default|from|of|in|typeof|instanceof|this|super|null|undefined|true|false)\b/g, 'kw'],
                    [/\b\d+(?:\.\d+)?\b/g, 'num'],
                    [/\b[A-Z][A-Za-z0-9_]*\b/g, 'cls'],
                    [/\b[a-zA-Z_][a-zA-Z0-9_]*(?=\s*\()/g, 'fn']
                ],
                json: [
                    [/"(?:[^"\\]|\\.)*"(?=\s*:)/g, 'key'],
                    [/"(?:[^"\\]|\\.)*"/g, 'str'],
                    [/-?\b\d+(?:\.\d+)?\b/g, 'num'],
                    [/\b(?:true|false|null)\b/g, 'bool']
                ],
                yaml: [
                    [/#[^\n]*/g, 'cmt'],
                    [/^[ \t]*(?:- )?[A-Za-z_][A-Za-z0-9_\-]*(?=:)/gm, 'key'],
                    [/'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*"/g, 'str'],
                    [/\b\d+(?:\.\d+)?\b/g, 'num']
                ]
            };

            const rules = (RULES[lang] || RULES.php).map(([re, cls]) => ({
                re: new RegExp(re.source, re.flags), cls, next: null
            }));

            let out = '';
            let pos = 0;

            while (pos < code.length) {
                let best = null;

                for (const rule of rules) {
                    if (!rule.next || rule.next.index < pos) {
                        rule.re.lastIndex = pos;
                        rule.next = rule.re.exec(code);
                        if (!rule.next) rule.next = { index: Infinity };
                    }
                    if (rule.next.index === Infinity || !rule.next[0]) continue;
                    if (!best || rule.next.index < best.next.index) best = rule;
                }

                if (!best) break;

                const m = best.next;
                out += this.escape(code.slice(pos, m.index));
                out += '<span class="' + best.cls + '">' + this.escape(m[0]) + '</span>';
                pos = m.index + m[0].length;
            }

            return out + this.escape(code.slice(pos));
        },

        escape(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }
    };

    window.PMEditor = Editor;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Editor.init());
    } else {
        Editor.init();
    }
})();