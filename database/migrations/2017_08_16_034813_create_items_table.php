<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('isbn')->nullable();
            $table->string('product_id')->nullable();
            $table->string('item_name');
            $table->integer('category_id');
            $table->integer('supplier_id');
            $table->integer('manufacturer_id')->nullable();
            $table->string('item_size')->nullable();
            $table->integer('item_reorder_level')->nullable();
            $table->integer('item_replenish_level')->nullable();
            $table->integer('days_to_expiration')->nullable();
            $table->text('description')->nullable();
            $table->boolean('price_include_tax')->default(false);
            $table->boolean('service_item')->default(false);
            $table->decimal('cost_price');
            $table->decimal('selling_price');
            $table->integer('item_quantity')->default(0);
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
        Schema::dropIfExists('items');
    }
}
