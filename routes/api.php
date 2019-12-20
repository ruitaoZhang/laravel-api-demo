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

Route::group([
    'prefix' => 'v1'
], function (){
    Route::post('login', 'Api\AuthController@login');
    Route::post('register', 'Api\AuthController@register');
    Route::get('test', 'Api\UserController@test');
    Route::get('testQuery', 'Api\UserController@testQuery');
    Route::group([
        'middleware' => ['auth:api', 'scope:user-api']
    ], function () {
        Route::post('user_info', 'Api\AuthController@userInfo');
        Route::get('loginOut', 'Api\AuthController@loginOut');
    });

    Route::post('adminLogin', 'Admin\AuthController@login');
    Route::post('adminRegister', 'Admin\AuthController@register');
    Route::group([
        'middleware' => ['auth:admin', 'scope:admin-api']
    ], function () {
        Route::get('admin_info', 'Admin\AuthController@adminInfo');
        Route::get('adminLoginOut', 'Admin\AuthController@loginOut');
    });
});
