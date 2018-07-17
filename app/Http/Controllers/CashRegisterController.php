<?php

namespace App\Http\Controllers;

use App\Enumaration\PaymentTypes;
use App\Library\SettingsSingleton;
use App\Model\CashRegisterTransaction;
use App\Enumaration\CashRegisterTransactionType;
use App\Model\Counter;
use App\Model\CurrencyDenomination;
use App\Model\CashRegister;
use App\Model\PaymentLog;
use App\Model\Sale;
use App\Model\Printer\FooterItem;
use App\Model\Printer\Item;
use App\Model\Printer\RegisterDetails;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

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
        //date_default_timezone_set(date_default_timezone_get());
        $opening_time =   date('Y-m-d h:i:s', time());
        $opened_by = Auth::user()->id;
        $cashRegisterOpenInfo = array(
            "opening_balance" => $opening_balance,
            "counter_id" => $counter_id,
			"user_id" => Auth::id(),
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

        $openingBalance = $cashRegister->getActiveRegisterOpeningBalance();
        $total_additions = $cashRegister->getTotalAddedAmountInActiveRegister();
        $total_subtractions = $cashRegister->getTotalSubtractedAmountInActiveRegister();
        $cash_sales = $cashRegister->getTotalSaleInCurrentRegister(CashRegisterTransactionType::$CASH_SALES);
        $check_sales = $cashRegister->getTotalSaleInCurrentRegister(CashRegisterTransactionType::$CHECK_SALES);
        $credit_card_sales = $cashRegister->getTotalSaleInCurrentRegister(CashRegisterTransactionType::$CREDIT_CARD_SALES);
        $debit_card_sales = $cashRegister->getTotalSaleInCurrentRegister(CashRegisterTransactionType::$DEBIT_CARD_SALES);
        $gift_card_sales = $cashRegister->getTotalSaleInCurrentRegister(CashRegisterTransactionType::$GIFT_CARD_SALES);
        $loyalty_card_sales = $cashRegister->getTotalSaleInCurrentRegister(CashRegisterTransactionType::$LOYALTY_CARD_SALES);
        $changedDue = DB::table('sales')->where('cash_register_id', $cashRegister->getCurrentActiveRegister()->id)
            ->where( 'due', '<', 0 )
            ->sum('due');
        $changedDue = -$changedDue;
        $cash_sales = $cash_sales - $changedDue;
        $refunded_sales_amount = $cashRegister->getRefundedSalesAmountInCashRegister($cashRegister->getCurrentActiveRegister()->id  );
        $denominations = CurrencyDenomination::all();
        $closing_balance = $openingBalance + $cash_sales  + $total_additions + $total_subtractions - $refunded_sales_amount;

        return view('cash_registers.close_cash_register',["denominations"=>$denominations,"openingBalance"=>$openingBalance,
            "additions"=>$total_additions,"subtractions"=>$total_subtractions,"sales"=>$cash_sales,"change_due"=>$changedDue,
            "refunded_amount"=>$refunded_sales_amount],compact('check_sales','credit_card_sales','debit_card_sales',
            'gift_card_sales','loyalty_card_sales','closing_balance'));

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
            return redirect()->route('cash_register_log_details',["register_id"=>$activeRegister->id]);
    }

    public function cashRegisterLogDetails($cashRegisterId) {

        $cashRegister = new CashRegister();
        $refunded_sales_amount = $cashRegister->getRefundedSalesAmountInCashRegister($cashRegisterId);

        $cashRegister = CashRegister::where("id",$cashRegisterId)->with('OpenedByUser','ClosedByUser','CashRegisterTransactions')->first();

        $saleList = Sale::where('cash_register_id', $cashRegisterId)->with('PaymentLogs')->get();
        $allTransactionArr = [];
		
        foreach( $saleList as $aSale )
        {
            $cashAmount = 0;
            $chequeAmount = 0;
            $creditCardAmount = 0;
            $debitCardAmount = 0;
            $giftCardAmount = 0;
            $loyalityAmount = 0;


            foreach( $aSale->PaymentLogs as $aPaymentLog )
            {
                if( $aPaymentLog->payment_type==PaymentTypes::$TypeList["Cash"] )
                    $cashAmount += floatval($aPaymentLog->paid_amount);
                else if($aPaymentLog->payment_type==PaymentTypes::$TypeList["Check"])
                    $chequeAmount += floatval($aPaymentLog->paid_amount);
                else if($aPaymentLog->payment_type==PaymentTypes::$TypeList["Credit Card"])
                    $creditCardAmount += floatval($aPaymentLog->paid_amount);
                else if($aPaymentLog->payment_type==PaymentTypes::$TypeList["Debit Card"])
                    $debitCardAmount += floatval($aPaymentLog->paid_amount);
                else if($aPaymentLog->payment_type==PaymentTypes::$TypeList["Gift Card"])
                    $giftCardAmount += floatval($aPaymentLog->paid_amount);
                else if($aPaymentLog->payment_type==PaymentTypes::$TypeList["Loyalty Card"])
                    $loyalityAmount += floatval($aPaymentLog->paid_amount);

            }

            if( $cashAmount > 0 )
            {
                $cashAmount  += $aSale->due ;
                $allTransactionArr[] = [
                                'sale_id' => $aSale->id,
                                'created_at' => $aSale->created_at,
                                'payment_type' => \App\Enumaration\CashRegisterTransactionType::$CASH_SALES,
                                'amount' => $cashAmount  
                            ];
            }
            if( $chequeAmount > 0 ) 
            {
                $allTransactionArr[] = [
                                'sale_id' => $aSale->id,
                                'created_at' => $aSale->created_at,
                                'payment_type' => \App\Enumaration\CashRegisterTransactionType::$CHECK_SALES,
                                'amount' => $chequeAmount  
                            ];
            }
            if( $creditCardAmount > 0 ) 
            {
                $allTransactionArr[] = [
                                'sale_id' => $aSale->id,
                                'created_at' => $aSale->created_at,
                                'payment_type' => \App\Enumaration\CashRegisterTransactionType::$CREDIT_CARD_SALES,
                                'amount' => $creditCardAmount  
                            ];
            }
            if( $debitCardAmount > 0 ) 
            {
                $allTransactionArr[] = [
                                'sale_id' => $aSale->id,
                                'created_at' => $aSale->created_at,
                                'payment_type' => \App\Enumaration\CashRegisterTransactionType::$DEBIT_CARD_SALES,
                                'amount' => $debitCardAmount  
                            ];
            }
            if( $giftCardAmount > 0 ) 
            {
                $allTransactionArr[] = [
                                'sale_id' => $aSale->id,
                                'created_at' => $aSale->created_at,
                                'payment_type' => \App\Enumaration\CashRegisterTransactionType::$GIFT_CARD_SALES,
                                'amount' => $giftCardAmount  
                            ];
            }
            if( $loyalityAmount > 0 ) 
            {
                $allTransactionArr[] = [
                                'sale_id' => $aSale->id,
                                'created_at' => $aSale->created_at,
                                'payment_type' => \App\Enumaration\CashRegisterTransactionType::$LOYALTY_CARD_SALES,
                                'amount' => $loyalityAmount  
                            ];
            }

        }



        foreach( $cashRegister->PaymentLogs as $aCashRegisterTransaction )
        {
            $addedAmount = 0;
            $subtractedAmount = 0;

            if($aCashRegisterTransaction->payment_type ==\App\Enumaration\CashRegisterTransactionType::$ADD_BALANCE)
            {
                $addedAmount += floatval(($aCashRegisterTransaction->paid_amount));
                $allTransactionArr[] = [
                                'created_at' => $aCashRegisterTransaction->created_at,
                                'payment_type' => \App\Enumaration\CashRegisterTransactionType::$ADD_BALANCE,
                                'amount' => $addedAmount
                            ];
            }
            if($aCashRegisterTransaction->payment_type ==\App\Enumaration\CashRegisterTransactionType::$SUBTRACT_BALANCE)
            {
                $subtractedAmount += floatval(($aCashRegisterTransaction->paid_amount));
                $allTransactionArr[] = [
                                'created_at' => $aCashRegisterTransaction->created_at,
                                'payment_type' => \App\Enumaration\CashRegisterTransactionType::$SUBTRACT_BALANCE,
                                'amount' => $subtractedAmount
                            ];
            }
        }

        $openedBy = $cashRegister->OpenedByUser->name;
        $closedBy = $cashRegister->closedByUser->name;
        $total_additions = PaymentLog::where('cash_register_id',$cashRegisterId)->where('payment_type',CashRegisterTransactionType::$ADD_BALANCE)->sum('paid_amount');
        $total_subtractions = PaymentLog::where('cash_register_id',$cashRegisterId)->where('payment_type',CashRegisterTransactionType::$SUBTRACT_BALANCE)->sum('paid_amount');
        $cash_sales = PaymentLog::where("cash_register_id",$cashRegister->id)->where('payment_type',CashRegisterTransactionType::$CASH_SALES)->sum('paid_amount');
        $cashRegisterTransactions = $cashRegister->CashRegisterTransactions;

        $changedDue = DB::table('sales')->where('cash_register_id', $cashRegisterId)
            ->where( 'due', '<', 0 )
            ->sum('due');
        $changedDue = -$changedDue;
        $cash_sales = $cash_sales - $changedDue;
        $expectedClosingSales = $cashRegister->opening_balance + ($cash_sales - $changedDue) +  ($total_additions - $total_subtractions);

        $paymentAmountSql = "select payment_type, sum(paid_amount) as total_paid_amount from payment_logs where id in ( select payment_log_id from payment_log_sale where sale_id in ( select id from sales where cash_register_id=? ) ) group by payment_type";
        $paymentAmountTotalList = DB::select( $paymentAmountSql, [$cashRegisterId] );

        $checkTotal = 0;
        $creditCardAmountTotal = 0;
        $debitCardAmountTotal = 0;
        $giftCardAmountTotal = 0;
        $loyalityAmountTotal = 0;
        foreach( $paymentAmountTotalList as $aPaymentTotal )
        {
            if($aPaymentTotal->payment_type=='Check')
                $checkTotal = $aPaymentTotal->total_paid_amount;
            else if($aPaymentTotal->payment_type=='Credit Card')
                $creditCardAmountTotal = $aPaymentTotal->total_paid_amount;
            else if($aPaymentTotal->payment_type=='Debit Card')
                $debitCardAmountTotal = $aPaymentTotal->total_paid_amount;
            else if($aPaymentTotal->payment_type=='Gift Card')
                $giftCardAmountTotal = $aPaymentTotal->total_paid_amount;
            else if($aPaymentTotal->payment_type=='Loyalty Card')
                $loyalityAmountTotal = $aPaymentTotal->total_paid_amount;
        }

        $paymentInfo = array(
            "checkTotal" => $checkTotal,
            "creditCardTotal" => $creditCardAmountTotal,
            "debitCardTotal" => $debitCardAmountTotal,
            "giftCardTotal" => $giftCardAmountTotal,
            "loyalityTotal" => $loyalityAmountTotal
        );

        return view('cash_registers.cash_register_log_details',["register"=>$cashRegister,
                "transactions"=>$allTransactionArr,"opened_by"=>$openedBy,"closed_by"=>$closedBy,
            "additions"=>$total_additions,"subtractions"=>$total_subtractions,"sales"=>$cash_sales,
            "paymentInfo"=>$paymentInfo, "changedDue"=>$changedDue,"refundedAmount"=>$refunded_sales_amount]);
        //dd($cashRegisterTotal);
    }


    public function printRegisterLogDetails($cashRegisterId){


        $cashRegister = CashRegister::where("id",$cashRegisterId)->with('OpenedByUser','ClosedByUser','CashRegisterTransactions')->first();
        $transactions = CashRegister::where("id",$cashRegisterId)->with('CashRegisterTransactions')->first()->CashRegisterTransactions;
        $closedBy = $cashRegister->closedByUser->name;

		$paymentAmountSql = "select sales.id,  payment_logs.payment_type , sum(payment_logs.paid_amount) as total_sale from payment_logs 
								join payment_log_sale 
									on payment_logs.id=payment_log_sale.payment_log_id
								join sales 
									on payment_log_sale.sale_id = sales.id
								where sales.cash_register_id=? 
									and sales.deleted_at is null
									and sales.sale_type = ?
									and sales.sale_status = ? group by sales.id, payment_logs.payment_type";
		$allTransactionArr = DB::select( $paymentAmountSql, [$cashRegisterId, \App\Enumaration\SaleTypes::$SALE, \App\Enumaration\SaleStatus::$SUCCESS] );
		
		$salesChargeAccountTransactionList = Sale::where('due', '>', '0')
					->where('sale_status',\App\Enumaration\SaleStatus::$LAYAWAY )
					->where('sale_type', \App\Enumaration\SaleTypes::$SALE)
					->where('cash_register_id', $cashRegisterId)
					->with('Customer')
					->get();
		
		//dd($cashSaleList);
        if( count($allTransactionArr)>0 )
        {
            $saleIdArr = [];
            foreach($allTransactionArr as $aSale)
                $saleIdArr[] = $aSale->id;

            $saleDetailsArr = DB::table('sales')->whereIn('id', $saleIdArr)->get();
            //foreach( $cashSaleList as &$aCashSale )
			foreach($allTransactionArr as &$aSale)
            {
                //$aCashSale->sale_added_on = ;
                $aSale->created_at = date('Y-m-d');
                $aSale->amount = 0;
                foreach( $saleDetailsArr as $salesDetails )
                {
                    if( $aSale->id==$salesDetails->id )
                    {
                        $aSale->created_at = $salesDetails->created_at;
                        $aSale->total_sale = floatval($aSale->total_sale) + floatval($salesDetails->due) ;
                        break;
                    }
                }
            }
			unset($aCashSale);
        }

        try {

            $counter_id = Cookie::get('counter_id',null);
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

            $header = new \App\Model\Printer\RegisterDetails("Date", "Employee", "Amount");
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
                        $closedBy,
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

            $header = new \App\Model\Printer\RegisterDetails("Date", "Employee", "Amount");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text($header);
            $printer->setEmphasis(false);
            $printer->feed();
            foreach($transactions as $aTransaction ) {

                if($aTransaction->payment_type==\App\Enumaration\CashRegisterTransactionType::$SUBTRACT_BALANCE)
                {
                    $printer->text(new RegisterDetails(
                        date_format($aTransaction->created_at,"Y-m-d"),
                        $closedBy,
                        number_format($aTransaction->amount,2),
                        date_format($aTransaction->created_at,"h:i:s")
                    ));
                    $printer->feed();
                }
            }
            $printer->feed();
			
			
			$paymentTypeMapping = array(
										'Cash' => "Cash Sales",
										'Check' => "Cheque Sales",
										"Debit Card" => "Debit Card Sales",
										"Credit Card" => "Credit Card Sales",
										"Gift Card" => "Gift Card Sales",
										"Loyalty Card" => "Loyalty Card Sales",
									);
			foreach( $paymentTypeMapping as $paymentTypeDB=>$paymentTypeShow  )
			{
				$printer->setJustification(Printer::JUSTIFY_CENTER);
				$printer->text("{$paymentTypeShow}\n");
				$printer->text("------------------------\n");
				$printer->feed();

				$header = new \App\Model\Printer\RegisterDetails("Date", "Employee", "Amount");
				$printer->setJustification(Printer::JUSTIFY_LEFT);
				$printer->setEmphasis(true);
				$printer->text($header);
				$printer->setEmphasis(false);
				$printer->feed();
				foreach($allTransactionArr as $aTransaction ) {

					if($aTransaction->payment_type==$paymentTypeDB)
					{
						$printer->text(new RegisterDetails(
							date("Y-m-d" , strtotime($aTransaction->created_at)),
							$closedBy,
							number_format($aTransaction->total_sale,2),
							date("h:i:s", strtotime($aTransaction->created_at))
						));
						$printer->feed();
					}
				}
				$printer->feed();
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
					$due = $asalesChargeAccountTransaction->due;
					$customerName = $asalesChargeAccountTransaction->Customer->first_name . ' ' . $asalesChargeAccountTransaction->Customer->last_name;
					$printer->text(new RegisterDetails(
						date( "Y-m-d", strtotime($asalesChargeAccountTransaction->created_at) ),
						$customerName,
						number_format($due,2),
						date( "h:i:s" ,strtotime($asalesChargeAccountTransaction->created_at) )
					));
					$printer->feed();
			}
			$printer->feed();
			
        } Catch (\Exception $e) {
			//dd($e);
			//dd($e);
            return redirect()->back()->with(["error" => $e->getMessage()]);
        } finally {
            if (isset($printer)) {
                $printer->cut();
                $printer->pulse();
                $printer->close();
                return redirect()->back();
            }
        }

    }

    public function printRegisterLogSummary($cashRegisterId)
    {
        $cashRegister = new CashRegister();
        $refunded_sales_amount = $cashRegister->getRefundedSalesAmountInCashRegister($cashRegisterId);

        $cashRegister = CashRegister::where("id",$cashRegisterId)->with('OpenedByUser','ClosedByUser','CashRegisterTransactions')->first();

        $openedBy = $cashRegister->OpenedByUser->name;
        $closedBy = $cashRegister->closedByUser->name;
        $total_additions = PaymentLog::where('cash_register_id',$cashRegisterId)->where('payment_type',CashRegisterTransactionType::$ADD_BALANCE)->sum('paid_amount');
        $total_subtractions = PaymentLog::where('cash_register_id',$cashRegisterId)->where('payment_type',CashRegisterTransactionType::$SUBTRACT_BALANCE)->sum('paid_amount');
        $cash_sales = PaymentLog::where("cash_register_id",$cashRegister->id)->where('payment_type',CashRegisterTransactionType::$CASH_SALES)->sum('paid_amount');
		
		
        $difference =  ($cashRegister->closing_balance - $cashRegister->opening_balance) - ( $cash_sales + $total_additions - $total_subtractions);

		//total sale
		$totalSale = DB::table('sales')->where('cash_register_id', $cashRegisterId)	
									->where( 'sale_type', \App\Enumaration\SaleTypes::$SALE )
									->where( 'sale_status', \App\Enumaration\SaleStatus::$SUCCESS )
									->sum('total_amount');
									
		$totalChargeCustomerSale = DB::table('sales')->where('cash_register_id', $cashRegisterId)	
									->where( 'sale_type', \App\Enumaration\SaleTypes::$SALE )
									->where( 'sale_status', \App\Enumaration\SaleStatus::$SUCCESS )
									->sum('total_amount');
		$totalReturnSale = DB::table('sales')->where('cash_register_id', $cashRegisterId)	
									->where( 'sale_type', \App\Enumaration\SaleTypes::$RETURN )
									->where( 'sale_status', \App\Enumaration\SaleStatus::$SUCCESS )
									->sum('total_amount');
		if($totalReturnSale<0)
			$totalReturnSale = -$totalReturnSale;
		
		$changedDue = DB::table('sales')->where('cash_register_id', $cashRegisterId)
									->where( 'sale_type', \App\Enumaration\SaleTypes::$SALE )
									->where( 'sale_status', \App\Enumaration\SaleStatus::$SUCCESS )
									->where( 'due', '<', 0 )
									->sum('due');
		$changedDue = -$changedDue;
		$expectedClosingSales = $cashRegister->opening_balance + ($cash_sales - $changedDue) +  ($total_additions - $total_subtractions);
        $cash_sales = $cash_sales - $changedDue;

        $paymentAmountSql = "select payment_type, sum(paid_amount) as total_paid_amount from payment_logs where id in ( select payment_log_id from payment_log_sale where sale_id in ( select id from sales where cash_register_id=? and deleted_at is null ) )  group by payment_type";
        $paymentAmountTotalList = DB::select( $paymentAmountSql, [$cashRegisterId] );
		
		$totalCharAccountAmount = DB::table('sales')
					->where('due', '>', '0')
					->where('sale_status',\App\Enumaration\SaleStatus::$LAYAWAY )
					->where('sale_type', \App\Enumaration\SaleTypes::$SALE)
					->where('cash_register_id', $cashRegisterId)
					->sum('due');
        
        $checkTotal = 0;
        $creditCardAmountTotal = 0;
        $debitCardAmountTotal = 0;
        $giftCardAmountTotal = 0;
        $loyalityAmountTotal = 0;
        foreach( $paymentAmountTotalList as $aPaymentTotal )
        {
            if($aPaymentTotal->payment_type=='Check')
                $checkTotal = $aPaymentTotal->total_paid_amount;
            else if($aPaymentTotal->payment_type=='Credit Card')
                $creditCardAmountTotal = $aPaymentTotal->total_paid_amount;
            else if($aPaymentTotal->payment_type=='Debit Card')
                $debitCardAmountTotal = $aPaymentTotal->total_paid_amount;
            else if($aPaymentTotal->payment_type=='Gift Card')
                $giftCardAmountTotal = $aPaymentTotal->total_paid_amount;
            else if($aPaymentTotal->payment_type=='Loyalty Card')
                $loyalityAmountTotal = $aPaymentTotal->total_paid_amount;
        }

        try {
            $counter_id = Cookie::get('counter_id',null);
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
            $printer->text("Register Log Summary\n");
            $printer->selectPrintMode();
            $printer->text( date('Y-m-d'). "\n");


            $printer->text("-------------------------------------------\n");
            $printer->feed();


            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text( new FooterItem('Register Log Id:', $cashRegister->id));
            $printer->text( new FooterItem('Open Employee:', $openedBy));
            $printer->text( new FooterItem('Close Employee:', $closedBy));
            $printer->text( new FooterItem('Shift Start:', date('Y-m-d h:i:s a',strtotime($cashRegister->opening_time)) ));
            $printer->text( new FooterItem('Shift End:', date('Y-m-d h:i:s a',strtotime($cashRegister->closing_time)) ));
            $printer->text( new FooterItem('Opening Sales:', '$'.number_format( $cashRegister->opening_balance, 2) ));
            //$printer->text( new FooterItem('Closing Sales:', '$'.number_format( $cashRegister->closing_balance, 2) ));
            $printer->text( new FooterItem('Closing Sales:', '$'.number_format( $expectedClosingSales, 2) ));
			
            $printer->text( new FooterItem('Cash Sales:', '$'.number_format( $cash_sales, 2) ));
            //$printer->text( new FooterItem('Difference:', '$'.number_format( $difference, 2) ));
            $printer->text( new FooterItem('Credit Card Sales:', '$'.number_format( $creditCardAmountTotal, 2) ));
            $printer->text( new FooterItem('Debit Card Sales:', '$'.number_format( $debitCardAmountTotal, 2) ));
            $printer->text( new FooterItem('Check Sales:', '$'.number_format( $checkTotal, 2) ));
            $printer->text( new FooterItem('Gift Card Sales:', '$'.number_format( $giftCardAmountTotal, 2) ));
            $printer->text( new FooterItem('Loyalty Card Sales:', '$'.number_format( $loyalityAmountTotal, 2) ));
//            $printer->text( new FooterItem('Changed Amount:', '$'.number_format( $changedDue, 2) ));
            $printer->text( new FooterItem('Refunded Amount:  ', '$'.number_format( $refunded_sales_amount, 2) ));

            $printer->text( new FooterItem('Cash Additions:', '$'.number_format( $total_additions, 2) ));
            $printer->text( new FooterItem('Cash Subtractions:', '$'.number_format( $total_subtractions, 2) ));

            $printer->text( new FooterItem('Total Sales:', '$'.number_format( $totalSale, 2) ));
			$printer->text( new FooterItem('Return Sales:', '$'.number_format( $totalReturnSale, 2) ));
			$printer->text( new FooterItem('Charge Account Sales:', '$'.number_format( $totalCharAccountAmount, 2) ));
            return redirect()->route('cash_register_log_details',["register_id"=>$cashRegister->id]);

        } Catch (\Exception $e) {
            return redirect()->back()->with(["error" => $e->getMessage()]);
        } finally {
            if (isset($printer)) {
                $printer->cut();
                $printer->pulse();
                $printer->close();
            }
        }
    }

}
