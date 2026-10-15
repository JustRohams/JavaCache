# Manifest.json Guide

Complete guide to writing Bedrock addon `manifest.json` files.

---

## What Is a Manifest?

Every Bedrock addon (behavior pack or resource pack) must contain a `manifest.json` at its root. It tells the game:

- The pack's name and description
- A unique identifier (UUID)
- The version
- What modules it contains
- The minimum engine version required

Without a valid manifest, the game refuses to load the pack.

---

## Basic Structure

```json
{
  "format_version": 2,
  "header": {
    "name": "My Addon",
    "description": "Created with PocketMine Tools",
    "uuid": "7e199f57-fff9-48c9-aba5-fd0835adbe7f",
    "version": [1, 0, 0],
    "min_engine_version": [1, 20, 0]
  },
  "modules": [
    {
      "type": "data",
      "uuid": "f3056213-4d18-495a-8ce9-a00a82f626d0",
      "version": [1, 0, 0]
    }
  ]
}
```

---

Field Reference

format_version

Always 2 for modern Bedrock. Version 1 is legacy and should not be used for new packs.

header

Field Type Required Description
name string ✅ Pack name shown in-game
description string ✅ Shown under the name
uuid string ✅ Unique identifier (v4 format)
version array ✅ [major, minor, patch]
min_engine_version array ✅ Minimum Bedrock version
lock_template_options bool ❌ Locks template settings
base_game_version array ❌ For game templates

modules

An array of module objects. Each module has:

Field Type Required Description
type string ✅ data / resources / script
uuid string ✅ Different UUID from the header
version array ✅ Module version
description string ❌ Optional description

---

Module Types

Type Purpose Folder in pack
data Behavior pack — blocks, items, entities, functions BP/
resources Resource pack — textures, sounds, UI RP/
script Script API module (GameTest) BP/scripts/
javascript Legacy scripting (deprecated) BP/scripts/

When to Use Each

· data — You're adding custom blocks, items, entities, or behaviors
· resources — You're adding textures, sounds, fonts, or UI changes
· script — You're writing JavaScript using the GameTest API
· javascript — Do not use for new projects

---

UUIDs

Rules

· Header UUID must be different from every module UUID
· Format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
· Lowercase or uppercase both work
· Must be unique across the entire Bedrock ecosystem
· Never reuse a UUID between two different packs

Format Breakdown

```
7e199f57-fff9-48c9-aba5-fd0835adbe7f
└─ 8 ──┘ └ 4┘ └ 4┘ └ 4┘ └─── 12 ───┘
```

Section Length Notes
1 8 chars Time-low
2 4 chars Time-mid
3 4 chars Version (starts with 4)
4 4 chars Variant (starts with 8, 9, a, or b)
5 12 chars Node

Generating UUIDs

Random (online):

· https://www.uuidgenerator.net/
· https://uuidgenerator.net/

Deterministic (recommended for PocketMine Tools users):

Use the UUID Generator tool in PocketMine Tools. Same pack name always produces the same UUIDs, which makes reproducible builds possible.

Python:

```python
import uuid
print(uuid.uuid4())
```

JavaScript:

```javascript
crypto.randomUUID()
```

Command line:

```bash
# Linux / macOS
uuidgen

# Windows PowerShell
[guid]::NewGuid().ToString()
```

PHP:

```php
function uuid_v4(): string {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
```

---

Versions

Versions are arrays of three integers: [major, minor, patch].

Format Valid
[1, 0, 0] ✅
[1, 20, 60] ✅
[0, 0, 1] ✅
"1.0.0" ❌ (string not allowed)
[1, 0] ❌ (must be 3 numbers)
[1.0, 0, 0] ❌ (must be integers)

Version Bumping

Change Bump
Bug fix, no new features [1, 0, 1]
New feature, backward compatible [1, 1, 0]
Breaking change [2, 0, 0]

---

Minimum Engine Version

Specifies the minimum Bedrock version that can load the pack.

Version When to use
[1, 19, 0] Wide compatibility, older features only
[1, 20, 0] Default for modern addons
[1, 20, 60] Using 1.20.60 features
[1, 21, 0] Using 1.21 features

Using a version higher than the player's client will show "unsupported pack" and prevent loading. Always set the lowest version that supports your features.

---

Complete Examples

Behavior Pack

```json
{
  "format_version": 2,
  "header": {
    "name": "My Behavior Pack",
    "description": "Custom blocks and items",
    "uuid": "a1b2c3d4-1111-4aaa-9bbb-111111111111",
    "version": [1, 0, 0],
    "min_engine_version": [1, 20, 0]
  },
  "modules": [
    {
      "type": "data",
      "uuid": "b2c3d4e5-2222-4bbb-8ccc-222222222222",
      "version": [1, 0, 0]
    }
  ]
}
```

Resource Pack

```json
{
  "format_version": 2,
  "header": {
    "name": "My Texture Pack",
    "description": "Custom textures",
    "uuid": "8e2f0e4a-1234-4abc-9def-567890abcdef",
    "version": [1, 0, 0],
    "min_engine_version": [1, 20, 0]
  },
  "modules": [
    {
      "type": "resources",
      "uuid": "9f3e1f5b-2345-4bcd-8ef0-678901bcdef0",
      "version": [1, 0, 0]
    }
  ]
}
```

Combined Behavior + Resource Pack

```json
{
  "format_version": 2,
  "header": {
    "name": "My Complete Addon",
    "description": "Blocks, items, and textures",
    "uuid": "11111111-2222-4333-8444-555555555555",
    "version": [1, 0, 0],
    "min_engine_version": [1, 20, 0]
  },
  "modules": [
    {
      "type": "data",
      "uuid": "22222222-3333-4444-8555-666666666666",
      "version": [1, 0, 0]
    },
    {
      "type": "resources",
      "uuid": "33333333-4444-4555-8666-777777777777",
      "version": [1, 0, 0]
    }
  ]
}
```

Script API Module

```json
{
  "format_version": 2,
  "header": {
    "name": "My Script Pack",
    "description": "Uses the Script API",
    "uuid": "44444444-5555-4666-8777-888888888888",
    "version": [1, 0, 0],
    "min_engine_version": [1, 20, 60]
  },
  "modules": [
    {
      "type": "script",
      "language": "javascript",
      "uuid": "55555555-6666-4777-8888-999999999999",
      "entry": "scripts/main.js",
      "version": [1, 0, 0]
    }
  ],
  "dependencies": [
    {
      "module_name": "@minecraft/server",
      "version": "1.5.0"
    }
  ]
}
```

---

Dependencies

If your pack requires another pack:

```json
{
  "format_version": 2,
  "header": { },
  "modules": [ ],
  "dependencies": [
    {
      "uuid": "c3d4e5f6-3333-4ccc-9ddd-333333333333",
      "version": [1, 0, 0]
    }
  ]
}
```

Script Module Dependencies

For Script API:

```json
"dependencies": [
  {
    "module_name": "@minecraft/server",
    "version": "1.5.0"
  },
  {
    "module_name": "@minecraft/server-ui",
    "version": "1.2.0"
  }
]
```

---

Folder Layout

Simple Pack

```
MyAddon/
├── manifest.json
├── pack_icon.png
├── texts/
│   └── en_US.lang
├── blocks/
├── items/
└── entities/
```

Combined Behavior + Resource

```
MyAddon/
├── BP/                    ← Behavior pack
│   ├── manifest.json
│   ├── pack_icon.png
│   ├── entities/
│   ├── items/
│   └── blocks/
└── RP/                    ← Resource pack
    ├── manifest.json
    ├── pack_icon.png
    ├── textures/
    └── sounds/
```

Both manifest.json files must have different UUIDs.

---

Packaging as .mcpack

A .mcpack file is a ZIP archive with manifest.json at the root.

Create Manually

Linux / macOS:

```bash
cd MyAddon
zip -r ../MyAddon.mcpack . -x ".*"
```

Windows: Right-click the folder → Send to → Compressed (zipped) folder → Rename .zip to .mcpack.

Python:

```python
import shutil
shutil.make_archive('MyAddon', 'zip', 'MyAddon')
# rename output to MyAddon.mcpack
```

Via PocketMine Tools

Open the Manifest Creator tool → fill in the fields → click Download .mcpack.

The server packages your manifest as a ZIP with the correct extension. Requires ext-zip.

---

Common Errors

Error Cause Fix
Invalid UUID Wrong format Use xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
Duplicate UUID Header and module share UUID Generate a different one for the module
Unsupported pack min_engine_version too high Lower it to match the target client
Missing manifest File not at root Move manifest.json to the pack root
Invalid version Version is a string Use [1, 0, 0] array form
Pack not showing Wrong module type Use data for BP, resources for RP
Dependency not found Wrong UUID or version Verify the dependency UUID
JSON parse error Trailing comma or missing brace Validate with JSONLint

---

Validation Tools

· PocketMine Tools Manifest Creator — Live preview + .mcpack export
· JSONLint — Validates JSON syntax
· VSCode with Blockception's Minecraft Bedrock Development extension
· Bedrock Wiki — https://wiki.bedrock.dev/

---

Best Practices

· ✅ Use deterministic UUIDs for reproducible builds
· ✅ Keep format_version at 2 for modern packs
· ✅ Use separate UUIDs for header and every module
· ✅ Set min_engine_version to the lowest version that works
· ✅ Include a pack_icon.png (256×256)
· ✅ Version bump on every release
· ❌ Never reuse UUIDs across different packs
· ❌ Never commit UUIDs that were generated for other people's projects
· ❌ Never leave version as a string

---

Reference

· Bedrock Wiki — Manifest
· Microsoft Docs — Manifest Reference
· Minecraft Creator Portal

---

See also:

· MOTD Colors Guide
· UUID Guide
· PocketMine Setup

---

[Home](README.md) · [Installation](INSTALL.md) · [API](API.md) · [Documentation index](README.md#documentation) · [Contributing](CONTRIBUTING.md)
