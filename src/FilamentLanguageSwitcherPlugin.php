<?php

namespace Islamv\FilamentLanguageSwitcher;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use InvalidArgumentException;
use Islamv\FilamentLanguageSwitcher\Http\Middleware\SetLocale;

class FilamentLanguageSwitcherPlugin implements Plugin
{
    protected static ?int $rememberLocaleDays = null;

    protected array|Closure $locales = [];

    protected bool $showFlags = true;

    protected bool $showOnAuthPages = false;

    protected string $renderHook = PanelsRenderHook::USER_MENU_BEFORE;

    public function getId(): string
    {
        return 'filament-language-switcher';
    }

    public static function make(): static
    {
        return new static;
    }

    public function locales(array|Closure $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    public function showFlags(bool $show = true): static
    {
        $this->showFlags = $show;

        return $this;
    }

    public function showOnAuthPages(bool $show = true): static
    {
        $this->showOnAuthPages = $show;

        return $this;
    }

    /**
     * Remember the selected locale in a cookie.
     * Call without arguments to store forever, or specify the number of days.
     */
    public function rememberLocale(?int $days = null): static
    {
        static::$rememberLocaleDays = $days ?? 0;

        return $this;
    }

    public function renderHook(string $hook): static
    {
        $this->renderHook = $hook;

        return $this;
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook(
            name: $this->renderHook,
            hook: fn (): View => $this->renderLanguageSwitcher(),
        );

        if ($this->showOnAuthPages) {
            $authHooks = [
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                PanelsRenderHook::AUTH_REGISTER_FORM_AFTER,
                PanelsRenderHook::AUTH_PASSWORD_RESET_REQUEST_FORM_AFTER,
            ];

            foreach ($authHooks as $authHook) {
                $panel->renderHook(
                    name: $authHook,
                    hook: fn (): View => $this->renderLanguageSwitcher(floating: true),
                );
            }
        }

        $panel->middleware([SetLocale::class]);
    }

    public static function getRememberLocaleDays(): ?int
    {
        return static::$rememberLocaleDays;
    }

    protected function renderLanguageSwitcher(bool $floating = false): View
    {
        $locales = $this->getLocales();
        $currentLocale = app()->getLocale();
        $currentLanguage = collect($locales)->firstWhere('code', $currentLocale);
        $otherLanguages = $locales;
        $showFlags = $this->showFlags;

        return view('filament-language-switcher::language-switcher', compact(
            'otherLanguages',
            'currentLanguage',
            'showFlags',
            'floating',
        ));
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function getLocales(): array
    {
        $locales = $this->locales instanceof Closure
            ? call_user_func($this->locales)
            : $this->locales;

        if (! empty($locales)) {
            return array_map(function ($locale) {
                if (is_string($locale)) {
                    if (! preg_match('/^[a-zA-Z]{2,3}(_[a-zA-Z]{2,4})?$/', $locale)) {
                        throw new InvalidArgumentException(
                            "Invalid locale code '$locale'. Expected a valid locale (e.g. 'en', 'ar').",
                        );
                    }

                    $locale = ['code' => $locale];
                }

                if (! isset($locale['name'])) {
                    $locale['name'] = $this->getLanguageName($locale['code']);
                }

                if (! isset($locale['flag'])) {
                    $locale['flag'] = $this->getCountryCode($locale['code']);
                }

                return $locale;
            }, $locales);
        }

        return $this->getFilamentLocales();
    }

    protected function getFilamentLocales(): array
    {
        $filamentLangPath = base_path('vendor/filament/filament/resources/lang');
        $locales = [];

        if (! File::isDirectory($filamentLangPath)) {
            return $locales;
        }

        $directories = File::directories($filamentLangPath);

        foreach ($directories as $directory) {
            $localeCode = basename($directory);

            if ($localeCode === 'vendor') {
                continue;
            }

            $locales[] = [
                'code' => $localeCode,
                'name' => $this->getLanguageName($localeCode),
                'flag' => $this->getCountryCode($localeCode),
            ];
        }

        return $locales;
    }

    protected function getLanguageName(string $localeCode): string
    {
        $languageNames = [
            'ar' => 'العربية',
            'en' => 'English',
            'fr' => 'Français',
            'es' => 'Español',
            'de' => 'Deutsch',
            'it' => 'Italiano',
            'tr' => 'Türkçe',
            'ru' => 'Русский',
            'zh_CN' => '简体中文',
            'ja' => '日本語',
            'ko' => '한국어',
            'pt_BR' => 'Português (Brasil)',
            'nl' => 'Nederlands',
            'fa' => 'فارسی',
            'ur' => 'اردو',
        ];

        return $languageNames[$localeCode] ?? ucfirst($localeCode);
    }

    protected function getCountryCode(string $localeCode): string
    {
        $countryMappings = [
            'ar' => 'ly',
            'en' => 'us',
            'fr' => 'fr',
            'es' => 'es',
            'de' => 'de',
            'it' => 'it',
            'tr' => 'tr',
            'ru' => 'ru',
            'zh_CN' => 'cn',
            'ja' => 'jp',
            'ko' => 'kr',
            'pt_BR' => 'br',
            'nl' => 'nl',
            'fa' => 'ir',
            'ur' => 'pk',
        ];

        return $countryMappings[$localeCode] ?? strtolower(substr($localeCode, 0, 2));
    }
}
