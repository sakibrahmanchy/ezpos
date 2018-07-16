<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCommentsColumnOfPaymentLogsToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_logs', function (Blueprint $table) {
            $table->text('comments')->nullable(true)->change();
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
            $table->text('comments')->nullable(false)->change();
        });
    }
}
