<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Database\Eloquent\Builder;

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
        Builder::macro('groupConcat', function(String $name, String $separator = null, Bool $distinct = false, String $columnName = null) {
            $separator = $separator ? "'$separator'" : '\',\'';
            $columnName = ' AS '.($columnName ?: $name);
            $name = $distinct ? 'DISTINCT '.$name : $name;
            $connection = config('database.default');
            switch (config("database.connections.{$connection}.driver"))
            {
                case 'mysql':
                case 'sqlite':
                    $sql = 'GROUP_CONCAT('.$name.' SEPARATOR '.$separator.')';
                    break;
                case 'pgsql':
                    $sql = 'ARRAY_TO_STRING(ARRAY(SELECT unnest(array_agg('.$name.'))), '.$separator.')';
                    break;
                case 'sqlsrv':
                    $sql = '(SELECT '.$name.' + \',\' FOR XML PATH(\'\'))';
                default:
                    throw new \Exception('Driver not supported.');
                    break;
            }

            return $this->selectRaw($sql . $columnName);
        });
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
