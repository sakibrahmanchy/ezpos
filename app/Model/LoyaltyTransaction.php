<?php

namespace App\Model;

use App\Library\SettingsSingleton;
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

    public function IncreaseCustomerLoyalty($customer_id,$total_amount){

        $settings = SettingsSingleton::get();
        $loyalty_incentive_percentage = $settings["customer_loyalty_percentage"];
        $creditLoyalty = ($total_amount * $loyalty_incentive_percentage)/ 100;
        $customer = Customer::where("id",$customer_id)->first();
        $customer->balance+=$creditLoyalty;
        if($customer->save())
            return $creditLoyalty;
        return 0;
    }

    public function DeductCustomerLoyalty($customer_id,$amountToDeduct){
        $customer = Customer::where("id",$customer_id)->first();
        $customer->balance-=$amountToDeduct;
        if($customer->save())
            return 1;
        return 0;
    }

    public function CustomerHasLoyalty($customer_id){
        $customer = Customer::where("id",$customer_id)->first();
        if(!is_null($customer->loyalty_card_number)){
            return true;
        }
        return false;
    }
}
