<?php

namespace App\Http\Controllers\Api;

use App\Enumaration\CashRegisterTransactionType;
use App\Enumaration\PaymentTypes;
use App\Enumaration\SaleStatus;
use App\Enumaration\SaleTypes;
use App\Enumaration\UserTypes;
use App\Http\Controllers\Controller;
use App\Model\CashRegister;
use App\Model\Counter;
use App\Model\Employee;
use App\Model\PaymentLog;
use App\Model\Printer\FooterItem;
use App\Model\Printer\RegisterDetails;
use App\Model\Sale;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;


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
        if(!CashRegister::isThereAnyOtherRegistersThatAreOpenedByTheUser($opened_by)) {

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
       $cashRegister = new CashRegister();
       $activeRegister = $cashRegister->getCurrentActiveRegister();

       if(is_null($activeRegister)) {
           return response()->json([
               'success'=> true,
               'message' => "No active cash register for current user",
               "data" => null
               ],200);
       }

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

    public function closeActiveRegister(Request $request) {
        $cashRegister = new CashRegister();
        $activeRegister = $cashRegister->getCurrentActiveRegister();
        if(is_null($activeRegister))
            return response()->json([
                'success'=>false,
                'message' => "Cash register is already closed.",
                "data" => null ],200);
        else {
            $activeRegister->closing_balance = $request->closing_amount;
            date_default_timezone_set(date_default_timezone_get());
            $closing_time =   date('Y-m-d h:i:s', time());
            $activeRegister->closing_time = $closing_time;
            $activeRegister->closed_by = Auth::user()->id;
            if($activeRegister->save())
                return response()->json([
                    'success'=>true,
                    'message' => "Cash Register Closed Successfully",
                    "data" =>["register" => $activeRegister]],200);
        }

    }


    public function cashRegisterLogDetails($cashRegisterId) {

        $rules = [
            "cash_register_id"=>'required|numeric'
        ];

        $validation = Validator::make(["cash_register_id"=>$cashRegisterId], $rules);
        if ($validation->fails()) {
            return response()->json(["success" => false,
                                     "message" => $validation->errors()->first()], 406);
        }


        $cashRegister = new CashRegister();
        $refunded_sales_amount = $cashRegister->getRefundedSalesAmountInCashRegister($cashRegisterId);

        $cashRegister = CashRegister::where("id",$cashRegisterId)->with('OpenedByUser','ClosedByUser','CashRegisterTransactions')->first();

        $saleList = PaymentLog::where('payment_logs.cash_register_id', $cashRegisterId)
            ->join('sales','sales.id','=','payment_logs.sale_id')
            ->where("sale_type",SaleTypes::$SALE)
            ->select(DB::raw('payment_logs.payment_type,payment_logs.paid_amount,payment_logs.cash_register_id,
            payment_logs.sale_id,sales.sale_type,payment_logs.sale_status,payment_logs.created_at'))->get();

        $allTransactionArr = CashRegister::generateTransactionData($saleList,$cashRegister,SaleStatus::$SUCCESS);
        $suspendedTransactionsLayAway = CashRegister::generateTransactionData($saleList,$cashRegister,SaleStatus::$LAYAWAY);
        $suspendedTransactionsEstimate = CashRegister::generateTransactionData($saleList,$cashRegister,SaleStatus::$ESTIMATE);
        $suspendedSalesTransactionsMerged = array_merge($suspendedTransactionsLayAway,$suspendedTransactionsEstimate);
        $deletedSales = Sale::getDeletedSales($cashRegisterId);

        $paymentInfo = CashRegister::generatePaymentAmount($cashRegisterId,[SaleStatus::$SUCCESS]);
        $paymentInfoSuspended = CashRegister::generatePaymentAmount($cashRegisterId, [SaleStatus::$LAYAWAY, SaleStatus::$ESTIMATE]);

        $openedBy = $cashRegister->OpenedByUser->name;
        $closedBy = $cashRegister->closedByUser->name;
        $total_additions = PaymentLog::where('cash_register_id',$cashRegisterId)->where('payment_type',CashRegisterTransactionType::$ADD_BALANCE)->sum('paid_amount');
        $total_subtractions = PaymentLog::where('cash_register_id',$cashRegisterId)->where('payment_type',CashRegisterTransactionType::$SUBTRACT_BALANCE)->sum('paid_amount');
        $cash_sales = $paymentInfo["cashTotal"];

        $expectedClosingSales = $cashRegister->opening_balance + $cash_sales  +  ($total_additions - $total_subtractions) + $paymentInfoSuspended["cashTotal"];



        return response()->json(["success"=>true,"message"=>"Successfull ","register"=>$cashRegister,
                                                                "transactions"=>$allTransactionArr,"suspendedTransactions"=>$suspendedSalesTransactionsMerged,
                                                                "opened_by"=>$openedBy,"closed_by"=>$closedBy, "additions"=>$total_additions,"subtractions"=>$total_subtractions,"sales"=>$cash_sales,
                                                                "paymentInfo"=>$paymentInfo, "paymentSuspended" => $paymentInfoSuspended,  "refundedAmount"=>$refunded_sales_amount, "closing_balance"=>$expectedClosingSales,
                                                                "deleted_sales"=>$deletedSales]);
    }


    public function printRegisterLogDetails($cashRegisterId, Request $request) {


        $rules = [
            "counter_id"=>'required|numeric'
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json(["success" => false,
                                     "message" => $validation->errors()->first()], 406);
        }

        $counter_id = $request->counter_id;
        $counter = Counter::where("id",$counter_id)->first();
        if(is_null($counter))
            return response()->json(["success"=>false, "message"=>"Counter does not exist"]);

        $cashRegister = CashRegister::where("id",$cashRegisterId)->with('OpenedByUser','ClosedByUser','CashRegisterTransactions')->first();
        $transactions = CashRegister::where("id",$cashRegisterId)->with('CashRegisterTransactions')->first()->CashRegisterTransactions;


        $saleList = PaymentLog::where('payment_logs.cash_register_id', $cashRegisterId)
            ->join('sales','sales.id','=','payment_logs.sale_id')
            ->where("sale_type",SaleTypes::$SALE)
            ->select(DB::raw('payment_logs.payment_type,payment_logs.paid_amount,payment_logs.cash_register_id,
            payment_logs.sale_id,sales.sale_type,payment_logs.sale_status,payment_logs.created_at'))->get();



        $allTransactionArr = CashRegister::generateTransactionData($saleList,$cashRegister,SaleStatus::$SUCCESS);
        $suspendedTransactionsLayAway = CashRegister::generateTransactionData($saleList,$cashRegister,SaleStatus::$LAYAWAY);
        $suspendedTransactionsEstimate = CashRegister::generateTransactionData($saleList,$cashRegister,SaleStatus::$ESTIMATE);
        $suspendedTransactionArr = array_merge($suspendedTransactionsLayAway,$suspendedTransactionsEstimate);

        $salesChargeAccountTransactionList = Sale::join('payment_logs','sales.id','=','payment_logs.sale_id')
            ->join('customers','sales.customer_id','=','customers.id')
            ->where('payment_logs.sale_status',\App\Enumaration\SaleStatus::$LAYAWAY )
            ->where('sale_type', \App\Enumaration\SaleTypes::$SALE)
            ->where('payment_logs.cash_register_id', $cashRegisterId)
            ->select(DB::raw('sale_id, paid_amount as paid_amount, first_name, last_name, payment_logs.created_at'))
            ->get();


        try {

            $counter_id = $request->counter_id;
            $counter = Counter::where("id",$counter_id)->first();

            if($counter->printer_connection_type && $counter->printer_connection_type==\App\Enumaration\PrinterConnectionType::USB_CONNECTION) {
                $connector = new WindowsPrintConnector($counter->name);
            }
            else {
                $ip_address = $counter->printer_ip;
                $port = $counter->printer_port;

                $connector = new NetworkPrintConnector($ip_address, $port);
            }

            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Register Log Details\n");
            $printer->selectPrintMode();
            $printer->text( date('Y-m-d'). "\n");


            $printer->text("-------------------------------------------\n");
            $printer->feed();

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Cash added\n");
            $printer->text("-------------------------\n");
            $printer->feed();

            $header = new \App\Model\Printer\RegisterDetails("Date", "Sale Id", "Amount");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text($header);
            $printer->setEmphasis(false);
            $printer->feed();
            foreach($transactions as $aTransaction ) {

                if($aTransaction->payment_type==\App\Enumaration\CashRegisterTransactionType::$ADD_BALANCE)
                {
                    $printer->text(new RegisterDetails(
                        date_format($aTransaction->created_at,"Y-m-d"),
                        $aTransaction->sale_id,
                        number_format($aTransaction->amount,2),
                        date_format($aTransaction->created_at,"h:i:s")
                    ));
                    $printer->feed();
                }
            }
            $printer->feed();

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Cash Subtracted\n");
            $printer->text("-----------------------\n");
            $printer->feed();

            $header = new \App\Model\Printer\RegisterDetails("Date", "Sale Id", "Amount");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text($header);
            $printer->setEmphasis(false);
            $printer->feed();
            foreach($transactions as $aTransaction ) {

                if($aTransaction->payment_type==\App\Enumaration\CashRegisterTransactionType::$SUBTRACT_BALANCE)
                {
                    $printer->text(new RegisterDetails(
                        date("Y-m-d" , strtotime($aTransaction['created_at'])),
                        $aTransaction['sale_id'],
                        number_format($aTransaction['amount'],2),
                        date("h:i:s", strtotime($aTransaction['created_at']))
                    ));
                    $printer->feed();
                }
            }
            $printer->feed();


            $paymentTypeMapping = array(
                PaymentTypes::$TypeList['Cash'] => "Cash Sales",
                PaymentTypes::$TypeList['Check'] => "Cheque Sales",
                PaymentTypes::$TypeList["Debit Card"] => "Debit Card Sales",
                PaymentTypes::$TypeList["Credit Card"] => "Credit Card Sales",
                PaymentTypes::$TypeList["Gift Card"] => "Gift Card Sales",
                PaymentTypes::$TypeList["Loyalty Card"] => "Loyalty Card Sales",
            );
            foreach( $paymentTypeMapping as $paymentTypeDB=>$paymentTypeShow  )
            {
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("{$paymentTypeShow}\n");
                $printer->text("------------------------\n");
                $printer->feed();

                $header = new \App\Model\Printer\RegisterDetails("Date", "Sale Id", "Amount");
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->setEmphasis(true);
                $printer->text($header);
                $printer->setEmphasis(false);
                $printer->feed();


                foreach($allTransactionArr as $aTransaction ) {

                    if($aTransaction['payment_type']==$paymentTypeDB)
                    {

                        $printer->text(new RegisterDetails(
                            date("Y-m-d" , strtotime($aTransaction['created_at'])),
                            $aTransaction['sale_id'],
                            number_format($aTransaction['amount'],2),
                            date("h:i:s", strtotime($aTransaction['created_at']))
                        ));
                        $printer->feed();
                    }
                }
                $printer->feed();
            }

            $printer->cut();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Suspended Sale Details\n");
            $printer->text("------------------------\n");
            $printer->feed();

            foreach( $paymentTypeMapping as $paymentTypeDB=>$paymentTypeShow  )
            {
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("{$paymentTypeShow}\n");
                $printer->text("------------------------\n");
                $printer->feed();

                $header = new \App\Model\Printer\RegisterDetails("Date", "Sale Id", "Amount");
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->setEmphasis(true);
                $printer->text($header);
                $printer->setEmphasis(false);
                $printer->feed();

                foreach($suspendedTransactionArr as $aTransaction ) {

                    if($aTransaction['payment_type']==$paymentTypeDB)
                    {
                        $printer->text(new RegisterDetails(
                            date("Y-m-d" , strtotime($aTransaction['created_at'])),
                            $aTransaction['sale_id'],
                            number_format($aTransaction['amount'],2),
                            date("h:i:s", strtotime($aTransaction['created_at']))
                        ));

                    }
                }

            }
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Charge Account\n");
            $printer->text("------------------------\n");
            $printer->feed();

            $header = new \App\Model\Printer\RegisterDetails("Date", "Customer", "Amount");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text($header);
            $printer->setEmphasis(false);
            $printer->feed();

            foreach($salesChargeAccountTransactionList as $asalesChargeAccountTransaction ) {
                $due = $asalesChargeAccountTransaction->paid_amount;
                $customerName = $asalesChargeAccountTransaction->first_name . ' ' . $asalesChargeAccountTransaction->last_name;
                $printer->text(new RegisterDetails(
                    date( "Y-m-d", strtotime($asalesChargeAccountTransaction->created_at) ),
                    $customerName,
                    number_format($due,2),
                    date( "h:i:s" ,strtotime($asalesChargeAccountTransaction->created_at) )
                ));
                $printer->feed();
            }
            $printer->feed();


            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Deleted Sales\n");
            $printer->text("------------------------\n");
            $printer->feed();

            $header = new \App\Model\Printer\RegisterDetails("Id",  "Deleted At", "Amount");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text($header);
            $printer->setEmphasis(false);
            $printer->feed();

            $deletedSales = Sale::getDeletedSales($cashRegisterId);
            foreach($deletedSales as $aDeletedSale ) {
                $printer->text(new RegisterDetails(
                    $aDeletedSale->id,
                    date( "h:i:s" ,strtotime($aDeletedSale->deleted_at) ),
                    number_format($aDeletedSale->total_amount,2)
                ));
                $printer->feed();
            }
            $printer->feed();

        } Catch (\Exception $e) {
            return redirect()->back()->with(["error" => $e->getMessage()]);
        } finally {
            if (isset($printer)) {
                $printer->cut();
                $printer->pulse();
                $printer->close();
                return response()->json(["success" => true, "message"=> "Successfully printed"]);
            }
        }

    }

    public function printRegisterLogSummary($cashRegisterId, Request $request)
    {

        $rules = [
            "counter_id"=>'required|numeric'
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json(["success" => false,
                                     "message" => $validation->errors()->first()], 406);
        }

        $counter_id = $request->counter_id;
        $counter = Counter::where("id",$counter_id)->first();
        if(is_null($counter))
            return response()->json(["success"=>false, "message"=>"Counter does not exist"]);


        $cashRegister = new CashRegister();
        $refunded_sales_amount = $cashRegister->getRefundedSalesAmountInCashRegister($cashRegisterId);

        $cashRegister = CashRegister::where("id",$cashRegisterId)->with('OpenedByUser','ClosedByUser','CashRegisterTransactions')->first();

        $openedBy = $cashRegister->OpenedByUser->name;
        $total_additions = PaymentLog::where('cash_register_id',$cashRegisterId)->where('payment_type',CashRegisterTransactionType::$ADD_BALANCE)->sum('paid_amount');
        $total_subtractions = PaymentLog::where('cash_register_id',$cashRegisterId)->where('payment_type',CashRegisterTransactionType::$SUBTRACT_BALANCE)->sum('paid_amount');

        //total sale
        $totalSale = PaymentLog::join('sales','sales.id','=','payment_logs.sale_id')
            ->where('payment_logs.cash_register_id','=',$cashRegisterId)
            ->whereIn('payment_logs.sale_status',[SaleStatus::$SUCCESS])
            ->where('sale_type',SaleTypes::$SALE)
            ->whereNull('refund_register_id')
            ->sum('paid_amount');


        $totalReturnSale = DB::table('sales')->where('cash_register_id', $cashRegisterId)
            ->where( 'sale_type', \App\Enumaration\SaleTypes::$RETURN )
            ->where( 'sale_status', \App\Enumaration\SaleStatus::$SUCCESS )
            ->sum('total_amount');
        if($totalReturnSale<0)
            $totalReturnSale = -$totalReturnSale;

        $paymentAmountTotalList = CashRegister::generatePaymentAmount($cashRegisterId,[SaleStatus::$SUCCESS]);
        $paymentAmountSuspendedList = CashRegister::generatePaymentAmount($cashRegisterId,[SaleStatus::$LAYAWAY,SaleStatus::$ESTIMATE]);
        $expectedClosingSales = $cashRegister->opening_balance + $paymentAmountTotalList["cashTotal"] +  ($total_additions - $total_subtractions) +
            $paymentAmountSuspendedList["cashTotal"];


        $totalCharAccountAmount = PaymentLog::join('sales','sales.id','=','payment_logs.sale_id')
            ->where('payment_logs.sale_status',\App\Enumaration\SaleStatus::$LAYAWAY )
            ->where('sale_type', \App\Enumaration\SaleTypes::$SALE)
            ->where('payment_logs.cash_register_id', $cashRegisterId)
            ->groupBy('sale_id')
            ->select('total_amount')->get();

        $totalCharAccountAmount = Sale::_eloquentToArray($totalCharAccountAmount,"total_amount");
        $totalCharAccountAmount = array_sum($totalCharAccountAmount);


        try {


            if($counter->printer_connection_type && $counter->printer_connection_type==\App\Enumaration\PrinterConnectionType::USB_CONNECTION) {
                $connector = new WindowsPrintConnector($counter->name);
            }
            else {
                $ip_address = $counter->printer_ip;
                $port = $counter->printer_port;

                $connector = new NetworkPrintConnector($ip_address, $port);
            }

            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Register Log Summary\n");
            $printer->selectPrintMode();
            $printer->text( date('Y-m-d'). "\n");


            $printer->text("-------------------------------------------\n");
            $printer->feed();


            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text( new FooterItem('Register Log Id:', $cashRegister->id));
            $printer->text( new FooterItem('Open Employee:', $openedBy));
            $printer->text( new FooterItem('Shift Start:', date('Y-m-d h:i:s a',strtotime($cashRegister->opening_time)) ));
            $printer->text( new FooterItem('Shift End:', date('Y-m-d h:i:s a',strtotime($cashRegister->closing_time)) ));
            $printer->text( new FooterItem('Opening Sales:', '$'.number_format( $cashRegister->opening_balance, 2) ));
            //$printer->text( new FooterItem('Closing Sales:', '$'.number_format( $cashRegister->closing_balance, 2) ));
            $printer->text( new FooterItem('Closing Sales:', '$'.number_format( $expectedClosingSales, 2) ));

            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Regular Sale Details\n");
            $printer->text("-------------------------------------------\n");
            $printer->feed();

            $printer->text( new FooterItem('Cash Sales:', '$'.number_format( $paymentAmountTotalList["cashTotal"], 2) ));
            //$printer->text( new FooterItem('Difference:', '$'.number_format( $difference, 2) ));
            $printer->text( new FooterItem('Credit Card Sales:', '$'.number_format( $paymentAmountTotalList["creditCardTotal"], 2) ));
            $printer->text( new FooterItem('Debit Card Sales:', '$'.number_format( $paymentAmountTotalList["debitCardTotal"], 2) ));
            $printer->text( new FooterItem('Check Sales:', '$'.number_format( $paymentAmountTotalList["checkTotal"], 2) ));
            $printer->text( new FooterItem('Gift Card Sales:', '$'.number_format( $paymentAmountTotalList["giftCardTotal"], 2) ));
            $printer->text( new FooterItem('Loyalty Card Sales:', '$'.number_format( $paymentAmountTotalList["loyalityTotal"], 2) ));

            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Suspended Sale Details\n");
            $printer->text("-------------------------------------------\n");
            $printer->feed();

            $printer->text( new FooterItem('Cash Sales:', '$'.number_format( $paymentAmountSuspendedList["cashTotal"], 2) ));
            //$printer->text( new FooterItem('Difference:', '$'.number_format( $difference, 2) ));
            $printer->text( new FooterItem('Credit Card Sales:', '$'.number_format( $paymentAmountSuspendedList["creditCardTotal"], 2) ));
            $printer->text( new FooterItem('Debit Card Sales:', '$'.number_format( $paymentAmountSuspendedList["debitCardTotal"], 2) ));
            $printer->text( new FooterItem('Check Sales:', '$'.number_format( $paymentAmountSuspendedList["checkTotal"], 2) ));
            $printer->text( new FooterItem('Gift Card Sales:', '$'.number_format( $paymentAmountSuspendedList["giftCardTotal"], 2) ));
            $printer->text( new FooterItem('Loyalty Card Sales:', '$'.number_format( $paymentAmountSuspendedList["loyalityTotal"], 2) ));

            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Other Details\n");
            $printer->text("-------------------------------------------\n");
            $printer->feed();

//            $printer->text( new FooterItem('Changed Amount:', '$'.number_format( $changedDue, 2) ));

            $printer->text( new FooterItem('Refunded Amount:  ', '$'.number_format( $refunded_sales_amount, 2) ));

            $printer->text( new FooterItem('Cash Additions:', '$'.number_format( $total_additions, 2) ));
            $printer->text( new FooterItem('Cash Subtractions:', '$'.number_format( $total_subtractions, 2) ));

            $printer->text( new FooterItem('Total Sale:', '$'.number_format( $totalSale, 2) ));
//            $printer->text( new FooterItem('Total Suspend Sale:', '$'.number_format( $totalSuspendedSale, 2) ));
            $printer->text( new FooterItem('Return Sales:', '$'.number_format( $totalReturnSale, 2) ));
            $printer->text( new FooterItem('Charge Account Sale:', '$'.number_format( $totalCharAccountAmount, 2) ));
//            $printer->text( new FooterItem('Charge Account Paid:', '$'.number_format( $totalCharAccountAmount - $totalCharAccountAmountDue, 2) ));
//            $printer->text( new FooterItem('Charge Account Due:', '$'.number_format( $totalCharAccountAmountDue, 2) ));
            return response()->json(["success" => true, "message"=> "Successfully printed"]);


        } Catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        } finally {
            if (isset($printer)) {
                $printer->cut();
                $printer->pulse();
                $printer->close();
                return response()->json(["success" => true, "message"=> "Successfully printed"]);
            }
        }
    }

}
