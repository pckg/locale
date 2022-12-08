<?php

namespace Pckg\Locale\Middleware;

class CatchUrlLanguageChange
{
    public function execute(callable $next)
    {
        if (!isHttp()) {
            return $next();
        }

        $lang = get('lang');
        if (!$lang) {
            return $next();
        }

        $router = router();
        if (!in_array('layout:backend', $router->get('tags'))) {
            return $next();
        }

        /**
         * @T00D00 - keep language id.
         */
        $_SESSION['pckg_dynamic_lang_id'] = get('lang');
        redirect();

        return $next();
    }
}
