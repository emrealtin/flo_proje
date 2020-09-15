<?php

use Faker\Factory as Faker;
use App\WareHouse;
use Illuminate\Database\Seeder;

class WareHouseSeeder extends Seeder
{
    public $priority = 0;

    public function run()
    {
        $faker = Faker::create();

        foreach (range(1,10) as $index) {

            $this->priority++;

            WareHouse::insert([
                'name' => "Depo ".$this->priority,
                'daily_order_limit' => $faker->numberBetween(5,20),
                'priority' => $this->priority,
                'stock' =>  $faker->numberBetween(1,100),
                'status' => 1,
            ]);

        }
    }
}
