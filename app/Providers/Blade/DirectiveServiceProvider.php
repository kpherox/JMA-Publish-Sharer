<?php

namespace App\Providers\Blade;

use Illuminate\Support\ServiceProvider;

class DirectiveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \Blade::directive('datetime', function ($time) {
            return "<?php echo \Carbon\Carbon::parse($time); ?>";
        });
    }
}
