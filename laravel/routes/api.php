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

Route::middleware('jwtauth')->group(function () {
    Route::get('notebook', 'api\NotebookController@index');
    Route::post('notebook', 'api\NotebookController@store');
    Route::get('notebook/{id}', 'api\NotebookController@show');
    Route::post('notebook/update', 'api\NotebookController@update');
    Route::post('notebook/delete', 'api\NotebookController@destroy');
    Route::post('notebook/modifyTitle', 'api\NotebookController@modifyTitle');

    Route::get('notebookMain', 'api\NotebookMainController@index');
    Route::post('notebookMain', 'api\NotebookMasinController@store');
    Route::post('notebookMain/delete', 'api\NotebookMainController@destroy');
});
