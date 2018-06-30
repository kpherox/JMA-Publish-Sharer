<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Eloquents\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'googleapi' => [
        'maps' => [
            'js' => [
                'key' => env('GOOGLE_MAPS_JS_API_KEY'),
            ],
        ],
    ],

    'twitter' => [
        'socialite' => true,
        'client_id' => env('TWITTER_KEY'),
        'client_secret' => env('TWITTER_SECRET'),
        'consumer_key' => env('TWITTER_API_KEY', env('TWITTER_KEY')),
        'consumer_secret' => env('TWITTER_API_SECRET', env('TWITTER_SECRET')),
        'access_token' => env('TWITTER_ACCESS_TOKEN'),
        'access_secret' => env('TWITTER_ACCESS_SECRET'),
        'simple_icons' => 'Twitter',
        'name' => 'Twitter',
        'oauth1' => true,
        'notification' => true,
    ],

    'line' => [
        'socialite' => true,
        'client_id' => env('LINE_CHANNEL_ACCESS_TOKEN'),
        'client_secret' => env('LINE_CHANNEL_SECRET'),
        'token' => env('LINE_BOT_CHANNEL_ACCESS_TOKEN'),
        'secret' => env('LINE_BOT_CHANNEL_SECRET'),
        'user_id' => env('LINE_DEFAULT_USER_ID'),
        'simple_icons' => 'Line',
        'name' => 'LINE',
        'notification' => true,
    ],

    'github' => [
        'socialite' => true,
        'client_id' => env('GITHUB_KEY'),
        'client_secret' => env('GITHUB_SECRET'),
        'simple_icons' => 'GitHub',
        'name' => 'GitHub',
    ],

];
