<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Runs during application boot. This provider performs no boot work.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Runs during provider registration. This provider registers no services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }
}
