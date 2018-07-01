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
    Route::prefix('github')->name('github.')->group(function () {
        Route::get('callback', 'GitHubAccountController@handleProviderCallback')->name('callback');
        Route::get('login', 'GitHubAccountController@redirectToProvider')->name('login')->middleware('guest');
        Route::get('linktouser', 'GitHubAccountController@linkToUser')->name('linktouser')->middleware('auth');
        Route::delete('unlink', 'GitHubAccountController@unlinkFromUser')->name('unlink')->middleware('auth');
    });

    Route::prefix('twitter')->name('twitter.')->group(function () {
        Route::get('callback', 'TwitterAccountController@handleProviderCallback')->name('callback');
        Route::get('login', 'TwitterAccountController@redirectToProvider')->name('login')->middleware('guest');
        Route::get('linktouser', 'TwitterAccountController@linkToUser')->name('linktouser')->middleware('auth');
        Route::delete('unlink', 'TwitterAccountController@unlinkFromUser')->name('unlink')->middleware('auth');
    });
});

Route::prefix('home')->name('home.')->group(function () {
    Route::get('/', 'HomeController@index')->name('index');
    Route::get('social-accounts', 'HomeController@accounts')->name('accounts');
});
