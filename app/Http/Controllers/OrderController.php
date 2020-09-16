<?php

namespace App\Http\Controllers;

use App\OrderProducts;
use App\Orders;
use App\StockInfo;
use App\WareHouse;
use Illuminate\Http\Request;
use Faker\Factory as Faker;
use Carbon\Carbon;

class OrderController extends Controller
{

    public $total_price;
    public $total_quantity;

    public function createOrder(Request $request){

        $warehouse = new WareHouseController();

        $stock_control = $warehouse->OrderStockControl($request->details);

        $this->total_quantity = 0;

        if($stock_control->getOriginalContent()['status'] == true){

            $faker = Faker::create();

            $order['code'] = $stock_control->getOriginalContent()['code'];
            $order['status'] = "success";
            $order['content'] = $stock_control->getOriginalContent()['content'];
            $order['order_id'] = $faker->numberBetween(111111,999999);
            $order['order_date'] = Carbon::today()->format('Y-m-d H:i:s');
            $order['warehouse_id'] = $stock_control->getOriginalContent()['warehouse_id'];
            $order['items'] = $request->details;

            // Deponun siparişi limiti alınacak

            $order_limit = $warehouse->getWarehouseOrderLimit($order['warehouse_id']);

            // Sipariş ürünleri stok hareket tablosuna ve sipariş detay tablolarına yazılacak

            foreach ($request->details as $item) { // Sipariş kalemleri döndürülecek

                $this->total_price = ($this->total_price+($item['price']*$item['quantity']));
                $this->total_quantity = ($this->total_quantity+$item['quantity']);

                StockInfo::create(
                    [
                    'barcode' => $item['barcode'],
                    'quantity' => $item['quantity'],
                    'warehouse_id' => $stock_control->getOriginalContent()['warehouse_id'],
                    'process_type' => 2
                    ]
                );

                OrderProducts::create(
                    [
                        'order_id' => $order['order_id'],
                        'barcode' => $item['barcode'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'warehouse_id' => $stock_control->getOriginalContent()['warehouse_id'],
                    ]
                );
            }

            //Sipariş üst bilgileri sipariş tablosuna yazılacak

            Orders::create(
                [
                'order_id' => $order['order_id'],
                'total_quantity' => $this->total_quantity,
                'total_price' => $this->total_price,
                'status' => 0
                ]
            );

            // Deponun sipariş limitinden düşülecek

            WareHouse::where('id', $order['warehouse_id'])->update(['daily_order_limit' => ($order_limit-1)]);

            //order ve order detaile yazılacak

            return response()->json($order);

        }else{

            $order['code'] = $stock_control->getOriginalContent()['code'];
            $order['status'] = "failed";
            $order['content'] = $stock_control->getOriginalContent()['content'];

            return response()->json($order);
        }
    }
}
