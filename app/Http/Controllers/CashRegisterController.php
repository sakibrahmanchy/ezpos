<?php

namespace App\Http\Controllers;

use App\Model\CurrencyDenomination;
use App\Model\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class CashRegisterController extends Controller
{
    public function openNewCashRegisterGet(){
        $denominations = CurrencyDenomination::all();
        $cashRegister = new CashRegister();
        $previousClosingBalance = $cashRegister->getPreviousClosingBalance();
        return view('cash_registers.open_cash_register',["denominations"=>$denominations,"previous_closing_balance"=>$previousClosingBalance]);
    }

    public function openNewCashRegister(Request $request){
        $this->validate($request,[
            "opening_balance"=>'required|numeric'
        ]);
        $counter_id = Cookie::get('counter_id');
        $opening_balance = $request->opening_balance;
        $current_balance = $opening_balance;
        date_default_timezone_set(date_default_timezone_get());
        $opening_time =   date('Y-m-d h:i:s', time());
        $opened_by = Auth::user()->id;
        $cashRegisterOpenInfo = array(
            "opening_balance" => $opening_balance,
            "counter_id" => $counter_id,
            "opening_time" => $opening_time,
            "current_balance"=>$current_balance,
            "opened_by"  => $opened_by
        );

        $cashRegisterId = DB::table('cash_registers')->insertGetId($cashRegisterOpenInfo);

        foreach ($request->all() as $aKey => $aValue){

            if(strpos($aKey,"denom") !== false){
                if(!is_null($aValue)&& $aValue!=0){
                    $id = str_after($aKey,"denom_");
                    DB::table('cash_register_day_detail_information')->insert([
                        "cash_register_id" => $cashRegisterId,
                        "note_id" => $id,
                        "quantity" => $aValue
                    ]);
                }
            }
        }

        return redirect()->route('new_sale');
    }

    public function addCashToRegister(){
        $cashRegister = new CashRegister();
        $total_amount_added_to_register = $cashRegister->getTotalAddedAmountInActiveRegister();
        return view('cash_registers.add_cash_to_register',["added_amount"=>$total_amount_added_to_register]);
    }

    public function addCashToRegisterPost(Request $request){
        $this->validate($request,[
            "amount"=> "required|numeric"
        ]);
        $cashRegister = new CashRegister();
        if($cashRegister->addCashToRegister($request->amount, $request->note)){
            return redirect()->route('new_sale')->with(["success"=>"Balance successfully added to register"]);
        }
        return redirect()->back()   ->with(["error"=>"Failed to add balance to register"]);
    }

    public function subtractCashFromRegister(){
        $cashRegister = new CashRegister();
        $total_amount_subtracted_from_register = $cashRegister->getTotalSubtractedAmountInActiveRegister();
        return view('cash_registers.subtract_cash_from_register',["subtracted_amount"=>$total_amount_subtracted_from_register]);
    }

    public function subtractCashFromRegisterPost(Request $request){
        $this->validate($request,[
            "amount"=> "required|numeric"
        ]);
        $cashRegister = new CashRegister();
        if($cashRegister->subtractCashFromRegister($request->amount, $request->note)){
            return redirect()->route('new_sale')->with(["success"=>"Balance successfully subtracted from register"]);
        }
        return redirect()->back()->with(["error"=>"Failed to subtract balance from register"]);
    }

    public function closeCurrentCashRegister()
    {

        $cashRegister = new CashRegister();
        $activeRegister = $cashRegister->getCurrentActiveRegister();
        if (!is_null($activeRegister))
        {
            $cash_info = DB::table('cash_registers')
                ->join('cash_register_transactions','cash_registers.id','=','cash_register_transactions.cash_register_id')
                ->where("cash_register_id",$activeRegister->id)->first();
            $total_additions = $cashRegister->getTotalAddedAmountInActiveRegister();
            $total_subtractions = $cashRegister->getTotalSubtractedAmountInActiveRegister();
            $cash_sales = 0;
        }
        $denominations = CurrencyDenomination::all();
         return view('cash_registers.close_cash_register',["denominations"=>$denominations,"cash_info"=>$cash_info,
                                "additions"=>$total_additions,"subtractions"=>$total_subtractions,"sales"=>$cash_sales]);

    }

    public function closeCashRegisterPost(Request $request){
        $cashRegister = new CashRegister();
        $activeRegister = $cashRegister->getCurrentActiveRegister();
        $activeRegister->closing_balance = $request->closing_amount;
        date_default_timezone_set(date_default_timezone_get());
        $closing_time =   date('Y-m-d h:i:s', time());
        $activeRegister->closing_time = $closing_time;
        $activeRegister->closed_by = Auth::user()->id;
        if($activeRegister->save())
            return redirect()->route('dashboard');
    }

}
