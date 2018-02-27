<?php

namespace App\Model;

use App\CashRegisterTransaction;
use App\Enumaration\CashRegisterTransactionType;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany('App\CashRegisterTransaction','cash_register_id','id');
    }

    public function additionSum(){
            return $this->CashRegisterTransactions()
            ->selectRaw('cash_register_id, sum(amount) as aggregate')
            ->where("transaction_type",CashRegisterTransactionType::$ADD_BALANCE)
            ->groupBy('cash_register_id');
    }

    public function subtractionSum(){
        return $this->CashRegisterTransactions()
            ->selectRaw('cash_register_id, sum(amount) as aggregate')
            ->where("transaction_type",CashRegisterTransactionType::$SUBTRACT_BALANCE)
            ->groupBy('cash_register_id');
    }

    public function saleSum(){
        return $this->CashRegisterTransactions()
            ->selectRaw('cash_register_id, sum(amount) as aggregate')
            ->where("transaction_type",CashRegisterTransactionType::$CASH_SALES)
            ->groupBy('cash_register_id');
    }

    public function Counter(){
        return $this->belongsTo('App\Model\Counter','counter_id','id');
    }


    public function getCurrentActiveRegister(){
        return $this->orderBy('created_at', 'desc')->where('closing_balance',null)->first();
    }

    public function getTotalAddedAmountInActiveRegister(){

        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){
            $cashRegisterId = $cash_register->id;
            $addedTotal = CashRegisterTransaction::where("cash_register_id",$cashRegisterId)
                ->where("transaction_type",CashRegisterTransactionType::$ADD_BALANCE)->sum("amount");
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
            $subtractedTotal = CashRegisterTransaction::where("cash_register_id",$cashRegisterId)
                ->where("transaction_type",CashRegisterTransactionType::$SUBTRACT_BALANCE)
                ->sum("amount");
            return $subtractedTotal;
        }
        return 0;
    }

    public function getPreviousClosingBalance(){
        $previousCashRegister = $this->orderBy('created_at', 'desc')->where('closing_balance','<>',null)->first();
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
                $cashRegisterTransaction = new CashRegisterTransaction();
                $cashRegisterTransaction->create([
                    "cash_register_id"=>$cash_register->id,
                    "amount"=>$amount,
                    "transaction_type"=>CashRegisterTransactionType::$ADD_BALANCE,
                    "comments" => $comment
                ]);
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
                $cashRegisterTransaction = new CashRegisterTransaction();
                $cashRegisterTransaction->create([
                    "cash_register_id"=>$cash_register->id,
                    "amount"=>$amount,
                    "transaction_type"=>CashRegisterTransactionType::$SUBTRACT_BALANCE,
                    "comments" => $comment
                ]);
                return true;
            }
        }
        return false;
    }

    public function getTotalSaleInCurrentRegister(){
        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){
            $total_sales_in_register = CashRegisterTransaction::where("cash_register_id",$cash_register->id)
                ->where("transaction_type",CashRegisterTransactionType::$CASH_SALES)->sum('amount');
            return $total_sales_in_register;
        }
        return 0;

    }

}
