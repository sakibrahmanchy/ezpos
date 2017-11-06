<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableItemKitPriceRule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_kit_price_rule', function(Blueprint $table)
        {
            $table->integer('item_kit_id')->unsigned()->nullable();
            $table->foreign('item_kit_id')->references('id')
                ->on('item_kits')->onDelete('cascade');

            $table->integer('price_rule_id')->unsigned()->nullable();
            $table->foreign('price_rule_id')->references('id')
                ->on('price_rules')->onDelete('cascade');

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
        Schema::dropIfExists('item_kit_price_rule');
    }
}
