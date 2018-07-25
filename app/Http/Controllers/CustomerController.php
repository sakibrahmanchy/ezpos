<?php

namespace App\Http\Controllers;

use App\Enumaration\SaleStatus;
use App\Enumaration\SaleTypes;
use App\Model\Customer;
use App\Model\Item;
use App\Model\LoyaltyTransaction;
use App\Model\PaymentLog;
use App\Model\PriceLevel;
use App\Model\Sale;
use App\Model\CustomerTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class CustomerController extends Controller
{
    public function GetCustomerForm()
    {
        return view('customers.new_customer');
    }

    public function AddCustomer(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'loyalty_card_number'=>'nullable|numeric|unique:customers',
            'account_number'=>'nullable|numeric|unique:customers',
            'email'=>'nullable|email|unique:customers'
        ];
        $allInput = $request->all();

        $validator = Validator::make($allInput, $rules);
        if ($validator->fails()) {


            return redirect()->route('new_customer')
                ->withErrors($validator)
                ->withInput($request->input());
        }

        $customerCredentials['first_name'] = $request->first_name;
        $customerCredentials['last_name'] = $request->last_name;
        $customerCredentials['phone'] = $request->phone;
        $customerCredentials['email'] = $request->email;
        $customerCredentials['address_1'] = $request->address_1;
        $customerCredentials['address_2'] = $request->address_2;
        $customerCredentials['city'] = $request->city;
        $customerCredentials['state'] = $request->state;
        $customerCredentials['zip'] = $request->zip;
        $customerCredentials['country'] = $request->country;
        if($request->loyalty_card_number){
            $customerCredentials['loyalty_card_number'] = $request->loyalty_card_number;
            $customerCredentials['balance'] = 0.0;
        }


        if(!is_null( $request->comments))
            $customerCredentials['comments'] = $request->comments;
        else
            $customerCredentials['comments'] = "";

        $customerCredentials['company_name'] = $request->company_name;
        $customerCredentials['account_number'] = $request->account_number;
        if(is_null($request->taxable))
             $customerCredentials['taxable'] = false;
        else
            $customerCredentials['taxable'] = true;
        $userImageToken = uniqid();
        $file = $request->file('image');

        if ($file) {
            $image = Image::make($file)->stream(); //Resizing image using Intervention Image
            Storage::disk('customer_user_pictures')->put($userImageToken . '.jpg', $image);  // Storing image in the disk as the name according to user id
            $customerCredentials['image_token'] = $userImageToken.'.jpg';
        }
        $customer=  \App\Model\Customer::create($customerCredentials);
        $customerId = $customer->id;


        return redirect()->route('customer_list');



    }

    public function GetCustomerList()
    {
        $customers = Customer::all();

        return view('customers.customer_list', ["customers" => $customers]);
    }

    public function EditCustomerGet($customerId)
    {

        $customerInfo = Customer::where('id',$customerId)->first();

        return view('customers.customer_edit', ['customer' => $customerInfo]);
    }


    public function  EditCustomerPost(Request $request, $customerId)
    {
        $customer = Customer::where("id", "=", $customerId)->first();

        /* var_dump($customer);*/
        $rules = [
            'first_name' => 'required',
            'loyalty_card_number'=>'nullable|numeric|unique:customers,loyalty_card_number,'.$customerId,
            'account_number'=>'nullable|numeric|unique:customers,account_number,'.$customerId,
            'email'=>'nullable|email|unique:customers,email,'.$customerId
        ];
        $allInput = $request->all();

        $validator = Validator::make($allInput, $rules);
        if ($validator->fails()) {


            return redirect()->route('customer_edit', ['customer_id' => $customerId])
                ->withErrors($validator)
                ->withInput($request->input());
        }



        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address_1 = $request->address_1;
        $customer->address_2 = $request->address_2;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->zip = $request->zip;
        $customer->country = $request->country;
        $customer->comments = $request->comments;

        if($request->loyalty_card_number){
            $customer->loyalty_card_number = $request->loyalty_card_number;
        }

        if(!is_null( $request->comments))
            $customer->comments = $request->comments;
        else
            $customer->comments = "";

        $customer->company_name = $request->company_name;
        $customer->account_number = $request->account_number;
        if(is_null($request->taxable))
            $customer->taxable = false;
        else
            $customer->taxable = true;
        $userImageToken = uniqid();
        $file = $request->file('image');

        if ($file) {
            $image = Image::make($file)->stream(); //Resizing image using Intervention Image
            Storage::disk('customer_user_pictures')->put($userImageToken . '.jpg', $image);  // Storing image in the disk as the name according to user id
            $customer->image_token = $userImageToken . ".jpg";
        }

        $customer->save();


        return redirect()->route('customer_list');

    }

    public function DeleteCustomerGet($customerId){

        $customer = new Customer();

        $customer = $customer::where("id",$customerId)->first();

        $customer->delete();

        return redirect()->route('customer_list');
    }

    public function DeleteCustomers(Request $request){

        $customer_list = $request->id_list;
        if(DB::table('customers')->whereIn('id',$customer_list)->delete())
            return response()->json(["success"=>true],200);
        return response()->json(["success"=>false],200);

    }

    public function UseLoyaltyCard(Request $request){

        $loyalty_card_number = $request->loyalty_card_number;
        $due = $request->due;

        if(Customer::where("loyalty_card_number",$loyalty_card_number)->exists()){

            $loyalty_card = Customer::where("loyalty_card_number",$loyalty_card_number)->first();

            if($loyalty_card->loyalty_card_number!=null){

                if($loyalty_card->balance>0){

                    $previous_balance = $loyalty_card->balance;

                    if($due<=$loyalty_card->balance){

                        $loyalty_card->balance -= $due;
                        $loyalty_card->save();

                        $current_balance = $loyalty_card->balance;
                        $balance_deducted = $previous_balance-$current_balance;
                        $due = $due-$balance_deducted;

                        return response()->json(["success"=>true,"due"=>$due,
                            "balance_deducted"=>$balance_deducted,"current_balance"=>$current_balance,"customer_id"=>$loyalty_card->id]);

                    }else{

                        $loyalty_card->balance -= $previous_balance;
                        $loyalty_card->save();

                        $current_balance = $loyalty_card->balance;
                        $balance_deducted = $previous_balance-$current_balance;
                        $due = $due-$balance_deducted;

                        return response()->json(["success"=>true,"due"=>$due,
                            "balance_deducted"=>$balance_deducted,"current_balance"=>$current_balance,"customer_id"=>$loyalty_card->id]);
                    }

                }
                else
                    return response()->json(["success"=>false,"message"=>"Low balance on loyalty card."],200);
            }
            else
                return response()->json(["success"=>false,"message"=>"Loyalty card is not active."],200);
        }else
            return response()->json(["success"=>false,"message"=>"Invalid loyalty card number."],200);
    }

    public function getCustomerProfile($customer_id){
        //DB::connection()->enableQueryLog();
        $customerInfo = Customer::with('transactions','transactionSum')->where("id",$customer_id)->first();
        //$queries = DB::getQueryLog();
        //dd($queries);
        $dueList = Sale::join('customer_transactions','sales.id','=','customer_transactions.sale_id')
            ->where("sale_type",SaleTypes::$SALE)
            ->where("sales.customer_id",$customer_id)
            ->where("sales.due",'>',0)
            ->select(DB::raw('*,customer_transactions.id as transaction_id'))
            ->get();

        $customerAdvancePayment = DB::table('customer_transactions')
                                ->where('customer_id',$customer_id)
                                ->whereNull('sale_id')
                                ->where('paid_amount','>',0)
                                ->sum('paid_amount');

		/*if($customerInfo->transactions->count()>0)
		{
			$oldestTransaction = $customerInfo->transactions->last();
			$oldestTransactionId = $oldestTransaction->id;
			$due = $customerInfo->GetPreviousDue( $customer_id, $oldestTransactionId );
		}*/
        $totalSale = Sale::where('customer_id',$customer_id)->count();
        return view('customers.customer_profile',["customer"=>$customerInfo,
                                                  "saleTotal"=>$totalSale, "dueList" => $dueList, "advance"=>$customerAdvancePayment]);
    }


    public function customerAddBalanceGet(Request $request) {

        if(!isset($request->customer_id))
            $customer_id = 0;
        else
            $customer_id = $request->customer_id;

        $customers = Customer::all();
        if($customer_id!=0){
            $customerInfo = Customer::with('transactions','transactionSum')->where("id",$customer_id)->first();
            $saleTotalInfo = Sale::where('customer_id',$customer_id)->get();
            return view('customers.customer_add_balance',
                ["customer_id"=>$customer_id,"customers"=>$customers,"customer"=>$customerInfo,"saleTotal"=>$saleTotalInfo]);
        }
        return view('customers.customer_add_balance',["customer_id"=>$customer_id,"customers"=>$customers]);

    }

    public function customerAddBalancePost(Request $request) {
        $this->validate($request,[
            "customer_id" => "required",
            "payment_method" => "required",
            "amount_to_add" => "required"
        ]);
		$customerTransactionObj = new CustomerTransaction();
		$customerTransactionObj->customer_id = $request->customer_id;
		$customerTransactionObj->transaction_type = \App\Enumaration\CustomerTransactionType::PAYMENT;
		$customerTransactionObj->paid_amount = $request->amount_to_add;
		$customerTransactionObj->save();
		
		$updateCustomerBalanceQuery = "update customers set account_balance=account_balance-? where id=?";
		DB::update( $updateCustomerBalanceQuery, [ $request->amount_to_add, $request->customer_id] );
		
        $paymentLog = new PaymentLog();
        $paymentLog->addNewPaymentLog( $request->payment_method, $request->amount_to_add,null,$request->customer_id,null);
        return redirect()->route('customer_balance_add',["customer_id"=>$request->customer_id]);
    }

    public function customerAssignPriceLevelGet($customerId) {
        $priceLevels = PriceLevel::all();
        $items = Item::all();
        $customer = Customer::where("id",$customerId)->with('Items')->first();
        return view('customers.customer_price_level_assign',["priceLevels"=>$priceLevels,"allItems"=>$items,"customer"=>$customer]);
    }

    public function customerAssignPriceLevelPost(Request $request) {
        $customer = Customer::where("id",$request->customer_id)->first();
        $id_list = $request->id_list;
        foreach ($id_list as $anId){
            if(!$customer->items->contains($anId)) {
                $customer->Items()->attach([$anId => ["price_level_id"=>$request->price_level_id]]);
            }else
            {
                DB::table('customer_item')
                    ->where("customer_id",$request->customer_id)
                    ->where("item_id",$anId)
                    ->update(["price_level_id"=>$request->price_level_id]);
            }
        }
    }

    public function removePriceLevelPost(Request $request){
        $customer = Customer::where("id",$request->customer_id)->first();
        $id_list = $request->id_list;
        foreach ($id_list as $anId){
            $customer->Items()->detach($anId);
        }
    }
}
