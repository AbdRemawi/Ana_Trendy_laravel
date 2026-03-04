<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

/**
 * Set Locale Middleware
 *
 * Determines and sets the application locale based on:
 * 1. User session preference
 * 2. Default locale from configuration
 *
 * Also shares the current locale and text direction with all views.
 * This ensures RTL/LTR rendering works correctly throughout the application.
 */
class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * Sets the application locale and shares it with all views.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $this->determineLocale($request);

        // Set application locale
        App::setLocale($locale);

        // Share locale and direction with all views for RTL/LTR support
        view()->share('locale', $locale);
        view()->share('direction', $this->getDirection($locale));

        return $next($request);
    }

    /**
     * Determine the locale for the current request.
     *
     * Priority:
     * 1. Session stored locale
     * 2. Default locale from config
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function determineLocale(Request $request): string
    {
        $locale = Session::get('locale', config('locale.default'));

        // Fallback to default if locale is not supported
        if (! $this->isSupported($locale)) {
            $locale = config('locale.default');
        }

        return $locale;
    }

    /**
     * Check if a locale is supported.
     *
     * @param  string  $locale
     * @return bool
     */
    protected function isSupported(string $locale): bool
    {
        return in_array($locale, config('locale.supported', []), true);
    }

    /**
     * Get the text direction for a given locale.
     *
     * @param  string  $locale
     * @return string
     */
    protected function getDirection(string $locale): string
    {
        return config("locale.directions.{$locale}", 'ltr');
    }
}
