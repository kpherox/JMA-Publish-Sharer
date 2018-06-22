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

Route::prefix('observatory')->group(function() {
    Route::get('{observatory}', 'MainController@observatory')->name('observatory');
});

Route::prefix('entry')->group(function() {
    Route::get('{uuid}.xml', 'MainController@entryXml')->name('entry.xml');
    Route::get('{uuid}.json', 'MainController@entryJson')->name('entry.json');
    Route::get('{entry}', 'MainController@entry')->name('entry');
});

Auth::routes();

Route::namespace('Auth')->group(function() {
    Route::prefix('github')->group(function() {
        Route::get('login', 'GitHubAccountController@redirectToProvider')->name('github.login');
        Route::get('linktouser', 'GitHubAccountController@linkToUser')->name('github.linktouser');
        Route::get('callback', 'GitHubAccountController@handleProviderCallback')->name('github.callback');
    });

    Route::prefix('twitter')->group(function() {
        Route::get('login', 'TwitterAccountController@redirectToProvider')->name('twitter.login');
        Route::get('linktouser', 'TwitterAccountController@linkToUser')->name('twitter.linktouser');
        Route::get('callback', 'TwitterAccountController@handleProviderCallback')->name('twitter.callback');
    });
});

Route::prefix('home')->group(function() {
    Route::get('/', 'HomeController@index')->name('home.index');
    Route::get('social-accounts', 'HomeController@accounts')->name('home.accounts');
});

