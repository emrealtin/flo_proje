<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('stock_info')) {

            Schema::create('stock_info', function (Blueprint $table) {
                $table->id();
                $table->string('barcode')->default(0);
                $table->integer('warehouse_id')->default(0);
                $table->integer('quantity')->default(0);
                $table->smallInteger('process_type')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_info');
    }
}
