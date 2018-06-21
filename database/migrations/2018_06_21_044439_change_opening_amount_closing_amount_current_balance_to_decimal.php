<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOpeningAmountClosingAmountCurrentBalanceToDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_registers', function($table) {
            $table->decimal('opening_balance')->change();
            $table->decimal('closing_balance')->change();
            $table->decimal('current_balance')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_registers', function($table) {
            $table->integer('opening_balance')->change();
            $table->integer('closing_balance')->change();
            $table->integer('current_balance')->change();
        });
    }
}
