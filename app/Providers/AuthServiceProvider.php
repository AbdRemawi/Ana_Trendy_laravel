<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\DashboardPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Authentication Service Provider.
 *
 * Registers application policies for authorization.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Role::class => \App\Policies\RolePolicy::class,
        Permission::class => \App\Policies\PermissionPolicy::class,
        User::class => UserPolicy::class,
        // Register dashboard policy for explicit authorization checks
        'dashboard' => DashboardPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Explicitly define dashboard gate for clarity and type safety
        Gate::define('view dashboard', function (User $user) {
            return $user->can('view dashboard');
        });
    }
}
