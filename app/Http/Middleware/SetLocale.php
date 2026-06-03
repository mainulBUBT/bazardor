<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $this->resolveLocale($request);

        $enabledLocales = \get_enabled_locales();

        if (in_array($locale, $enabledLocales)) {
            app()->setLocale($locale);
        } else {
            app()->setLocale(\get_default_locale());
        }

        return $next($request);
    }

    protected function resolveLocale(Request $request): string
    {
        if ($request->is('api/*')) {
            return $request->header('X-localization', '')
                ?: \get_default_locale();
        }

        return session('admin_locale', '')
            ?: $request->getPreferredLanguage(\get_enabled_locales())
            ?: \get_default_locale();
    }
}
