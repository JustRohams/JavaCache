# Plugin Structure Guide

Recommended folder layout and file organization for PocketMine-MP plugins.

---

## Overview

PocketMine plugins follow a predictable structure that the server reads at startup. Getting the layout right makes your plugin:

- **Loadable** — PocketMine finds classes via PSR-4-style namespaces
- **Maintainable** — clean separation of concerns
- **Distributable** — Poggit can build it into a `.phar`
- **Extendable** — other developers can add features easily

This guide covers the minimum and full layouts, naming rules, and common patterns.

---

## Minimum Viable Plugin

Every PocketMine plugin needs **three things**:

```

MyPlugin/
├── plugin.yml
├── src/
│   └── Author/
│       └── myplugin/
│           └── Main.php
└── resources/
└── config.yml        (optional)

```

### `plugin.yml`

The plugin's identity card.

```yaml
name: MyPlugin
main: Author\myplugin\Main
version: "1.0.0"
api: ["5.0.0"]
author: Author
description: "A simple plugin"
```

src/Author/myplugin/Main.php

The entry point.

```php
<?php
declare(strict_types=1);

namespace Author\myplugin;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $this->getLogger()->info("MyPlugin enabled!");
    }

    protected function onDisable(): void {
        $this->getLogger()->info("MyPlugin disabled!");
    }
}
```

That's it. PocketMine loads the file, reads plugin.yml, and calls onEnable().

---

Full Plugin Layout

As your plugin grows, split logic into folders:

```
MyPlugin/
├── plugin.yml
├── src/
│   └── Author/
│       └── myplugin/
│           ├── Main.php
│           ├── command/
│           │   └── ExampleCommand.php
│           ├── listener/
│           │   └── PlayerListener.php
│           ├── manager/
│           │   └── DataManager.php
│           ├── task/
│           │   └── BroadcastTask.php
│           ├── form/
│           │   └── ShopForm.php
│           └── utils/
│               └── Message.php
└── resources/
    ├── config.yml
    ├── data.sql
    └── lang/
        ├── en.yml
        └── fa.yml
```

---

Folder Purposes

Folder Purpose Examples
command/ Command classes ExampleCommand.php, AdminCommand.php
listener/ Event listeners PlayerListener.php, BlockListener.php
manager/ Business logic DataManager.php, EconomyManager.php
task/ Scheduled tasks BroadcastTask.php, CooldownTask.php
form/ Form UI classes ShopForm.php, SettingsForm.php
utils/ Static helpers Message.php, Math.php
entity/ Custom entities PetEntity.php
item/ Custom items CustomSword.php
block/ Custom blocks CustomOre.php
database/ DB adapters SqliteAdapter.php
event/ Custom events PlayerLevelUpEvent.php

---

Naming Conventions

Element Convention Example
Namespace lowercase, no dots Author\myplugin
Class name PascalCase ExampleCommand
Method camelCase onPlayerJoin
Property camelCase $dataManager
Constant UPPER_SNAKE_CASE MAX_PLAYERS
File name matches class name ExampleCommand.php
Folder lowercase, singular command/, listener/

Namespace Rules

· The namespace must match the folder structure under src/
· Case-sensitive on Linux servers
· Typically Author\pluginName where Author is capitalized

Example:

```
src/
└── RohaM/
    └── ranksystem/
        └── Main.php
```

```php
namespace RohaM\ranksystem;
```

If any part doesn't match, the class won't autoload.

---

plugin.yml Reference

Required Fields

Field Description
name Plugin name (no spaces, case-sensitive)
main Fully-qualified class name of the main class
version Semantic version string
api Array of supported API versions

Optional Fields

Field Description
author Author name
authors Array of author names
description Short description
website URL
load STARTUP, POSTWORLD, or default
depend Hard dependencies (see Dependencies Guide)
softdepend Optional dependencies
loadbefore Plugins that must load after yours
commands Command definitions
permissions Permission definitions

Example with Everything

```yaml
name: MyPlugin
main: Author\myplugin\Main
version: "1.0.0"
api: ["5.0.0"]
author: RohaM
authors:
  - RohaM
  - Contributor
description: "A plugin that does things"
website: https://github.com/RohaM/MyPlugin
load: POSTWORLD

depend:
  - FormAPI

softdepend:
  - EconomyAPI
  - PurePerms

commands:
  example:
    description: "Example command"
    usage: "/example <arg>"
    permission: myplugin.command.example
    aliases: [ex, sample]

permissions:
  myplugin.command.example:
    description: "Use the example command"
    default: op
  myplugin.admin:
    description: "Full admin access"
    default: op
    children:
      myplugin.command.example: true
```

---

Main Class Patterns

Singleton Pattern (recommended)

```php
<?php
declare(strict_types=1);

namespace Author\myplugin;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    private static Main $instance;

    public static function getInstance(): Main {
        return self::$instance;
    }

    protected function onLoad(): void {
        self::$instance = $this;
    }

    protected function onEnable(): void {
        $this->saveDefaultConfig();
    }

    protected function onDisable(): void {
        // cleanup
    }
}
```

With Manager Registration

```php
class Main extends PluginBase {

    private static Main $instance;
    private DataManager $dataManager;

    public static function getInstance(): Main {
        return self::$instance;
    }

    protected function onLoad(): void {
        self::$instance = $this;
    }

    protected function onEnable(): void {
        $this->saveDefaultConfig();

        $this->dataManager = new DataManager($this);

        $this->registerCommands();
        $this->registerListeners();
        $this->registerTasks();
    }

    private function registerCommands(): void {
        $map = $this->getServer()->getCommandMap();
        $map->register("example", new \Author\myplugin\command\ExampleCommand($this));
    }

    private function registerListeners(): void {
        $this->getServer()->getPluginManager()->registerEvents(
            new \Author\myplugin\listener\PlayerListener($this),
            $this
        );
    }

    private function registerTasks(): void {
        $this->getScheduler()->scheduleRepeatingTask(
            new \Author\myplugin\task\BroadcastTask($this),
            20 * 60
        );
    }

    public function getDataManager(): DataManager {
        return $this->dataManager;
    }
}
```

Why singletons? Many classes need to call back into the plugin (for config, logger, scheduler). A static getInstance() avoids passing $plugin everywhere.

---

Command Class Template

```php
<?php
declare(strict_types=1);

namespace Author\myplugin\command;

use Author\myplugin\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ExampleCommand extends Command {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct(
            "example",
            "Example command",
            "/example <arg>",
            ["ex", "sample"]
        );
        $this->setPermission("myplugin.command.example");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if (!$this->testPermission($sender)) return false;

        if (!$sender instanceof Player) {
            $sender->sendMessage("§cRun this in-game.");
            return true;
        }

        if (empty($args)) {
            $sender->sendMessage("§eUsage: /example <arg>");
            return true;
        }

        $arg = $args[0];
        $sender->sendMessage("§aYou said: §f$arg");
        return true;
    }
}
```

---

Listener Class Template

```php
<?php
declare(strict_types=1);

namespace Author\myplugin\listener;

use Author\myplugin\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $event->setJoinMessage("§a+ §f" . $player->getName());
    }

    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $event->setQuitMessage("§c- §f" . $player->getName());
    }
}
```

Naming: The method name doesn't matter — PocketMine reads the type-hint to know which event to listen for.

---

Manager Class Template

```php
<?php
declare(strict_types=1);

namespace Author\myplugin\manager;

use Author\myplugin\Main;
use pocketmine\player\Player;

class DataManager {

    private Main $plugin;
    private array $cache = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->load();
    }

    public function get(Player $player, string $key, mixed $default = null): mixed {
        return $this->cache[$player->getName()][$key] ?? $default;
    }

    public function set(Player $player, string $key, mixed $value): void {
        $this->cache[$player->getName()][$key] = $value;
    }

    public function add(Player $player, string $key, int $amount): void {
        $this->cache[$player->getName()][$key] =
            ($this->cache[$player->getName()][$key] ?? 0) + $amount;
    }

    public function save(): void {
        file_put_contents(
            $this->plugin->getDataFolder() . 'data.json',
            json_encode($this->cache, JSON_PRETTY_PRINT)
        );
    }

    private function load(): void {
        $file = $this->plugin->getDataFolder() . 'data.json';
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            $this->cache = is_array($data) ? $data : [];
        }
    }
}
```

---

Task Class Template

```php
<?php
declare(strict_types=1);

namespace Author\myplugin\task;

use Author\myplugin\Main;
use pocketmine\scheduler\Task;

class BroadcastTask extends Task {

    private Main $plugin;
    private int $counter = 0;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(): void {
        $messages = $this->plugin->getConfig()->get("broadcasts", [
            "§eWelcome to the server!",
            "§aCheck out /shop",
        ]);

        $message = $messages[$this->counter % count($messages)];
        $this->plugin->getServer()->broadcastMessage($message);
        $this->counter++;
    }
}
```

---

Form Class Template

```php
<?php
declare(strict_types=1);

namespace Author\myplugin\form;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class ShopForm {

    public static function open(Player $player): void {
        $form = new SimpleForm([self::class, 'handle']);
        $form->setTitle("Shop");
        $form->setContent("Choose an item:");
        $form->addButton("Sword — 100 coins");
        $form->addButton("Shield — 150 coins");
        $form->addButton("Back");
        $player->sendForm($form);
    }

    public static function handle(Player $player, ?int $data): void {
        if ($data === null) return;

        match ($data) {
            0 => self::buySword($player),
            1 => self::buyShield($player),
            2 => MainMenuForm::open($player),
            default => null,
        };
    }

    private static function buySword(Player $player): void {
        // ...
    }

    private static function buyShield(Player $player): void {
        // ...
    }
}
```

---

Utils Class Template

```php
<?php
declare(strict_types=1);

namespace Author\myplugin\utils;

use pocketmine\player\Player;

final class Message {

    private function __construct() {}

    public static function prefix(): string {
        return "§8[§bMyPlugin§8]§r ";
    }

    public static function send(Player $player, string $text): void {
        $player->sendMessage(self::prefix() . $text);
    }

    public static function success(Player $player, string $text): void {
        self::send($player, "§a" . $text);
    }

    public static function error(Player $player, string $text): void {
        self::send($player, "§c" . $text);
    }
}
```

final + private constructor — prevents instantiation and extension; a common idiom for utility classes.

---

Resources Folder

Everything in resources/ is copied to the plugin's data folder on first run.

```
resources/
├── config.yml
├── data.sql
├── lang/
│   ├── en.yml
│   └── fa.yml
└── icons/
    └── shop.png
```

Access from code:

```php
// Copy to data folder (once)
$this->saveResource("config.yml");     // single file
$this->saveResource("data.sql");
$this->saveResource("lang/en.yml");

// Read from data folder
$config = yaml_parse(file_get_contents(
    $this->getDataFolder() . "config.yml"
));

// Access bundled config directly
$config = $this->getConfig();  // reads config.yml from data folder
```

Never write into resources/ at runtime — it may be read-only inside a .phar.

---

Data Folder

At runtime, PocketMine creates a data folder for each plugin:

```
plugin_data/
└── MyPlugin/
    ├── config.yml
    ├── data.json
    └── lang/
        ├── en.yml
        └── fa.yml
```

Access via:

```php
$folder = $this->getDataFolder();
```

This path is writable and persists across restarts.

---

Database (SQLite)

Schema File

resources/data.sql:

```sql
CREATE TABLE IF NOT EXISTS players (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE NOT NULL,
    coins INTEGER DEFAULT 0,
    level INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Adapter

```php
<?php
declare(strict_types=1);

namespace Author\myplugin\manager;

use Author\myplugin\Main;
use SQLite3;

class Database {

    private SQLite3 $db;

    public function __construct(Main $plugin) {
        $plugin->saveResource("data.sql");
        $this->db = new SQLite3($plugin->getDataFolder() . "data.sqlite");
        $this->db->exec(file_get_contents($plugin->getDataFolder() . "data.sql"));
    }

    public function insert(string $name): void {
        $stmt = $this->db->prepare("INSERT OR IGNORE INTO players (name) VALUES (:name)");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function getCoins(string $name): int {
        $stmt = $this->db->prepare("SELECT coins FROM players WHERE name = :name");
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        return $result ? (int)$result['coins'] : 0;
    }

    public function addCoins(string $name, int $amount): void {
        $stmt = $this->db->prepare("UPDATE players SET coins = coins + :amount WHERE name = :name");
        $stmt->bindValue(":amount", $amount, SQLITE3_INTEGER);
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        $stmt->execute();
    }
}
```

Important: Always use prepared statements with bound parameters. Never concatenate user input into SQL — that's a SQL injection.

---

Best Practices

One Class Per File

Every class goes in its own file, named after the class.

```
✅ command/ExampleCommand.php   → class ExampleCommand
❌ command/Commands.php         → class ExampleCommand + class AdminCommand
```

Type Hints Everywhere

```php
public function addCoins(Player $player, int $amount): bool
```

Not:

```php
public function addCoins($player, $amount)
```

declare(strict_types=1)

At the top of every PHP file. Catches type mismatches early.

Register in onEnable, Not onLoad

onEnable runs after the world is loaded. Register commands, listeners, and tasks there.

Use onDisable for Cleanup

Save data, close database connections, cancel tasks.

Don't Block the Main Thread

Database queries, file I/O, and network calls should be async or in a task.

Never Hardcode Paths

Use getDataFolder() and getResource().

Never global

Pass dependencies through the constructor or use the singleton pattern.

---

Anti-Patterns

Anti-Pattern Problem Fix
All logic in Main Becomes unmaintainable Extract into managers
new Main() anywhere Multiple plugin instances Use Main::getInstance()
Hardcoded file paths Breaks on different servers Use getDataFolder()
Global variables Hard to test Pass as constructor params
var_dump() in production Log spam Use getLogger()->debug()
No type hints Runtime type errors Add type hints
Synchronous DB queries Freezes server Use async tasks
Ignoring null checks Crashes Always check before use

---

Building a .phar

With DevTools

1. Install DevTools.phar in plugins/
2. Start the server
3. Place your plugin folder in plugins/MyPlugin/
4. In the console:

```
> makeplugin MyPlugin
```

This produces plugins/MyPlugin.phar.

With Poggit

Commit your code to GitHub and submit it to Poggit. Poggit builds automatically on every push.

See Dependencies Guide for .poggit.yml examples.

---

Reference

· PocketMine plugin.yml Docs
· Poggit Documentation
· PMMP Source on GitHub

---

Related Guides

· FormAPI Guide
· Dependencies Guide
· PocketMine Setup
· plugin.yml Reference

---

Tools

Use the Plugin Creator tool in PocketMine Tools to generate this structure automatically — choose your features, and it produces the folders, plugin.yml, and starter classes.

---

[Home](README.md) · [Installation](INSTALL.md) · [API](API.md) · [Documentation index](README.md#documentation) · [Contributing](CONTRIBUTING.md)
