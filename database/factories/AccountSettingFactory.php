<?php

use Faker\Generator as Faker;

$factory->define(App\Eloquents\AccountSetting::class, function (Faker $faker) {
    return [
        'type' => 'notification',
    ];
});
