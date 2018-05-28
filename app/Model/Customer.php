<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use SoftDeletes;

    public function Sales(){
        return $this->hasMany('App\Model\Sale');
    }

    public function Items() {
        return $this->belongsToMany('App\Model\Item');
    }

    protected $fillable = ['first_name','last_name','phone','image_token','image','address_1','address_2','city','state','zip',
        'country', 'comments','comapny_name','account_number','taxable','loyalty_card_number','balance', 'email'];

    public function transactionSum()  {
        return $this->hasMany('App\Model\Transaction')
            ->selectRaw('sum(sale_amount) as total_receivable, sum(sale_amount-amount_paid) as totalDue, sum(amount_paid) as totalPaid, customer_id' )
            ->groupBy('customer_id');
    }

    public function getBalance($customer_id,$date) {

        $sql = 'select sum(sale_amount) as total_receivable, sum(sale_amount-amount_paid) as totalDue, 
                                               sum(amount_paid) as totalPaid, customer_id from transactions 
                                               where customer_id = ?
                                               and date(created_at) <= ? group by customer_id';

        $generatedResult = DB::select($sql,array($customer_id,$date));

        $balance = 0;
        if(!empty($generatedResult))
            $balance = $generatedResult[0]->totalDue;

        return $balance;
    }


    public function transactions()  {
        return $this->hasMany('App\Model\Transaction')->orderBy('id','asc');
    }}

