<?php

namespace Tests\Unit;
use Tests\TestCase;
use App\Products;

class CreateOrderTest extends TestCase
{
    public $priority = 0;

    public function test_create_order()
    {

        $this->seed('ProductSeeder');
        $this->seed('WareHouseSeeder');
        $this->seed('StockInfoSeeder');

        $data['details'] = array(); // Örnek sipariş dizisi

        $test_products = Products::where('status', 1)->offset(0)->limit(3)->get(); // Uyuşan kalemler üretiliyor

        //$test_products = Products::where('status', 1)->inRandomOrder()->offset(0)->limit(3)->get(); // Uyuşmayan kalemler üretiliyor

        foreach ($test_products as $product) {

            array_push($data['details'],
                array (
                'barcode' => $product->barcode,
                'price' => $this->faker->numberBetween(10,50),
                'quantity' => 1,
            ));

        }

        $response = $this->postJson('api/createOrder', $data);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }
}
