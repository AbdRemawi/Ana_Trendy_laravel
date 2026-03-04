<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

/**
 * Authentication Controller
 *
 * Handles user authentication flow including login and logout.
 * Uses Form Request validation for clean separation of concerns.
 * All authentication logic follows Laravel best practices.
 */
class LoginController extends Controller
{
    /**
     * Display the login page.
     *
     * Only accessible to guest users (handled by route middleware).
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * Validates credentials, authenticates the user, and redirects appropriately.
     * Uses Form Request for validation and authentication logic.
     *
     * Security measures:
     * - Rate limiting via throttle middleware (5 attempts per minute)
     * - Session regeneration to prevent session fixation
     * - Status validation after authentication to prevent timing attacks
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Destroy an authenticated session.
     *
     * Logs the user out and invalidates the session to prevent session fixation.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Get the post-login redirect path.
     *
     * This method allows for easy customization of the redirect destination.
     * Can be extended to support role-based redirects in the future.
     *
     * @return string
     */
    protected function redirectPath(): string
    {
        return route('dashboard');
    }
}

