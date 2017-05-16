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

Route::get('/home',                     'HomeController@index');

Route::get('/dashboard',                'DashboardController@index');

Route::get('/user/{id}',                'UserController@index');
Route::post('/user/{id}',               'UserController@update');
Route::get('/user/{id}/{factuur_id}',   'UserController@fetchFacturen');
Route::post('/user/{id}/{factuur_id}',  'UserController@updateFacturen');
Route::get('/fetch_fackosten',          'UserController@fetchFackosten');
Route::get('/add_fackosten',            'UserController@addFackosten');
Route::get('/update_fackosten',         'UserController@updateFackosten');
Route::get('/remove_fackosten',         'UserController@removeFackosten');
Route::get('/fetch_kosten',             'UserController@fetchKosten');
Route::get('/update_kosten',            'UserController@updateKosten');
Route::get('/fetch_urenkm',             'UserController@fetchUrenkm');
Route::get('/update_urenkm',            'UserController@updateUrenkm');
Route::get('/remove_row',               'UserController@removeRow');

Route::get('temp',                      'TempController@index');
Route::post('temp/update',              'TempController@update');