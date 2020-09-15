<?php

namespace App\Http\Controllers;

use App\StockInfo;
use Illuminate\Http\Request;

class StockInfoController extends Controller
{
    public function index()
    {
        return StockInfo::all();
    }

}
