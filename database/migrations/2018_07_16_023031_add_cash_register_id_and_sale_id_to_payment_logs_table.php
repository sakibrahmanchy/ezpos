<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCashRegisterIdAndSaleIdToPaymentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_logs', function (Blueprint $table) {
            $table->integer('cash_register_id');
            $table->integer('sale_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_logs', function (Blueprint $table) {
            $table->dropColumn('cash_register_id');
            $table->dropColumn('sale_id');
        });
    }
}
