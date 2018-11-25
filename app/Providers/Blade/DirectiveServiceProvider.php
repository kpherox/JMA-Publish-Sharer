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
        \Blade::directive('datetime', function ($time, $timezone = null) {
            $timezone = $timezone ?: 'config(\'app.timezone\')';

            return "<?php
                \$carbon = \Carbon\Carbon::parse($time);
                \$carbon->setTimezone($timezone);
                echo \$carbon;
            ?>";
        });

        \Blade::directive('formatToHTML', function ($text) {
            return "<?php echo formatToHTML(e($text)); ?>";
        });

        \Blade::directive('formatIppanho', function ($text) {
            return "<?php echo formatIppanhoText(e($text)); ?>";
        });
    }
}
