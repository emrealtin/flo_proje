<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProducts extends Model
{
    protected $table = 'order_product';

    protected $fillable = [
        'order_id',
        'barcode',
        'quantity',
        'price',
        'warehouse_id'
    ];
}
