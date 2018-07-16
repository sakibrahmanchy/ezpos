<?php

namespace App\Model;

use App\Enumaration\CashRegisterTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class CashRegister extends Model
{

    protected $fillable = ['opening_balance','closing_balance','current_balance','counter_id',
        'opened_by','closed_by','opening_time','closing_time'];


    public function OpenedByUser(){
        return $this->belongsTo('App\Model\User','opened_by','id');
    }

    public function ClosedByUser(){
        return $this->belongsTo('App\Model\User','closed_by','id');
    }

    public function CashRegisterTransactions(){
//        return $this->hasMany('App\Model\CashRegisterTransaction','cash_register_id','id');
        return $this->hasMany('App\Model\PaymentLog','cash_register_id','id');
    }

    public function PaymentLogs(){
        return $this->hasMany('App\Model\PaymentLog','cash_register_id','id');
    }

    public function additionSum(){
            return $this->PaymentLogs()
            ->selectRaw('cash_register_id, sum(paid_amount) as aggregate')
            ->where("payment_type",CashRegisterTransactionType::$ADD_BALANCE)
            ->groupBy('cash_register_id');
    }

    public function subtractionSum(){
        return $this->PaymentLogs()
            ->selectRaw('cash_register_id, sum(paid_amount) as aggregate')
            ->where("payment_type",CashRegisterTransactionType::$SUBTRACT_BALANCE)
            ->groupBy('cash_register_id');
    }

    public function saleSum(){
        return $this->PaymentLogs()
            ->selectRaw('cash_register_id, sum(paid_amount) as aggregate')
            ->where("payment_type",CashRegisterTransactionType::$CASH_SALES)
            ->groupBy('cash_register_id');
    }

    public function Counter(){
        return $this->belongsTo('App\Model\Counter','counter_id','id');
    }


    public function getCurrentActiveRegister(){
        return $this->orderBy('opening_time', 'desc')
                ->where('closing_balance',null)
                ->where( 'user_id',Auth::id() )->first();
    }

    public function getTotalAddedAmountInActiveRegister(){

        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){
            $cashRegisterId = $cash_register->id;
            $addedTotal = PaymentLog::where("cash_register_id",$cashRegisterId)
                ->where("payment_type",CashRegisterTransactionType::$ADD_BALANCE)->sum("paid_amount");
            return $addedTotal;
        }
        return 0;
    }

    public function getActiveRegisterOpeningBalance(){
        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){
            $cashRegisterId = $cash_register->id;
            $opening_balance = CashRegister::where("id",$cashRegisterId)->first()->opening_balance;
            return $opening_balance;
        }
        return 0;
    }

    public function getTotalSubtractedAmountInActiveRegister(){

        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){
            $cashRegisterId = $cash_register->id;
            $subtractedTotal = PaymentLog::where("cash_register_id",$cashRegisterId)
                ->where("payment_type",CashRegisterTransactionType::$SUBTRACT_BALANCE)
                ->sum("paid_amount");
            return $subtractedTotal;
        }
        return 0;
    }

    public function getPreviousClosingBalance(){
        $previousCashRegister = $this->orderBy('opening_time', 'desc')->where('closing_balance','<>',null)->where( 'user_id',Auth::id() )->first();
        if(!is_null($previousCashRegister))
            return $previousCashRegister->closing_balance;
        else
            return 0.0;
    }

    public function addCashToRegister($amount, $comment){
        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){

            $cash_register->current_balance += $amount;
            if($cash_register->save()){
//                $cashRegisterTransaction = new CashRegisterTransaction();
//                $cashRegisterTransaction->create([
//                    "cash_register_id"=>$cash_register->id,
//                    "amount"=>$amount,
//                    "payment_type"=>CashRegisterTransactionType::$ADD_BALANCE,
//                    "comments" => $comment
//                ]);
                $paymentLog = new PaymentLog;
                $paymentLog->addNewPaymentLog(CashRegisterTransactionType::$ADD_BALANCE,$amount,null,null,$comment);
                return true;
            }
        }
        return false;
    }

    public function subtractCashFromRegister($amount, $comment){
        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){

            $cash_register->current_balance -= $amount;
            if($cash_register->save()){
//                $cashRegisterTransaction = new CashRegisterTransaction();
//                $cashRegisterTransaction->create([
//                    "cash_register_id"=>$cash_register->id,
//                    "amount"=>$amount,
//                    "payment_type"=>CashRegisterTransactionType::$SUBTRACT_BALANCE,
//                    "comments" => $comment
//                ]);
                $paymentLog = new PaymentLog;
                $paymentLog->addNewPaymentLog(CashRegisterTransactionType::$SUBTRACT_BALANCE,$amount,null,null,$comment);
                return true;
            }
        }
        return false;
    }

    public function getTotalSaleInCurrentRegister($transactionType){
        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){
            $total_sales_in_register = PaymentLog::where("cash_register_id",$cash_register->id)
                ->where("payment_type",$transactionType)->sum('paid_amount');
            return $total_sales_in_register;
        }
        return 0;

    }

    public function getRefundedSalesInCashRegister($register_id) {
        $refundedSales = Sale::withTrashed()->where("refund_register_id",$register_id)->get();
        return $refundedSales;
    }

    public function getRefundedSalesAmountInCashRegister($register_id) {
        $refundedSalesAmount = Sale::withTrashed()->where("refund_register_id",$register_id)->sum('total_amount');
        return $refundedSalesAmount;
    }

}
