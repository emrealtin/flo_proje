<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockInfo;
use App\WareHouse;

class WareHouseController extends Controller
{

    public $warehouse_priority; // Depo önceliği
    public $warehouse_id; // Seçili Depo ID
    public $order_stock_status; // Siparişin tamamı depoda karşılanıyor mu


    public function getWareHouseStock($id = false)
    {

        if(!$id){

            // Depo ID belirtilmemişse tüm depoların toplam stoklarını verir

            $stock_info = StockInfo::groupBy('warehouse_id')
                ->selectRaw('warehouse_id, sum(quantity) as stock')
                //->where('process_type',1)
                ->get();

        }else{

            // Depo ID belirtilmişse depoya ait toplam stoğu verir

            $stock_info = StockInfo::selectRaw('sum(quantity) as stock')
                ->where('warehouse_id',$id)
                ->get();
        }

        return $stock_info;

    }

    public function getProductStock($barcode, $warehouse_id = false){

        if(!$warehouse_id){

            //Depo ID gönderilmezse tüm depolardaki stok sayısını verir

            $stock_info = StockInfo::groupBy('barcode')
                ->selectRaw('sum(quantity) as stock')
                ->get();

        }else{

            //Depo ID gönderilirse depoya ait stok sayısını verir

            $stock_info = StockInfo::groupBy('barcode')
                ->selectRaw('sum(quantity) as stock')
                ->where('warehouse_id',$warehouse_id)
                ->get();
        }

        return $stock_info;
    }

    public function setWarehouse($proirity)
    {

        $warehouse = WareHouse::select('id')->where('priority',$proirity)
            ->get();

        return $warehouse;

    }

    public function OrderStockControl($data)
    {

        for($i = 1; $i <= $this->TotalWarehouse(); $i++) { // Toplam depo sayısı kadar öncelik kontrolü yapılacak

            $this->warehouse_priority = $i; // Öncelik tanımlanacak

            $this->warehouse_id = $this->setWarehouse($this->warehouse_priority); // Öncelik sayısına göre Depo ID getirilecek

            foreach ($data->barcode as $item) {

                // Seçili depoda ilgili stok miktarına bakılacak

                $product_stock = $this->getProductStock($item->barcode, $this->warehouse_id);

                if ($product_stock < $item->quantity) { // Seçili depoda stok karşılanmıyorsa

                    if($this->TotalWarehouse() < $this->warehouse_id) { // Tüm öncelikler denenmediyse bir sonraki önceliğe geçilecek

                        $this->warehouse_id = $this->warehouse_id + 1;

                    }else{ // Kontrol edilecek öncelik kalmadıysa sipariş tamamlanamayacak

                        return false;
                    }

                } else {

                    return true;

                }

            }

        }

    }

    public function TotalWarehouse(){

        $warehouse = WareHouse::get();

        return $warehouse->count();
    }
}
