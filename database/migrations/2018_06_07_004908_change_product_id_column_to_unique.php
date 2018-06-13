<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeProductIdColumnToUnique extends Migration
{
    public function up()
    {
        Schema::table('items', function($table) {
            $table->string('product_id')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function($table) {
            $table->dropUnique('product_id');
        });
    }
}
