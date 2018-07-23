<?php

namespace App\Model;

use App\Enumaration\PaymentTransactionTypes;
use App\Enumaration\PaymentTypes;
use App\Http\Controllers\CashRegisterController;
use Illuminate\Database\Eloquent\Model;
use function PHPSTORM_META\elementType;

class PaymentLog extends Model
{
    //

    public function Sales(){
        return $this->belongsToMany('App\Model\Sale');
    }

    public function addNewPaymentLog($payment_type, $paid_amount,

        $sale = null, $customer_id , $comments) {

        $paymentLog = new PaymentLog();

        $paymentLog->payment_type = $payment_type;
        $paymentLog->paid_amount = $paid_amount;
        if(!is_null($sale)) {
            $paymentLog->sale_id = $sale->id;
            $paymentLog->sale_status = $sale->sale_status;
        }
        
		$cash_register = new CashRegister();
        if(!is_null($sale)){
            if(!is_null($sale->cash_register_id))
                $paymentLog->cash_register_id = $sale->cash_register_id;
            else
                $paymentLog->cash_register_id = 0;
        }
        else
            $paymentLog->cash_register_id = $cash_register->getCurrentActiveRegister()->id;

        $paymentLog->comments = $comments;

        $paymentLog->save();

        if(!is_null($sale)){
            $sale->paymentLogs()->attach($paymentLog);

            if($sale->customer_id!=0){

                $transaction = new Transaction();
                $transaction->addNewTransaction($customer_id,$sale->id,$paymentLog->paid_amount,$sale->total_amount,
                    PaymentTransactionTypes::$SALE,$paymentLog->id);
            }

        }else{

            if($customer_id!=null || $customer_id != 0){
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
