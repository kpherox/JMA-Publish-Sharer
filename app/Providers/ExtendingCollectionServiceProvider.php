<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class ExtendingCollectionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('sortWithOrderBy', function ($callback, $order, $options = SORT_REGULAR, $descending = false, $holdKey = true) {
            if (is_string($callback)) {
                $callback = function ($item) use ($callback) {
                    return data_get($item, $callback);
                };
            }

            $sorted = $this->sortBy(function ($item, $key) use ($order, $callback) {
                return $order->search($callback($item, $key));
            }, $options, $descending);

            if ($holdKey) {
                return $sorted;
            }

            return $sorted->values();
        });

        Collection::macro('sortByKind', function ($options = SORT_REGULAR, $descending = false) {
            $kinds = collect(config('jmaxml.kinds'))->keys();

            return $this->sortWithOrderBy('kind_of_info', $kinds, $options, $descending, false);
        });

        Collection::macro('sortByType', function ($options = SORT_REGULAR, $descending = false) {
            $types = collect(config('jmaxml.feedtypes'));

            return $this->sortWithOrderBy('type', $types, $options, $descending, false);
        });
    }
}
