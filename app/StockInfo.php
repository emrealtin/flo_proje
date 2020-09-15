<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockInfo extends Model
{
    protected $table = 'stock_info';
    protected $fillable = [
        'barcode',
        'warehouse_id',
        'quantity',
        'process_type'
    ];
}
