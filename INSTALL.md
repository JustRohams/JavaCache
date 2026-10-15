# Installation Guide

Complete setup instructions for PocketMine Tools — from download to first run.

---

## Table of Contents

- [Requirements](#requirements)
- [Quick Install](#quick-install)
- [Apache Setup](#apache-setup)
- [Nginx Setup](#nginx-setup)
- [PHP Built-in Server](#php-built-in-server)
- [Shared Hosting](#shared-hosting)
- [Subfolder Installation](#subfolder-installation)
- [Permissions](#permissions)
- [Font Setup](#font-setup)
- [Sound Setup](#sound-setup)
- [Verifying the Install](#verifying-the-install)
- [Troubleshooting](#troubleshooting)
- [Updating](#updating)
- [Uninstalling](#uninstalling)

---

## Requirements

### Mandatory

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| **PHP** | 8.0 | 8.2 or 8.3 |
| **Web Server** | Apache 2.4 / Nginx 1.18 | Apache 2.4+ |
| **Disk Space** | 5 MB | 20 MB (with fonts) |
| **Browser** | Chrome 100+, Firefox 100+, Safari 15+ | Latest version |

### PHP Extensions

| Extension | Required | Purpose |
|-----------|----------|---------|
| `json` | ✅ Yes | Reading/writing JSON configs |
| `mbstring` | ⚠️ Recommended | Better Unicode for Persian text |
| `zip` | ⚠️ Optional | `.mcpack` download in Manifest Creator |

### Check PHP Version

```bash
php -v
```

Expected output:

```
PHP 8.2.x (cli) ...
```

Check Extensions

```bash
php -m | grep -E 'json|mbstring|zip'
```

Expected:

```
json
mbstring
zip
```

If zip is missing, the .mcpack download will not work — everything else will.

---

Quick Install

Fastest path — works on any machine with PHP 8.0+.

```bash
# 1. Download
git clone https://github.com/JustRohams/PocketMineTools.git
cd PocketMineTools

# 2. Start the built-in server
php -S localhost:8000

# 3. Open in your browser
# http://localhost:8000
```

Done. No build step, no database, no configuration file to create.

---

Apache Setup

Step 1 — Enable Required Modules

```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod deflate
sudo a2enmod expires
sudo systemctl restart apache2
```

Step 2 — Create a Virtual Host

Create /etc/apache2/sites-available/pocketmine-tools.conf:

```apache
<VirtualHost *:80>
    ServerName pocketmine-tools.local
    DocumentRoot /var/www/pocketmine-tools

    <Directory /var/www/pocketmine-tools>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/pmtools-error.log
    CustomLog ${APACHE_LOG_DIR}/pmtools-access.log combined
</VirtualHost>
```

Step 3 — Enable the Site

```bash
sudo a2ensite pocketmine-tools.conf
sudo systemctl reload apache2
```

Step 4 — Add Local Hostname (optional)

Edit /etc/hosts:

```
127.0.0.1   pocketmine-tools.local
```

Now visit: http://pocketmine-tools.local

Step 5 — Verify .htaccess

The included .htaccess handles clean URLs, security headers, and caching. If it is missing, the site still works — but URLs like /form-creator will 404. Use /?page=form-creator instead.

---

Nginx Setup

Nginx ignores .htaccess, so you must add these rules manually.

Create /etc/nginx/sites-available/pocketmine-tools:

```nginx
server {
    listen 80;
    server_name pocketmine-tools.local;
    root /var/www/pocketmine-tools;
    index index.php;

    # Clean URLs
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Block sensitive folders
    location ~ ^/(config|includes|languages|data|docs)/ {
        deny all;
        return 403;
    }

    # Cache static assets
    location ~* \.(css|js|woff2?|ttf|otf|png|jpg|jpeg|gif|svg|ico|mp3)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }

    # Security headers
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Gzip
    gzip on;
    gzip_types text/css application/javascript application/json image/svg+xml;
    gzip_min_length 1024;
}
```

Enable and reload:

```bash
sudo ln -s /etc/nginx/sites-available/pocketmine-tools /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

Adjust php8.2-fpm.sock to match your installed PHP version.

---

PHP Built-in Server

Ideal for local development.

```bash
cd pocketmine-tools
php -S localhost:8000
```

Or on a specific interface:

```bash
php -S 0.0.0.0:8000
```

Or with a router script to emulate .htaccess:

Create router.php:

```php
<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $uri;

if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

if ($uri !== '/' && !str_contains($uri, '.')) {
    $_GET['page'] = ltrim($uri, '/');
}

require __DIR__ . '/index.php';
```

Then run:

```bash
php -S localhost:8000 router.php
```

Now clean URLs work locally too.

---

Shared Hosting

Most shared hosts (cPanel, DirectAdmin, Plesk) work out of the box.

Steps

1. Upload all files to public_html/ via FTP or File Manager
2. Set permissions — folders to 755, files to 644
3. Ensure data/ is writable (755 or 775)
4. Verify PHP 8.0+ is selected in your hosting panel
5. Enable mod_rewrite if using clean URLs (usually on by default)

If You're in a Subfolder

Change base_url in config/app.php:

```php
'base_url' => '/pmtools/',
```

And update RewriteBase in .htaccess:

```apache
RewriteBase /pmtools/
```

---

Subfolder Installation

If installing at https://example.com/pmtools/ instead of the domain root:

1. Update config/app.php

```php
'base_url' => '/pmtools/',
```

2. Update .htaccess

```apache
RewriteBase /pmtools/
```

3. Update robots.txt

```
Sitemap: /pmtools/sitemap.xml
```

4. Update sitemap.xml

Change all <loc> entries:

```xml
<loc>/pmtools/?page=form-creator</loc>
```

Everything else works without changes.

---

Permissions

The following folders must be writable by the web server:

Folder Permission Purpose
data/ 755 or 775 Store uuid-map.json, cache
data/logs/ 755 or 775 Error logs

Linux / macOS

```bash
chmod -R 755 data/
chmod -R 755 assets/
chmod 644 *.php
```

If Running as www-data

```bash
sudo chown -R www-data:www-data data/
sudo chmod -R 775 data/
```

Windows

Right-click → Properties → Security → Edit → give IIS_IUSRS Modify permission.

Test Permissions

Create test-write.php in the root:

```php
<?php
$test = __DIR__ . '/data/.write-test';
$ok = @file_put_contents($test, 'ok') !== false;
if ($ok) @unlink($test);
echo $ok ? '✅ Writable' : '❌ Not writable';
```

Open it in the browser, then delete the file afterward.

---

Font Setup

Fonts load from Google Fonts by default. To self-host (for offline use or privacy):

1. Navigate to the Fonts Folder

```bash
cd assets/fonts
```

2. Download Fonts

See assets/fonts/README.md for detailed instructions. Quick version:

```bash
# Vazirmatn (Persian)
curl -L -o Vazirmatn-Regular.woff2 \
  "https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/fonts/webfonts/Vazirmatn-Regular.woff2"

# JetBrains Mono (Code)
curl -L -o JetBrainsMono-Regular.woff2 \
  "https://cdn.jsdelivr.net/gh/JetBrains/JetBrainsMono@v2.304/fonts/webfonts/JetBrainsMono-Regular.woff2"
```

3. Add Preload (Optional)

In pages/partials/head.php, add before the stylesheet links:

```html
<link rel="preload" href="<?= e(asset('fonts/Vazirmatn-Regular.woff2')) ?>"
      as="font" type="font/woff2" crossorigin>
```

4. Verify

Open DevTools → Network → filter font. You should see the .woff2 files loaded with status 200.

---

Sound Setup

The site ships with UI sounds enabled by default. Two options:

Option A — Use Existing Sounds

If you have .mp3 files ready, place them in assets/sounds/:

```
assets/sounds/
├── click.mp3
├── hover.mp3
├── success.mp3
├── error.mp3
└── toggle.mp3
```

Option B — Download Google Material Sounds

Google's official Material sound pack is available via npm:

```bash
npm install material-sounds
cp node_modules/material-sounds/*.mp3 assets/sounds/
```

Or manually download from Google Material Design Sound Resources.

Option C — Disable Sounds

In config/app.php:

```php
'features' => [
    'sounds' => false,
],
```

Users can also toggle sounds in-browser via localStorage.

---

Verifying the Install

1. Open the Installer

Visit:

```
http://your-server/install.php
```

You will see a checklist:

```
✓ PHP >= 8.0
✓ ext-json
✓ ext-mbstring
✓ ext-zip (optional)
✓ data/ writable
✓ config/ exists
✓ includes/ exists
✓ pages/ exists
```

2. Test Each Tool

URL Expected
/ Home page with 9 tool cards
/?page=form-creator Form builder loads
/?page=uuid-generator UUID generator loads
/?lang=fa Persian UI with RTL layout
/?page=does-not-exist Custom 404 page

3. Test the API

```bash
curl "http://your-server/api/uuid.php?action=random"
```

Expected:

```json
{
  "ok": true,
  "action": "random",
  "uuid": "d3b07384-d9a1-4c1e-9f0b-2c8e7a6f5d4c"
}
```

4. Delete the Installer

Once verified, delete install.php:

```bash
rm install.php
```

---

Troubleshooting

Blank White Page

Cause: PHP fatal error.

Fix:

1. Edit config/app.php and set 'debug' => true
2. Reload the page — the error will now appear
3. Check data/logs/app.log if enabled

500 Internal Server Error

Cause: Usually a bad .htaccess directive or missing module.

Fix:

1. Temporarily rename .htaccess to .htaccess.bak
2. Reload the page — if it works, the issue is in .htaccess
3. Enable required modules: a2enmod rewrite headers
4. Check the server error log: /var/log/apache2/error.log

Clean URLs Return 404

Cause: mod_rewrite not enabled or .htaccess ignored.

Fix (Apache):

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Ensure AllowOverride All is set in your vhost.

Fix (Nginx): Add the try_files rule from the Nginx Setup section.

Persian Text Renders Wrong

Cause: Missing mbstring extension or wrong charset.

Fix:

```bash
sudo apt install php8.2-mbstring
sudo systemctl restart apache2
```

Ensure charset = utf-8 in .editorconfig.

Fonts Don't Load

Cause: CORS or wrong path.

Fix:

1. Check DevTools → Network → font requests
2. Ensure Access-Control-Allow-Origin: * is set for fonts
3. Verify the path is relative in CSS: url('Vazirmatn-Regular.woff2')

Sounds Don't Play

Cause: Browser autoplay policy blocks audio before user interaction.

Fix: This is expected — sounds start playing after the first click. Nothing to configure.

.mcpack Download Fails

Cause: ext-zip not installed.

Fix:

```bash
sudo apt install php8.2-zip
sudo systemctl restart apache2
```

data/ Not Writable

Fix:

```bash
chmod -R 775 data/
sudo chown -R www-data:www-data data/
```

Composer Autoload Errors

Cause: Composer not used at runtime.

Fix: You don't need Composer to run PocketMine Tools. Ignore any autoload warnings.

---

Updating

From Git

```bash
cd pocketmine-tools
git pull origin main
```

From ZIP

1. Download the latest release
2. Extract to a temp folder
3. Preserve your data:
   · Copy data/uuid-map.json (if you care about UUID mappings)
   · Copy config/app.php (if you customized it)
4. Overwrite the installation folder
5. Restore the preserved files

After Updating

· Clear browser cache (Ctrl+Shift+R)
· Check the CHANGELOG for breaking changes
· Revisit /install.php if you still have it

---

Uninstalling

Full Removal

```bash
cd ..
rm -rf pocketmine-tools
```

Apache

```bash
sudo a2dissite pocketmine-tools.conf
sudo rm /etc/apache2/sites-available/pocketmine-tools.conf
sudo systemctl reload apache2
```

Nginx

```bash
sudo rm /etc/nginx/sites-enabled/pocketmine-tools
sudo rm /etc/nginx/sites-available/pocketmine-tools
sudo systemctl reload nginx
```

Database

None — PocketMine Tools uses JSON files only. Nothing else to clean up.

---

Next Steps

· Read the README.md for a full feature overview
· See API.md for the JSON endpoint reference
· Check CONTRIBUTING.md if you plan to contribute
· Browse the docs/ folder for tool-specific guides

---

Need help? Open an issue: https://github.com/JustRohams/PocketMineTools

---

[Home](README.md) · [Installation](INSTALL.md) · [API](API.md) · [Documentation index](README.md#documentation) · [Contributing](CONTRIBUTING.md)
