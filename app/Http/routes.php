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
// Homepage
Route::get('/', 'FrontController@index');
// Homepage Ajax
Route::post('/vk-data', 'FrontController@vk_data');

Route::auth();
// Dashboard
Route::get('/dashboard', 'DashboardController@index');
Route::get('/dashboard/settings', 'DashboardController@settings');
Route::get('/dashboard/help', 'DashboardController@help');
Route::post('/dashboard/settings', 'DashboardController@store');
// Dashboard Ajax
Route::post('/dashboard/start-import/', 'DashboardController@start_import');

