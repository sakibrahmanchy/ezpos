<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['total_amount_of_charge','customer_id','last_date_of_payment'];

    public function Transactions() {
        return $this->belongsToMany('App\Model\CustomerTransaction','customer_transaction_invoice','invoice_id','customer_transaction_id');
    }

    public function Customer() {
        return $this->belongsTo('App\Model\Customer','customer_id');
    }

    public function PaymentLogs() {
        return $this->hasMany('App\Model\PaymentLog');
    }
}
