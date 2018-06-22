<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\AbstractPaginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Add the following line
        \Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Paginator support bootstrap 4
        AbstractPaginator::defaultView('pagination::bootstrap-4');
        AbstractPaginator::defaultSimpleView('pagination::simple-bootstrap-4');
    }
}
