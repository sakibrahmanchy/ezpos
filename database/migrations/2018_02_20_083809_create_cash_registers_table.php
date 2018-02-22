<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('counter_id');
            $table->integer('opening_balance');
            $table->integer('closing_balance')->nullable();
            $table->integer('current_balance')->nullable();
            $table->dateTime('opening_time');
            $table->dateTime('closing_time')->nullable();
            $table->integer('opened_by')->nullable();
            $table->integer('closed_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('cash_registers');
    }
}
