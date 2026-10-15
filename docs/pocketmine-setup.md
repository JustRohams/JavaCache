# PocketMine-MP Setup Guide

Complete guide to setting up a PocketMine-MP development environment.

---

## Overview

This guide walks you through installing and running PocketMine-MP locally so you can test plugins before publishing them.

**What you'll have at the end:**

- A running PocketMine-MP server on your machine
- The ability to load and test custom plugins
- A workflow for building `.phar` files
- Optional: Poggit integration for CI builds

**Time required:** 15–30 minutes.

---

## Requirements

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| **PHP** | 8.0 | 8.2 or 8.3 |
| **RAM** | 1 GB | 2 GB+ |
| **Disk** | 500 MB | 2 GB |
| **OS** | Windows 10, macOS 11, Ubuntu 20.04 | Latest |

**Required PHP extensions:**

- `json`
- `mbstring`
- `sqlite3`
- `openssl`
- `curl`
- `zip`
- `phar`
- `yaml` (for parsing config files)

---

## Method 1 — Pre-built Binary (Easiest)

The PocketMine team ships pre-compiled binaries with PHP bundled.

### Linux / macOS

```bash
# Download the latest stable
curl -L -o PocketMine-MP.phar \
  https://github.com/pmmp/PocketMine-MP/releases/latest/download/PocketMine-MP.phar

# Download the start script
curl -L -o start.sh \
  https://github.com/pmmp/PocketMine-MP/releases/latest/download/start.sh

# Make executable
chmod +x start.sh

# Run
./start.sh
```

Windows

1. Go to the PocketMine-MP Releases page
2. Download:
   · PocketMine-MP.phar
   · start.cmd
   · bin_windows (extract into the same folder)
3. Double-click start.cmd

First-Run Setup

On first launch, PocketMine asks:

```
Please choose a language:
1. English
2. Español
...
```

Then:

```
Do you want to accept the license? (y/n)
Do you want to enable a static PHP binary? (y/n)
Please choose a server name: My Server
Please choose a port: 19132
```

After setup, the server starts and shows:

```
[INFO]: Starting Minecraft: Bedrock Edition server version v1.20.x
[INFO]: Server started on 0.0.0.0:19132
```

Stop with Ctrl+C or /stop in the console.

---

Method 2 — Build from Source

For advanced users who want to modify PocketMine itself.

Prerequisites

Install PHP 8.2+ with the extensions listed above.

Ubuntu / Debian:

```bash
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-sqlite3 \
    php8.2-curl php8.2-zip php8.2-xml php8.2-yaml php8.2-opcache \
    php8.2-dev composer git
```

macOS (Homebrew):

```bash
brew install php@8.2 composer git
```

Windows:

Download PHP 8.2 from windows.php.net and enable the extensions in php.ini.

Clone and Build

```bash
git clone https://github.com/pmmp/PocketMine-MP.git
cd PocketMine-MP

composer install
```

Run

```bash
php src/PocketMine.php --no-wizard --disable-ansi
```

Or use the wizard:

```bash
php src/PocketMine.php
```

---

Method 3 — Docker

For an isolated, portable environment.

Create docker-compose.yml:

```yaml
version: '3.8'
services:
  pocketmine:
    image: pmmp/pocketmine-mp:latest
    container_name: pmmp
    ports:
      - "19132:19132/udp"
    volumes:
      - ./server:/data
    stdin_open: true
    tty: true
    restart: unless-stopped
```

Run:

```bash
docker-compose up -d
docker-compose logs -f pocketmine
```

Connect on localhost:19132.

---

Folder Structure

After the first run, your server folder looks like:

```
PocketMine-MP/
├── PocketMine-MP.phar
├── start.sh (or start.cmd)
├── bin/                     ← PHP binaries (if bundled)
├── plugins/                 ← Your plugins go here
├── worlds/                  ← Generated worlds
├── players/                 ← Per-player data
├── resource_packs/          ← Client resource packs
├── server.properties        ← Main server config
├── pocketmine.yml           ← PocketMine settings
├── ops.txt                  ← Operators
├── banned-ips.txt
├── banned-players.txt
├── permissions.yml
├── whitelist.txt
└── server.log
```

---

Installing Plugins

From Poggit (Recommended)

1. Go to Poggit or Poggit Plugins
2. Find the plugin you want
3. Download the .phar
4. Drop it into plugins/
5. Restart the server (or use reload in the console)

From Source

If you have a plugin's source folder:

```bash
cd plugins/
mkdir MyPlugin/
cd MyPlugin/
# copy plugin.yml, src/, resources/ here
```

PocketMine will load the folder as a DevTools plugin (slower than .phar, but easier to edit).

Building a .phar Manually

Install DevTools:

```bash
# Download DevTools.phar
curl -L -o plugins/DevTools.phar \
  https://poggit.pmmp.io/r/DevTools.phar

# Restart server
```

Then in the server console:

```
> makeplugin MyPlugin
```

This builds plugins/MyPlugin.phar from the folder.

---

Server Configuration

server.properties

Key settings:

```properties
server-name=My PocketMine Server
server-port=19132
gamemode=survival
difficulty=normal
max-players=20
online-mode=true
white-list=false
enable-query=true
enable-rcon=false
motd=§aWelcome §fto §6My Server
```

online-mode=true requires players to be authenticated with Xbox Live. Set false only for local testing.

pocketmine.yml

Advanced settings, including:

· Async worker counts
· Chunk sending rates
· Anti-cheat options
· Level settings

Generally, leave defaults unless you know what you're changing.

MOTD Colors

The motd= line supports Minecraft color codes:

```properties
motd=§a§lWelcome §r§fto §6My Server
```

See the MOTD Colors Guide for the full palette.

---

Testing Plugins

1. Write Your Plugin

Use PocketMine Tools to generate:

· plugin.yml
· Main.php
· Command classes
· Listener classes

2. Deploy Locally

```bash
cp -r MyPlugin/ /path/to/PocketMine-MP/plugins/
```

3. Start the Server

```bash
./start.sh
```

Watch the console for:

```
[INFO]: Enabling MyPlugin v1.0.0
```

If you see an error instead:

```
[ERROR]: Could not load plugin 'MyPlugin': ...
```

Read the error carefully — it usually points to the exact file and line.

4. Test In-Game

Connect with Minecraft Bedrock:

1. Open Play → Servers → Add Server
2. Server Address: 127.0.0.1 (if playing on the same machine)
3. Port: 19132
4. Join

For LAN testing with another device, use your computer's local IP (e.g. 192.168.1.42:19132).

5. Reload After Changes

In the server console:

```
> reload
```

Or stop and restart the server.

For PHP class changes, a full restart is required — reload doesn't reload compiled classes.

---

Development Workflow

Recommended Setup

```
workspace/
├── server/                     ← PocketMine-MP installation
│   └── plugins/
│       └── MyPlugin/           ← Symlink to your source
└── MyPlugin/                   ← Your plugin source
    ├── plugin.yml
    ├── src/
    └── resources/
```

Creating the Symlink

Linux / macOS:

```bash
cd server/plugins/
ln -s ../../MyPlugin MyPlugin
```

Windows (PowerShell, admin):

```powershell
New-Item -ItemType SymbolicLink -Path "server\plugins\MyPlugin" -Target "MyPlugin"
```

Now you can edit files in MyPlugin/ and they instantly appear in server/plugins/MyPlugin/.

Iteration Loop

1. Edit MyPlugin/src/Main.php
2. Restart server
3. Test in-game
4. Check server.log for errors
5. Repeat

Using DevTools

Load the plugin folder directly (no .phar needed):

```
plugins/MyPlugin/
├── plugin.yml
├── src/
└── resources/
```

PocketMine loads it on startup. This is the fastest iteration loop for development.

---

Publishing to Poggit

1. Push to GitHub

Your plugin must be in a public GitHub repository.

2. Create .poggit.yml

At the repository root:

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
```

3. Submit to Poggit

1. Go to poggit.pmmp.io/ci
2. Sign in with GitHub
3. Click Submit
4. Paste your repository URL
5. Poggit builds it automatically

4. Automate Releases

Poggit builds on every push. To submit a release:

1. Go to your project page on Poggit
2. Click the build you want to release
3. Click Submit Release
4. Fill in version, changelog, and category

The release appears on poggit.pmmp.io/plugins instantly.

---

Common Errors

Error Cause Fix
Could not load plugin 'X' Missing dependency or syntax error Check plugin.yml and PHP syntax
PHP Fatal error Syntax error or missing class Read the file and line number
Call to undefined method Using API that changed Check the PMMP version
Class not found Wrong namespace or missing use Verify namespace matches folder structure
Unknown dependency A depend plugin isn't installed Install it or move to softdepend
Permission denied Wrong file permissions chmod +x start.sh
Port already in use Another server is running Change server-port or stop the other server
Out of memory Too many plugins or low RAM Increase memory-limit in php.ini or start.sh

---

Useful Commands

In the server console:

Command Effect
reload Reload all plugins (not compiled classes)
stop Stop the server cleanly
op <player> Give operator status
deop <player> Remove operator status
whitelist on Enable the whitelist
whitelist add <player> Add a player to the whitelist
plugins List loaded plugins
version Show server version
makeplugin <name> Build a .phar (DevTools required)

In-game:

Command Effect
/op <player> Give op (requires console)
/gamemode creative Change gamemode
/tp <player> Teleport to a player
/plugins List plugins

---

Performance Tips

server.properties

```properties
view-distance=8
tick-radius=4
compression-level=6
```

Lower view distance means less memory and CPU.

pocketmine.yml

```yaml
chunk-sending:
    per-tick: 4
    spawn-threshold: 32

chunk-ticking:
    per-tick: 40
    tick-radius: 3
```

Increase per-tick values for smoother play, at the cost of CPU.

Async Workers

```yaml
settings:
    async-workers: auto
```

auto picks a sensible number based on CPU cores.

---

Updating PocketMine

Backup First

```bash
cp -r server/ server-backup-$(date +%Y%m%d)/
```

Update the .phar

```bash
curl -L -o PocketMine-MP.phar \
  https://github.com/pmmp/PocketMine-MP/releases/latest/download/PocketMine-MP.phar
```

Update Plugins

Check each plugin's page on Poggit for a compatible version. Plugins built for API 4 will not work on API 5 without an update.

Test

Start the server, watch for plugin load errors, then connect in-game.

---

Backing Up

Backup these folders regularly:

```
server/
├── worlds/          ← Your builds and players
├── plugins/         ← Plugin data files
├── players/         ← Per-player data
├── server.properties
├── pocketmine.yml
├── ops.txt
└── whitelist.txt
```

Compress:

```bash
tar -czf backup-$(date +%Y%m%d).tar.gz worlds/ players/ plugins/*/data/ server.properties pocketmine.yml
```

---

External Resources

· PocketMine-MP GitHub
· PocketMine Docs
· Poggit
· Poggit Virions
· Bedrock Wiki
· PMMP Discord

---

Related Guides

· FormAPI Guide
· Plugin Structure
· Dependencies Guide
· Manifest Guide

---

Tools

Use the Plugin Creator tool in PocketMine Tools to scaffold a plugin with the correct folder structure, plugin.yml, and starter classes in seconds.

---

[Home](README.md) · [Installation](INSTALL.md) · [API](API.md) · [Documentation index](README.md#documentation) · [Contributing](CONTRIBUTING.md)
