<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate for Dashboard view
        Gate::define('view-dashboard', function ($user) {
            return in_array($user->user_type, ['superAdmin', 'Admin', 'Operator']);
        });

        // Gate for Boats view
        Gate::define('view-boats', function ($user) {
            return in_array($user->user_type, ['superAdmin', 'Boat']);
        });

        // Gate for Map view
        Gate::define('view-map', function ($user) {
            return in_array($user->user_type, ['superAdmin', 'Admin', 'Operator']);
        });

        // Gate for Reports view
        Gate::define('view-reports', function ($user) {
            return in_array($user->user_type, ['superAdmin', 'Admin']);
        });

        // Gate for Users view
        Gate::define('view-users', function ($user) {
            return $user->user_type === 'superAdmin';
        });

        // Grant all access to Super Admin before any other gate check
        Gate::before(function ($user) {
            if ($user->user_type === 'superAdmin') {
                return true;  // Super Admin can access all gates
            }
        });
    }
}