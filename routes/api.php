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
        Route::post('login', 'AuthController@store');
        Route::post('register', 'AuthController@register');
    });
});


Route::group(['prefix' => 'admin', 'middleware' => ['CheckLocation', 'PanelWeb']], function () {
    Route::resource('user', 'UserController');
    Route::resource('categories', 'CategoriesController');
    Route::resource('cities', 'CitiesController');
    Route::resource('products', 'ProductsController');
    Route::resource('shop', 'ShopController');
    Route::resource('order', 'OrderController');
    Route::resource('purchase', 'PurchaseController');
    Route::resource('paymentPlatform', 'PaymentPlatformController');
    Route::resource('paymentSetting', 'PaymentSettingController');
    Route::resource('shopHours', 'HoursController');
});

