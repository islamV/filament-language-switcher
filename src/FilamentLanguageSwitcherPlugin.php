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

    protected array $excludedLocales = [];

    protected bool $showFlags = true;

    protected bool $circularFlags = true;

    protected bool $showOnAuthPages = false;

    protected string $renderHook = PanelsRenderHook::USER_MENU_BEFORE;

    /**
     * Map of language codes to native names.
     */
    protected static array $languageNames = [
        'af' => 'Afrikaans',
        'am' => 'አማርኛ',
        'ar' => 'العربية',
        'ar_AE' => 'العربية (الإمارات)',
        'ar_BH' => 'العربية (البحرين)',
        'ar_DZ' => 'العربية (الجزائر)',
        'ar_EG' => 'العربية (مصر)',
        'ar_IQ' => 'العربية (العراق)',
        'ar_JO' => 'العربية (الأردن)',
        'ar_KW' => 'العربية (الكويت)',
        'ar_LB' => 'العربية (لبنان)',
        'ar_LY' => 'العربية (ليبيا)',
        'ar_MA' => 'العربية (المغرب)',
        'ar_OM' => 'العربية (عمان)',
        'ar_QA' => 'العربية (قطر)',
        'ar_SA' => 'العربية (السعودية)',
        'ar_SY' => 'العربية (سوريا)',
        'ar_TN' => 'العربية (تونس)',
        'ar_YE' => 'العربية (اليمن)',
        'az' => 'Azərbaycan',
        'be' => 'Беларуская',
        'bg' => 'Български',
        'bn' => 'বাংলা',
        'bs' => 'Bosanski',
        'ca' => 'Català',
        'cs' => 'Čeština',
        'cy' => 'Cymraeg',
        'da' => 'Dansk',
        'de' => 'Deutsch',
        'de_AT' => 'Deutsch (Österreich)',
        'de_CH' => 'Deutsch (Schweiz)',
        'el' => 'Ελληνικά',
        'en' => 'English',
        'en_AU' => 'English (Australia)',
        'en_CA' => 'English (Canada)',
        'en_GB' => 'English (UK)',
        'en_IE' => 'English (Ireland)',
        'en_IN' => 'English (India)',
        'en_NZ' => 'English (New Zealand)',
        'en_US' => 'English (US)',
        'en_ZA' => 'English (South Africa)',
        'es' => 'Español',
        'es_AR' => 'Español (Argentina)',
        'es_CL' => 'Español (Chile)',
        'es_CO' => 'Español (Colombia)',
        'es_ES' => 'Español (España)',
        'es_MX' => 'Español (México)',
        'es_PE' => 'Español (Perú)',
        'et' => 'Eesti',
        'eu' => 'Euskara',
        'fa' => 'فارسی',
        'fi' => 'Suomi',
        'fil' => 'Filipino',
        'fr' => 'Français',
        'fr_BE' => 'Français (Belgique)',
        'fr_CA' => 'Français (Canada)',
        'fr_CH' => 'Français (Suisse)',
        'gl' => 'Galego',
        'gu' => 'ગુજરાતી',
        'he' => 'עברית',
        'hi' => 'हिन्दी',
        'hr' => 'Hrvatski',
        'hu' => 'Magyar',
        'hy' => 'Հայերեն',
        'id' => 'Bahasa Indonesia',
        'is' => 'Íslenska',
        'it' => 'Italiano',
        'it_CH' => 'Italiano (Svizzera)',
        'ja' => '日本語',
        'ka' => 'ქართული',
        'kk' => 'Қазақша',
        'km' => 'ភាសាខ្មែរ',
        'kn' => 'ಕನ್ನಡ',
        'ko' => '한국어',
        'ku' => 'Kurdî',
        'ky' => 'Кыргызча',
        'lo' => 'ລາວ',
        'lt' => 'Lietuvių',
        'lv' => 'Latviešu',
        'mk' => 'Македонски',
        'ml' => 'മലയാളം',
        'mn' => 'Монгол',
        'mr' => 'मराठी',
        'ms' => 'Bahasa Melayu',
        'my' => 'မြန်မာဘာသာ',
        'ne' => 'नेपाली',
        'nl' => 'Nederlands',
        'nl_BE' => 'Nederlands (België)',
        'no' => 'Norsk',
        'nb' => 'Norsk Bokmål',
        'nn' => 'Norsk Nynorsk',
        'pa' => 'ਪੰਜਾਬੀ',
        'pl' => 'Polski',
        'ps' => 'پښتو',
        'pt' => 'Português',
        'pt_BR' => 'Português (Brasil)',
        'pt_PT' => 'Português (Portugal)',
        'ro' => 'Română',
        'ru' => 'Русский',
        'si' => 'සිංහල',
        'sk' => 'Slovenčina',
        'sl' => 'Slovenščina',
        'sq' => 'Shqip',
        'sr' => 'Српски',
        'sv' => 'Svenska',
        'sw' => 'Kiswahili',
        'ta' => 'தமிழ்',
        'te' => 'తెలుగు',
        'th' => 'ไทย',
        'tl' => 'Tagalog',
        'tr' => 'Türkçe',
        'ug' => 'ئۇيغۇرچە',
        'uk' => 'Українська',
        'ur' => 'اردو',
        'uz' => 'Oʻzbekcha',
        'vi' => 'Tiếng Việt',
        'zh' => '中文',
        'zh_CN' => '简体中文',
        'zh_HK' => '繁體中文 (香港)',
        'zh_TW' => '繁體中文 (台灣)',
        'zu' => 'isiZulu',
    ];

    /**
     * Map of language codes to default country ISO 3166-1 flag codes.
     */
    protected static array $countryMappings = [
        'af' => 'za',
        'am' => 'et',
        'ar' => 'ly',
        'ar_AE' => 'ae',
        'ar_BH' => 'bh',
        'ar_DZ' => 'dz',
        'ar_EG' => 'eg',
        'ar_IQ' => 'iq',
        'ar_JO' => 'jo',
        'ar_KW' => 'kw',
        'ar_LB' => 'lb',
        'ar_LY' => 'ly',
        'ar_MA' => 'ma',
        'ar_OM' => 'om',
        'ar_QA' => 'qa',
        'ar_SA' => 'sa',
        'ar_SY' => 'sy',
        'ar_TN' => 'tn',
        'ar_YE' => 'ye',
        'az' => 'az',
        'be' => 'by',
        'bg' => 'bg',
        'bn' => 'bd',
        'bs' => 'ba',
        'ca' => 'es',
        'cs' => 'cz',
        'cy' => 'gb',
        'da' => 'dk',
        'de' => 'de',
        'de_AT' => 'at',
        'de_CH' => 'ch',
        'el' => 'gr',
        'en' => 'us',
        'en_AU' => 'au',
        'en_CA' => 'ca',
        'en_GB' => 'gb',
        'en_IE' => 'ie',
        'en_IN' => 'in',
        'en_NZ' => 'nz',
        'en_US' => 'us',
        'en_ZA' => 'za',
        'es' => 'es',
        'es_AR' => 'ar',
        'es_CL' => 'cl',
        'es_CO' => 'co',
        'es_ES' => 'es',
        'es_MX' => 'mx',
        'es_PE' => 'pe',
        'et' => 'ee',
        'eu' => 'es',
        'fa' => 'ir',
        'fi' => 'fi',
        'fil' => 'ph',
        'fr' => 'fr',
        'fr_BE' => 'be',
        'fr_CA' => 'ca',
        'fr_CH' => 'ch',
        'gl' => 'es',
        'gu' => 'in',
        'he' => 'il',
        'hi' => 'in',
        'hr' => 'hr',
        'hu' => 'hu',
        'hy' => 'am',
        'id' => 'id',
        'is' => 'is',
        'it' => 'it',
        'it_CH' => 'ch',
        'ja' => 'jp',
        'ka' => 'ge',
        'kk' => 'kz',
        'km' => 'kh',
        'kn' => 'in',
        'ko' => 'kr',
        'ku' => 'iq',
        'ky' => 'kg',
        'lo' => 'la',
        'lt' => 'lt',
        'lv' => 'lv',
        'mk' => 'mk',
        'ml' => 'in',
        'mn' => 'mn',
        'mr' => 'in',
        'ms' => 'my',
        'my' => 'mm',
        'ne' => 'np',
        'nl' => 'nl',
        'nl_BE' => 'be',
        'no' => 'no',
        'nb' => 'no',
        'nn' => 'no',
        'pa' => 'in',
        'pl' => 'pl',
        'ps' => 'af',
        'pt' => 'pt',
        'pt_BR' => 'br',
        'pt_PT' => 'pt',
        'ro' => 'ro',
        'ru' => 'ru',
        'si' => 'lk',
        'sk' => 'sk',
        'sl' => 'si',
        'sq' => 'al',
        'sr' => 'rs',
        'sv' => 'se',
        'sw' => 'ke',
        'ta' => 'in',
        'te' => 'in',
        'th' => 'th',
        'tl' => 'ph',
        'tr' => 'tr',
        'ug' => 'cn',
        'uk' => 'ua',
        'ur' => 'pk',
        'uz' => 'uz',
        'vi' => 'vn',
        'zh' => 'cn',
        'zh_CN' => 'cn',
        'zh_HK' => 'hk',
        'zh_TW' => 'tw',
        'zu' => 'za',
    ];

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

    public function excludeLocales(array $locales): static
    {
        $this->excludedLocales = $locales;

        return $this;
    }

    public function showFlags(bool $show = true): static
    {
        $this->showFlags = $show;

        return $this;
    }

    public function circularFlags(bool $circular = true): static
    {
        $this->circularFlags = $circular;

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
        $currentLanguage = collect($locales)->firstWhere('code', $currentLocale)
            ?? [
                'code' => $currentLocale,
                'name' => static::getLanguageName($currentLocale),
                'flag' => static::getCountryCode($currentLocale),
            ];
        $otherLanguages = $locales;
        $showFlags = $this->showFlags;
        $circularFlags = $this->circularFlags;

        return view('filament-language-switcher::language-switcher', compact(
            'otherLanguages',
            'currentLanguage',
            'showFlags',
            'circularFlags',
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

        if (empty($locales)) {
            $locales = config('filament-language-switcher.locales', []);
        }

        if (! empty($locales)) {
            $parsed = array_map(function ($locale) {
                if (is_string($locale)) {
                    if (! preg_match('/^[a-zA-Z]{2,3}(_[a-zA-Z]{2,4})?$/', $locale)) {
                        throw new InvalidArgumentException(
                            "Invalid locale code '$locale'. Expected a valid locale (e.g. 'en', 'ar', 'zh_CN').",
                        );
                    }

                    $locale = ['code' => $locale];
                }

                if (! isset($locale['name']) || blank($locale['name'])) {
                    $locale['name'] = static::getLanguageName($locale['code']);
                }

                if (! isset($locale['flag']) || blank($locale['flag'])) {
                    $locale['flag'] = static::getCountryCode($locale['code']);
                }

                return $locale;
            }, $locales);

            if (! empty($this->excludedLocales)) {
                $parsed = array_values(array_filter($parsed, fn ($l) => ! in_array($l['code'], $this->excludedLocales, true)));
            }

            return $parsed;
        }

        return $this->getFilamentLocales();
    }

    protected function getFilamentLocales(): array
    {
        $filamentLangPath = base_path('vendor/filament/filament/resources/lang');
        $locales = [];

        if (File::isDirectory($filamentLangPath)) {
            $directories = File::directories($filamentLangPath);

            foreach ($directories as $directory) {
                $localeCode = basename($directory);

                if ($localeCode === 'vendor' || in_array($localeCode, $this->excludedLocales, true)) {
                    continue;
                }

                $locales[] = [
                    'code' => $localeCode,
                    'name' => static::getLanguageName($localeCode),
                    'flag' => static::getCountryCode($localeCode),
                ];
            }
        }

        if (empty($locales)) {
            $locales = [
                ['code' => 'en', 'name' => static::getLanguageName('en'), 'flag' => static::getCountryCode('en')],
                ['code' => 'ar', 'name' => static::getLanguageName('ar'), 'flag' => static::getCountryCode('ar')],
            ];
        }

        return $locales;
    }

    public static function getLanguageName(string $localeCode): string
    {
        if (isset(static::$languageNames[$localeCode])) {
            return static::$languageNames[$localeCode];
        }

        // Try language prefix if locale is formatted like en_US or zh_CN
        $baseLocale = explode('_', $localeCode)[0];
        if (isset(static::$languageNames[$baseLocale])) {
            return static::$languageNames[$baseLocale];
        }

        return ucfirst($localeCode);
    }

    public static function getCountryCode(string $localeCode): string
    {
        if (isset(static::$countryMappings[$localeCode])) {
            return static::$countryMappings[$localeCode];
        }

        $baseLocale = strtolower(explode('_', $localeCode)[0]);
        if (isset(static::$countryMappings[$baseLocale])) {
            return static::$countryMappings[$baseLocale];
        }

        return strtolower(substr($localeCode, 0, 2));
    }
}
