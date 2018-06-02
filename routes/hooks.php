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
 * WebSub webhooks
 */
Route::prefix('websub')->name('websub.')->group(function () {
    Route::get('subscriber', 'WebSubController@subscribeCheck')->name('subscribeCheck');
    Route::post('subscriber', 'WebSubController@receiveFeed')->name('receiveFeet');
});

// old prefix
Route::prefix('push')->name('push.')->group(function() {
    Route::get('subscriber', 'WebSubController@subscribeCheck')->name('subscribeCheck');
    Route::post('subscriber', 'WebSubController@receiveFeed')->name('receiveFeet');
});

