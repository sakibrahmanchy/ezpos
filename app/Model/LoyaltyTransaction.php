<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    public function NewLoyaltyTransaction($customer_id,$transaction_amount,$transaction_type,$sale_id){
        $loyaltyTransaction = new LoyaltyTransaction();
        $loyaltyTransaction->customer_id = $customer_id;
        $loyaltyTransaction->transaction_type = $transaction_type;
        $loyaltyTransaction->transaction_amount = $transaction_amount;
        $loyaltyTransaction->sale_id = $sale_id;
        $loyaltyTransaction->save();
    }
}
