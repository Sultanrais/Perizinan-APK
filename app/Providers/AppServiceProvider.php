<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Perizinan;
use App\Observers\PerizinanObserver;

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
        Perizinan::observe(PerizinanObserver::class);
    }
}
