<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;

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
        Relation::morphMap([
            'hotel' => 'App\\Models\\Hotel',
            'event' => 'App\\Models\\Event',
            'tour' => 'App\\Models\\Tour',
        ]);

        // Use Bootstrap 5 pagination views globally
        Paginator::useBootstrapFive();
    }
}
