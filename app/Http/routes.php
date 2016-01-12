<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;

Route::get('/', [
    'as' => 'user.home',
    'uses' => 'UserController@getHome'
]);

// authentication and registration routes
Route::get('/auth/login', [
    'as' => 'auth.login.show',
    'uses' => 'Auth\AuthController@getLogin'
]);
Route::post('/auth/login', [
    'as' => 'auth.login',
    'uses' => 'Auth\AuthController@postLogin'
]);
Route::get('/auth/logout', [
    'as' => 'auth.logout',
    'uses' => 'Auth\AuthController@getLogout'
]);
Route::get('/auth/register', [
    'as' => 'auth.register.show',
    'uses' => 'Auth\AuthController@getRegister'
]);
Route::post('/auth/register', [
    'as' => 'auth.register',
    'uses' => 'Auth\AuthController@postRegister'
]);
Route::get('/auth/confirm_register/{token}', [
    'as' => 'auth.register.confirm',
    'uses' => 'Auth\AuthController@confirmRegisterEmail'
]);

// reset forgotten password routes
Route::get('/auth/reset_password/email', [
    'as' => 'auth.reset_password.email.show',
    'uses' => 'Auth\SettingsController@getEmail'
]);
Route::post('/auth/reset_password/email', [
    'as' => 'auth.reset_password.email',
    'uses' => 'Auth\SettingsController@postEmail'
]);
Route::get('/auth/reset_password/{token}', [
    'as' => 'auth.reset_password.show',
    'uses' => 'Auth\SettingsController@getReset'
]);
Route::post('/auth/reset_password', [
    'as' => 'auth.reset_password',
    'uses' => 'Auth\SettingsController@postReset'
]);

// change password routes
Route::get('/settings/change_password', [
    'as' => 'settings.change_password.show',
    'uses' => 'Auth\SettingsController@getChangePassword'
]);
Route::post('/settings/change_password', [
    'as' => 'settings.change_password',
    'uses' => 'Auth\SettingsController@postChangePassword'
]);

// change email routes
Route::get('/settings/change_email', [
    'as' => 'settings.change_email.show',
    'uses' => 'Auth\SettingsController@getChangeEmail'
]);
Route::post('/settings/change_email', [
    'as' => 'settings.change_email',
    'uses' => 'Auth\SettingsController@postChangeEmail'
]);
Route::get('/settings/change_email/{token}', [
    'as' => 'settings.change_email.confirm',
    'uses' => 'Auth\SettingsController@confirmChangeEmail'
]);
