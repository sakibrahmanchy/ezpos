<?php

namespace App\Http\Controllers\Api;

use App\Enumaration\PaymentTypes;
use App\Enumaration\SaleStatus;
use App\Enumaration\UserTypes;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
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
                                     "message" => $validation->errors()->first()], 200);
        }

        $processedOrder = new ProcessOrder($request);

        $processedItems = $processedOrder->processItems();
        $paymentInfo = $processedOrder->processPaymentInfo();
        $saleInfo = $processedOrder->processSaleInfo();
        $sale = new Sale();
        $sale_id  = $sale->InsertSale($saleInfo,$processedItems,null,SaleStatus::$LAYAWAY);

        DB::connection('mysql_restaurant')->table('orders')->where('id', $request->order_id)->update(['ezpos_sale_id' => $sale_id]);

        return response(["success"=>true, "message"=>"Sale Successfull","data"=>$sale_id]);
    }

    public function getSaleReceipt($saleId) {
        $sale = Sale::withTrashed()->where("id", $saleId)->with('items', 'paymentlogs', 'customer', 'counter')->first();

        /*return response()->json(['sale'=>$sale], 200);*/
        if ($sale == null)
            return response(["success"=>false, "message"=>"Sale not found"]);
        else{
            return response(["success"=>true, "message"=>"Sale found","data"=>$sale]);
        }

    }

    public function printOrder($orderId, Request $request) {
        $saleController = new SaleController();
        $saleController->printSaleReciept($orderId,$request);
        return response(["success"=>true, "message"=>"Successfully printed"]);

    }


}
