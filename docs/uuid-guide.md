# UUID Guide

Complete guide to UUIDs in PocketMine Tools — deterministic, random, paired, and reverse.

---

## What Is a UUID?

A **UUID** (Universally Unique Identifier) is a 128-bit number used to identify information in computer systems. In Minecraft Bedrock, UUIDs are used to uniquely identify:

- Addon packs (behavior, resource, script)
- Pack modules inside a manifest
- Players (internally)
- Custom entities and items

**Format:**

```

xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx

```

| Section | Length | Meaning |
|---------|--------|---------|
| 1 | 8 hex | Time-low |
| 2 | 4 hex | Time-mid |
| 3 | 4 hex | Version (`4` for random UUIDs) |
| 4 | 4 hex | Variant (`8`, `9`, `a`, or `b`) |
| 5 | 12 hex | Node |

**Example:**

```

7e199f57-fff9-48c9-aba5-fd0835adbe7f

```

---

## Why UUIDs Matter

Every Bedrock addon `manifest.json` must contain **at least two distinct UUIDs**:

1. **Header UUID** — identifies the pack
2. **Module UUID** — identifies each module in the pack

Rules:

- Header and module UUIDs must be **different**
- Two different packs must have **different** UUIDs
- If two packs share a UUID, the game refuses to load either
- If you update the pack, keep the same UUIDs (users get the update)

See the [Manifest Guide](manifest-guide.md) for full details.

---

## Types of UUIDs in PocketMine Tools

The **UUID Generator** tool supports four modes:

| Mode | Purpose |
|------|---------|
| **Deterministic** | Same key always produces the same UUID |
| **Random** | Fresh random UUID every time |
| **Pair** | Two UUIDs (Header + Module) from one key |
| **Reverse** | Recover the original key from a UUID |

---

## Deterministic UUIDs

### What They Are

A **deterministic UUID** is generated from a fixed input (a "key") plus a numeric seed. Same key + same seed = same UUID, every time.

### How It Works

PocketMine Tools computes:

```

uuid = SHA256("PMTOOLS_v2_ROHAM_2026|" + key.toLowerCase() + "|" + seed)

```

Then combines the result with a second hash of the same input + `"|second"`, XORs them together to get 16 bytes, and sets the version (`4`) and variant (`RFC 4122`) bits.

### When to Use

- You want **reproducible builds** — every time you build the pack, it gets the same UUID
- You want to **regenerate the UUID later** from the same key
- You manage multiple packs and want to avoid collisions systematically
- You want to commit your UUIDs to version control without regenerating them

### Example

**Input:**

```

key  = "MyPlugin"
seed = 0

```

**Output:**

```

8f4a2b3c-5d6e-4f7a-9b8c-1d2e3f4a5b6c

```

Change the key to `"MyPlugin2"`:

```

c1a2b3d4-6e5f-4a8b-9c0d-2e3f4a5b6c7d

```

Change the seed to `1` (same key):

```

a3b4c5d6-7e8f-4a9b-8c1d-3e4f5a6b7c8d

```

### Using with PocketMine Tools

1. Open the **UUID Generator** tool
2. Select mode: **Deterministic**
3. Enter your key (e.g. `MyPlugin`)
4. Enter a seed (usually `0`)
5. Click **Generate**
6. Copy the result

For paired UUIDs, use **Pair** mode — it produces both `header` (seed `0`) and `module` (seed `1`) UUIDs in one click.

---

## Random UUIDs

### What They Are

A standard UUID v4, generated from cryptographically secure random bytes.

### When to Use

- You don't care about reproducibility
- You generate UUIDs once per project and never again
- You want to match a UUID that was already generated externally

### When NOT to Use

- You want to regenerate the UUID later from the same key
- You're managing multiple packs and want a systematic approach
- You want to diff two builds and have them match

### Example

```

d3b07384-d9a1-4c1e-9f0b-2c8e7a6f5d4c

```

### Using with PocketMine Tools

1. Open the **UUID Generator** tool
2. Select mode: **Random**
3. Click **Generate**
4. Copy the result

Every click produces a different value.

---

## Pair Mode

### What It Does

Generates **two UUIDs** from one key — one for the manifest `header`, one for the manifest `modules[0]`. This is exactly what a Bedrock addon needs.

Internally:

```

header = deterministic(key, seed=0)
module = deterministic(key, seed=1)

```

### When to Use

- You're building a Bedrock addon manifest
- You want both UUIDs to be reproducible
- You want to avoid manually generating two separate UUIDs

### Example

**Input:**

```

key = "My Addon"

```

**Output:**

```json
{
  "header": "7e199f57-fff9-48c9-aba5-fd0835adbe7f",
  "module": "f3056213-4d18-495a-8ce9-a00a82f626d0"
}
```

The two values are guaranteed to differ.

Using with PocketMine Tools

1. Open the UUID Generator tool
2. Select mode: Pair
3. Enter your pack name
4. Click Generate
5. Copy both UUIDs into your manifest

---

Reverse Mode

What It Does

Given a UUID, returns the original key that was used to generate it.

How It Works

Deterministic UUIDs are computed from a one-way hash — mathematically, you can't reverse SHA-256. So the reverse lookup relies on a mapping file:

· Whenever you generate a UUID with the site, the mapping uuid → key is stored in data/uuid-map.json
· Reverse mode simply reads that file

Limitation: Only UUIDs generated on this server can be reversed. Random UUIDs cannot.

When to Use

· You have a UUID in a manifest and want to know which key produced it
· You're auditing a set of files and want to trace them back
· You lost the original key and want to recover it

Example

Input:

```
uuid = "8f4a2b3c-5d6e-4f7a-9b8c-1d2e3f4a5b6c"
```

Output:

```
key = "MyPlugin"
```

If the UUID isn't in the map:

```json
{
  "ok": false,
  "error": "not_found"
}
```

Using with PocketMine Tools

1. Open the UUID Generator tool
2. Select mode: Reverse
3. Paste the UUID
4. Click Generate
5. See the recovered key (or "not found")

---

API Reference

generate

```
GET /api/uuid.php?action=generate&key=MyPlugin&seed=0
```

Response:

```json
{
  "ok": true,
  "action": "generate",
  "uuid": "8f4a2b3c-5d6e-4f7a-9b8c-1d2e3f4a5b6c",
  "key": "MyPlugin",
  "seed": 0
}
```

random

```
GET /api/uuid.php?action=random
```

Response:

```json
{
  "ok": true,
  "action": "random",
  "uuid": "d3b07384-d9a1-4c1e-9f0b-2c8e7a6f5d4c"
}
```

pair

```
GET /api/uuid.php?action=pair&key=My Addon
```

Response:

```json
{
  "ok": true,
  "action": "pair",
  "key": "My Addon",
  "header": "7e199f57-fff9-48c9-aba5-fd0835adbe7f",
  "module": "f3056213-4d18-495a-8ce9-a00a82f626d0"
}
```

reverse

```
GET /api/uuid.php?action=reverse&uuid=8f4a2b3c-5d6e-4f7a-9b8c-1d2e3f4a5b6c
```

Response:

```json
{
  "ok": true,
  "action": "reverse",
  "uuid": "8f4a2b3c-5d6e-4f7a-9b8c-1d2e3f4a5b6c",
  "key": "MyPlugin"
}
```

---

Practical Workflows

Workflow 1 — New Bedrock Addon

1. Choose a stable name for your pack (e.g. com.example.mypack)
2. Open UUID Generator → Pair mode
3. Enter the name
4. Copy the two UUIDs
5. Paste into manifest.json:

```json
{
  "header":  { "uuid": "<header>", ... },
  "modules": [{ "uuid": "<module>", ... }]
}
```

6. Save both UUIDs in your README or changelog for future reference

Workflow 2 — Rebuilding an Existing Pack

If you saved the original key:

1. Open UUID Generator → Pair mode
2. Enter the same key
3. Get the same two UUIDs
4. Rebuild your pack — the game will treat it as an update, not a new pack

Workflow 3 — Auditing Existing Manifests

1. Extract UUIDs from each manifest.json
2. Open UUID Generator → Reverse mode
3. Paste each UUID
4. If it's in the map, you'll see the original key
5. If it's not, it was generated elsewhere

Workflow 4 — Multiple Packs

For a project with several packs, use a naming convention:

```
key = "com.example.project.packA"
key = "com.example.project.packB"
key = "com.example.project.resources"
```

Each key yields its own stable pair of UUIDs. Because the algorithm is deterministic, the same key will always produce the same UUIDs — no collisions across packs.

---

Data Storage

data/uuid-map.json

This file stores the uuid → key mapping used by reverse mode.

Structure:

```json
{
  "8f4a2b3c-5d6e-4f7a-9b8c-1d2e3f4a5b6c": "MyPlugin",
  "7e199f57-fff9-48c9-aba5-fd0835adbe7f": "My Addon"
}
```

Limits:

· Capped at 5,000 entries
· When the limit is hit, the oldest 1,000 entries are removed
· Never committed to Git (in .gitignore)

Backup tip: If you rely on reverse mode, back up this file periodically.

---

Preventing UUID Collisions

A collision occurs when two different packs share a UUID. This causes the game to refuse to load one or both packs.

Rules to Avoid Collisions

Rule Why
Never reuse a UUID from another project Even if that project is abandoned
Never copy a UUID from a tutorial The author may still use it
Use different seeds for different modules in the same pack Prevents duplicate UUIDs within a manifest
Use a namespace-style key e.g. com.example.mypack
Never commit UUIDs generated for other people Respects their namespace

Detecting Collisions

Bedrock will show an error like:

```
Pack "MyAddon" uses the same UUID as "MyAddon2"
```

Fix: Regenerate the UUID in one of the packs (only one — the other should keep its original UUID to preserve user data).

---

Best Practices

· ✅ Use deterministic UUIDs for reproducible builds
· ✅ Use a namespace-style key (com.example.mypack)
· ✅ Keep a record of your keys and UUIDs in your README or changelog
· ✅ Use Pair mode for Bedrock manifests
· ✅ Back up data/uuid-map.json if you rely on reverse mode
· ❌ Never reuse UUIDs from other projects
· ❌ Never copy UUIDs from tutorials or examples
· ❌ Never use 00000000-0000-0000-0000-000000000000 (reserved)
· ❌ Never modify a UUID after publishing a pack — this creates a "new" pack

---

Common Mistakes

Mistake Problem Fix
Header and module UUIDs are the same Pack won't load Use Pair mode to generate both
Reusing a UUID from a tutorial Collision with that tutorial's pack Generate a fresh UUID
Changing the UUID after release Users see a "new" pack, not an update Revert to the original UUID
Using all-zeros UUID Reserved, will fail Generate a valid UUID
Using a string version of the UUID in JSON Parse error Use array form in manifests
Assuming reverse mode works for random UUIDs Returns not_found Use deterministic mode if you need reverse

---

Reference

· RFC 4122 — UUID Spec
· Wikipedia — Universally Unique Identifier
· Bedrock Wiki — Manifest

---

Related Guides

· Manifest Guide
· Plugin Structure
· PocketMine Setup

---

Tools

Use the UUID Generator in PocketMine Tools to create deterministic, random, paired, or reversed UUIDs — all in your browser.

---

[Home](README.md) · [Installation](INSTALL.md) · [API](API.md) · [Documentation index](README.md#documentation) · [Contributing](CONTRIBUTING.md)
