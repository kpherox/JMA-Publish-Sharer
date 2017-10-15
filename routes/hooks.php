<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| WebHooks Routes
|--------------------------------------------------------------------------
|
| Here is where you can register WebHooks routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * PubSubHubbub(PuSH) webhooks
 */
Route::prefix('push')->group(function () {
    Route::get('subscriber', 'PushController@subscriber');
    Route::post('subscriber', 'PushController@receiveFeed');
});

