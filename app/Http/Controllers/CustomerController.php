<?php

namespace App\Http\Controllers;

use App\Model\Customer;
use App\Model\LoyaltyTransaction;
use App\Model\PaymentLog;
use App\Model\Sale;
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
            'loyalty_card_number'=>'nullable|unique:customers'
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
            'loyalty_card_number'=>'nullable|unique:customers,loyalty_card_number,'.$customerId,
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
        $customerInfo = Customer::with('transactions','transactionSum')->where("id",$customer_id)->first();
        $transactionHistory = Customer::with('transactions','transactionSum')
            ->where('id',$customer_id)->take(2)->get();
        $saleInfo = Sale::with('items')->where('customer_id',$customer_id)->take(7)->get();
        $saleTotalInfo = Sale::where('customer_id',$customer_id)->get();
        return view('customers.customer_profile',["customer"=>$customerInfo,
                                                  'sales'=>$saleInfo,
                                                  "saleTotal"=>$saleTotalInfo,
                                                  "transactionHistory"=>$transactionHistory]);
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
        $paymentLog = new PaymentLog();
        $paymentLog->addNewPaymentLog( $request->payment_method, $request->amount_to_add,null,$request->customer_id);
        return redirect()->route('customer_balance_add',["customer_id"=>$request->customer_id]);
    }
}
