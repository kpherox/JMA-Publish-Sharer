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
            $kindOrder = collect(config('jmaxml.kinds'))->keys();
            return $this->sort(function ($a, $b) use ($kindOrder) {
                return ($kindOrder->search($a->kind_of_info) > $kindOrder->search($b->kind_of_info));
            });
        });

        Collection::macro('sortByFeedType', function () {
            $typeOrder = collect(config('jmaxml.feedtypes'));

            return $this->sort(function ($a, $b) use ($typeOrder) {
                return ($typeOrder->search($a->type) > $typeOrder->search($b->type));
            });
        });
    }
}
