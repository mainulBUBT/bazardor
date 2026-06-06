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

        // For admin: session → DB preference → browser → default
        if ($request->is('admin/*')) {
            $sessionLocale = session('admin_locale', '');
            if ($sessionLocale) {
                return $sessionLocale;
            }

            $admin = auth()->guard('admin')->user();
            if ($admin && $admin->locale) {
                return $admin->locale;
            }
        }

        return $request->getPreferredLanguage(\get_enabled_locales())
            ?: \get_default_locale();
    }
}
