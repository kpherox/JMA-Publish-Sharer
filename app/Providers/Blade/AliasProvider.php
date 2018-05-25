<?php

namespace App\Providers\Blade;

use Illuminate\Support\ServiceProvider;

class AliasProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Alias components/navbar-right.blade.php
        \Blade::component('components.navbar-right', 'navbarRight');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
