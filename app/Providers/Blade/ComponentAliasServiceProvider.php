<?php

namespace App\Providers\Blade;

use Illuminate\Support\ServiceProvider;

class ComponentAliasServiceProvider extends ServiceProvider
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
}
