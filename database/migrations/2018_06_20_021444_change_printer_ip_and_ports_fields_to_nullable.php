<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePrinterIpAndPortsFieldsToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('counters', function($table) {
            $table->string('printer_ip')->nullable(true)->change();
            $table->string('printer_port')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('counters', function($table) {
            $table->string('printer_ip')->nullable(false)->change();
            $table->string('printer_port')->nullable(false)->change();
        });
    }
}
