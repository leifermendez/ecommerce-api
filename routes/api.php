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

/**
 * Front API ------
 */

Route::group(['prefix' => '1.0', 'middleware' => ['FrontWeb','CookiesSuggestions']], function () {

    /**
     * Rutas libres
     */

    Route::resource('/auth', '_FrontAuth');
    Route::get('/auth/reset_password/{email}', '_FrontAuth@password');
    Route::post('/auth/reset_password', '_FrontAuth@resetPassword');

    Route::group(['prefix' => 'rest'], function () {

        Route::resource('/suggestions', '_FrontSuggestion')
        ->only([
            'index'
        ]);

        Route::resource('/zone-available', '_FrontZoneAvailable')
            ->only([
                'index'
            ]);

        Route::resource('/banners', '_FrontBanners')
            ->only([
                'index'
            ]);

        Route::resource('/products', '_FrontProducts')
            ->only([
                'index',
            ])
            ->middleware('CheckLocation');

        Route::resource('/products', '_FrontProducts')
            ->only([
                'show'
            ]);

        Route::resource('/search', '_FrontSearch')
            ->only([
                'index'
            ])
            ->middleware('CheckLocation');

        Route::resource('/seller', '_FrontSeller')
            ->only([
                'show'
            ]);

        Route::resource('/shop', '_FrontShop');

        Route::resource('/newsletter', '_FrontNewsletter')
            ->only([
                'store'
            ]);

        Route::resource('/categories', '_FrontCategories')
            ->only([
                'index',
                'show'
            ]);

        Route::resource('/check-cif', '_FrontCif')
            ->only([
                'show'
            ]);

        Route::resource('/stripe-auth', 'ExternalStripeAuthController')
            ->only([
                'index',
                'store'
            ]);

        Route::resource('/blog', '_FrontBlog')
            ->only([
                'show',
                'index'
            ]);

        Route::resource('/prevent-check', '_FrontPreventCheck')
            ->only([
                'show'
            ]);

            Route::resource('/comments', '_FrontComments')
            ->only([
                'index'
            ]);

    });

    /**
     * FIN Rutas libres
     */


    /**
     * Rutas protegidas
     *
     */
    Route::group(['prefix' => 'rest', 'middleware' => ['AuthJWT']], function () {

        Route::resource('/shopping-cart', '_FrontShoppingCart')
            ->middleware('CheckLocation');

        Route::resource('/purchase', '_FrontPurchase');

        Route::resource('/sales', '_FrontSales');

        Route::resource('/shipping', '_FrontShipping');

        Route::resource('/payment-user', '_FronUserPayment');

        Route::resource('/product-category', '_FrontProductsCategories');

        Route::resource('/products', '_FrontProducts')
            ->only([
                'store',
                'update',
                'destroy'
            ]);

        Route::resource('/schedules', '_FrontShopSchedules');

        Route::resource('/products-variations', '_FrontProductVariations');

        Route::resource('/comments', '_FrontComments')
            ->only([
                'store',
                'show'
            ]);

        Route::resource('/payment', '_FrontPayment')
            ->only([
                'store'
            ]);

        Route::resource('/user', '_FrontUser')
            ->only([
                'show',
                'update',
                'destroy'
            ]);

        Route::resource('/validatePhone', '_FrontValidatePhone')
            ->only([
                'store',
                'update'
            ]);

        Route::resource('/delivery', '_FrontDelivery')
            ->only([
                'store',
                'show'
            ]);

        Route::resource('/clear-cache', '_FrontCache')
            ->only([
                'store'
            ]);

        Route::resource('/media', '_FrontAttached');

        Route::resource('/product-media', '_FrontAttachedProducts');

        Route::resource('/attributes-category', '_FrontAttributesCategories');

        Route::resource('/pickup-address', '_FrontPickupAddress');
    });


    /**
     * Fin rutas protegidas
     */

});


/**
 * Fin Front API ------
 */


/**
 * Grupo ADMIN --------
 */
Route::group(['prefix' => 'admin', 'middleware' => ['PanelWeb']], function () {
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
    Route::resource('productAttributes', 'ProductsAttributesController');
    Route::resource('productVariation', 'ProductVariationController');
    Route::resource('productCategories', 'ProductsCategories');
    Route::resource('shippingAddress', 'ShippingAddressController');
    Route::resource('banners', 'BannersController');
    Route::resource('userPayment', 'UserPaymentController');
    Route::resource('newsletter', 'NewsletterController');
    Route::resource('productAttached', 'ProductAttachedController');
    Route::resource('attached', 'AttachedController');
    Route::resource('zoneAvailable', 'ZoneAvailableController');
    Route::resource('blog', 'BlogController');
    Route::resource('mailMarketing', 'MailMarketing');

    Route::resource('validateCif', 'ExternalCifController');
    Route::resource('validatePhone', 'ExternalSmsController');
    Route::resource('delivery', 'ExternalDeliveryController');
    Route::post('/products/csv', 'ProductVariationController@import');
    /**
     * Esta ruta debe pensarse creo que lo mejor es hacer un compoenent stripe por el lado del front
     * Success: http://localhost?scope=read_write&code={AUTHORIZATION_CODE}
     * Denied: http://localhost?error=access_denied&error_description=The%20user%20denied%20your%20request
     */
    Route::resource('stripe_auth', 'ExternalStripeAuthController');
});


Route::group(['prefix' => 'admin'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::get('/', 'AuthController@index');
        Route::post('login', 'AuthController@store');
        Route::post('register', 'AuthController@register');
        Route::get('reset_password/{email}', 'AuthController@password');
        Route::post('reset_password', 'AuthController@resetPassword');
    });
});

/**
 * Fin Grupo ADMIN --------
 */