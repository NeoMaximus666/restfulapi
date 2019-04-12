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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group([

    'middleware' => 'api',
//    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'Api\AuthController@login');
    Route::post('logout', 'Api\AuthController@logout');
    Route::post('refresh', 'Api\AuthController@refresh');
    Route::post('me', 'Api\AuthController@me');

});

Route::group(['prefix' => 'v1','middleware' => 'api'], function (){

    Route::resource('meeting', 'Api\MeetingController', [
        'except' => ['create', 'edit']
    ]);
    Route::resource('meeting/registration', 'Api\RegisterMeetingController', [
        'only' => ['store', 'destroy']
    ]);

    Route::post('/user/register', [
        'uses' => 'Api\UsersRegistrationController@store'
    ]);
    /*
    Route::post('/user/signin', [
        'uses' => 'AuthController@signin'
    ]);*/
});