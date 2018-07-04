<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->increments('id');
			$table->tinyInteger('transaction_type');
			$table->double('sale_amount', 10,2);
			$table->double('paid_amount', 10,2);
			$table->integer('cash_register_id');
			$table->bigInteger('sale_id')->nullable()->unsigned();
			$table->integer('customer_id')->unsigned();
			$table->timestamps();
        });
		
		Schema::table('customer_transactions', function($table) {
			$table->foreign('sale_id')->references('id')->on('sales');
			$table->foreign('customer_id')->references('id')->on('customers');
		});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_transactions');
    }
}
