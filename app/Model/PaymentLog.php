<?php

namespace App\Model;

use App\Enumaration\PaymentTransactionTypes;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    //

    public function Sales(){
        return $this->belongsToMany('App\Model\Sale');
    }

    public function addNewPaymentLog($payment_type, $paid_amount,

        $sale = null, $customer_id ) {

        $paymentLog = new PaymentLog();

        $paymentLog->payment_type = $payment_type;
        $paymentLog->paid_amount = $paid_amount;

        $paymentLog->save();

        if(!is_null($sale)){
            $sale->paymentLogs()->attach($paymentLog);

            if($sale->customer_id!=0){

                $transaction = new Transaction();
                $transaction->addNewTransaction($customer_id,$sale->id,$paymentLog->paid_amount,$sale->total_amount,
                    PaymentTransactionTypes::$SALE,$paymentLog->id);
            }

        }else{

            if($customer_id!=null){
                $transactionData = array(
                    "customer_id" => $customer_id,
                    "sale_id" => null,
                    "amount_paid" => $paid_amount,
                    "sale_amount" => null,
                    "payment_transaction_type" => PaymentTransactionTypes::$DUE_PAYMENT,
                    "payment_log_id" => $paymentLog->id
                );
                $transaction = new Transaction();
                $transaction->addNewTransaction($customer_id,null,$paymentLog->paid_amount, 0,
                    PaymentTransactionTypes::$DUE_PAYMENT,$paymentLog->id);
            }

        }

        return $paymentLog;
    }
}
