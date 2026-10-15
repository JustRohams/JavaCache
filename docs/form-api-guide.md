# FormAPI Guide

Complete guide to building PocketMine forms with [jojoe77777/FormAPI](https://github.com/jojoe77777/FormAPI).

---

## What Is FormAPI?

FormAPI is a library for PocketMine-MP that lets you build **native Bedrock UI forms** using PHP objects instead of raw JSON. It provides three form types:

| Type | Use case | Returns |
|------|----------|---------|
| **SimpleForm** | Menu with a list of buttons | `int` (button index) or `null` |
| **ModalForm** | Binary choice (Yes / No) | `bool` or `null` |
| **CustomForm** | Multiple input fields | `array` of values or `null` |

Every form sends JSON to the client, which renders it natively. When the player clicks a button or submits input, the client sends the response back, and FormAPI calls your callback.

---

## Installation

### Via Virion (Poggit — recommended)

In your plugin's `.poggit.yml`:

```yaml
projects:
  MyPlugin:
    path: ""
    libs:
      src: src
    virions:
      - FormAPI
```

Poggit will automatically clone and build FormAPI into your plugin's .phar.

Via Composer

```bash
composer require jojoe77777/formapi
```

Then require vendor/autoload.php in your Main.php:

```php
require_once __DIR__ . '/../vendor/autoload.php';
```

Manual

1. Download the FormAPI source from GitHub
2. Place it in virions/FormAPI/ inside your plugin
3. Reference it with use jojoe77777\FormAPI\SimpleForm;

---

SimpleForm

A menu-style form with a list of buttons.

Full Example

```php
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

$form = new SimpleForm(function (Player $player, ?int $data) {
    if ($data === null) {
        // Player closed the form
        return;
    }

    switch ($data) {
        case 0:
            $player->sendMessage("You picked Yes");
            break;
        case 1:
            $player->sendMessage("You picked No");
            break;
        case 2:
            $player->sendMessage("Goodbye!");
            break;
    }
});

$form->setTitle("Main Menu");
$form->setContent("Choose an option:");
$form->addButton("Yes");
$form->addButton("No");
$form->addButton("Exit");

$player->sendForm($form);
```

Adding Icons to Buttons

```php
$form->addButton("Shop", 0, "textures/items/emerald");
$form->addButton("Info", 0, "textures/ui/info");
$form->addButton("Exit", 0, "textures/ui/cancel");
```

The second parameter is the icon type:

Value Meaning
0 Texture path (from a resource pack)
1 URL (Java only — not supported on Bedrock)

Since Bedrock doesn't support URL icons, always pass 0.

Common Texture Paths

Path Icon
textures/ui/icon_recipe_item Generic item
textures/ui/icon_recipe_nature Leaf
textures/ui/icon_recipe_equipment Sword
textures/items/emerald Emerald
textures/items/diamond Diamond
textures/items/gold_ingot Gold ingot
textures/ui/gear Gear
textures/ui/back Back arrow
textures/ui/cancel X mark
textures/ui/confirm Checkmark
textures/ui/info Info bubble

---

ModalForm

A form with exactly two buttons. Use it for confirmations.

Full Example

```php
use jojoe77777\FormAPI\ModalForm;

$form = new ModalForm(function (Player $player, ?bool $data) {
    if ($data === null) {
        // Player closed the form
        return;
    }

    if ($data === true) {
        $player->sendMessage("You confirmed!");
        // Do the action
    } else {
        $player->sendMessage("You cancelled.");
    }
});

$form->setTitle("Confirm Action");
$form->setContent("Are you sure you want to teleport?");
$form->setButton1("§aYes");
$form->setButton2("§cNo");

$player->sendForm($form);
```

Notes

· setButton1 corresponds to true
· setButton2 corresponds to false
· You cannot add more or fewer buttons
· Use § color codes in button text for visual feedback

---

CustomForm

A form with multiple input fields. Returns an array of values.

Field Types

Method Signature Returns
addLabel (string $text) —
addInput (string $text, string $placeholder, string $default) string
addToggle (string $text, bool $default) bool
addDropdown (string $text, array $options, int $default) int (index)
addSlider (string $text, float $min, float $max, float $step, float $default) float
addStepSlider (string $text, array $steps, int $default) int (index)

Full Example

```php
use jojoe77777\FormAPI\CustomForm;

$form = new CustomForm(function (Player $player, ?array $data) {
    if ($data === null) return;

    $name    = $data[0];   // addInput  → string
    $enabled = $data[1];   // addToggle → bool
    $mode    = $data[2];   // addDropdown → int (index)
    $amount  = $data[3];   // addSlider → float
    $quality = $data[4];   // addStepSlider → int (index)

    $modes = ["Survival", "Creative", "Adventure"];
    $player->sendMessage("Name: $name");
    $player->sendMessage("Enabled: " . ($enabled ? "yes" : "no"));
    $player->sendMessage("Mode: " . $modes[$mode]);
    $player->sendMessage("Amount: $amount");
});

$form->setTitle("Player Settings");
$form->addLabel("Fill in the details below:");
$form->addInput("Name", "Enter your name", "Steve");
$form->addToggle("Enable PvP", true);
$form->addDropdown("Gamemode", ["Survival", "Creative", "Adventure"], 0);
$form->addSlider("View distance", 4, 32, 2, 12);
$form->addStepSlider("Difficulty", ["Peaceful", "Easy", "Normal", "Hard"], 2);

$player->sendForm($form);
```

Field Index Mapping

The $data array indices match the order of add* calls:

```
addLabel(...)       → (no index — labels don't return values)
addInput(...)       → $data[0]
addToggle(...)      → $data[1]
addDropdown(...)    → $data[2]
addSlider(...)      → $data[3]
addStepSlider(...)  → $data[4]
```

Important: addLabel does not consume an index. Labels are display-only.

---

Getting the Player

The closure receives a Player object as the first argument. Use it for context:

```php
$form = new SimpleForm(function (Player $player, ?int $data) {
    if ($data === null) return;

    $name  = $player->getName();
    $world = $player->getWorld()->getFolderName();
    $pos   = $player->getPosition();

    $player->sendMessage("Hello $name!");
    $player->sendMessage("You're in $world at " . (int)$pos->x . ", " . (int)$pos->z);
});
```

---

Handling Null (Form Closed)

Always check $data === null first. If the player closes the form without clicking, $data will be null. Skipping the check causes a PHP type error.

```php
// ✅ Correct
if ($data === null) return;

// ❌ Wrong — will crash
switch ($data) { ... }
```

---

Nested Forms

You can open a new form from inside a callback. This is how menus work.

```php
function showMainMenu(Player $player): void {
    $form = new SimpleForm(function (Player $player, ?int $data) {
        if ($data === null) return;

        match ($data) {
            0 => showShopMenu($player),
            1 => showSettingsMenu($player),
            default => null,
        };
    });
    $form->setTitle("Main Menu");
    $form->setContent("Choose:");
    $form->addButton("Shop");
    $form->addButton("Settings");
    $player->sendForm($form);
}

function showShopMenu(Player $player): void {
    $form = new SimpleForm(function (Player $player, ?int $data) {
        if ($data === null) return;
        // handle shop selection
    });
    $form->setTitle("Shop");
    $form->addButton("Buy");
    $form->addButton("Sell");
    $form->addButton("Back");
    $player->sendForm($form);
}
```

---

Opening a Form Later

If you need to open a form after a delay (e.g. after the current form closes), schedule a task:

```php
use pocketmine\scheduler\ClosureTask;

Main::getInstance()->getScheduler()->scheduleDelayedTask(
    new ClosureTask(function () use ($player) {
        showMainMenu($player);
    }),
    20   // 1 second later (20 ticks)
);
```

Do not call $player->sendForm() directly inside the callback — the client may still be closing the previous form.

---

Passing Data to the Callback

Use use to capture variables:

```php
$shopItems = [
    ["name" => "Sword",  "price" => 100],
    ["name" => "Shield", "price" => 150],
];

$form = new SimpleForm(function (Player $player, ?int $data) use ($shopItems) {
    if ($data === null) return;

    $item = $shopItems[$data];
    $player->sendMessage("You bought {$item['name']} for {$item['price']}");
});

$form->setTitle("Shop");
foreach ($shopItems as $item) {
    $form->addButton($item['name'] . " — " . $item['price']);
}
$player->sendForm($form);
```

---

Using Managers

For larger plugins, extract form logic into dedicated classes:

```php
namespace MyPlugin\form;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class ShopForm {

    public static function open(Player $player): void {
        $form = new SimpleForm([self::class, 'handle']);
        $form->setTitle("Shop");
        $form->addButton("Buy");
        $form->addButton("Sell");
        $player->sendForm($form);
    }

    public static function handle(Player $player, ?int $data): void {
        if ($data === null) return;

        match ($data) {
            0 => self::openBuy($player),
            1 => self::openSell($player),
        };
    }

    private static function openBuy(Player $player): void {
        // ...
    }

    private static function openSell(Player $player): void {
        // ...
    }
}
```

Call with:

```php
ShopForm::open($player);
```

---

Tips

· ✅ Always type-hint ?int, ?bool, or ?array on the closure
· ✅ Check for null first, before any other logic
· ✅ Use switch or match for SimpleForm — more readable than chained if
· ✅ Cache expensive data outside the closure via use
· ✅ Use §a (green) and §c (red) in ModalForm buttons
· ✅ Give buttons descriptive text
· ❌ Never call sendForm() from inside the callback directly — schedule it
· ❌ Never build forms on every tick
· ❌ Don't add labels expecting them to appear in $data

---

Common Mistakes

Mistake Symptom Fix
Missing null check PHP type error on close Add if ($data === null) return;
Using switch without null check Crash Return early
Sending form from inside callback Client ignores it Schedule a task
Wrong icon type (1 on Bedrock) Icons don't show Use 0
Mismatched field indices Wrong values Count add* calls carefully
Adding labels expecting index Off-by-one errors Labels don't take an index
Reusing $form variable for nested Wrong form opens Use a new variable

---

Debugging

Add temporary logging inside the callback to see what's happening:

```php
$form = new SimpleForm(function (Player $player, ?int $data) {
    var_dump($data);                          // shows null or int
    Main::getInstance()->getLogger()->info("Form response: " . var_export($data, true));
    if ($data === null) return;
    // ...
});
```

Remember to remove debug output before shipping.

---

Complete Working Example

A full self-contained plugin that shows a menu when a player joins:

```php
<?php
declare(strict_types=1);

namespace MyPlugin;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    protected function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $this->openMenu($event->getPlayer());
    }

    private function openMenu(Player $player): void {
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) return;

            switch ($data) {
                case 0:
                    $player->sendMessage("§aYou chose option 1!");
                    break;
                case 1:
                    $player->sendMessage("§eYou chose option 2!");
                    break;
                case 2:
                    $player->sendMessage("§cGoodbye!");
                    break;
            }
        });

        $form->setTitle("Welcome, " . $player->getName());
        $form->setContent("Please choose an option below.");
        $form->addButton("Option 1", 0, "textures/items/emerald");
        $form->addButton("Option 2", 0, "textures/items/diamond");
        $form->addButton("Exit", 0, "textures/ui/cancel");

        $player->sendForm($form);
    }
}
```

---

---

[Home](README.md) · [Installation](INSTALL.md) · [API](API.md) · [Documentation index](README.md#documentation) · [Contributing](CONTRIBUTING.md)
