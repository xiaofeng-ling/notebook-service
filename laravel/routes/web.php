<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect("/notebookMain");
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('notebook/search/{id}', 'NotebookController@search');
Route::post('notebook/getList', 'NotebookController@getList');
Route::post('notebook/modifyTitle/{id}', 'NotebookController@modifyTitle');
Route::resource('notebook', 'NotebookController', ['except' => ['create', 'edit']]);

Route::post('notebookMain/getList', 'NotebookMainController@getList');
Route::resource('notebookMain', 'NotebookMainController', ['except' => ['create', 'edit']]);
