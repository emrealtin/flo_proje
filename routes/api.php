<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/orders', 'OrderController@store')->name('order.store');
Route::get('/getStock', 'WareHouseController@getStockInfo')->name('warehouse.getstock');