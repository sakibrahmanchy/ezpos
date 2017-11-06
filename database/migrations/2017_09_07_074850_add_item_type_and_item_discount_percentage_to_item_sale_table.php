<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddItemTypeAndItemDiscountPercentageToItemSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_sale', function($table) {
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
        Schema::table('item_sale', function($table) {
            $table->dropColumn('item_type');
            $table->dropColumn('item_discount_percentage');
        });
    }
}
