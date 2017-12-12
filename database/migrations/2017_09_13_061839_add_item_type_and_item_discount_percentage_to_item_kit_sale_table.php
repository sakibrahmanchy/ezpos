<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddItemTypeAndItemDiscountPercentageToItemKitSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_kit_sale', function($table) {
            $table->string('item_type');
            $table->string('item_discount_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_kit_sale', function ($table) {
            $table->dropColumn('item_type');
            $table->dropColumn('item_discount_percentage');
        });
    }
}
