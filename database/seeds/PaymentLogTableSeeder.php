<?php

use Illuminate\Database\Seeder;

class PaymentLogTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentLogs = \App\Model\PaymentLog::all();
        $paymentTypeList =  ['Cash', 'Check','Debit Card', 'Credit Card', 'Gift Card', 'Loyalty Card'];
        foreach ($paymentLogs as $aPaymentLog) {
            $paymentLogSale = DB::table('payment_log_sale')->where("payment_log_id",$aPaymentLog->id)->first();
            if($aPaymentLog->sale_id==0) {
                if(!is_null($paymentLogSale)) {
                    $paymentLogSaleId = $paymentLogSale->sale_id;
                    $aPaymentLog->sale_id = $paymentLogSaleId;
                    $aPaymentLog->save();
                }
            }
            if($aPaymentLog->cash_register_id==0) {
                if(!is_null($paymentLogSale)) {
                    $paymentLogSaleId = $paymentLogSale->sale_id;
                    $sale = \App\Model\Sale::where("id", $paymentLogSaleId)
                        ->first();
                    if (!is_null($sale)) {
                        $cashRegisterId = $sale->cash_register_id;
                        if (!is_null($cashRegisterId)) {
                            $aPaymentLog->cash_register_id = $cashRegisterId;
                            $aPaymentLog->save();
                        }
                    }
                }
            }
            if(in_array($aPaymentLog->payment_type, $paymentTypeList)) {
                $aPaymentLog->payment_type = \App\Enumaration\PaymentTypes::$TypeList[$aPaymentLog->payment_type];
                $aPaymentLog->save();
            }
        }
    }
}
