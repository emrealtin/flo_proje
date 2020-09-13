<?php

namespace App\Http\Controllers;

use App\Orders;
use App\WareHouse;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function createOrder(Request $request){


        $warehouse = new WareHouseController();

        $stock_control = $warehouse->OrderStockControl($request);

        if($stock_control){





        }
    }
}
