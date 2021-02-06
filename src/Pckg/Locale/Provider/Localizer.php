<?php

namespace Pckg\Locale\Provider;

use Pckg\Cache\Handler\InvalidateCacheByRecord;
use Pckg\Framework\Provider;
use Pckg\Locale\Console\PrepareLocalizer;
use Pckg\Locale\Record\Language;

class Localizer extends Provider
{

    public function consoles()
    {
        return [
            PrepareLocalizer::class,
        ];
    }

    public function listeners()
    {
        return [
            Language::class . '.inserted' => [
                InvalidateCacheByRecord::class,
            ],
            Language::class . '.updated'  => [
                InvalidateCacheByRecord::class,
            ],
            Language::class . '.deleted'  => [
                InvalidateCacheByRecord::class,
            ],
        ];
    }
}
