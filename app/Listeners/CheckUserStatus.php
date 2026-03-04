<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Attempting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class CheckUserStatus
{
    /**
     * Handle the event.
     */
    public function handle(Attempting $event): void
    {
        $email = $event->credentials['email'] ?? null;

        if (!$email) {
            return;
        }

        $user = User::where('email', $email)->first();

        if ($user && !$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => __('auth.account_inactive'),
            ]);
        }
    }
}
