<?php
/**
 * Created by PhpStorm.
 * User: ByteLab
 * Date: 8/8/2018
 * Time: 11:19 AM
 */

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\Counter;
use App\Model\Customer;

class CustomerController extends Controller {

    public function getCustomerList()
    {
        $customers = Customer::all();
        if(!is_null($customers))
            return response()->json(['success'=>true, 'data'=>$customers], 200);

        return response()->json(['success'=>false, 'data'=>null],500);
    }


    public function getCustomer($customer_id) {
        $customer = Customer::where('id',$customer_id)->first();
        if(!is_null($customer))
            return response()->json(['success'=>true, 'data'=>$customer], 200);

        return response()->json(['success'=>false, 'message'=>'No such customer', 'data'=>null],500);
    }
}