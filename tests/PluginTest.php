<?php

namespace Islamv\FilamentLanguageSwitcher\Tests;

use Islamv\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;

class PluginTest extends TestCase
{
    public function test_plugin_instantiation_and_id(): void
    {
        $plugin = FilamentLanguageSwitcherPlugin::make();

        $this->assertEquals('filament-language-switcher', $plugin->getId());
    }

    public function test_plugin_locales_configuration(): void
    {
        $plugin = FilamentLanguageSwitcherPlugin::make()
            ->locales([
                ['code' => 'en', 'name' => 'English', 'flag' => 'us'],
                ['code' => 'ar', 'name' => 'العربية', 'flag' => 'ly'],
            ]);

        $locales = $plugin->getLocales();

        $this->assertCount(2, $locales);
        $this->assertEquals('en', $locales[0]['code']);
        $this->assertEquals('English', $locales[0]['name']);
        $this->assertEquals('us', $locales[0]['flag']);
        $this->assertEquals('ar', $locales[1]['code']);
        $this->assertEquals('العربية', $locales[1]['name']);
        $this->assertEquals('ly', $locales[1]['flag']);
    }

    public function test_plugin_remember_locale(): void
    {
        $plugin = FilamentLanguageSwitcherPlugin::make()
            ->rememberLocale(30);

        $this->assertEquals(30, FilamentLanguageSwitcherPlugin::getRememberLocaleDays());
    }
}
