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

        $processedItems = $processedOrder->processItems();
        dd($processedItems);
//        $customer_id = $processedOrder->processCustomerAndGetId();
//        $paymentInfo = $processedOrder->processPaymentInfo();
//
//        dd($paymentInfo);
//        dd($customer_id);
    }


}
