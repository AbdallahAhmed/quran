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

// Authentication
Route::post('/auth', 'API\AuthController@login');
Route::get('/auth/resendCode', 'API\AuthController@resendCode');
Route::post('/auth/verify', 'API\AuthController@verify');
Route::post('/auth/forget-password', 'API\AuthController@forgetPassword');
Route::post('/auth/reset-password', 'API\AuthController@resetPassword');

Route::post('/register', 'API\AuthController@register');

Route::get('/page', 'API\PageController@index');
Route::get('/ayah', 'API\AyatController@index');
Route::get('/search', 'API\AyatController@search');
Route::get('/surah', 'API\SuratController@index');
Route::get('/juz', 'API\JuzController@index');


Route::get('/contests', 'API\ContestController@index');
Route::get('/contests/details', 'API\ContestController@details');

Route::group(["middleware" => ['api-auth']], function ($router) {

    // profile
    $router->post('/profile/update', 'API\AuthController@update');
    $router->get('/profile/token_reset', 'API\AuthController@tokenReset');


    // contests
    $router->post('contests/create', 'API\ContestController@create');
    $router->post('contests/join', 'API\ContestController@join');
    $router->post('contests/leave', 'API\ContestController@leave');
    $router->get('contests/current', 'API\ContestController@current');

    // bookmarks
    $router->get('bookmarks', 'API\BookmarkController@index');
    $router->post('bookmarks/create', 'API\BookmarkController@create');
    $router->post('bookmarks/delete', 'API\BookmarkController@delete');
    $router->post('bookmarks/clear', 'API\BookmarkController@clear');
});


