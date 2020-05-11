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

//
//Auth::routes();

//Route::get('/ecommerce-panel/home', 'HomeController@index')->name('home');
//

Route::prefix('ecommerce-panel')->group(function () {
    Route::get('/', 'Installer\WelcomeController@test');
});
