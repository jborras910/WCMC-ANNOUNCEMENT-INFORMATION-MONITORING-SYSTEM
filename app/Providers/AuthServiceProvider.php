<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Abilities (manage-users, review-slides, manage-slides, manage-roles,
        // view-all-activity-logs, ...) are now database-backed permissions via
        // spatie/laravel-permission. That package registers its own Gate::before
        // hook, so `@can(...)`, `Gate::allows(...)`, and the `can:` route
        // middleware all resolve straight against the permissions/roles tables —
        // no Gate::define(...) needed here.
    }
}
