<?php

namespace App\Providers;

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
        // Sidebar active-state helper used by the admin layout.
        // Usage in Blade: class="{{ nav_active(['admin.users.*']) }}"
        if (! function_exists('nav_active')) {
            function nav_active(array $patterns, string $class = 'active'): string
            {
                return request()->routeIs(...$patterns) ? $class : '';
            }
        }
    }
}
