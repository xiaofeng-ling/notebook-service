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

Route::post('notebook/modifyTitle/{id}', 'api\NotebookController@modifyTitle')->middleware('jwtauth');
Route::resource('notebook', 'api\NotebookController', ['except' => ['create', 'edit']])->middleware('jwtauth');

Route::resource('notebookMain', 'api\NotebookMainController', ['except' => ['create', 'edit']])->middleware('jwtauth');
