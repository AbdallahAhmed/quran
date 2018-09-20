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

Route::post('/auth','API\AuthController@login');

Route::post('/register','API\AuthController@register');

Route::get('/page', 'API\PageController@show');
Route::get('/ayah', 'API\AyatController@show');

Route::group(["middleware" => ['api-auth']], function ($router) {
    Route::post('/profile/update','API\AuthController@update');


    Route::get('/profile/token_reset','API\AuthController@tokenReset');
});


