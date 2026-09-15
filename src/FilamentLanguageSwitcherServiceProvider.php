<?php

namespace Islamv\FilamentLanguageSwitcher;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentLanguageSwitcherServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-language-switcher';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web');
    }
}
