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

Route::get('/', function () {
    return redirect('dashboard');
});

Auth::routes();

Route::get('/home', 'HomeController@index');
Route::get('/dashboard', 'DashboardController@index');
Route::get('/user/{id}', 'UserController@index');
Route::post('/user/{id}', 'UserController@update');
Route::get('/user/{id}/{factuur_id}', 'UserController@fetch_facturen');
Route::post('/user/{id}/{factuur_id}', 'UserController@update_facturen');
Route::get('/remove_facturen', 'UserController@remove_facturen');
Route::get('/fetch_fackosten', 'UserController@fetch_fackosten');
Route::get('/add_fackosten', 'UserController@add_fackosten');
Route::get('/update_fackosten', 'UserController@update_fackosten');
Route::get('/remove_fackosten', 'UserController@remove_fackosten');
Route::get('/fetch_kosten', 'UserController@fetch_kosten');
Route::get('/update_kosten', 'UserController@update_kosten');
Route::get('/remove_kosten', 'UserController@remove_kosten');
Route::get('/fetch_urenkm', 'UserController@fetch_urenkm');
Route::get('/update_urenkm', 'UserController@update_urenkm');
Route::get('/remove_urenkm', 'UserController@remove_urenkm');
Route::get('temp', 'TempController@index');
Route::post('temp/update', 'TempController@update');