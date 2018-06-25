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
        Collection::macro('sortWithOrderBy', function ($order, $callback, $options = SORT_REGULAR, $descending = false) {
            if (is_string($callback)) {
                $callback = function ($item) use ($callback) {
                    return data_get($item, $callback);
                };
            }

            return $this->sortBy(function ($item, $key) use ($order, $callback) {
                return $order->search($callback($item, $key));
            }, $options, $descending);
        });

        Collection::macro('sortByKind', function ($options = SORT_REGULAR, $descending = false) {
            $kinds = collect(config('jmaxml.kinds'))->keys();
            return $this->sortWithOrderBy($kinds, 'kind_of_info', $options, $descending);
        });

        Collection::macro('sortByType', function ($options = SORT_REGULAR, $descending = false) {
            $types = collect(config('jmaxml.feedtypes'));
            return $this->sortWithOrderBy($types, 'type', $options, $descending);
        });
    }
}
