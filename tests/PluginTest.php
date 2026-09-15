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

    public function test_plugin_locales_configuration_with_arrays(): void
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

    public function test_plugin_locales_configuration_with_strings(): void
    {
        $plugin = FilamentLanguageSwitcherPlugin::make()
            ->locales(['en', 'ar', 'fr', 'tr', 'de', 'es']);

        $locales = $plugin->getLocales();

        $this->assertCount(6, $locales);
        $this->assertEquals('English', $locales[0]['name']);
        $this->assertEquals('us', $locales[0]['flag']);
        $this->assertEquals('العربية', $locales[1]['name']);
        $this->assertEquals('ly', $locales[1]['flag']);
        $this->assertEquals('Français', $locales[2]['name']);
        $this->assertEquals('fr', $locales[2]['flag']);
        $this->assertEquals('Türkçe', $locales[3]['name']);
        $this->assertEquals('tr', $locales[3]['flag']);
    }

    public function test_plugin_exclude_locales(): void
    {
        $plugin = FilamentLanguageSwitcherPlugin::make()
            ->locales(['en', 'ar', 'fr'])
            ->excludeLocales(['fr']);

        $locales = $plugin->getLocales();

        $this->assertCount(2, $locales);
        $this->assertEquals(['en', 'ar'], array_column($locales, 'code'));
    }

    public function test_plugin_remember_locale(): void
    {
        $plugin = FilamentLanguageSwitcherPlugin::make()
            ->rememberLocale(30);

        $this->assertEquals(30, FilamentLanguageSwitcherPlugin::getRememberLocaleDays());
    }

    public function test_language_and_country_helpers(): void
    {
        $this->assertEquals('العربية', FilamentLanguageSwitcherPlugin::getLanguageName('ar'));
        $this->assertEquals('English', FilamentLanguageSwitcherPlugin::getLanguageName('en'));
        $this->assertEquals('Deutsch', FilamentLanguageSwitcherPlugin::getLanguageName('de'));
        $this->assertEquals('ly', FilamentLanguageSwitcherPlugin::getCountryCode('ar'));
        $this->assertEquals('us', FilamentLanguageSwitcherPlugin::getCountryCode('en'));
        $this->assertEquals('de', FilamentLanguageSwitcherPlugin::getCountryCode('de'));
        $this->assertEquals('sa', FilamentLanguageSwitcherPlugin::getCountryCode('ar_SA'));
    }
}
