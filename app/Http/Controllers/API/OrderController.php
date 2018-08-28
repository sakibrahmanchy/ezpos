<?php

namespace App\Http\Controllers\Api;

use App\Enumaration\PaymentTypes;
use App\Enumaration\SaleStatus;
use App\Enumaration\UserTypes;
use App\Http\Controllers\Controller;
use App\Model\Api\ProcessOrder;
use App\Model\CashRegister;
use App\Model\Customer;
use App\Model\Employee;
use App\Model\Sale;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;


class OrderController extends Controller
{
    public function processOrder(Request $request) {

        $rules = [
            'items' => 'required|array|min:1',
            'counter_id' => 'required|integer',
            'cash_register_id' => 'required|integer',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json(["success" => false,
                                     "message" => $validation->errors()->first()], 406);
        }


        $processedOrder = new ProcessOrder($request);

        $processedItems = $processedOrder->processItems();
        $paymentInfo = $processedOrder->processPaymentInfo();
        $saleInfo = $processedOrder->processSaleInfo();
        $sale = new Sale();
        $receipt_id  = $sale->InsertSale($saleInfo,$processedItems,$paymentInfo,SaleStatus::$SUCCESS);
        dd($receipt_id);

//        $saleInfo = $processedOrder->processSaleInfo();
//
//        dd($paymentInfo);
//        dd($customer_id);
    }


}
