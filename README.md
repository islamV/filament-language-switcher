# Filament Language Switcher

<p align="center">
    <img src="https://raw.githubusercontent.com/islamv/filament-language-switcher/main/art/banner.png" alt="Filament Language Switcher Banner" width="100%" style="border-radius: 12px; margin-bottom: 20px;">
</p>

<p align="center">
    <a href="https://packagist.org/packages/islamv/filament-language-switcher"><img src="https://img.shields.io/packagist/v/islamv/filament-language-switcher.svg?style=flat-square" alt="Latest Version on Packagist"></a>
    <a href="https://packagist.org/packages/islamv/filament-language-switcher"><img src="https://img.shields.io/packagist/dt/islamv/filament-language-switcher.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.2%20|%208.3%20|%208.4%20|%208.5-777bb4.svg?style=flat-square" alt="PHP Version"></a>
    <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-v4%20|%20v5-fdae4b.svg?style=flat-square" alt="Filament Version"></a>
    <a href="LICENSE.md"><img src="https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square" alt="Software License"></a>
</p>

An elegant, blazing-fast, and comprehensive **Language Switcher Plugin for Filament Panels** with 80+ built-in world languages, automatic country flags, multi-layered session/cookie persistence, seamless RTL/LTR layout transitions, and multi-subdomain support.

---

## 🌟 Key Features

- 🌍 **80+ Built-in World Languages & Flags**: Instant support for all major world languages (Arabic, English, French, Spanish, German, Turkish, Chinese, Japanese, and 70+ more) with native labels and high-resolution SVG flags out of the box.
- 🔄 **Intelligent Multi-Layered Locale Resolution**: Resolves and synchronizes locale across Active Sessions, Started Session Stores, Encrypted Cookies, Raw Cookies, and Custom Subdomain sessions.
- 🧭 **RTL / LTR Automatic Adaptation**: Fully switches text direction (`dir="rtl"` / `dir="ltr"`) and Filament layout styles instantly on language switch.
- 🎨 **Modern & Sleek UI**:
  - Circular country flag button trigger with smooth hover glow and tooltip.
  - High-contrast dropdown items with active checkmark indicators.
  - Native redirect anchors ensuring zero Alpine JS runtime errors.
- 🔐 **Auth Page Floating Switcher**: Optionally display a neat floating language switcher on Login, Registration, and Password Reset screens.
- ⚙️ **Zero Configuration Needed**: Auto-discovers installed Filament/Laravel translations or allows fully customized locale lists and flag mappings.
- ⚡ **Multi-Panel & Multi-Domain Ready**: Works seamlessly across distinct panel paths (`/admin`, `/app`, `/merchant`, `/portal`) and dedicated subdomains.

---

## 📦 Requirements

- **PHP**: `^8.2`, `^8.3`, `^8.4`, or `^8.5`
- **Laravel Framework**: `^11.0`, `^12.0`, or `^13.0`
- **Filament**: `^4.0` or `^5.0`

---

## 🚀 Installation

Install the package via Composer:

```bash
composer require islamv/filament-language-switcher
```

### 🔧 Local / Monorepo Path Repository Setup

If you are developing or testing the plugin locally from a custom directory, add it to your project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/media/islam/web/filament-plugins/filament-language-switcher",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "islamv/filament-language-switcher": "*@dev"
    }
}
```

Then run:

```bash
composer update islamv/filament-language-switcher
```

---

## ⚙️ Configuration & Panel Setup

Register the plugin inside your Filament Panel Provider (e.g. `AdminPanelProvider.php`, `MerchantPanelProvider.php`, etc.):

### 1. Default Setup (Auto-Discovers Installed Locales)

```php
use Islamv\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentLanguageSwitcherPlugin::make()
                ->rememberLocale(30)
                ->showOnAuthPages(),
        ]);
}
```

### 2. Explicit Locales Setup

You can define specific languages with custom names or flags:

```php
use Islamv\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentLanguageSwitcherPlugin::make()
                ->locales([
                    ['code' => 'en', 'name' => 'English', 'flag' => 'us'],
                    ['code' => 'ar', 'name' => 'العربية', 'flag' => 'ly'],
                    ['code' => 'fr', 'name' => 'Français', 'flag' => 'fr'],
                    ['code' => 'tr', 'name' => 'Türkçe', 'flag' => 'tr'],
                ])
                ->rememberLocale(60) // Remember locale cookie for 60 days
                ->showOnAuthPages(), // Display floating switcher on auth screens
        ]);
}
```

### 3. Simplified String Syntax (Auto-Resolves Name & Flag)

Because the plugin includes a comprehensive 80+ language database, you can simply pass locale codes:

```php
FilamentLanguageSwitcherPlugin::make()
    ->locales(['en', 'ar', 'fr', 'de', 'es', 'it', 'tr', 'ru', 'zh_CN'])
    ->rememberLocale()
```

---

## 🛠️ Plugin Options Reference

| Method | Type | Description |
| :--- | :--- | :--- |
| `locales(array\|Closure $locales)` | `array\|Closure` | List of allowed locales (strings or arrays with `code`, `name`, `flag`). |
| `excludeLocales(array $locales)` | `array` | List of locale codes to hide from the switcher. |
| `rememberLocale(?int $days = null)` | `int` | Persists chosen locale in a cookie (`null`/`0` = forever, or number of days). |
| `showFlags(bool $show = true)` | `bool` | Enables or disables country flag badges in trigger and dropdown items. |
| `circularFlags(bool $circular = true)` | `bool` | Toggles circular badge styling for flags. |
| `showOnAuthPages(bool $show = true)` | `bool` | Automatically renders a floating language switcher on login/register/reset pages. |
| `renderHook(string $hook)` | `string` | Customizes where the language switcher renders in the Filament topbar (defaults to `USER_MENU_BEFORE`). |

---

## 📁 Publishing Configuration & Views

### Publish Config File (Optional)

```bash
php artisan vendor:publish --tag="filament-language-switcher-config"
```

This creates `config/filament-language-switcher.php`:

```php
return [
    'remember_days' => 30,
    'show_flags' => true,
    'show_on_auth_pages' => true,
    'locales' => [
        ['code' => 'en', 'name' => 'English', 'flag' => 'us'],
        ['code' => 'ar', 'name' => 'العربية', 'flag' => 'ly'],
    ],
];
```

### Publish Blade Views (Optional)

```bash
php artisan vendor:publish --tag="filament-language-switcher-views"
```

---

## 🔔 Events

The plugin dispatches a `LocaleChanged` event whenever a user changes language:

```php
use Islamv\FilamentLanguageSwitcher\Events\LocaleChanged;
use Illuminate\Support\Facades\Event;

Event::listen(function (LocaleChanged $event) {
    // Access changed locales:
    $new = $event->newLocale;
    $old = $event->oldLocale;
    
    // Example: Save preferred locale to authenticated user profile
    if (auth()->check()) {
        auth()->user()->update(['preferred_locale' => $new]);
    }
});
```

---

## 🌐 Supported Languages Out of the Box

The plugin contains built-in mappings for all standard language codes, including:

| Code | Native Name | Default Flag |
| :--- | :--- | :--- |
| `ar` / `ar_LY` | العربية (ليبيا) | 🇱🇾 `ly` |
| `ar_SA` | العربية (السعودية) | 🇸🇦 `sa` |
| `ar_EG` | العربية (مصر) | 🇪🇬 `eg` |
| `ar_AE` | العربية (الإمارات) | 🇦🇪 `ae` |
| `en` / `en_US` | English (US) | 🇺🇸 `us` |
| `en_GB` | English (UK) | 🇬🇧 `gb` |
| `fr` | Français | 🇫🇷 `fr` |
| `es` | Español | 🇪🇸 `es` |
| `de` | Deutsch | 🇩🇪 `de` |
| `it` | Italiano | 🇮🇹 `it` |
| `tr` | Türkçe | 🇹🇷 `tr` |
| `ru` | Русский | 🇷🇺 `ru` |
| `zh_CN` | 简体中文 | 🇨🇳 `cn` |
| `ja` | 日本語 | 🇯🇵 `jp` |
| `ko` | 한국어 | 🇰🇷 `kr` |
| `pt_BR` | Português (Brasil) | 🇧🇷 `br` |
| `nl` | Nederlands | 🇳🇱 `nl` |
| `fa` | فارسی | 🇮🇷 `ir` |
| `ur` | اردو | 🇵🇰 `pk` |
| *+60 more* | *Full ISO-639 support* | *Automatic* |

---

## 🧪 Testing

Run test suite:

```bash
composer test
```

Run code styling:

```bash
composer lint
```

---

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 👤 Author

- **Islam Abdelkarim** - [GitHub](https://github.com/islamv)
