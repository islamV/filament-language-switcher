<?php

namespace Islamv\FilamentLanguageSwitcher\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LocaleChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $newLocale,
        public readonly string $oldLocale,
    ) {}
}
