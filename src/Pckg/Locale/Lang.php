<?php namespace Pckg\Locale;

use Pckg\Locale\Entity\Languages;
use Pckg\Locale\LangInterface as LangAdapter;

class Lang implements LangAdapter
{

    protected $langId = 'en';

    public function __construct($language = null)
    {
        if ($language) {
            $this->langId = $language;
        }
    }

    public function setLangId($langId)
    {
        $this->langId = $langId;

        return $this;
    }

    public function langId($section = null)
    {
        return $this->langId;
    }

    public function getLanguages()
    {
        return (new Languages())->all();
    }

}
