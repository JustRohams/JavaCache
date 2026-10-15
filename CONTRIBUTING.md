# Contributing to PocketMine Tools

Thanks for your interest in improving PocketMine Tools! This guide will help you get started.

---

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Ways to Contribute](#ways-to-contribute)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
  - [PHP](#php)
  - [JavaScript](#javascript)
  - [CSS](#css)
  - [HTML](#html)
  - [Markdown](#markdown)
- [Commit Messages](#commit-messages)
- [Pull Requests](#pull-requests)
- [Reporting Bugs](#reporting-bugs)
- [Suggesting Features](#suggesting-features)
- [Translations](#translations)
- [Adding a New Tool](#adding-a-new-tool)
- [Testing Checklist](#testing-checklist)
- [Recognition](#recognition)
- [Questions](#questions)

---

## Code of Conduct

### Our Pledge

We are committed to providing a **welcoming, friendly, and harassment-free** environment for everyone, regardless of:

- Age, body size, disability, ethnicity
- Gender identity or expression
- Level of experience
- Nationality, personal appearance
- Race, religion, or sexual identity

### Expected Behavior

- Be **kind** and **patient**
- Welcome **beginners** and their questions
- Give **constructive** feedback
- Focus on **what's best** for the community

### Unacceptable Behavior

- Trolling, insults, or personal attacks
- Public or private harassment
- Publishing others' private information
- Any conduct that would be inappropriate in a professional setting

### Enforcement

Violations may result in a warning, temporary ban, or permanent ban at the maintainer's discretion. Report issues by contacting the maintainer directly.

---

## Ways to Contribute

You don't have to write code to contribute:

| Type | How |
|------|-----|
| 🐛 **Report bugs** | Open an issue with steps to reproduce |
| 💡 **Suggest features** | Open an issue describing the use case |
| 📝 **Improve docs** | Fix typos, clarify steps, add examples |
| 🌍 **Translate** | Add a new language or improve existing ones |
| 🎨 **Design** | Improve UI, icons, colors |
| 🧪 **Test** | Try the tools, report what breaks |
| 💻 **Code** | Fix bugs, add features, refactor |
| ⭐ **Star** | Show support on GitHub |

---

## Getting Started

### 1. Fork the Repository

Click **Fork** on GitHub.

### 2. Clone Your Fork

```bash
git clone https://github.com/YOUR-USERNAME/PocketMineTools.git
cd PocketMineTools
```

3. Add the Upstream Remote

```bash
git remote add upstream https://github.com/JustRohams/PocketMineTools.git
```

4. Start a Local Server

```bash
php -S localhost:8000
```

Open http://localhost:8000.

5. Verify PHP Version

```bash
php -v
# Must be 8.0+
```

---

Development Workflow

1. Sync with Upstream

Before starting new work:

```bash
git checkout main
git pull upstream main
```

2. Create a Feature Branch

```bash
git checkout -b feature/my-feature
```

Naming conventions:

Prefix Use for
feature/ New feature
fix/ Bug fix
docs/ Documentation
refactor/ Code cleanup
style/ Formatting, no logic change
test/ Tests

3. Make Your Changes

· Keep changes small and focused
· One feature or fix per branch
· Test locally before committing

4. Commit Often

```bash
git add .
git commit -m "feat: add XYZ feature"
```

See Commit Messages for format.

5. Push and Open a PR

```bash
git push origin feature/my-feature
```

Then open a Pull Request on GitHub.

---

Coding Standards

PHP

· PHP 8.0+ syntax
· PSR-12 style
· 4 spaces for indentation (no tabs)
· declare(strict_types=1); at the top of every file
· Type hints on all function parameters and returns
· Docblocks on classes and public methods
· No short tags — always <?php ?>, never <? ?>

Example:

```php
<?php
declare(strict_types=1);

/**
 * Generate a random UUID v4.
 */
function random_uuid(): string
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
```

Rules:

· ✅ Use e() or __e() for all output
· ✅ Use json_response() for API responses
· ✅ Use partial() for reusable page parts
· ❌ Never echo raw user input
· ❌ Never use eval() or shell functions

JavaScript

· ES6+ syntax
· 4 spaces for indentation
· IIFE wrapper for every file
· 'use strict'; at the top of every IIFE
· Semicolons required
· Single quotes for strings
· const / let only — no var
· Event delegation over individual listeners

Example:

```javascript
(function () {
    'use strict';

    const state = { count: 0 };

    function increment() {
        state.count++;
    }

    document.addEventListener('click', (e) => {
        if (e.target.matches('[data-increment]')) {
            increment();
        }
    });
})();
```

Rules:

· ✅ Attach state to a namespace (window.PMTools...)
· ✅ Use debounce() for input handlers
· ✅ Use textContent over innerHTML for user data
· ❌ Never use jQuery or any external library
· ❌ Never inline event handlers (onclick="...")

CSS

· 4 spaces for indentation
· kebab-case class names
· CSS variables for all colors, spacing, fonts
· Mobile-first approach
· No !important unless absolutely necessary

Example:

```css
.tool-card {
    display: flex;
    gap: var(--space-4);
    padding: var(--space-5);
    background: var(--bg-1);
    border-radius: var(--radius-lg);
}
```

Rules:

· ✅ Use tokens from variables.css
· ✅ Group related rules
· ✅ Keep specificity low
· ❌ Never hardcode colors
· ❌ Never use IDs for styling

HTML

· Semantic elements (<header>, <nav>, <main>, <article>)
· Two spaces for indentation in PHP templates
· aria-label on icon-only buttons
· data-* attributes for JS hooks
· data-i18n for translatable text

Example:

```html
<button class="icon-btn" data-sound="click" aria-label="<?= __e('common.copy') ?>">
    <span class="material-symbols-rounded">content_copy</span>
</button>
```

Rules:

· ✅ Escape everything with e() or __e()
· ✅ Use <button> for actions, <a> for links
· ✅ Add alt on every image
· ❌ Never use <div> for buttons
· ❌ Never inline <style> or <script>

Markdown

· 2 spaces for indentation
· One sentence per line (optional but preferred)
· ATX headings (#, ##, ###)
· Fenced code blocks with language tags
· Tables for structured data

Example:

```markdown
## Section Title

Use `e()` to escape output.

```php
echo e($userInput);
```
```

Rules:

· ✅ Wrap long lines at ~100 chars
· ✅ Add blank lines around code blocks
· ✅ Use present tense ("Add feature", not "Added")
· ❌ Never mix tabs and spaces

---

Commit Messages

Follow Conventional Commits:

```
<type>(<scope>): <subject>

<body>

<footer>
```

Types

Type Meaning
feat New feature
fix Bug fix
docs Documentation only
style Formatting (no logic change)
refactor Code restructure
perf Performance improvement
test Adding or updating tests
chore Build, deps, config
i18n Translations
a11y Accessibility

Scope (optional)

Use the tool or folder name:

· form-creator
· uuid-generator
· dependencies
· nav
· api
· i18n

Examples

```
feat(uuid-generator): add reverse mode

Allows recovering the original key from a UUID
by consulting the server-side mapping file.

Closes #42
```

```
fix(form-creator): update code on every keystroke

Previously the code only updated on blur.
```

```
docs(readme): clarify Nginx setup
```

Rules

· ✅ Imperative mood: "add" not "added"
· ✅ Lowercase subject (except proper nouns)
· ✅ No period at the end of the subject
· ✅ Max 72 characters for the subject
· ✅ Blank line between subject and body
· ❌ Never commit secrets, API keys, or passwords

---

Pull Requests

Before Opening

☐ Your branch is up to date with main
☐ Code follows the style guide
☐ All existing tests pass (manual, if applicable)
☐ You've added or updated documentation
☐ You've added translations for both EN and FA
☐ No debug console.log or var_dump left behind

PR Title

Use the same format as commit messages:

```
feat(uuid-generator): add reverse mode
```

PR Description Template

```markdown
## What

Brief description of the change.

## Why

Explain the motivation. Link related issues.

## How

Describe the approach — especially if it's non-obvious.

## Screenshots

If UI changed, add before/after screenshots.

## Checklist

- [ ] Tested on Chrome and Firefox
- [ ] Tested on mobile viewport
- [ ] Added translations (EN + FA)
- [ ] Updated documentation if needed
- [ ] Closes #<issue-number>
```

Review Process

1. A maintainer reviews within 3 days
2. You may be asked for changes
3. Once approved, the PR is merged
4. Your branch is deleted

What Gets Merged

· Bug fixes ✅
· New features that fit the roadmap ✅
· Documentation improvements ✅
· Translation additions ✅
· Performance improvements ✅
· Refactoring that reduces complexity ✅

What Gets Rejected

· Changes to unrelated files ❌
· Introduction of external dependencies ❌
· Breaking changes without discussion ❌
· Features outside the project scope ❌

---

Reporting Bugs

Before Reporting

1. Search existing issues — it may already be known
2. Update to the latest version
3. Try a different browser
4. Check your PHP version and extensions

Bug Report Template

```markdown
## Description

A clear description of the bug.

## Steps to Reproduce

1. Go to `/form-creator`
2. Add a button
3. Type text
4. See error

## Expected Behavior

The code should update.

## Actual Behavior

The code doesn't update.

## Environment

- PHP: 8.2.10
- Server: Apache 2.4.57
- OS: Ubuntu 22.04
- Browser: Chrome 120
- Version: 2.0.0

## Screenshots / Console Errors

(Paste here)
```

What Makes a Good Bug Report

· ✅ Reproducible steps
· ✅ Exact error messages
· ✅ Environment details
· ✅ Screenshots or console output
· ❌ "It doesn't work"

---

Suggesting Features

Before Suggesting

1. Search existing issues — it may already be planned
2. Check the roadmap in README
3. Think about scope — is it related to PocketMine?

Feature Request Template

```markdown
## Problem

What problem does this feature solve?

## Proposed Solution

How would you implement it?

## Alternatives

What else did you consider?

## Additional Context

Screenshots, links, examples from other tools.
```

What Fits the Project

· ✅ PocketMine-specific tools
· ✅ Improvements to existing tools
· ✅ New language translations
· ✅ Accessibility improvements
· ❌ Generic code editors
· ❌ Server management dashboards
· ❌ Anything requiring a database

---

Translations

We currently support English and Persian. Adding a new language is very welcome.

How to Add a Language

1. Copy languages/en.php to languages/xx.php (xx = ISO 639-1 code)
2. Translate every value, keep all keys identical
3. Add an entry to config/languages.php:

```php
'xx' => [
    'code'   => 'xx',
    'name'   => 'Language Name',
    'native' => 'Native Name',
    'flag'   => '🏳️',
    'dir'    => 'ltr',  // or 'rtl'
    'font'   => 'Font, sans-serif',
],
```

4. Add xx to supported_langs in config/app.php
5. Test the site with ?lang=xx
6. Open a PR with the title i18n: add <Language> translation

Translation Guidelines

· Keep technical terms in English when they don't translate well
  (e.g. "Form", "MOTD", "Virion", "UUID")
· Match the tone of the English strings (friendly, direct)
· Don't translate placeholder names like {name}, {count}
· Preserve leading/trailing spaces and punctuation
· Run the site in RTL mode if your language is right-to-left

Improving Existing Translations

Found a typo or awkward phrasing? Open a PR that fixes only that. Small translation PRs are always welcome.

---

Adding a New Tool

Adding a tool takes about 30 minutes. Here's the complete checklist.

1. Create the Page

pages/my-tool.php:

```php
<?php
declare(strict_types=1);
$pageScript = 'my-tool.js';
partial('head');
partial('loader');
partial('header');
?>

<main class="page-tool">
    <div class="container">
        <header class="tool-head">
            <a href="<?= e(url('home')) ?>" class="back-btn">
                <span class="material-symbols-rounded">arrow_back</span>
                <?= __e('common.back') ?>
            </a>
            <h1 class="tool-head-title"><?= __e('my_tool.title') ?></h1>
        </header>

        <div class="tool-layout">
            <!-- Editor -->
            <section class="editor-panel">
                <div class="panel-head">
                    <span class="panel-title">
                        <span class="material-symbols-rounded">code</span>
                        <?= __e('common.code_editor') ?>
                    </span>
                </div>
                <pre class="editor-pre"><code id="code-output"></code></pre>
            </section>

            <!-- Form -->
            <section class="form-panel">
                <!-- inputs here -->
            </section>
        </div>
    </div>
</main>

<?php partial('footer'); ?>
```

2. Add the Route

config/routes.php:

```php
'my-tool' => 'my-tool.php',
```

3. Add the Home Card

pages/home.php, inside $tools:

```php
['slug' => 'my-tool', 'icon' => 'build', 'color' => 'teal', 'key' => 'my_tool'],
```

4. Create the Script

assets/js/my-tool.js:

```javascript
(function () {
    'use strict';

    const state = {};

    function render() {
        const out = document.getElementById('code-output');
        if (out) out.textContent = 'TODO';
    }

    function init() {
        render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
```

5. Add Translations

languages/en.php:

```php
'my_tool' => [
    'title'    => 'My Tool',
    'subtitle' => 'Description of my tool',
],
```

languages/fa.php:

```php
'my_tool' => [
    'title'    => 'ابزار من',
    'subtitle' => 'توضیح ابزار من',
],
```

6. Add CSS (if needed)

assets/css/my-tool.css — and link it in pages/partials/head.php.

7. Update Documentation

· Add to README's tool table
· Add to CHANGELOG under [Unreleased] > Added
· Add to sitemap.xml
· Add to docs/ if it needs a full guide

8. Test

☐ Page loads at /?page=my-tool
☐ Works in English
☐ Works in Persian (?lang=fa)
☐ Works on mobile
☐ Copy button works
☐ No console errors

---

Testing Checklist

Before submitting a PR, run through this list.

Functionality

☐ Page loads without errors
☐ All inputs respond to changes
☐ Code updates in real time
☐ Copy button copies the correct text
☐ Download button produces a valid file
☐ Reset / clear button works

Languages

☐ English works
☐ Persian works with ?lang=fa
☐ RTL layout is correct
☐ Code editor stays LTR

Themes

☐ Dark theme looks correct
☐ Light theme looks correct
☐ Toggle persists across reloads

Responsive

☐ Desktop (1280px)
☐ Tablet (768px)
☐ Mobile (375px)

Browsers

☐ Chrome / Edge
☐ Firefox
☐ Safari

Accessibility

☐ All buttons have labels
☐ Keyboard navigation works
☐ Focus indicators visible
☐ Images have alt attributes

Console

☐ No JavaScript errors
☐ No network 404s
☐ No PHP warnings

---

Recognition

Contributors are recognized in:

· The Credits section of the README
· The Contributors page (/?page=creators)
· GitHub's Contributors tab

Notable contributions may also be mentioned in release notes.

---

Questions

· 💬 GitHub Discussions — for open-ended questions
· 🐛 GitHub Issues — for bugs and feature requests
· 📧 Email — for security issues (see SECURITY.md if present)

Before asking, please:

1. Read the README
2. Search existing issues
3. Try reproducing on the latest version

---

Thank you for making PocketMine Tools better!

Every contribution — even a typo fix — helps the community.

---

See also: README.md · INSTALL.md · API.md · CHANGELOG.md

---

[Home](README.md) · [Installation](INSTALL.md) · [API](API.md) · [Documentation index](README.md#documentation) · [Contributing](CONTRIBUTING.md)
