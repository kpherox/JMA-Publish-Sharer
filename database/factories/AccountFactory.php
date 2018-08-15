<?php

use Faker\Generator as Faker;

$factory->define(App\Eloquents\LinkedSocialAccount::class, function (Faker $faker) {
    static $password;

    return [
        'provider_name' => null,
        'provider_id' => $faker->numberBetween($min = 1, $max = 100000000),
        'name' => $faker->name,
        'nickname' => $faker->userName,
        'avatar' => $faker->imageUrl,
        'token' => str_random(10),
        'token_secret' => str_random(10),
    ];
});
