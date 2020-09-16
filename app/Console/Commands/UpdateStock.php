<?php

namespace App\Console\Commands;

use App\Http\Controllers\WareHouseController;
use Illuminate\Console\Command;
use App\WareHouse;

class UpdateStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warehouse stock update process';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $warehouse = new WareHouseController();

        $get_stock = $warehouse->getWareHouseStockCalc();

        foreach ($get_stock as $item) {

            if($item->stock) {

                WareHouse::where('id', $item->warehouse_id)
                    ->update(['stock' => $item->stock]);

            }

       }

        return $this->info("Update successfully.");
    }
}
