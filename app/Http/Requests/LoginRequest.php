<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Login Form Request
 *
 * Handles validation and authentication for user login.
 * Uses Laravel's built-in authentication with proper security measures.
 */
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Guests can always attempt to log in.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => __('auth.email'),
            'password' => __('auth.password'),
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * This method handles the actual authentication logic, keeping the controller clean.
     * Status validation happens AFTER authentication to prevent timing attacks.
     * Supports authentication using either email or mobile number.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->getCredentials();

        // Use Laravel's built-in authentication
        // The 'remember' functionality is handled by the checkbox value
        if (! Auth::attempt($credentials, $this->filled('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Check user status AFTER successful authentication
        // This prevents account enumeration via timing attacks
        if (! Auth::user()->isActive()) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => __('auth.suspended'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Get the authentication credentials based on input type.
     *
     * Determines if the input is an email or mobile number and returns
     * the appropriate credentials array for authentication.
     *
     * @return array
     */
    protected function getCredentials(): array
    {
        $login = $this->input('email');

        // Check if the input is an email format
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        return [
            $field => $login,
            'password' => $this->input('password'),
        ];
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new \Illuminate\Auth\Events\Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(): string
    {
        return strtolower($this->input('email')) . '|' . $this->ip();
    }
}
