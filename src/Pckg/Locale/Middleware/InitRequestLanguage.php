<?php namespace Pckg\Locale\Middleware;

use Pckg\Locale\Record\Language;

class InitRequestLanguage
{

    public function execute(callable $next)
    {
        /**
         * Check for locale settings.
         */
        if (!isHttp()) {
            return $next();
        }

        $match = request()->getMatch();
        $langCode = $match['language'] ?? null;

        if ($langCode) {
            $this->setFromLangCode($langCode);

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
        $language = Language::gets(['domain' => $domain]);
        if ($language) {
            $language->setAsCurrent();

            return $next();
        }

        /**
         * Check for default language?
         */
        $language = Language::gets(['frontend' => true]);
        if ($language) {
            $language->setAsCurrent();

            return $next();
        }

        /**
         * Check for any language?
         */
        $language = Language::gets([]);
        if ($language) {
            $language->setAsCurrent();
        }

        return $next();
    }

    protected function setFromLangCode($langCode)
    {
        $language = Language::gets(['slug' => $langCode]);
        $language->setAsCurrent();
    }

}