# Changelog

All notable changes to **PocketMine Tools** are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## Table of Contents

- [[Unreleased]](#unreleased)
- [[2.0.0] - 2026-01-15](#200---2026-01-15)
- [[1.2.0] - 2025-11-20](#120---2025-11-20)
- [[1.1.0] - 2025-09-05](#110---2025-09-05)
- [[1.0.0] - 2025-07-01](#100---2025-07-01)
- [Versioning Policy](#versioning-policy)
- [Change Categories](#change-categories)

---

## [Unreleased]

### Planned

- Persian documentation completion
- SQLite schema builder tool
- Scoreboard generator tool
- Entity NBT editor
- Export whole project as ZIP bundle

---

## [2.0.0] - 2026-01-15

Full rewrite. New architecture, bilingual support, and 4 new tools.

### Added

#### New Tools

- **UUID Generator** — Deterministic, random, and paired UUID generation with reverse lookup
- **Dependencies Builder** — Manage `depend`, `softdepend`, and `virion` entries with dual output (`plugin.yml` / `.poggit.yml`)
- **Command Creator** — Generate PHP command classes with dynamic arguments
- **Coordinates Converter** — Overworld ↔ Nether coordinate math

#### Bilingual System

- Persian (فارسی) translation for the entire UI
- Automatic language detection via `?lang=`, cookie, or `Accept-Language`
- Full RTL layout support with code editor direction fix
- Language switcher in the header
- 200+ translation keys per language

#### Theme System

- Dark theme (default) with Google Material-inspired palette
- Light theme with automatic system preference detection
- Theme toggle in the header
- Persistence via `localStorage`

#### Design System

- CSS custom properties for all design tokens
- 14 dedicated stylesheets (variables, main, loader, nav, cards, editor, forms, buttons, footer, uuid, motd, dependencies, home, responsive)
- Responsive breakpoints at 1024px, 768px, 480px
- Print stylesheet
- `prefers-reduced-motion` support

#### Loader Animation

- SVG-based ring + checkmark loader
- 5 progress stages with Persian/English status text
- Session-based skip (loads fast on second visit)

#### Sounds

- Google Material-style UI sounds
- Click, hover, success, error, toggle variants
- Optional — user can disable via `localStorage`
- Throttled hover playback (300ms)

#### Icons

- Google Material Symbols Rounded throughout
- No emoji in the UI

#### New API Endpoints

- `api/uuid.php` — 4 actions: `generate`, `reverse`, `random`, `pair`
- `api/convert.php` — 2 actions: `o2n`, `n2o`
- `api/dependencies.php` — 3 actions: `list`, `validate`, `save`
- `api/manifest.php` — 2 actions: `generate`, `pack`

#### Project Structure

- Complete folder separation: `config/`, `includes/`, `languages/`, `pages/`, `api/`, `assets/`, `data/`, `docs/`
- Partial system for reusable page parts (`head`, `header`, `nav`, `loader`, `footer`)
- Dedicated `pages/404.php`

#### Documentation

- `README.md` — full feature overview
- `INSTALL.md` — setup guide for Apache, Nginx, shared hosting
- `API.md` — JSON endpoint reference
- `CONTRIBUTING.md` — contribution guidelines
- `CHANGELOG.md` — this file
- `docs/` folder with tool-specific guides (EN + FA)

#### Developer Experience

- `.editorconfig` for cross-IDE formatting
- `.gitignore` covering IDE, OS, build artifacts
- `install.php` environment checker
- Composer and npm metadata files

### Changed

- **Complete rewrite** of the PHP backend
- **Complete rewrite** of all JavaScript (IIFE pattern, event delegation)
- **Complete rewrite** of all CSS (variables-first approach)
- Route system now uses `config/routes.php` instead of a switch statement
- All output escaped via `e()` and `__e()` helpers
- Navigation redesigned with Material Symbols icons
- Editor panel is now sticky on desktop
- All output panels have copy + download buttons

### Improved

- Better error handling — graceful fallback to 404 page
- Better performance — CSS variables, debounced inputs, event delegation
- Better accessibility — semantic HTML, ARIA labels, keyboard navigation
- Better SEO — `sitemap.xml`, `robots.txt`, Open Graph tags
- Better caching — 1-year cache for fonts, images, CSS, JS
- Better security — headers, path validation, escaped output

### Fixed

- Manifest Creator now generates two **distinct** UUIDs (Header + Module)
- Form Creator now updates the code on every keystroke (was on blur)
- plugin.yml Creator now escapes quotes in descriptions
- MOTD preview now handles all 16 Minecraft colors correctly

### Deprecated

- Nothing. 2.0.0 is a full rewrite with no backward compatibility.

### Removed

- jQuery dependency (was never needed)
- Bootstrap CDN usage
- Inline styles in HTML
- Hardcoded English strings
- Old `functions.php` (split into `helpers.php` + `constants.php`)

### Security

- All routes validated via `preg_replace('/[^a-z0-9\-]/', '', ...)`
- `Options -Indexes` disables directory listing
- `/config`, `/includes`, `/data`, `/docs` blocked in `.htaccess`
- `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`
- Escaped all user input before output
- Validated UUID format before lookup
- Rate-limit-ready (disabled by default)

---

## [1.2.0] - 2025-11-20

Last release before the 2.0 rewrite.

### Added

- **Dependencies Builder** (early version) — basic `depend` list builder
- **UUID Generator** (early version) — random UUID only
- Copy-to-clipboard button on all output panels

### Changed

- `manifest.json` Creator now supports three pack types
- `plugin.yml` Creator now validates API version format

### Fixed

- Fixed a bug where MOTD preview would not update on paste
- Fixed scroll position resetting after copy

---

## [1.1.0] - 2025-09-05

### Added

- **MOTD Creator** — first version with 16-color palette
- **Coordinates Converter** — basic math, no validation
- Dark theme (light was the only option before)

### Changed

- Rewrote the Form Creator tabs — now with icons
- Improved mobile layout for all tool pages

### Fixed

- Fixed a crash when `data/` was not writable
- Fixed incorrect Nether math (was dividing by 7 instead of 8)

---

## [1.0.0] - 2025-07-01

First public release.

### Added

- **Form Creator** (SimpleForm, ModalForm, CustomForm)
- **Plugin Creator** (basic folder structure)
- **plugin.yml Creator** (with commands section)
- **manifest.json Creator** (with manual UUID)
- Home page with tool cards
- Basic dark theme
- English-only UI
- PHP backend + vanilla JavaScript

### Notes

- This version had no bilingual support, no loader, and no sounds
- File layout was flat (`index.php`, `functions.php`, `style.css`, `script.js`)

---

## Versioning Policy

PocketMine Tools follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html):

MAJOR.MINOR.PATCH


| Change | Bumps |
|--------|-------|
| Breaking changes | **MAJOR** |
| New features (backward compatible) | **MINOR** |
| Bug fixes, tweaks | **PATCH** |

### Pre-release Tags

- `-alpha.1` — early preview, unstable
- `-beta.1` — feature-complete, testing
- `-rc.1` — release candidate

Example: `2.1.0-beta.2`

---

## Change Categories

Every release uses these sections:

| Section | Meaning |
|---------|---------|
| **Added** | New features |
| **Changed** | Changes to existing behavior |
| **Deprecated** | Features scheduled for removal |
| **Removed** | Features that were removed |
| **Fixed** | Bug fixes |
| **Security** | Vulnerability fixes |
| **Improved** | Performance / quality improvements (non-breaking) |

---

## Release Cadence

| Type | Frequency |
|------|-----------|
| Major | Every 12–18 months |
| Minor | Every 2–4 months |
| Patch | As needed (1–2 weeks) |

---

## Links

- **Repository:** https://github.com/JustRohams/PocketMineTools
- **Issues:** https://github.com/JustRohams/PocketMineTool/issues
- **Releases:** https://github.com/JustRohams/PocketMineTools/releases
- **Compare versions:** https://github.com/JustRohams/PocketMineTools/compare

---

**See also:** [README.md](README.md) · [INSTALL.md](INSTALL.md) · [CONTRIBUTING.md](CONTRIBUTING.md)
