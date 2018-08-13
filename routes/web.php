<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'MainController@index')->name('index');

Route::prefix('observatory')->group(function () {
    Route::get('{observatory}', 'MainController@observatory')->name('observatory');
});

Route::prefix('entry')->group(function () {
    Route::get('{entry}.xml', 'MainController@entryXml')->name('entry.xml');
    Route::get('{entry}.json', 'MainController@entryJson')->name('entry.json');
    Route::get('{entry}', 'MainController@entry')->name('entry');
});

Auth::routes();

Route::namespace('Auth')->group(function () {
    $socialite = [
        'github' => 'GitHubAccountController',
        'twitter' => 'TwitterAccountController',
        'line' => 'LineAccountController',
    ];

    foreach ($socialite as $provider => $controller) {
        Route::prefix($provider)->name($provider.'.')->group(function () use ($controller) {
            Route::get('callback', $controller.'@handleProviderCallback')->name('callback');
            Route::get('login', $controller.'@redirectToProvider')->name('login')->middleware('guest');
            Route::get('linktouser', $controller.'@linkToUser')->name('linkToUser')->middleware('auth');
            Route::post('notify', $controller.'@testNotify')->name('notify')->middleware('auth');
            Route::delete('unlink', $controller.'@unlinkFromUser')->name('unlink')->middleware('auth');
        });
    }
});

Route::prefix('home')->name('home.')->group(function () {
    Route::get('/', 'HomeController@index')->name('index');
    Route::get('social-accounts', 'HomeController@accounts')->name('accounts');
});
