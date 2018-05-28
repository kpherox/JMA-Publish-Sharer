<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        config([
            'services.twitter.redirect' => url('twitter/callback'),
            'services.github.redirect' => url('github/callback'),
        ]);
    }
}
