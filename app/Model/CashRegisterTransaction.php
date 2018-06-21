<?php

namespace App\Model;

use App\Enumaration\CashRegisterTransactionType;
use Illuminate\Database\Eloquent\Model;

class CashRegisterTransaction extends Model
{
    protected $fillable = ['cash_register_id','amount','transaction_type','comments'];


    public function totalAdditions(){
        return CashRegisterTransaction::where("id",$this->id)->sum('amount');
        //return $this->sum('amount')->where('cash_register_transactions.transaction_type',CashRegisterTransactionType::$ADD_BALANCE);
    }

    public function newCashRegisterTransaction($saleId, $paidAmount, $paymentType) {
        $cashRegisterTransaction = new CashRegisterTransaction();

        $cashRegister = new CashRegister();
        $activeCashRegiser = $cashRegister->getCurrentActiveRegister();
        $cashRegisterToChange = CashRegister::where("id",$activeCashRegiser->id)->first();
        $cashRegisterToChange->current_balance += $paidAmount;

        if($cashRegisterToChange->save()){
            $cashRegisterTransaction->cash_register_id = $activeCashRegiser->id;
            $cashRegisterTransaction->amount = $paidAmount;
            $cashRegisterTransaction->transaction_type = $paymentType;
            $cashRegister->comments = "Sales for sale: ".$saleId;
            $cashRegisterTransaction->save();
        }
    }
}
