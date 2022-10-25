<?php

namespace Pckg\Locale\Middleware;

use Pckg\Locale\Record\Language;

class InitRequestLanguage
{

    public function execute(callable $next)
    {
        if (isHttp()) {
            /**
             * Check for http stuff.
             */
            $languages = localeManager()->getFrontendLanguages()->keyBy('slug');
            $match = request()->getMatch();
            $langCode = $match['language'] ?? null;

            if ($langCode && isset($languages[$langCode])) {
                message('Setting language from route match.');
                $this->setFromLangCode($langCode);

                return $next();
            }

            /**
             * Check for custom param.
             */
            $getLang = request()->get('_lang_id');

            if ($getLang && isset($languages[substr($getLang, 0, 2)])) {
                message('Setting locale from _lang_id parameter.');
                $this->setFromLangCode(substr($getLang, 0, 2));

                return $next();
            }

            /**
             * Check for header stuff.
             */
            $headerLocale = request()->header('X-Pckg-Locale');

            if ($headerLocale && isset($languages[substr($headerLocale, 0, 2)])) {
                message('Setting locale from HTTP header.');
                $this->setFromLangCode(substr($headerLocale, 0, 2));

                return $next();
            }

            /**
             * Try to get language from session.
             */
            $sessionLang = $_SESSION['pckg_dynamic_lang_id'] ?? null;
            if ($sessionLang && isset($languages[$sessionLang])) {
                message('Setting locale from Session (' . $sessionLang . ').');
                $this->setFromLangCode($sessionLang);

                return $next();
            }

            $domain = server('HTTP_HOST');
            $language = localeManager()->getLanguageBy('domain', $domain);
            if ($language) {
                message('Setting language from domain.');
                $language->setAsCurrent();

                return $next();
            }
        }

        /**
         * Check for default frontend language?
         */
        $language = localeManager()->getDefaultFrontendLanguage();
        if ($language) {
            message('Setting default frontend language.');
            $language->setAsCurrent();

            return $next();
        }

        /**
         * Check for default frontend language?
         */
        $language = localeManager()->getFrontendLanguages()->first();
        if ($language) {
            message('Setting any frontend language.');
            $language->setAsCurrent();

            return $next();
        }

        /**
         * Check for any language?
         */
        $language = localeManager()->getLanguages()->first();
        if ($language) {
            message('Setting first language found.');
            $language->setAsCurrent();
        }

        return $next();
    }

    protected function setFromLangCode($langCode)
    {
        $language = localeManager()->getLanguageBy('slug', $langCode);
        $language->setAsCurrent();
    }
}
