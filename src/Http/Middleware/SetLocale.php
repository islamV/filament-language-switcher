<?php

namespace Islamv\FilamentLanguageSwitcher\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Islamv\FilamentLanguageSwitcher\Events\LocaleChanged;
use Islamv\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use Throwable;

class SetLocale
{
    /**
     * Supported locales.
     *
     * @var array<string>
     */
    protected array $supportedLocales = [];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $oldLocale = App::getLocale();
        $locale = $this->resolveLocale($request);

        if ($locale) {
            App::setLocale($locale);
        }

        if ($request->hasSession() && $locale) {
            if ($request->session()->get('locale') !== $locale) {
                $request->session()->put('locale', $locale);
            }
        }

        $rememberDays = FilamentLanguageSwitcherPlugin::getRememberLocaleDays();
        if ($rememberDays !== null && $locale) {
            $cookie = $rememberDays === 0
                ? cookie()->forever('filament_language_switcher_locale', $locale)
                : cookie('filament_language_switcher_locale', $locale, $rememberDays * 24 * 60);

            cookie()->queue($cookie);
        }

        if ($locale && $oldLocale !== $locale) {
            event(new LocaleChanged(newLocale: $locale, oldLocale: $oldLocale));
        }

        return $next($request);
    }

    /**
     * Resolve locale from request query, session, cookies, or configuration.
     */
    protected function resolveLocale(Request $request): string
    {
        // 1. Check URL query parameters (?lang=ar, ?locale=ar, etc.)
        $queryLocale = $request->query('lang')
            ?? $request->query('locale')
            ?? $request->query('change-language')
            ?? $request->query('change-locale');

        if ($queryLocale && is_string($queryLocale) && $this->isLocaleSupported($queryLocale)) {
            return $queryLocale;
        }

        // 2. Check if request has an active session
        if ($request->hasSession()) {
            $sessionLocale = $request->session()->get('locale');
            if ($sessionLocale && $this->isLocaleSupported($sessionLocale)) {
                return $sessionLocale;
            }
        }

        // 3. Check if global session manager has an active/started session
        if (app()->bound('session')) {
            try {
                $session = app('session');
                if ($session->isStarted()) {
                    $sessionLocale = $session->get('locale');
                    if ($sessionLocale && $this->isLocaleSupported($sessionLocale)) {
                        return $sessionLocale;
                    }
                }
            } catch (Throwable) {
                // Ignore session lookup errors
            }
        }

        // 4. Check already decrypted cookie (if EncryptCookies ran)
        $cookieLocale = $request->cookie('filament_language_switcher_locale')
            ?? $request->cookie('filament_language_switch_locale');

        if ($cookieLocale && $this->isLocaleSupported($cookieLocale)) {
            return $cookieLocale;
        }

        // 5. Check raw encrypted or plaintext language cookie
        $rawCookie = $request->cookies->get('filament_language_switcher_locale')
            ?? $request->cookies->get('filament_language_switch_locale');

        if ($rawCookie) {
            $val = $this->decryptCookieValue($rawCookie);
            if ($val && $this->isLocaleSupported($val)) {
                return $val;
            }
        }

        // 6. Check session cookie if session driver is database/file/redis
        $sessionCookieName = config('session.cookie');
        $rawSessionCookie = $sessionCookieName ? $request->cookies->get($sessionCookieName) : null;

        if ($rawSessionCookie && app()->bound('session')) {
            try {
                $sessionId = $this->decryptCookieValue($rawSessionCookie);
                if (is_string($sessionId) && filled($sessionId)) {
                    $driver = app('session')->driver();
                    $driver->setId($sessionId);
                    $driver->start();
                    $sessionLocale = $driver->get('locale');
                    if ($sessionLocale && $this->isLocaleSupported($sessionLocale)) {
                        return $sessionLocale;
                    }
                }
            } catch (Throwable) {
                // Ignore session retrieval errors
            }
        }

        return config('app.locale', 'en');
    }

    /**
     * Check if the given locale code is supported.
     */
    protected function isLocaleSupported(string $locale): bool
    {
        if (empty($this->supportedLocales)) {
            return preg_match('/^[a-zA-Z]{2,3}(_[a-zA-Z]{2,4})?$/', $locale) === 1;
        }

        return in_array($locale, $this->supportedLocales, true);
    }

    /**
     * Decrypt and unpack a cookie value if encrypted.
     */
    protected function decryptCookieValue(mixed $value): ?string
    {
        if (! is_string($value) || blank($value)) {
            return null;
        }

        if ($this->isLocaleSupported($value)) {
            return $value;
        }

        try {
            $decrypted = Crypt::decrypt($value, false);
            if (is_string($decrypted)) {
                $unserialized = @unserialize($decrypted);
                $result = ($unserialized !== false || $decrypted === 'b:0;') ? $unserialized : $decrypted;
                if (is_string($result) && $this->isLocaleSupported($result)) {
                    return $result;
                }
            }
        } catch (DecryptException|Throwable) {
            // Value is not encrypted or invalid
        }

        return null;
    }
}
