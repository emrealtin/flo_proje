<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockInfo;
use App\WareHouse;
use Illuminate\Support\Facades\DB;

class WareHouseController extends Controller
{
    public $warehouse_priority; // Depo önceliği
    public $warehouse_id; // Seçili Depo ID
    public $order_stock_status; // Siparişin depoda karşılama durumu
    public $warehouse_stock_status; // Stokların tamamı depoda karşılanıyor mu
    public $warehouse_limit_status; // Depo limit durumu
    public $warehouse_status; // Depo limit durumu

    public function getWareHouseStock(Request $request)
    {

        if(!$request->id){

            // Depo ID belirtilmemişse tüm depoların toplam stoklarını verir

            $stock_info = DB::select(' SELECT warehouse_id, ((select sum(quantity) as G from stock_info where process_type = 1 and warehouse_id = S.warehouse_id) - (select sum(quantity) as G from stock_info where process_type = 2 and warehouse_id = S.warehouse_id)) as STOK FROM stock_info as S GROUP BY warehouse_id');


        }else{

            // Depo ID belirtilmişse depoya ait toplam stoğu verir

            $input_stock_info = StockInfo::where('warehouse_id',$request->id)
                ->where('process_type',1)
                ->sum('quantity');


            $output_stock_info = StockInfo::where('warehouse_id',$request->id)
                ->where('process_type',2)
                ->sum('quantity');

            $stock_info = ($input_stock_info-$output_stock_info);
        }

        return $stock_info;

    }

    public function getProductStock($barcode, $warehouse_id = null){

        if(!$warehouse_id){

            //Depo ID gönderilmezse tüm depolardaki stok sayısını verir

            $stock_info = StockInfo::where('barcode',$barcode)
                ->sum('quantity');

        }else{

            //Depo ID gönderilirse depoya ait stok sayısını verir

            $input_stock_info = StockInfo::where('warehouse_id',$warehouse_id)
                ->where('barcode',$barcode)
                ->where('process_type',1)
                ->sum('quantity');


            $output_stock_info = StockInfo::where('warehouse_id',$warehouse_id)
                ->where('barcode',$barcode)
                ->where('process_type',2)
                ->sum('quantity');

            $stock_info = ($input_stock_info-$output_stock_info);
        }

        return $stock_info;
    }

    public function setWarehouse($proirity)
    {
        $warehouse = WareHouse::select('id')->where('priority',$proirity)
            ->first();

        if($warehouse){

            return $warehouse->id;

        }else{

            return 0;
        }
    }

    public function OrderStockControl($data)
    {

        $this->order_stock_status = true;
        $this->warehouse_limit_status = true;
        $this->warehouse_status = true;

        $this->warehouse_priority = 1; // 1. öncelikten başlanacak

        $this->warehouse_id = $this->setWarehouse($this->warehouse_priority); // İlk öncelikteki Depo ID alınacak

        if($this->warehouse_id != 0) {

            for ($i = 1; $i <= $this->TotalWarehouse(); $i++) { // Toplam depo sayısı kadar öncelik kontrolü yapılacak

                if ($this->getWarehouseOrderLimit($this->warehouse_id) > 0) {

                    $this->warehouse_limit_status = true;

                    if ($this->WareHouseStockControl($this->warehouse_id, $data) !== true) {

                        // Tüm stoklar önceliği belirlenen depodan karşılanamıyorsa yeni öncelik tanımlanır

                        if ($this->warehouse_priority < $this->TotalWarehouse()) { // Tüm öncelikler denenmediyse bir sonraki önceliğe geçilecek

                            $this->warehouse_priority = $this->warehouse_priority + 1;

                            $this->warehouse_id = $this->setWarehouse($this->warehouse_priority); // Yeni öncelik sayısına göre Depo ID getirilecek

                            $this->order_stock_status = false;

                        } else { // Kontrol edilecek öncelik kalmadıysa sipariş tamamlanamayacak

                            $this->order_stock_status = false;
                        }

                    } else {

                        $this->order_stock_status = true;
                    }

                } else {

                    $this->warehouse_priority = $this->warehouse_priority + 1;

                    $this->warehouse_id = $this->setWarehouse($this->warehouse_priority); // Yeni öncelik sayısına göre Depo ID getirilecek

                    $this->order_stock_status = false;

                    $this->warehouse_limit_status = false;

                }
            }

        }else{

            $this->warehouse_status = false;
        }

        if($this->warehouse_status != 0) {

            if ($this->warehouse_limit_status) { // Depo limiti varsa

                if ($this->order_stock_status) { // Stoklar yeterli ise

                    return response()->json([
                        'code' => '100',
                        'status' => $this->order_stock_status,
                        'warehouse_id' => $this->warehouse_id,
                        'content' => 'Sipariş oluşturuldu.',
                    ]);

                } else {

                    return response()->json([
                        'code' => '101',
                        'status' => $this->order_stock_status,
                        'warehouse_id' => $this->warehouse_id,
                        'content' => 'Sipariş kalemlerinin tamamı herhangi bir depoda bulunamadı.',
                    ]);
                }

            } else {

                return response()->json([
                    'code' => '102',
                    'status' => false,
                    'content' => 'Depolardaki sipariş limiti yetersiz.',
                ]);
            }

        }else{

            return response()->json([
                'code' => '103',
                'status' => false,
                'content' => 'Depo bulunamadı.',
            ]);

        }
    }

    public function WareHouseStockControl($warehouse_id, $data){

        $this->warehouse_stock_status = true;

        foreach ($data as $item) { // Sipariş kalemleri döndürülecek

            // Seçili depoda ilgili stok miktarına bakılacak

            $product_stock = $this->getProductStock($item['barcode'], $warehouse_id);

            if ($product_stock < $item['quantity']) { // Seçili depoda stok karşılanmıyorsa

                $this->warehouse_stock_status = false;

            }
        }

        return $this->warehouse_stock_status;
    }

    public function TotalWarehouse(){

        $warehouse = WareHouse::get();

        return $warehouse->count();
    }

    public function getWarehouseOrderLimit($warehouse_id){

        $warehouse_limit = WareHouse::select('daily_order_limit')->where('id',$warehouse_id)->first();

        return $warehouse_limit->daily_order_limit;

    }
}