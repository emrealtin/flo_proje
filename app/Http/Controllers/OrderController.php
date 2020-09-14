<?php

namespace App\Http\Controllers;

use App\Orders;
use App\WareHouse;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function createOrder(Request $request){

        /*

         * {
                "details": [
                    {
                        "barcode": "SW10001",
                        "price": 35.99,
                        "quantity": 1
                    },
                     {
                        "barcode": "SW10001",
                        "price": 35.99,
                        "quantity": 2
                    }
                ]
            }
        */

        $warehouse = new WareHouseController();

        $stock_control = $warehouse->OrderStockControl($request->details);

        if($stock_control){


            echo "OK";


        }
    }
}
