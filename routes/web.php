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

Route::prefix('twitter')->group(function() {
    Route::get('login', 'Auth\TwitterAccountController@redirectToProvider')->name('twitter.login');
    Route::get('linktouser', 'Auth\TwitterAccountController@linkToUser')->name('twitter.linktouser');
    Route::get('callback', 'Auth\TwitterAccountController@handleProviderCallback')->name('twitter.callback');
});

Route::prefix('home')->group(function() {
    Route::get('/', 'HomeController@index')->name('home.index');
    Route::get('social-accounts', 'HomeController@accounts')->name('home.accounts');
});

