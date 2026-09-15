# Filament Language Switcher

An elegant, robust, and customizable Language Switcher plugin for Filament panels with full RTL and multi-subdomain support.

## Features

- 🌍 Seamless language switching with immediate redirect and state persistence.
- 🔄 Multi-layered locale resolution (Session, Encrypted Cookies, Raw Cookies, Session Store).
- 🧭 Automatic RTL / LTR layout switching and Filament translation integration.
- 🎨 Beautiful, modern circular flag triggers and checkmark-highlighted active dropdown items.
- 🛡️ Subdomain and multi-panel aware.
- 🔐 Auth page floating switcher support (Login, Registration, Password Reset).

## Installation

```bash
composer require islamv/filament-language-switcher
```

## Usage

Register the plugin in your Filament Panel Provider:

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
                ])
                ->rememberLocale(30)
                ->showOnAuthPages(),
        ]);
}
```

## Options

- `locales(array $locales)`: Define available languages with `code`, `name`, and optional `flag`.
- `rememberLocale(int $days = 0)`: Persist selected language in a cookie (forever or for `N` days).
- `showFlags(bool $show = true)`: Show or hide flag badges.
- `showOnAuthPages(bool $show = true)`: Render floating language switcher on login/register pages.
- `renderHook(string $hook)`: Customize the render hook where the switcher appears.

## License

The MIT License (MIT).
