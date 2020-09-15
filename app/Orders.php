<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_id',
        'total_quantity',
        'total_price',
        'status'
    ];
}
