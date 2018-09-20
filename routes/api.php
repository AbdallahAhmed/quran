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

Route::post('/auth', 'API\AuthController@login');

Route::post('/register', 'API\AuthController@register');

Route::get('/page', 'API\PageController@index');
Route::get('/ayah', 'API\AyatController@index');
Route::get('/surah', 'API\SuratController@index');
Route::get('/juz', 'API\JuzController@index');

Route::group(["middleware" => ['api-auth']], function ($router) {

    // profile
    $router->post('/profile/update', 'API\AuthController@update');
    $router->get('/profile/token_reset', 'API\AuthController@tokenReset');


    // contests
    $router->post('contests/create', 'API\ContestController@create');

    $router->post('contests/join', 'API\ContestController@join');

    $router->post('contests/leave', 'API\ContestController@leave');
});


