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

Route::get('example', 'ExternalCifController@searchCompany');

Route::group(['prefix' => 'admin', 'middleware' => ['CheckLocation', 'PanelWeb']], function () {
    Route::resource('user', 'UserController');
    Route::resource('categories', 'CategoriesController');
    Route::resource('cities', 'CitiesController');
    Route::resource('products', 'ProductsController');
    Route::resource('shop', 'ShopController');
    Route::resource('order', 'OrderController');
    Route::resource('purchase', 'PurchaseController');
    Route::resource('purchaseDetail', 'PurchaseDetailController');
    Route::resource('paymentPlatform', 'PaymentPlatformController');
    Route::resource('paymentSetting', 'PaymentSettingController');
    Route::resource('shopHours', 'HoursController');
    Route::resource('currencies', 'CurrenciesController');
    Route::resource('reports', 'ReportController');
    Route::resource('ticketSupports', 'TicketSupportController');
    Route::resource('comments', 'CommentsController');
    Route::resource('questions', 'QuestionsController');
    Route::resource('faq', 'FaqController');
    Route::resource('attributes', 'AttributesController');
    Route::resource('categoryAttributes', 'CategoryAttributesController');
    Route::resource('productVariation', 'ProductVariationController');
    Route::resource('shippingAddress', 'ShippingAddressController');
    Route::resource('banners', 'BannersController');
    Route::resource('userPayment', 'UserPaymentController');
    Route::resource('newsletter', 'NewsletterController');
    Route::resource('productAttached', 'ProductAttachedController');
    Route::resource('attached', 'AttachedController');
    Route::resource('zoneAvailable', 'ZoneAvailableController');

    Route::resource('validateCif', 'ExternalCifController');
    Route::resource('validatePhone', 'ExternalSmsController');
    Route::resource('delivery', 'ExternalDeliveryController');
    /**
     * Esta ruta debe pensarse creo que lo mejor es hacer un compoenent stripe por el lado del front
     * Success: http://localhost?scope=read_write&code={AUTHORIZATION_CODE}
     * Denied: http://localhost?error=access_denied&error_description=The%20user%20denied%20your%20request

     */
    Route::resource('stripe_auth', 'ExternalStripeAuthController');
});

Route::group(['prefix' => 'admin', 'middleware' => ['CheckLocation']], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::get('/', 'AuthController@index');
        Route::post('login', 'AuthController@store');
        Route::post('register', 'AuthController@register');
    });
});
