<?php

namespace App;

use App\Enumaration\CashRegisterTransactionType;
use Illuminate\Database\Eloquent\Model;

class CashRegisterTransaction extends Model
{
    protected $fillable = ['cash_register_id','amount','transaction_type','comments'];


    /**
     * @return mixed
     */
    public function getCashRegisterId()
    {
        return $this->cash_register_id;
    }

    /**
     * @param mixed $cash_register_id
     */
    public function setCashRegisterId($cash_register_id)
    {
        $this->cash_register_id = $cash_register_id;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getTransactionType()
    {
        return $this->transaction_type;
    }

    /**
     * @param mixed $transaction_type
     */
    public function setTransactionType($transaction_type)
    {
        $this->transaction_type = $transaction_type;
    }


    public function totalAdditions(){
        return CashRegisterTransaction::where("id",$this->id)->sum('amount');
        //return $this->sum('amount')->where('cash_register_transactions.transaction_type',CashRegisterTransactionType::$ADD_BALANCE);
    }
}
