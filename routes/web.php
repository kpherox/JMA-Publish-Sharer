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

Route::view('/', 'welcome')->name('index');

Auth::routes();

Route::namespace('Auth')->group(function() {
    Route::prefix('github')->group(function() {
        Route::get('login', 'GitHubAccountController@redirectToProvider')->name('github.login')->middleware('guest');
        Route::get('linktouser', 'GitHubAccountController@linkToUser')->name('github.linktouser')->middleware('auth');
        Route::get('callback', 'GitHubAccountController@handleProviderCallback')->name('github.callback');
    });

    Route::prefix('twitter')->group(function() {
        Route::get('login', 'TwitterAccountController@redirectToProvider')->name('twitter.login')->middleware('guest');
        Route::get('linktouser', 'TwitterAccountController@linkToUser')->name('twitter.linktouser')->middleware('auth');
        Route::get('callback', 'TwitterAccountController@handleProviderCallback')->name('twitter.callback');
    });
});

Route::prefix('home')->group(function() {
    Route::get('/', 'HomeController@index')->name('home.index');
    Route::get('social-accounts', 'HomeController@accounts')->name('home.accounts');
});

