<?php

namespace Tests\Unit;
use Tests\TestCase;

class WareHouseStockTest extends TestCase
{

    public function test_warehouse_stock()
    {

        $data['id'] = $this->faker->numberBetween(1,10);

        $this->post('api/getWareHouseStock',$data)
            ->assertStatus(200);
    }
}
