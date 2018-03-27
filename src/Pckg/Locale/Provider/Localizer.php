<?php namespace Pckg\Locale\Provider;

use Pckg\Framework\Provider;
use Pckg\Locale\Console\PrepareLocalizer;

class Localizer extends Provider
{

    public function consoles()
    {
        return [
            PrepareLocalizer::class,
        ];
    }

}