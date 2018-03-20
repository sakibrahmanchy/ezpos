<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    public function Sales(){
        return $this->hasMany('App\Model\Sale');
    }

    protected $fillable = ['first_name','last_name','phone','image_token','image','address_1','address_2','city','state','zip',
        'country', 'comments','comapny_name','account_number','taxable','loyalty_card_number','balance'];

    public function transactionSum()  {
        return $this->hasMany('App\Model\Transaction')
            ->selectRaw('sum(sale_amount-amount_paid) as totalDue, sum(amount_paid) as totalPaid, customer_id' )
            ->groupBy('customer_id');
    }

    public function transactions()  {
        return $this->hasMany('App\Model\Transaction');
    }
}
