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
        /*return $this->hasMany('App\Model\Transaction')
            ->selectRaw('sum(sale_amount) as total_receivable, sum(sale_amount-amount_paid) as totalDue, sum(amount_paid) as totalPaid, customer_id' )
            ->groupBy('customer_id');*/
		return $this->hasMany('App\Model\CustomerTransaction')
            ->whereRaw('sale_amount >= paid_amount')
                        ->selectRaw('sum(sale_amount) as total_receivable, sum(sale_amount-paid_amount) as totalDue, sum(paid_amount) as totalPaid, customer_id' );

    }

    public function getBalance($customer_id,$date) {

        $sql = 'select sum(sale_amount) as total_receivable, sum(sale_amount-paid_amount) as totalDue, 
                                               sum(paid_amount) as totalPaid, customer_id from customer_transactions   
                                               where customer_id = ?
                                               and date(created_at) <= ?';

        $generatedResult = DB::select($sql,array($customer_id,$date));

        $balance = 0;
        if(!empty($generatedResult))
            $balance = $generatedResult[0]->totalDue;

        return $balance;
    }


    public function transactions()  {
        return $this->hasMany('App\Model\CustomerTransaction')->orderBy('id','desc')->take(10);
    }
	
	public function allTransactions()  {
        return $this->hasMany('App\Model\CustomerTransaction')->orderBy('id','desc')->take(10);
    }
	
	public function GetPreviousDue($customer_id,$oldest_id)
	{
		 $sql = 'select sum(sale_amount-paid_amount) as totalDue 
								from customer_transactions where customer_id = ? and id < ?';

        $generatedResult = DB::select($sql,array($customer_id,$oldest_id));

        $totalDue = 0;
        if(!empty($generatedResult))
            $totalDue = $generatedResult[0]->totalDue;
		return $totalDue;
	}
}

