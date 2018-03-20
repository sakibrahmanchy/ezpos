<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Transaction extends Model
{
    protected $fillable = ['customer_id','amount_paid','sale_amount','payment_transaction_type','payment_log_id'];

    public function addNewTransaction($transactionData){
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
