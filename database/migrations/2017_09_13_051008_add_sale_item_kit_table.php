<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSaleItemKitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_kit_sale', function (Blueprint $table) {
            $table->integer('sale_id');
            $table->integer('item_kit_id');
            $table->decimal('quantity');
            $table->decimal('unit_price');
            $table->decimal('total_price');
            $table->decimal('discount_amount');
            $table->integer('price_rule_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_kit_sale');
    }
}
