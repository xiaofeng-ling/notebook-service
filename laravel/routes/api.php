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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('refresh', 'api\ApiAuthController@refresh')->name('refresh');
Route::post('login', 'api\ApiAuthController@login');
Route::post('logout', 'api\ApiAuthController@logout')->name('logout');

Route::post('/add', function() {
    return (function() {
        return apiJson(request()->user());
    })();
})->middleware('jwtauth');
