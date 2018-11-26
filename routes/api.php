<?php

use App\Models\Juz;
use App\User;
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
Route::post('/auth/add_user', 'API\AuthController@register');

Route::get('/page', 'API\PageController@index');
Route::get('/ayah', 'API\AyatController@index');
Route::get('/search', 'API\AyatController@search');
Route::get('/surah', 'API\SuratController@index');
Route::get('/JuzAll', 'API\JuzController@JuzAll');
Route::get('/juz', 'API\JuzController@index');
Route::get('/juz/sections', 'API\JuzController@sections');

Route::get('/surat', function () {
    $surat = \App\Models\Surat::select(['id', 'name', 'englishname'])->get()->toArray();
    return response()->json($surat);
   // file_put_contents('surat.json', json_encode($surat));
});
Route::get('send', function (){
   $notify = new \App\Http\Controllers\NotificationController("sss", "ssss");
   $notify->send("dCvrvQxy95c:APA91bG8eyS-xKI9uP3u4AKEsDTZd6j2DhIwnj-9Fgw1T_IHsXJRqFrWbF-spvbG_iFFdOOSo31L1Tlfqmk5etQqQyrmOuudBwjM88XMyydW7T_fD5ArJ1TV4YpdVD4BzAIYeY1yXdvx", []);
});
Route::get('/contests', 'API\ContestController@index');
Route::get('/contests/details', 'API\ContestController@details');

Route::group(["middleware" => ['api-auth']], function ($router) {

    //logout to clear device token
    $router->post('/logout', 'API\AuthController@logout');
    $router->post('/lang', 'API\AuthController@changeLang');

    // profile
    $router->post('/profile/update', 'API\AuthController@update');
    $router->get('/profile/token_reset', 'API\AuthController@tokenReset');


    // contests
    $router->post('contests/create', 'API\ContestController@create');
    $router->post('contests/join', 'API\ContestController@join');
    $router->post('contests/leave', 'API\ContestController@leave');
    $router->get('contests/current', 'API\ContestController@current');
    $router->post('contests/updates', 'API\ContestController@updates');

    // bookmarks
    $router->get('bookmarks', 'API\BookmarkController@index');
    $router->post('bookmarks/create', 'API\BookmarkController@create');
    $router->post('bookmarks/delete', 'API\BookmarkController@delete');
    $router->post('bookmarks/clear', 'API\BookmarkController@clear');

    //khatemas
    $router->get('khatemas', 'API\KhatemaController@index');
    $router->post('khatemas/create', 'API\KhatemaController@create');
    $router->post('khatemas/update', 'API\KhatemaController@update');

    //notifications
    $router->get('notifications', 'API\NotificationController@index');
});


