<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('name');
            $table->string('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('requires_coupon')->default(false);
            $table->string('coupon_code')->nullable();
            $table->boolean('show_on_reciept')->default(true);
            $table->boolean('active')->default(true);
            $table->integer('items_to_buy')->default(0);
            $table->integer('items_to_get')->default(0);
            $table->decimal('spend_amount')->default(0);
            $table->decimal('percent_off')->default(0);
            $table->decimal('fixed_of')->default(0);
            $table->integer('num_times_to_apply')->default(0);
            $table->boolean('unlimited')->default(true);
            $table->integer('qty_to_by')->default(0);
            $table->decimal('flat_unit_discount')->default(0);
            $table->integer('percent_unit_discount')->default(0);
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
        Schema::dropIfExists('price_rules');
    }
}
