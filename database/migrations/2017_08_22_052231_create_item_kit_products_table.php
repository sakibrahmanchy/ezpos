<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemKitProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_kit_products', function (Blueprint $table) {
            $table->integer('item_kit_id');
            $table->integer('item_id');
            $table->integer('quantity');
            $table->timestamps();
            $table->unique(array('item_kit_id', 'item_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_kit_products');
    }
}
