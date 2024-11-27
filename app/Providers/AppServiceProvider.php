<?php

namespace App\Providers;

use App\Console\Commands\CreateWalletsForUsers;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->commands([
            CreateWalletsForUsers::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $helperPath = app_path('Helpers/settings.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }
}
