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

Route::get('/sas', function () {
    dd(remaining_time_human(10081));
    $users = User::with('contest')->get();
    foreach ($users as $user) {
        $contest = $user->contest[0];
        $expired_at = $contest->expired_at;
        $contest_remaining = $expired_at->diffInMinutes(\Carbon\Carbon::now());
        $contest_all = $expired_at->diffInMinutes($contest->start_at);
        $contest_precentage = (int)(($contest_remaining / $contest_all) * 100);
       // if ($contest_precentage == 15 or $contest_precentage == 50 or $contest_precentage == 75) {
            $contest_pages = get_contest_pages($contest->juz_from, $contest->juz_to);
            $user_pages = json_decode($contest->pivot->pages ? $contest->pivot->pages : '[]');
            $read_percentage = count($user_pages) > 0 ? (int)((count($user_pages) / count($contest_pages) * 100)) : 0;
        //}
    }
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


