<?php

namespace App\Http\Controllers;

use App\Model\Customer;
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

        $customerId = \App\Model\Customer::create($customerCredentials)->id;


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

}