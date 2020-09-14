<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockInfo;
use App\WareHouse;

class WareHouseController extends Controller
{

    public $warehouse_priority; // Depo önceliği
    public $warehouse_id; // Seçili Depo ID
    public $order_stock_status; // Siparişin depoda karşılama durumu
    public $warehouse_stock_status; // Stokların tamamı depoda karşılanıyor mu

    public function getWareHouseStock(Request $request)
    {

        if(!$request->id){

            // Depo ID belirtilmemişse tüm depoların toplam stoklarını verir

            $stock_info = StockInfo::groupBy('warehouse_id')
                ->selectRaw('warehouse_id, sum(quantity) as stock')
                //->where('process_type',1)
                ->get();

        }else{

            // Depo ID belirtilmişse depoya ait toplam stoğu verir

            $stock_info = StockInfo::selectRaw('sum(quantity) as stock')
                ->where('warehouse_id',$request->id)
                ->get();
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

            $stock_info = StockInfo::where('barcode',$barcode)
                ->where('warehouse_id',$warehouse_id)
                ->sum('quantity');
        }

        return $stock_info;
    }

    public function setWarehouse($proirity)
    {

        $warehouse = WareHouse::select('id')->where('priority',$proirity)
            ->first();

        return $warehouse->id;

    }

    public function OrderStockControl($data)
    {

        $this->order_stock_status = true;

        $this->warehouse_priority = 1; // 1. öncelikten başlanacak

        $this->warehouse_id = $this->setWarehouse($this->warehouse_priority); // İlk öncelikteki Depo ID alınacak

        for($i = 1; $i < $this->TotalWarehouse(); $i++) { // Toplam depo sayısı kadar öncelik kontrolü yapılacak

            if($this->WareHouseStockControl($this->warehouse_id,$data) !== true){

                // Tüm stoklar önceliği belirlenen depodan karşılanamıyorsa yeni öncelik tanımlanır

                if($this->warehouse_priority< $this->TotalWarehouse()) { // Tüm öncelikler denenmediyse bir sonraki önceliğe geçilecek

                    $this->warehouse_priority = $this->warehouse_priority + 1;

                    $this->warehouse_id = $this->setWarehouse($this->warehouse_priority); // Yeni öncelik sayısına göre Depo ID getirilecek

                    $this->order_stock_status = false;

                }else{ // Kontrol edilecek öncelik kalmadıysa sipariş tamamlanamayacak

                    $this->order_stock_status = false;

                }
            }else{

                $this->order_stock_status = true;
            }
        }

        //Depo bilgisi dönecek

        return $this->order_stock_status;

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
}