<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyRelationshipForSaleId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_sale', function (Blueprint $table) {
            $table->bigInteger('sale_id')->change();
            $table->foreign('sale_id')
                ->references('id')->on('sales')
                ->onDelete('cascade');
        });
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->bigInteger('sale_id')->change();
            $table->foreign('sale_id')
                ->references('id')->on('sales')
                ->onDelete('cascade');
        });
        Schema::table('payment_log_sale', function (Blueprint $table) {
            $table->bigInteger('sale_id')->change();
            $table->foreign('sale_id')
                ->references('id')->on('sales')
                ->onDelete('cascade');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->bigInteger('sale_id')->change();
            $table->foreign('sale_id')
                ->references('id')->on('sales')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_sale', function (Blueprint $table) {
            $table->integer('sale_id')->change();
            $table->dropForeign(['sale_id']);
        });
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->integer('sale_id')->change();
            $table->dropForeign(['sale_id']);
        });
        Schema::table('payment_log_sale', function (Blueprint $table) {
            $table->integer('sale_id')->change();
            $table->dropForeign(['sale_id']);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('sale_id')->change();
            $table->dropForeign(['sale_id']);
        });
    }
}
