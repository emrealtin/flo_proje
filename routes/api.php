<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/getWareHouseStock', 'WareHouseController@getWareHouseStock')->name('getWareHouseStock');
Route::post('/createOrder', 'OrderController@createOrder')->name('createOrder');