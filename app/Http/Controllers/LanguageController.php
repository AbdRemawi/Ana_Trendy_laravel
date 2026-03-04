<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Language Controller
 *
 * Handles application language switching.
 * Validates requested locales and persists user preference to session.
 *
 * The SetLocale middleware reads the session value on subsequent requests
 * to apply the language and text direction.
 */
class LanguageController extends Controller
{
    /**
     * Switch the application language.
     *
     * Validates the requested locale against supported languages,
     * stores it in the user's session, and redirects back.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale  The locale code (e.g., 'ar', 'en')
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        // Validate locale is supported
        if (! $this->isSupported($locale)) {
            abort(404, __('auth.language_not_supported'));
        }

        // Store in session for persistence
        Session::put('locale', $locale);

        // Redirect back to the previous page
        return redirect()->back()
            ->with('locale_switched', $locale);
    }

    /**
     * Check if a locale is supported.
     *
     * Uses configuration to ensure single source of truth.
     *
     * @param  string  $locale
     * @return bool
     */
    protected function isSupported(string $locale): bool
    {
        return in_array($locale, config('locale.supported', []), true);
    }
}
