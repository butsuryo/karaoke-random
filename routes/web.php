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
    return view('welcome');
});

Route::get('/', 'IndexController@show');
Route::post('/', 'IndexController@show');

Route::get('/start', 'LotteryController@start');
Route::post('/start', 'LotteryController@start');


Route::post('/restart', 'LotteryController@restart');

Route::get('/lottery/{cnt}', 'LotteryController@lottery');
Route::post('/lottery/{cnt}', 'LotteryController@lottery');

Route::get('/finish/{fileTimestamp}', 'FinishController@show');
