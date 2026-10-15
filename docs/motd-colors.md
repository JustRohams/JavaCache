# MOTD Colors Guide

Complete reference for Minecraft Bedrock MOTD color codes and formatting.

---

## Overview

The **Message of the Day (MOTD)** is the text shown next to a server in the multiplayer list. On Bedrock Edition, MOTD supports **16 colors** and **5 text formats** using the `§` (section sign) character.

---

## The `§` Character

Every color and format code starts with `§` — a special Unicode character (U+00A7).

**How to type it:**

| Platform | Method |
|----------|--------|
| Windows | `Alt` + `0167` on numpad |
| macOS | `Option` + `6` |
| Linux | `Compose` + `S` + `O` |
| Anywhere | Copy this: `§` |
| In code | `"\u{00A7}"` (PHP) or `"\u00A7"` (JS) |

---

## Color Codes

All 16 colors, in the order they appear in the PocketMine Tools palette.

| Code | Name (EN) | Name (FA) | Hex | Preview |
|------|-----------|-----------|-----|---------|
| `§0` | Black | مشکی | `#000000` | ██████ |
| `§1` | Dark Blue | آبی تیره | `#0000AA` | ██████ |
| `§2` | Dark Green | سبز تیره | `#00AA00` | ██████ |
| `§3` | Dark Aqua | آبی یخی تیره | `#00AAAA` | ██████ |
| `§4` | Dark Red | قرمز تیره | `#AA0000` | ██████ |
| `§5` | Dark Purple | بنفش تیره | `#AA00AA` | ██████ |
| `§6` | Gold | نارنجی | `#FFAA00` | ██████ |
| `§7` | Gray | خاکستری | `#AAAAAA` | ██████ |
| `§8` | Dark Gray | خاکستری تیره | `#555555` | ██████ |
| `§9` | Blue | آبی روشن | `#5555FF` | ██████ |
| `§a` | Green | سبز روشن | `#55FF55` | ██████ |
| `§b` | Aqua | آبی یخی روشن | `#55FFFF` | ██████ |
| `§c` | Red | قرمز روشن | `#FF5555` | ██████ |
| `§d` | Light Purple | صورتی | `#FF55FF` | ██████ |
| `§e` | Yellow | زرد | `#FFFF55` | ██████ |
| `§f` | White | سفید | `#FFFFFF` | ██████ |

---

## Format Codes

| Code | Name (EN) | Name (FA) | Effect |
|------|-----------|-----------|--------|
| `§l` | Bold | ضخیم | **Bold text** |
| `§o` | Italic | ایتالیک | *Italic text* |
| `§n` | Underline | زیرخط | Underlined text |
| `§m` | Strikethrough | خط‌خورده | ~~Struck text~~ |
| `§r` | Reset | ریست | Removes all formatting |

### Combining Colors and Formats

---

[Home](README.md) · [Installation](INSTALL.md) · [API](API.md) · [Documentation index](README.md#documentation) · [Contributing](CONTRIBUTING.md)
