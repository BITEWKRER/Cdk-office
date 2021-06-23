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

use Illuminate\Support\Facades\Route;

Route::get('/selfBill','renovation\billController@index');
Route::get('/custom','renovation\customizeController@index');
Route::get('/getInfo','renovation\renovationController@index');
Route::get('/getunit','renovation\renovationController@getunit');
Route::get('/getCustomID','renovation\renovationController@getCustomID');
Route::get('/getClassify','renovation\renovationController@getClassify');
Route::get('/modelJson','renovation\renovationController@modelJson');
Route::get('/downloadWord','utils\filesController@downloadWord');
Route::get('/getDocx','utils\filesController@getDocx');
Route::get('/getFileType','utils\filesController@getFileType');
Route::get('/deleteDocx','utils\filesController@deleteDocx');
Route::get('/isVip','utils\filesController@isVip');
Route::get('/getOpenID','utils\filesController@getOpenID');
Route::get('/isAdd','utils\filesController@isAdd');
Route::get('/getCloudSize','utils\filesController@getCloudSize');
Route::get('/profile','renovation\profileController@index');




