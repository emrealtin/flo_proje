<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use App\Products;

class ProductSeeder extends Seeder
{

    public $product_order = 0;

    public function run()
    {
        $faker = Faker::create();

        foreach (range(1,100) as $index) {

            $this->product_order++;

            Products::insert([
                'barcode' => $faker->ean13,
                'product_name' => "Ürün ".$this->product_order,
                'status' => 1
            ]);
        }
    }
}
