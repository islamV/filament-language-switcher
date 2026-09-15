<?php

namespace Islamv\FilamentLanguageSwitcher\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Support\SupportServiceProvider;
use Islamv\FilamentLanguageSwitcher\FilamentLanguageSwitcherServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Stijnvanouplines\BladeCountryFlags\BladeCountryFlagsServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeCountryFlagsServiceProvider::class,
            SupportServiceProvider::class,
            FilamentServiceProvider::class,
            FilamentLanguageSwitcherServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('app.key', 'base64:6Cu/ozMDumxWsmjiTXalQN3tlOoMmPoi4TueggYparent=');
    }
}
