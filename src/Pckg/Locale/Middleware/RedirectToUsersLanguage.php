<?php namespace Pckg\Locale\Middleware;

use Pckg\Locale\Record\Language;

class RedirectToUsersLanguage
{

    public function execute(callable $next)
    {
        /**
         * Check for locale settings.
         */
        if (!isHttp()) {
            return $next();
        }

        /**
         * Skip non-http requests.
         */
        if (true || !config('multilingual')) {
            return $next();
        }

        $httpAcceptLanguages = explode(';', server('HTTP_ACCEPT_LANGUAGE'));
        $currentLocale = localeManager()->getCurrent();
        $currentLang = substr($currentLocale, 0, 2);
        foreach ($httpAcceptLanguages as $httpAcceptLanguage) {
            $httpAcceptLanguage = explode(',', $httpAcceptLanguage);
            foreach ($httpAcceptLanguage as $lang) {
                if (strpos($lang, ':')) {
                    continue;
                }
                $lang = str_replace('-', '_', $lang);
                $language = Language::gets(['frontend' => true, 'locale' => $lang]);
                if (!$language) {
                    $language = Language::gets(['frontend' => true, 'slug' => $lang]);
                }
                if (!$language) {
                    continue;
                }

                if ($currentLang == $language->slug) {
                    message('Current language ' . $currentLang . ' is http language ' . $language->slug . ' ' .
                            server('HTTP_ACCEPT_LANGUAGE'));

                    return $next();
                } else {
                    $routeName = router()->getName();
                    $routeName = substr($routeName, 0, strpos($routeName, ':')) . ':' . $language->slug;
                    $url = url($routeName, router()->get('data'), true);
                    message('Redirect to ' . $url . ' for http language ' . $lang . ' ' .
                            server('HTTP_ACCEPT_LANGUAGE'));
                }
            }
        }

        return $next();
    }

    protected function setFromLangCode($langCode)
    {
        $language = Language::gets(['slug' => $langCode]);
        $language->setAsCurrent();
    }

}