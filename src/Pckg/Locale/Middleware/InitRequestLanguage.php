<?php namespace Pckg\Locale\Middleware;

use Pckg\Locale\Record\Language;

class InitRequestLanguage
{

    public function execute(callable $next)
    {
        if (isHttp()) {
            /**
             * Check for http stuff.
             */
            $match = request()->getMatch();
            $langCode = $match['language'] ?? null;

            if ($langCode) {
                message('Setting language from route match.');
                $this->setFromLangCode($langCode);

                return $next();
            }

            /**
             * Check for header stuff.
             */
            $headerLocale = request()->header('X-Pckg-Locale');

            if ($headerLocale) {
                message('Setting locale from HTTP header.');
                $this->setFromLangCode(substr($headerLocale, 0, 2));

                return $next();
            }

            /**
             * Try to get language from previous request.
             */
            /*if ($referer = server('HTTP_REFERER', null)) {
                $url = parse_url($referer);
                $match = (new ResolveRoute(router(), $url['path'],
                                           first($url['host'], server('HTTP_HOST'), config('domain'))))->execute();
                $langCode = $match['language'] ?? null;

                if ($langCode) {
                    $this->setFromLangCode($langCode);

                    return $next();
                }
            }*/

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