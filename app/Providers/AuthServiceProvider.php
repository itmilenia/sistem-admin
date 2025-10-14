<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate universal: owner bisa semua
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('owner')) {
                return true;
            }
        });
    }
}
