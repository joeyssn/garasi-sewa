<?php

namespace App\Providers;

use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // `php artisan serve` only forwards a fixed list of environment variables to the
        // PHP server it starts. On Windows, TEMP/TMP are not in that list, so PHP cannot
        // create temporary files for uploads ("The payment proof failed to upload").
        ServeCommand::$passthroughVariables = array_merge(ServeCommand::$passthroughVariables, ['TEMP', 'TMP']);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
