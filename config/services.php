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
        'socialite' => env('ENABLE_TWITTER_LOGIN', false),
        'client_id' => env('TWITTER_KEY', ''),
        'client_secret' => env('TWITTER_SECRET', ''),
        'consumer_key' => env('TWITTER_API_KEY', env('TWITTER_KEY', '')),
        'consumer_secret' => env('TWITTER_API_SECRET', env('TWITTER_SECRET', '')),
        'access_token' => env('TWITTER_ACCESS_TOKEN', ''),
        'access_secret' => env('TWITTER_ACCESS_SECRET', ''),
        'simple_icons' => 'Twitter',
        'name' => 'Twitter',
        'oauth1' => true,
        'notification' => env('ENABLE_TWITTER_NOTIFY', false),
    ],

    'line' => [
        'socialite' => env('ENABLE_LINE_LOGIN', false),
        'client_id' => env('LINE_CHANNEL_ID', ''),
        'client_secret' => env('LINE_CHANNEL_SECRET', ''),
        'token' => env('LINE_BOT_ACCESS_TOKEN', ''),
        'secret' => env('LINE_BOT_SECRET', ''),
        'user_id' => env('LINE_DEFAULT_USER_ID', ''),
        'simple_icons' => 'Line',
        'name' => 'LINE',
        'notification' => env('ENABLE_LINE_NOTIFY', false),
    ],

    'github' => [
        'socialite' => env('ENABLE_GITHUB_LOGIN', false),
        'client_id' => env('GITHUB_KEY', ''),
        'client_secret' => env('GITHUB_SECRET', ''),
        'simple_icons' => 'GitHub',
        'name' => 'GitHub',
    ],

];
