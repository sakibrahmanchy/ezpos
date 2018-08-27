<?php

namespace App\Http\Controllers\Api;

use App\Enumaration\UserTypes;
use App\Http\Controllers\Controller;
use App\Model\CashRegister;
use App\Model\Counter;
use App\Model\Employee;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;


class CashRegisterController extends Controller
{
    public function openCashRegister(Request $request) {

        $rules = [
            "opening_balance"=>'required|numeric',
            "counter_id"=> "required|numeric"
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json(["success" => false,
                                     "message" => $validation->errors()->first()], 406);
        }

        $opening_balance = $request->opening_balance;
        $counter_id = $request->counter_id;
        $opened_by = Auth::user()->id;
        //date_default_timezone_set(date_default_timezone_get());
        if(CashRegister::isThereAnyOtherRegistersThatAreOpenedByTheUser($opened_by)) {

            if(Counter::where("id",$counter_id)->exists()) {
                $cashRegisterId = $this->createNewCashRegister($counter_id,$opening_balance, $opened_by);
                return response()->json([
                    'success'=>true,
                    'message' => "Cash Register Opened",
                    "data" =>['cash_register_id'=>$cashRegisterId]],200);
            }
            else{
                return response()->json([
                    'success'=>false,
                    'message' => "Invalid counter id"],422);
            }
        }
        return response()->json([
            'success'=>false,
            'message' => "This user has already opened a cash register which needs to be closed."],403);

    }


    public function getActiveCashRegister()
    {
        dd(Auth::user());
       $cashRegister = new CashRegister();
       $activeRegister = $cashRegister->getCurrentActiveRegister();
        return response()->json([
            'success'=>true,
            'message' => "User currently has an active cash register",
            "data" =>["cash_register_id" => $activeRegister->id]],200);
    }

    public function createNewCashRegister($counter_id, $opening_balance, $opened_by) {
        $opening_time =   date('Y-m-d h:i:s', time());
        $cashRegisterOpenInfo = array(
            "opening_balance" => $opening_balance,
            "counter_id" => $counter_id,
            "user_id" => Auth::id(),
            "opening_time" => $opening_time,
            "current_balance"=>$opening_balance,
            "opened_by"  => $opened_by
        );
        return DB::table('cash_registers')->insertGetId($cashRegisterOpenInfo);
    }



//    public function closeCurrentCashRegister()
//    {
//        $cashRegister = new CashRegister();
//
//        $openingBalance = $cashRegister->getActiveRegisterOpeningBalance();
//        $total_additions = $cashRegister->getTotalAddedAmountInActiveRegister();
//        $total_subtractions = $cashRegister->getTotalSubtractedAmountInActiveRegister();
//
//        $salePaymentInfo = CashRegister::generatePaymentAmount($cashRegister->getCurrentActiveRegister()->id, [SaleStatus::$SUCCESS]);
//        $suspendedSalePaymentInfo = CashRegister::generatePaymentAmount($cashRegister->getCurrentActiveRegister()->id,
//            [SaleStatus::$ESTIMATE, SaleStatus::$LAYAWAY]);
//
//        $cash_sales = $salePaymentInfo["cashTotal"];
//
//
//        $refunded_sales_amount = $cashRegister->getRefundedSalesAmountInCashRegister($cashRegister->getCurrentActiveRegister()->id  );
//        $denominations = CurrencyDenomination::all();
//        $closing_balance = $openingBalance + $salePaymentInfo["cashTotal"]  + $total_additions + $total_subtractions + $suspendedSalePaymentInfo["cashTotal"] - $refunded_sales_amount;
//
//        return view('cash_registers.close_cash_register',["denominations"=>$denominations,"openingBalance"=>$openingBalance,
//                                                          "additions"=>$total_additions,"subtractions"=>$total_subtractions,"sales"=>$cash_sales,"change_due"=>$salePaymentInfo["changedDue"],
//                                                          "refunded_amount"=>$refunded_sales_amount],compact('salePaymentInfo','suspendedSalePaymentInfo','closing_balance'));
//
//    }

}
