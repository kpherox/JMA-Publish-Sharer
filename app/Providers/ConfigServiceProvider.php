<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        collect(config('services'))->keys()->each(function ($provider) {
            if (config('services.'.$provider.'.socialite', false)) {
                config(['services.'.$provider.'.redirect' => url($provider.'/callback')]);
            }
        });
    }
}
