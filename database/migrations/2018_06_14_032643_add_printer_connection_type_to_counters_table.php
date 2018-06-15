<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Enumaration\PrinterConnectionType;

class AddPrinterConnectionTypeToCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('counters', function (Blueprint $table) {
			$table->tinyInteger('printer_connection_type')
				->default(PrinterConnectionType::CONNECT_VIA_NETWORK);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('counters', function (Blueprint $table) {
            $table->dropColumn('printer_connection_type');
        });
    }
}
