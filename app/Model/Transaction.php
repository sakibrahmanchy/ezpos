<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Transaction extends Model
{
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
