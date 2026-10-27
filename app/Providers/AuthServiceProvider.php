<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return ($user->hasRole('Super Admin') || $user->role === 'Super Admin') ? true : null;
        });

        Gate::define('roles-view', function ($user) {
            return $user->can('roles-and-permissions-display') || $user->can('roles-display');
        });

        Gate::define('permissions-view', function ($user) {
            return $user->can('roles-and-permissions-display') || $user->can('permissions-display');
        });

        Gate::define('quotation-approvals-view', function ($user) {
            return $user->can('quotation-approval')
                || $user->can('discount-approval')
                || $user->can('terms-approval');
        });
    }
}
