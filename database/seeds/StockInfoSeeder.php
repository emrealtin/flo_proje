<?php

use Faker\Factory as Faker;
use App\StockInfo;
use Illuminate\Database\Seeder;

class StockInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1,10) as $index) {
            StockInfo::insert([
                'barcode' => $faker->ean13,
                'warehouse_id' => $faker->numberBetween(1,10),
                'quantity' => $faker->randomDigit,
                'process_type' => $faker->numberBetween(1,2),
            ]);
        }
    }
}
