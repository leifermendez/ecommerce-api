<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => '1.0', 'middleware' => ['CheckLocation']], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::resource('login', 'AuthController');
        Route::resource('register', 'UserController', ['only' => [
            'store'
        ]]);
    });
});

Route::group(['prefix' => '1.0', 'middleware' => ['CheckLocation', 'FrontWeb']], function () {
    Route::resource('user', 'AuthController');
});
