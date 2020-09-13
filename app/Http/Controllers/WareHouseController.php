<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockInfo;
use App\WareHouse;

class WareHouseController extends Controller
{
    public function getStockInfo()
    {

        $stock_info = StockInfo::groupBy('warehouse_id')
            ->selectRaw('warehouse_id, sum(quantity) as stock')
            //->where('process_type',1)
            ->get();


        return $stock_info;
    }
}
