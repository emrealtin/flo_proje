<?php

namespace Tests\Unit;
use Tests\TestCase;

class WareHouseStockListTest extends TestCase
{

    public function test_warehouse_stock_list()
    {
        $this->post('api/getWareHouseStock')
            ->assertStatus(200);
    }
}
