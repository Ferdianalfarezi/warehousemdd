<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Schedule;
use App\Observers\ScheduleObserver;

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
        // Gate for managing users (only superadmin and admin)
        Gate::define('manage-users', function ($user) {
            return $user->role && in_array($user->role->nama, ['superadmin', 'admin']);
        });

        // Gate for superadmin only
        Gate::define('superadmin-only', function ($user) {
            return $user->role && $user->role->nama === 'superadmin';
        });

        // Gate for admin and above
        Gate::define('admin-access', function ($user) {
            return $user->role && in_array($user->role->nama, ['superadmin', 'admin', 'kepala divisi']);
        });

        Schedule::observe(ScheduleObserver::class);
    }
}