<?php

use Illuminate\Database\Seeder;

class SaleCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $saleIdList = \App\Model\Sale::pluck('id')->toArray();
        DB::table('item_sale')->whereNotIn("sale_id",$saleIdList)->delete();
        DB::table('loyalty_transactions')->whereNotIn("sale_id",$saleIdList)->delete();
        DB::table('payment_log_sale')->whereNotIn("sale_id",$saleIdList)->delete();
        DB::table('transactions')->whereNotIn("sale_id",$saleIdList)->delete();
    }
}
