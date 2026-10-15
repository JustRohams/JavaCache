# Dependencies Guide

Complete guide to PocketMine plugin dependencies — `depend`, `softdepend`, and Virions.

---

## Overview

PocketMine plugins can declare relationships with other plugins and libraries. There are three mechanisms:

| Mechanism | Purpose | Declared in |
|-----------|---------|-------------|
| `depend` | Hard requirement — plugin won't load without it | `plugin.yml` |
| `softdepend` | Optional — loaded after if present | `plugin.yml` |
| Virion | Bundled library — compiled into your `.phar` | `.poggit.yml` |

Choosing the right mechanism matters for load order, distribution, and compatibility.

---

## `depend` — Hard Dependencies

### What It Means

If any plugin listed under `depend` is **missing or disabled**, your plugin will **not load at all**. PocketMine will skip it and log an error.

### When to Use

- Your plugin's core features require another plugin to function
- Example: a shop plugin that stores currency in **EconomyAPI** — without it, nothing works
- Example: an API-only plugin that other plugins must extend

### Syntax

```yaml
depend:
  - EconomyAPI
  - FormAPI
  - AnotherPlugin
```

Load Order

Plugins in depend load before your plugin. This guarantees their classes are available when your onEnable() runs.

Example

```yaml
name: MyShop
main: MyShop\Main
version: "1.0.0"
api: ["5.0.0"]
author: RohaM

depend:
  - EconomyAPI
  - FormAPI
```

If EconomyAPI is missing, MyShop won't start and the console shows:

```
Could not load plugin 'MyShop': Unknown dependency 'EconomyAPI'
```

In Code

Once you declare the dependency, you can safely grab it:

```php
$economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
if ($economy === null) {
    $this->getLogger()->error("EconomyAPI not found!");
    $this->getServer()->getPluginManager()->disablePlugin($this);
    return;
}
```

---

softdepend — Optional Dependencies

What It Means

Plugins listed under softdepend are loaded before yours if available, but your plugin loads regardless of whether they exist.

When to Use

· Adding optional integrations (e.g. rank prefixes if a rank plugin is installed)
· Making your plugin extendable without requiring the extension
· Avoiding hard failures when a related plugin is unavailable

Syntax

```yaml
softdepend:
  - PurePerms
  - RankSystem
  - ScoreFactory
```

Load Order

If a softdepend is present, it loads before your plugin. If not present, your plugin still loads and getPlugin() returns null.

Example

```yaml
name: MyChat
main: MyChat\Main
version: "1.0.0"
api: ["5.0.0"]

softdepend:
  - PurePerms
  - RankSystem
```

In Code

Always null-check before using:

```php
protected function onEnable(): void {
    $perms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");

    if ($perms !== null) {
        $this->getLogger()->info("PurePerms detected — enabling group integration.");
        $this->hasPurePerms = true;
    } else {
        $this->getLogger()->info("PurePerms not found — running in standalone mode.");
        $this->hasPurePerms = false;
    }
}
```

Best Practice

Wrap optional features in a guard:

```php
public function formatName(Player $player): string {
    if (!$this->hasPurePerms) {
        return $player->getName();
    }

    $group = $this->perms->getUserDataMgr()->getGroup($player);
    return "[" . $group->getName() . "] " . $player->getName();
}
```

---

Virions

What Is a Virion?

A virion is a reusable PHP library packaged to work with PocketMine plugins. Unlike regular plugins, virions:

· Are not loaded at runtime by the server
· Are compiled directly into your .phar at build time
· Don't need to be installed on the server separately
· Can be shared across many plugins

FormAPI, InvMenu, Commando, and many more are distributed as virions.

When to Use a Virion

· You want to bundle a library into your plugin
· You don't want server owners to install extra plugins
· The library is meant to be a dependency for other plugins

When NOT to Use a Virion

· The library is a standalone plugin with its own commands and events
· The library updates frequently and you want users to get updates automatically
· The library is very large and bloats your .phar

Declaring Virions in .poggit.yml

Add to the root of your repository:

```yaml
---
projects:
  MyPlugin:
    path: ""
    libs:
      src: src
    virions:
      - FormAPI
      - InvMenu
      - Commando
```

When Poggit builds your plugin, it clones each virion and merges its source into your .phar.

Common Virions

Virion Author Purpose
FormAPI jojoe77777 Native Bedrock UI forms
InvMenu muqsit Virtual inventory GUIs
Commando CortexPE Advanced command framework
ScoreFactory JackMD Efficient scoreboards
libPMMap dktapps Rendering maps on items
libasynql poggit Async database queries
libsnooze poggit Event waiting / delays

Virion Dependencies

Some virions depend on others. For example, InvMenu uses libforms internally. Poggit resolves transitive dependencies automatically.

---

Combining depend, softdepend, and Virions

Real-world example:

```yaml
name: MyEconomy
main: MyEconomy\Main
version: "2.0.0"
api: ["5.0.0"]
author: RohaM
description: "Economy system with shop and rank integration"

depend:
  - AnotherCorePlugin       # hard requirement

softdepend:
  - PurePerms               # optional rank support
  - RankSystem              # optional rank support
  - ScoreFactory            # optional scoreboard
```

.poggit.yml:

```yaml
---
projects:
  MyEconomy:
    path: ""
    libs:
      src: src
    virions:
      - FormAPI             # bundled UI
      - InvMenu             # bundled menus
      - libasynql           # bundled async DB
```

Result:

· AnotherCorePlugin is required for load
· PurePerms / RankSystem / ScoreFactory enhance features if present
· FormAPI, InvMenu, libasynql are compiled into your .phar

---

Load Order Rules

PocketMine loads plugins in this order:

1. Plugins with no dependencies
2. Plugins whose dependencies are already loaded
3. depend targets load before their dependents
4. softdepend targets also load before their dependents, if present

If there's a circular dependency (A depends on B, B depends on A), neither will load.

Example Load Sequence

Given:

· Plugin A: no deps
· Plugin B: depends on A
· Plugin C: softdepends on B
· Plugin D: depends on C

Load order: A → B → C → D

---

Complete plugin.yml Examples

Example 1 — Simple with one hard dependency

```yaml
name: MyEconomy
main: MyEconomy\Main
version: "1.0.0"
api: ["5.0.0"]
author: RohaM
description: "Uses FormAPI for menus"

depend:
  - FormAPI
```

Example 2 — With optional integrations

```yaml
name: MyRank
main: MyRank\Main
version: "1.0.0"
api: ["5.0.0"]
author: RohaM
description: "Rank system with optional integrations"

softdepend:
  - PurePerms
  - RankSystem
  - EconomyAPI
```

Example 3 — Mixed hard and soft

```yaml
name: MyHub
main: MyHub\Main
version: "1.0.0"
api: ["5.0.0"]
author: RohaM
description: "Lobby system"

depend:
  - FormAPI

softdepend:
  - EconomyAPI
  - PurePerms
```

Example 4 — Full featured

```yaml
name: MyServerCore
main: MyServerCore\Main
version: "3.0.0"
api: ["5.0.0"]
author: RohaM
description: "Server core with optional integrations"

depend:
  - FormAPI

softdepend:
  - EconomyAPI
  - PurePerms
  - RankSystem
  - ScoreFactory
  - SimpleEconomy

commands:
  core:
    description: "Server core command"
    permission: myservercore.command.core

permissions:
  myservercore.use:
    default: true
  myservercore.admin:
    default: op
```

---

Real-World Integration Example

Full example showing hard + soft dependency handling:

```php
<?php
declare(strict_types=1);

namespace MyServerCore;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;

class Main extends PluginBase {

    private ?object $economy = null;
    private ?object $perms = null;
    private bool $hasScoreFactory = false;

    protected function onEnable(): void {
        $this->loadHardDependencies();
        $this->detectSoftDependencies();
    }

    private function loadHardDependencies(): void {
        // FormAPI is compiled in as a virion — no lookup needed
        // But if it were a plugin:
        $formApi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($formApi === null) {
            $this->getLogger()->error("FormAPI is missing — plugin will disable.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }

    private function detectSoftDependencies(): void {
        $pm = $this->getServer()->getPluginManager();

        $economy = $pm->getPlugin("EconomyAPI");
        if ($economy !== null) {
            $this->economy = $economy;
            $this->getLogger()->info("EconomyAPI detected.");
        }

        $perms = $pm->getPlugin("PurePerms");
        if ($perms !== null) {
            $this->perms = $perms;
            $this->getLogger()->info("PurePerms detected.");
        }

        $this->hasScoreFactory = $pm->getPlugin("ScoreFactory") !== null;
        if ($this->hasScoreFactory) {
            $this->getLogger()->info("ScoreFactory detected.");
        }
    }

    public function giveCoins(Player $player, int $amount): bool {
        if ($this->economy === null) return false;
        return $this->economy->addMoney($player->getName(), $amount);
    }

    public function getRank(Player $player): string {
        if ($this->perms === null) return "Player";
        return $this->perms->getUserDataMgr()->getGroup($player)->getName();
    }

    public function updateScoreboard(Player $player): void {
        if (!$this->hasScoreFactory) return;
        // update scoreboard
    }
}
```

---

Testing Dependencies

Testing depend (Hard)

1. Install your plugin without the required dependency
2. Expected: plugin does not load, console shows an error
3. Install the dependency
4. Expected: plugin loads fine

Testing softdepend (Optional)

1. Install your plugin without any softdepend
2. Expected: plugin loads, optional features are disabled
3. Install a softdepend
4. Expected: plugin loads, optional features activate

Testing Virions

1. Build with Poggit or manually merge virion sources
2. Deploy the .phar to a clean server (no virion folder)
3. Expected: plugin works — virions are inside the .phar

---

Common Pitfalls

Pitfall Problem Fix
Declaring a virion as depend Server looks for a missing plugin Remove from plugin.yml, add to .poggit.yml
Declaring a plugin as a virion Plugin never loads Move to plugin.yml depend / softdepend
Using softdepend for a required plugin Silent failures at runtime Change to depend
Using depend for an optional integration Plugin refuses to load Change to softdepend
Missing null check on softdepend PHP errors when integration absent Always null-check
Circular dependencies Neither plugin loads Redesign to remove the cycle
Virion bloat Large .phar Only include what you use

---

Best Practices

Choose the Right Mechanism

· depend — Only when your plugin cannot function without it
· softdepend — For optional enhancements
· Virion — For libraries you want bundled, not user-installed

Always Null-Check Soft Dependencies

```php
if ($plugin !== null) {
    // safe to use
}
```

Log Dependency Detection

Let server owners know what integrations are active:

```php
$this->getLogger()->info("PurePerms integration: enabled");
```

Pin Virion Versions

In .poggit.yml, if you need a specific version:

```yaml
virions:
  - FormAPI@5.0.0
```

Document Your Dependencies

In your README, list:

```markdown
## Requirements

### Required
- FormAPI (virion — bundled)

### Optional
- EconomyAPI — enables shop features
- PurePerms — enables rank prefixes
```

---

Reference

· PocketMine plugin.yml Docs
· Poggit Build Docs
· Virion List on Poggit
· FormAPI Virion

---

Related Guides

· FormAPI Guide
· Plugin Structure
· plugin.yml Reference

---

Tools

Use the Dependencies Builder tool in PocketMine Tools to generate plugin.yml and .poggit.yml snippets visually — with presets for common libraries like FormAPI, InvMenu, Commando, and ScoreFactory.

---

[Home](README.md) · [Installation](INSTALL.md) · [API](API.md) · [Documentation index](README.md#documentation) · [Contributing](CONTRIBUTING.md)
