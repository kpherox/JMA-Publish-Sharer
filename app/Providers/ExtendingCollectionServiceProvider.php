<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

class ExtendingCollectionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('sortByKind', function () {
            $kindOrder = collect(config('jmaxmlkinds'))->keys();
            return $this->sort(function ($a, $b) use($kindOrder) {
                return ($kindOrder->search($a->kind_of_info) > $kindOrder->search($b->kind_of_info));
            });
        });
    }
}
