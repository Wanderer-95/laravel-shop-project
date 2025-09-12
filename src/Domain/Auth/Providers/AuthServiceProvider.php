<?php

namespace Domain\Auth\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(ActionsServiceProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
