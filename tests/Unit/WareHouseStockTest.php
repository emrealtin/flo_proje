<?php

namespace Tests\Unit;
use Tests\TestCase;

class WareHouseStockTest extends TestCase
{

    public function test_warehouse_stock()
    {

        $this->seed('ProductSeeder');
        $this->seed('WareHouseSeeder');
        $this->seed('StockInfoSeeder');

        $data['id'] = $this->faker->numberBetween(1,10);

        $this->post('api/getWareHouseStock',$data)
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }
}
