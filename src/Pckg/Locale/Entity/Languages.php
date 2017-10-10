<?php namespace Pckg\Locale\Entity;

use Pckg\Database\Entity;
use Pckg\Locale\Record\Language;

class Languages extends Entity
{

    protected $record = Language::class;

    public function boot()
    {
        $this->joinTranslations();
    }

}