<?php

namespace App\Model;

use App\Enumaration\CashRegisterTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CashRegisterTransaction extends Model
{
    protected $fillable = ['cash_register_id','amount','transaction_type','comments'];


    public function totalAdditions(){
        return CashRegisterTransaction::where("id",$this->id)->sum('amount');
    }

    public function newCashRegisterTransaction($saleId, $paidAmount, $paymentType, $paymentLogId) {
        $cashRegisterTransaction = new CashRegisterTransaction();

        $cashRegister = new CashRegister();
        $activeCashRegiser = $cashRegister->getCurrentActiveRegister();
        $cashRegisterToChange = CashRegister::where("id",$activeCashRegiser->id)->first();
        $cashRegisterToChange->current_balance += $paidAmount;

        if($cashRegisterToChange->save()){
            $cashRegisterTransaction->cash_register_id = $activeCashRegiser->id;
            $cashRegisterTransaction->amount = $paidAmount;
            $cashRegisterTransaction->transaction_type = $paymentType;
            $cashRegisterTransaction->comments = "Sales for sale: ".$saleId;
            $cashRegisterTransaction->payment_log_id = $paymentLogId;
            $cashRegisterTransaction->save();
        }
    }


    public function deleleCashRegisterTransactionByPaymentLogId($paymentLogId) {
        DB::table('cash_register_transactions')->where("payment_log_id",$paymentLogId)->delete();
    }
}
