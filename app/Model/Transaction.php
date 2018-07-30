<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Transaction extends Model
{
    protected $fillable = ['customer_id','sale_id','paid_amount','sale_amount','cash_register_id','transaction_type'];
    protected $table='customer_transactions';

    public function addNewTransaction($customer_id, $sale_id = null, $amount_paid, $sale_amount = 0,
        $payment_transaction_type, $cash_register_id = null){

        $transactionData = array(
            "customer_id" => $customer_id,
            "sale_id" => $sale_id,
            "paid_amount" => $amount_paid,
            "sale_amount" => $sale_amount,
            "transaction_type" => $payment_transaction_type,
            "cash_register_id" => $cash_register_id
        );
        return Transaction::create($transactionData);
    }

    public function validateTransaction(Request $request){

        return $this->validate($request,[
            'customer_id' => 'required',
            'amount_paid' => 'required',
            'sale_amount' => 'required',
            'payment_transaction_type' => 'required'
        ]);
    }
}
