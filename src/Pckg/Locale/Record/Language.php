<?php

namespace Pckg\Locale\Record;

use Pckg\Database\Record;
use Pckg\Locale\Entity\Languages;
use Pckg\Manager\Locale;

class Language extends Record
{

    protected $entity = Languages::class;

    public function getRootUrl()
    {
        return '/' . $this->slug . '/';
    }

    public function getSwitchUrlAttribute()
    {
        return /*router()->getUri() . */'?lang=' . $this->slug;
    }

    public function getCurrentPageUrlAttribute()
    {
        $domainUrl = '//' . $this->domain;
        $match = request()->getMatch();
        $name = $match['name'] ?? null;

        /**
         * No domain is set, we cannot make match with other routes.
         */
        if (!$name) {
            return $domainUrl;
        }

        $pos = strpos($name, ':');
        if ($pos) {
            $name = substr($name, 0, $pos);

            return url($name . ':' . $this->slug, router()->get('data'), true, false);
        }

        /**
         * Check for platform pages.
         */
        return url($name, router()->get('data'), true, false);
    }

    public function setAsCurrent()
    {
        /**
         * Set app locale and language
         */
        message('Setting ' . $this->slug . ' as language and ' . $this->locale . ' as locale');
        localeManager()->setCurrent($this->locale, $this->slug);
    }

}