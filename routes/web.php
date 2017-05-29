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
Route::get('/user', function () {
    return redirect('users');
});

Auth::routes();

Route::get('/dashboard',                'DashboardController@index');
Route::get('/users',                    'UsersController@index');
Route::get('/users/api',                'UsersController@api');
Route::get('/user/{id}',                'UserInfoController@index');
Route::post('/user/{id}',               'UserInfoController@update');
Route::get('/user/{id}/{factuur_id}',   'UserInfoController@fetchFacturen');
Route::post('/user/{id}/{factuur_id}',  'UserInfoController@updateFacturen');
Route::get('/fetch_fackosten',          'UserInfoController@fetchFackosten');
Route::get('/add_fackosten',            'UserInfoController@addFackosten');
Route::get('/update_fackosten',         'UserInfoController@updateFackosten');
Route::get('/remove_fackosten',         'UserInfoController@removeFackosten');
Route::get('/fetch_kosten',             'UserInfoController@fetchKosten');
Route::get('/update_kosten',            'UserInfoController@updateKosten');
Route::get('/fetch_urenkm',             'UserInfoController@fetchUrenkm');
Route::get('/update_urenkm',            'UserInfoController@updateUrenkm');
Route::get('/remove_row',               'UserInfoController@removeRow');
Route::get('/incasso/toggle',           'UserInfoController@toggle');
Route::get('/betalingen',               'BetalingenController@index');
Route::get('/betalingen/api',           'BetalingenController@api');
