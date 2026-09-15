<?php

use Illuminate\Support\Facades\Route;
use Islamv\FilamentLanguageSwitcher\Events\LocaleChanged;
use Islamv\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;

Route::group(['middleware' => ['web']], static function () {
    Route::get('filament/switch-language/{code}', static function ($code) {
        $oldLocale = request()->session()->get('locale', config('app.locale', 'en'));

        request()->session()->put('locale', $code);

        $rememberDays = FilamentLanguageSwitcherPlugin::getRememberLocaleDays();

        if ($rememberDays !== null) {
            $cookie = $rememberDays === 0
                ? cookie()->forever('filament_language_switcher_locale', $code)
                : cookie('filament_language_switcher_locale', $code, $rememberDays * 24 * 60);

            cookie()->queue($cookie);
        }

        event(new LocaleChanged(newLocale: $code, oldLocale: $oldLocale));

        if (request()->has('redirect') && filled(request('redirect'))) {
            return redirect()->to(request('redirect'));
        }

        return redirect()->back(fallback: '/');
    })->name('filament-language-switcher.switch');
});
