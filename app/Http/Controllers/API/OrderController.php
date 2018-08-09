<?php

namespace App\Http\Controllers\Api;

use App\Enumaration\PaymentTypes;
use App\Enumaration\UserTypes;
use App\Http\Controllers\Controller;
use App\Model\Api\ProcessOrder;
use App\Model\CashRegister;
use App\Model\Customer;
use App\Model\Employee;
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
        $processedOrder = new ProcessOrder($request);

//        $customer_id = $processedOrder->processCustomerAndGetId();
//        $paymentInfo = $processedOrder->processPaymentInfo();
//
//        dd($paymentInfo);
//        dd($customer_id);
    }

    public function processCustomerAndGetId($allData) {

        if(isset($allData->customer_id) && CheckNull($allData->customer_id))
            return $allData->customer_id;

        if(CheckNull($allData->customer_name)) {
            $customer_id = DB::table('customers')->InsertGetId([
                'first_name' => $allData->customer_name,
                'address_1' => $allData->customer_address,
                'phone' => $allData->customer_phone
            ]);

            return $customer_id;
        }
        return 0;
    }

    public function processPaymentInfo($allData) {
//        dd($allData->all());
        return array(
            "payment_type" => PaymentTypes::$TypeList[$allData->payment_method],
            "paid_amount" => $allData->paid
        );
    }

    public function processItems($items) {
        $processedItems = array();

        foreach( $items as $anItem ) {
            $currentProcessedItem['item_id'] = $anItem->id;
            $currentProcessedItem['unit_price'] = $anItem->perUnitPrice;
            $currentProcessedItem['cost_price'] = $anItem->stock_price;
            $currentProcessedItem['total_price'] = $anItem->totalPrice;
            $currentProcessedItem['quantity'] = $anItem->quantity;
            $currentProcessedItem['discount_amount'] = $anItem->quantity;
            $currentProcessedItem['price_rule_id'] = $anItem->quantity;
            $currentProcessedItem['item_type'] = $anItem->quantity;
            $currentProcessedItem['item_discount_percentage'] = $anItem->quantity;
            $currentProcessedItem['sale_discount_amount'] = $anItem->quantity;
            $currentProcessedItem['item_profit'] = $anItem->quantity;
            $currentProcessedItem['tax_amount'] = $anItem->quantity;
            $currentProcessedItem['tax_rate'] = $anItem->quantity;
            $currentProcessedItem['is_price_taken_from_barcode'] = false;
        }
    }

    public function calculateDiscountAmount() {

    }


}
