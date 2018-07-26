<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CustomerTransaction extends Model
{
	protected $table = 'customer_transactions';
	protected $fillable = ['customer_id','sale_id','paid_amount','sale_amount','transaction_type','cash_register_id'];

    public function Invoices() {
        return $this->belongsToMany('App\Model\Invoice','customer_transaction_invoice','invoice_id','customer_transaction_id');
    }
}
?>