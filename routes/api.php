<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/getStock', 'WareHouseController@getStockInfo')->name('getStockInfo');
Route::post('/createOrder', 'OrderController@createOrder')->name('createOrder');