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
Auth::routes();


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', 'Admin\DashboardController@index')
        ->name('AdminHome');

    Route::post('/mail', 'Admin\DashboardController@saveMail')
        ->name('AdminSaveMail');

    Route::post('/sms', 'Admin\DashboardController@saveSMS')
        ->name('AdminSaveSMS');

    Route::post('/stripe', 'Admin\DashboardController@saveStripe')
        ->name('AdminSaveStripe');
});

Route::prefix('install-2')->group(function () {

    Route::get('/', 'Installer\WelcomeController@welcome')
        ->name('InstallerWelcome');

    Route::get('/account', 'Installer\WelcomeController@account')
        ->name('InstallerWAccount');

    Route::post('/account', 'Installer\EnvironmentInstaller@saveFileWizard')
        ->name('InstallerSaveEnv');

    Route::get('/migrations', 'Installer\MigrationsController@overview')
        ->name('InstallerMigrations');

    Route::post('/migrations', 'Installer\MigrationsController@database')
        ->name('InstallerMigrationsSave');

    Route::get('/overview', 'Installer\OverViewController@overview')
        ->name('InstallerOverview');

});
