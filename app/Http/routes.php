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

Route::get('/', function () {
    return response()->json(['Welcome dude'],200);
});

Route::group(['prefix' => 'api/v1'], function() {
    Route::resource('post', 'PostController', [
        'except' => ['edit', 'create']
    ]);

    Route::post('signup', [
        'uses' => 'ApiAuthController@store'
    ]);

    Route::post('signin', [
        'uses' => 'ApiAuthController@signin'
    ]);
});
