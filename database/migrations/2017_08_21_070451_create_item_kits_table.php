<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemKitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_kits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('isbn')->nullable();
            $table->string('product_id')->nullable();
            $table->string('item_kit_name');
            $table->string('category_id');
            $table->string('item_kit_description')->nullable();
            $table->boolean('price_include_tax')->default(false);
            $table->decimal('cost_price');
            $table->decimal('selling_price');
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
        Schema::dropIfExists('item_kits');
    }
}
