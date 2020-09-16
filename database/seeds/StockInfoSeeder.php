<?php

use Faker\Factory as Faker;
use App\StockInfo;
use App\Products;
use Illuminate\Database\Seeder;

class StockInfoSeeder extends Seeder
{

    public function run()
    {
        $faker = Faker::create();

        $products1 = Products::where('status', 1)->offset(0)->limit(20)->get();
        $products2 = Products::where('status', 1)->offset(0)->limit(20)->get();
        $products3 = Products::where('status', 1)->offset(20)->limit(20)->get();

        foreach ($products1 as $product) {

            StockInfo::insert([
                'barcode' => $product->barcode,
                'warehouse_id' => 2,
                'quantity' => $faker->numberBetween(1,10),
                'process_type' => 1,
            ]);
        }

        foreach ($products2 as $product) {

            StockInfo::insert([
                'barcode' => $product->barcode,
                'warehouse_id' => 2,
                'quantity' => $faker->numberBetween(1,15),
                'process_type' => 1,
            ]);
        }

        foreach ($products3 as $product) {

            StockInfo::insert([
                'barcode' => $product->barcode,
                'warehouse_id' => $faker->numberBetween(1,10),
                'quantity' => $faker->numberBetween(1,10),
                'process_type' => $faker->numberBetween(1,2),
            ]);
        }

    }
}
