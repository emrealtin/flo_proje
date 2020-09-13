<?php

namespace App\Http\Controllers;

use App\Orders;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function createOrder(Request $request){


        $barcode  = $request->barcode;
        $quantity = $request->quantity;


    }
}
