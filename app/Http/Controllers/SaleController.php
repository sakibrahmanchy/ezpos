<?php

namespace App\Http\Controllers;

use App\Enumaration\CashRegisterTransactionType;
use App\Enumaration\DateTypes;
use App\Enumaration\SaleStatus;
use App\Enumaration\UserTypes;
use app\Http\Controllers\Reports\ReportTotal;
use App\Library\SettingsSingleton;
use App\Model\CashRegister;
use App\Model\CashRegisterTransaction;
use App\Model\Category;
use App\Model\Counter;
use App\Model\Customer;
use App\Model\CustomerTransaction;
use App\Model\Employee;
use App\Model\Invoice;
use App\Model\Item;
use App\Model\ItemKit;
use App\Model\LoyaltyTransaction;
use App\Model\Manufacturer;
use App\Model\PaymentLog;
use App\Model\Printer\FooterItem;
use App\Model\Sale;
use App\Model\SaleStatusLog;
use App\Model\Supplier;
use App\Model\User;
use Faker\Provider\Barcode;
use Faker\Provider\tr_TR\DateTime;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use PhpParser\Node\Scalar\String_;

class SaleController extends Controller
{

    public function GetSaleForm()
    {
        $cashRegister = new CashRegister();
        $activeCashRegister = $cashRegister->getCurrentActiveRegister();
        if(!is_null($activeCashRegister)){
            // Use active cash register
            $customerList = Customer::all();
            if(Auth::user()->user_type!=UserTypes::$SUPER_ADMIN) {
                if(\Illuminate\Support\Facades\Cookie::get('counter_id')!=null){
                    $counter_id = \Illuminate\Support\Facades\Cookie::get('counter_id');
                    $employee = Employee::where("user_id", "=", \Illuminate\Support\Facades\Auth::user()->id)->with('counters')->first();
                    $employeeCounterList = array();
                    foreach($employee->counters as $aCounter) {
                        array_push($employeeCounterList,$aCounter->id);
                    }
                    if(!in_array($counter_id,$employeeCounterList)) {
                        return redirect()->route('error-401');
                    }
                }
            }

				return view('sales.new_design.new_sale_vue', ['customerList' => $customerList]);
    //			else
    //				return view('sales.new_sale', ['customerList' => $customerList]);
        }else{
            // A new cash register should be opened
            return redirect()->route('open_cash_register');
        }
    }

    public function AddSale(Request $request)
    {
        $saleInfo = $request->sale_info;
        $productInfos = $request->product_infos;
        $paymentInfos = $request->payment_infos;

        $sale = new Sale();
        $sale_id = $sale->InsertSale($saleInfo, $productInfos, $paymentInfos, $saleInfo['status']);
        echo $sale_id;
    }

    public function SuspendSale(Request $request)
    {

        $saleInfo = $request->sale_info;
        $productInfos = $request->product_infos;
        $paymentInfos = $request->payment_infos;

        $sale = new Sale();
        $sale->InsertSale($saleInfo, $productInfos, $paymentInfos, $saleInfo['status']);

    }

    public function GetSaleReceipt($sale_id)
    {

        $sale = Sale::withTrashed()->where("id", $sale_id)->with('items', 'paymentlogs', 'customer', 'counter')->first();
        $counterList = Counter::get();

        /*return response()->json(['sale'=>$sale], 200);*/
        if ($sale == null)
            return redirect()->back()->with(["error" => "Sale id not found!"]);
        else
            return view('sales.sale_receipt', ["sale" => $sale, 'counter_list'=>$counterList]);

    }

    public function GetSuspendedSales()
    {

        $suspendedSales = Sale::where('sale_status', SaleStatus::$ESTIMATE)->orWhere('sale_status', SaleStatus::$LAYAWAY)->with('items', 'paymentlogs','customer')->get();

        foreach ($suspendedSales as $aSale) {
            $aSale->item_count = count($aSale->items);
            $item_names = [];
            foreach ($aSale->items as $anItem) {
                array_push($item_names, $anItem->item_name);
            }

            $aSale->item_names = $item_names;
        }


        return view('sales.suspended_sale_list', ["suspended_sales" => $suspendedSales]);

    }

    public function showLastSaleReceipt()
    {

        $sale = Sale::withTrashed()->orderBy('id', 'desc')->first();
        if (!is_null($sale))
            $sale_id = $sale->id;
        else
            return redirect()->back()->with(["error" => "No sale found!"]);

        return redirect()->route('sale_receipt', ['sale_id' => $sale_id]);
    }

    public function DownloadSaleReceipt(Request $request, $sale_id)
    {


        $sale = Sale::withTrashed()->where("id", $sale_id)->with(['items' => function ($query) {
            $query->where('items.product_type', '<>', 2);
        }, 'paymentlogs', 'customer'])->first();
        if(isset($request->receipt_print)) {

            return view('sales.web_print',["sale" => $sale]);
        }else{
            $pdf = PDF::loadView('sales.sale_receipt_pdf', ["sale" => $sale]);
            return $pdf->download('ezpos-sale-receipt.pdf');
        }

    }

    public function MailSaleReceipt($sale_id)
    {

        $sale = Sale::withTrashed()->where("id", $sale_id)->with(['items' => function ($query) {
            $query->where('items.product_type', '<>', 2);
        }, 'paymentlogs', 'customer'])->first();



        if (isset($sale->customer->id)) {
            $customer = Customer::where('id',$sale->customer_id)->first();
        }

        if (isset($customer->email) && !is_null($customer->email)) {

            Mail::send('sales.emails_sales_receipt', ["sale" => $sale, "customer" => $customer], function ($m) use ($sale, $customer) {
                $m->from('sales@mg.grimspos.com', 'EZPOS');

                $pdf = PDF::loadView('sales.sale_receipt_pdf', ["sale" => $sale, "customer" => $customer]);
                $m->to($customer->email, $customer->name)->subject('Sale receipt for purchase!');
                $m->attachData($pdf->output(), 'invoice.pdf', ['mime' => 'application/pdf']);
            });

            // check for failures
            if (Mail::failures()) {
                return redirect()->route('sale_receipt', $sale_id)->with('error', 'Error sending email');
            }

            // otherwise everything is okay ...
            return redirect()->route('sale_receipt', $sale_id)->with('success', 'Email successfully sent');
        }

        return redirect()->route('sale_receipt', $sale_id)->with('error', 'Sorry. This customer has no email id.');

    }

    public function SearchSaleGet(Request $request)
    {
        $dateTypes = new DateTypes();
        $dateTypes = $dateTypes->getDates();

        if (isset($_GET['act'])) {
            $field_name = $_GET['w'];
            $search_param = $_GET['term'];

            $search_param = (string)'%' . $search_param . '%';


            if ($field_name == "customers") {
                $customers = Customer::where("first_name", "like", $search_param)->orWhere("last_name", "like", $search_param)->get();
                echo json_encode($customers);
            } else if ($field_name == "employees") {
                $employees = User::where("name", "like", $search_param)->get();
                echo json_encode($employees);
            } else if ($field_name == "itemsCategory") {
                $categories = Category::where("category_name", "like", $search_param)->get();
                echo json_encode($categories);
            } else if ($field_name == "suppliers") {
                $suppliers = Supplier::where("first_name", "like", $search_param)->orWhere("last_name", "like", $search_param)->orWhere("company_name", "like", $search_param)->get();
                echo json_encode($suppliers);
            } else if ($field_name == "itemsKitName") {
                $itemKits = ItemKit::where("item_kit_name", "like", $search_param)->get();
                echo json_encode($itemKits);
            } else if ($field_name == "itemsName") {
                $items = Item::where("item_name", "like", $search_param)->get();
                echo json_encode($items);
            } else if ($field_name == "salesPerson") {
                $salesPersons = Employee::where("first_name", "like", $search_param)->orWhere("last_name", "like", $search_param)->get();
                echo json_encode($salesPersons);
            } else if ($field_name == "manufacturer") {
                $manufacturers = Manufacturer::where("manufacturer_name", "like", $search_param)->get();
                echo json_encode($manufacturers);
            }

        } else {
            if (isset($_GET['isPosted'])) {
                if (isset($_GET['report_type'])) {

                    $reportType = $_GET['report_type'];

                    if ($reportType == "simple") {

                        $dateRange = explode("/", $_GET['report_date_range_simple']);
                        $startDate = $dateRange[0];
                        $endDate = $dateRange[1];
                    } else {

                        $startDate = $_GET['start_date_formatted'];
                        $endDate = $_GET['end_date_formatted'];
                    }
                }


                $fieldPresets = array(
                    "1" => "sales.customer_id",
                    "2" => "itemsSN",
                    "3" => "sales.employee_id",
                    "4" => "items.category_id",
                    "5" => "items.supplier_id",
                    "6" => "saleType",
                    "7" => "sales.total_amount",
                    "8" => "items.id",
                    "9" => "items.id",
                    "10" => "sales.id",
                    "11" => "paymentType",
                    "12" => "saleItemDescription",
                    "13" => "sales.employee_id",
                    "15" => "items.manufacturer_id"
                );

                $relationalPresets = array(
                    "1" => "in",
                    "2" => "not in",
                    "7" => ">",
                    "8" => "<",
                    "9" => "="
                );

                $fields = $request->field;
                $conditions = $request->condition;
                $values = $request->value;
                $matchType = $request->matchType;

                $magicArray = [];
                $loadData = [];
                $index = 0;

                if ($fields && !is_null($fields))
                    foreach ($fields as $aField) {
                        if ($aField) {
                            $magicArray[$index] = array();
                            array_push($magicArray[$index], $fieldPresets[$aField]);
                            $index++;
                        }

                    }
                $index = 0;
                if ($conditions && !is_null($conditions))
                    foreach ($conditions as $aCondition) {
                        array_push($magicArray[$index], $relationalPresets[$aCondition]);
                        $index++;
                    }
                $index = 0;

                if ($values && !is_null($values))
                    foreach ($values as $aValue) {
                        array_push($magicArray[$index], $aValue);
                        $index++;
                    }

                $sales = DB::table("sales")
                    ->join("item_sale", "item_sale.sale_id", '=', 'sales.id')
                    ->join('items', 'items.id', '=', 'item_sale.item_id')
                    ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
                    ->leftJoin('users', 'sales.employee_id', '=', 'users.id');


                $in_operation_fields = [];
                $in_operations_values = [];
                $notInOperations_fields = [];
                $notInOperations_values = [];

                $inOperationLength = 0;
                foreach ($magicArray as $anArray) {
                    if (in_array("in", $anArray)) {
                        $key = array_search($anArray, $magicArray);
                        array_push($in_operation_fields, $anArray[0]);
                        array_push($in_operations_values, explode(",", $anArray[2]));
                        unset($magicArray[$key]);
                        $inOperationLength++;
                    }

                }


                for ($index = 0; $index < $inOperationLength; $index++) {
                    if ($matchType == 'matchType_All')
                        $sales = $sales->whereIn($in_operation_fields[$index], $in_operations_values[$index]);
                    else
                        $sales = $sales->orWhereIn($in_operation_fields[$index], $in_operations_values[$index]);
                }


                $notInOperationLength = 0;
                foreach ($magicArray as $anArray) {
                    if (in_array("not in", $anArray)) {
                        $key = array_search($anArray, $magicArray);
                        array_push($notInOperations_fields, $anArray[0]);
                        array_push($notInOperations_values, explode(",", $anArray[2]));
                        unset($magicArray[$key]);
                        $notInOperationLength++;
                    }
                }

                for ($index = 0; $index < $notInOperationLength; $index++) {
                    if ($matchType == 'matchType_All')
                        $sales = $sales->whereNotIn($notInOperations_fields[$index], $notInOperations_values[$index]);
                    else
                        $sales = $sales->orWhereNotIn($notInOperations_fields[$index], $notInOperations_values[$index]);
                }

                if ($matchType == 'matchType_All') {
                    $results = $sales->where($magicArray);
                } else if ($matchType == 'matchType_Or') {
                    $results = $sales->orWhere($magicArray);
                }

                $results = $results->whereDate("sales.created_at", ">=", $startDate)->whereDate("sales.created_at", "<=", $endDate);

                $results = $results->where("sales.deleted_at", null)
                    ->where("items.deleted_at", null)
                    ->select(DB::raw(('*, COUNT(*) as item_count,sales.created_at as sale_create_date')))
                    ->groupBy('sales.id');


                $items = $results->get();

                return view('sales.search_sale', ["dateTypes" => $dateTypes, "items" => $items]);


            } else
                return view('sales.search_sale', ["dateTypes" => $dateTypes]);
        }

    }

    public function printSaleReciept($sale_id, Request $request)
    {
        $print_type = $request->print_type;
        $sale = Sale::withTrashed()->where("id", $sale_id)->with('items', 'paymentlogs', 'customer')->first();
        if ($sale == null)
            return redirect()->route('new_sale')->with(["error" => 'Sale id not found']);

            $created_at = date("d/m/Y H:i:s a",strtotime($sale->created_at));
        try {
            $settings = SettingsSingleton::get();

            $counter_id = 0;
            if($request->has('counter_id'))
                $counter_id = intval($request->counter_id);
            if($counter_id == 0)
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
            $printer->text("Receipt\n");
            $printer->selectPrintMode();
            $printer->text( $created_at. "\n");
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $printer->text($settings['company_name']. "\n");
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
			$printer->text("Order No." . $sale->id . "\n");

			$printer->selectPrintMode();
            if($settings['address_line_1']!=""||$settings['address_line_1']!=null)
				$printer->text(wordwrap($settings['address_line_1'] . "\n",43,"\n",false));
			if($settings['address_line_2']!=""||$settings['address_line_2']!=null)
				$printer->text(wordwrap($settings['address_line_2'] . "\n",43,"\n",false));

			if($settings['email_address']!=""||$settings['email_address']!=null)
				$printer->text(wordwrap($settings['email_address'] . "\n",43,"\n",false));

            if($settings['phone']!=""||$settings['phone']!=null) {
                $printer->text('Phone: '.$settings['phone'] . "\n");
                $printer->selectPrintMode();
            }
            if($settings['website']!=""||$settings['website']!=null) {
                $printer->text('Website: '.$settings['website'] . "\n");
                $printer->selectPrintMode();
            }
            $printer->text("Cashier: " . Auth::user()->name . "\n");
            if( isset($sale->customer->id) )
			{
				$customerNameText = "Customer Name: " . $sale->customer->first_name . " " . $sale->customer->last_name;
				$printer->text(wordwrap( $customerNameText . "\n",43,"\n",false));
				if($sale->customer->loyalty_card_number && strlen($sale->customer->loyalty_card_number)>0)
				{
					$loyalityCarNumber = $sale->customer->loyalty_card_number;
					$loyalityCarNumberMasked = str_repeat('X', strlen($loyalityCarNumber) - 4) . substr($loyalityCarNumber, -4);
					$printer->text('Loyality Card No: ' . $loyalityCarNumberMasked . "\n");
				}
			}
			$printer->selectPrintMode();

            $printer->text("------------------------------------------\n");
            $header = new \App\Model\Printer\Item("Qty", "Name", "Unit", "Total");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text($header);

            $items = array();
            $totalItemPrice = 0;
            foreach ($sale->items as $anItem) {
                $item_name = "";
                if($anItem->product_id!="discount-01!XcQZc003ab") {
                    if ($anItem->pivot->is_price_taken_from_barcode) {
                        $item_name = $anItem->item_name . '@'
                            . $anItem->pivot->unit_price . '/'
                            . $anItem->item_size;
                    } else {
                        $item_name = $anItem->item_name;
                    }

                    $lineTotal = $anItem->pivot->quantity
                        * $anItem->pivot->unit_price;
                    $totalItemPrice += $lineTotal;
                    $toPrint = new \App\Model\Printer\Item(
                        round($anItem->pivot->quantity), $item_name,
                        number_format($anItem->pivot->unit_price, 2),
                        number_format($lineTotal, 2)
                    );
                    array_push($items, $toPrint);
                }
            }


            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(false);
            foreach ($items as $item) {
                $printer->text($item);
            }

            $printer->text("-------------------------------------------\n");


            $subtotal = new FooterItem('Subtotal', number_format($totalItemPrice, 2) );

            $printer->setEmphasis(true);
            $printer->text($subtotal);

            $printer->feed();

            // Calculating Total Line Discount
            $items = array();
            $totalLineDiscountAmount = 0;
            foreach ($sale->items as $anItem) {
                if($anItem->product_id!="discount-01!XcQZc003ab") {
                    if($anItem->pivot->is_price_taken_from_barcode)
                        $item_name = $anItem->item_name.'@'.$anItem->pivot->unit_price.'/'.$anItem->item_size;
                    else
                        $item_name = $anItem->item_name;
                    $amountDiscounted = ($anItem->pivot->unit_price * $anItem->pivot->quantity * $anItem->pivot->item_discount_percentage)/100;
                    $totalLineDiscountAmount += $amountDiscounted;
                    $toPrint = new \App\Model\Printer\Item(round($anItem->pivot->quantity), $item_name, number_format($anItem->pivot->item_discount_percentage, 2) , number_format($amountDiscounted,2) );
                    if($amountDiscounted)
                    array_push($items, $toPrint);
                }
            }

            $totalDiscountAmount = $totalLineDiscountAmount + $sale->sales_discount;

            if(!$totalDiscountAmount == 0) {
                $printer->text("------------------Discount----------------------\n");
                $printer->feed();

                //Printing Line Discounts
                if(!$totalLineDiscountAmount == 0) {
                    $header = new \App\Model\Printer\Item("Qty", "Name", "Discount(%)", "Total");
                    $printer->setJustification(Printer::JUSTIFY_LEFT);
                    $printer->setEmphasis(true);
                    $printer->text($header);

                    $printer->setJustification(Printer::JUSTIFY_LEFT);
                    $printer->setEmphasis(false);
                    foreach ($items as $item) {
                        $printer->text($item);
                    }
                    $printer->text("-------------------------------------------\n");
                    $printer->setEmphasis(true);
                    $totalLineDiscount = new FooterItem('Total Line Discount ', number_format( $totalLineDiscountAmount, 2));
                    $printer->text($totalLineDiscount);
                    $printer->text("-------------------------------------------\n");
                }

                //Printing Discount On Entire Sale
                if(! $sale->sales_discount == 0) {
                    $totalLineDiscount = new FooterItem('Discount On Entire Sale ', number_format( $sale->sales_discount, 2));
                    $printer->text($totalLineDiscount);
                    $printer->text("-------------------------------------------\n");
                }

                //Calculating and printing Total Sales Discount
                $totalDiscount = new FooterItem('Total Discount ', number_format($totalDiscountAmount , 2));
                if(!$totalDiscountAmount==0) {
                    $printer->text($totalDiscount);
                }


                $printer->feed();
                $printer->feed();
                $printer->text("-------------------------------------------\n");

            }


            if(!$totalDiscountAmount==0) {
                $printer->text($subtotal);
                $printer->text($totalDiscount);
            }

            $printer->text("-------------------------------------------\n");

            if($settings["tax_rate"]>0)
                $tax = new FooterItem('VAT (' . number_format($settings['tax_rate'], 2) . '%)', number_format($sale->tax_amount, 2) );

            $total = new FooterItem('Total', number_format($sale->total_amount, 2) );
            if($sale->due>=0)
                $due = new FooterItem('Due', number_format($sale->due, 2) );
            else
                $due = new FooterItem('Change Due', number_format($sale->due, 2) );
            if($settings["tax_rate"]>0)
                $printer->text($tax);
            $printer->setEmphasis(true);
            $printer->text($total);
            $printer->setEmphasis(false);
            $printer->text($due);
            $printer->selectPrintMode();
            $printer->feed();

            if (!empty($sale->paymentlogs)) {
                $printer->text("-------------------------------------------\n");
                $printer->setEmphasis(true);
                $printer->text("Payments");
                $printer->feed();
                $printer->setEmphasis(false);
                foreach ($sale->paymentlogs as $aPayment) {
                    $payment = new FooterItem(array_search($aPayment->payment_type, \App\Enumaration\PaymentTypes::$TypeList)." Tendered", number_format($aPayment->paid_amount , 2));
                    $printer->text($payment);
                }
            }


			if( $sale->comment && strlen($sale->comment)>0 )
			{
				$printer->feed();
				$printer->text(wordwrap( $sale->comment . "\n",43,"\n",false));
			}
            $printer->feed();

			$printer->setJustification(Printer::JUSTIFY_CENTER);
            if($print_type==1)
			{
				$printer->setEmphasis(true);
				$printer->text("CUSTOMER COPY");
				$printer->feed();
				$printer->feed();
				$printer->setJustification(Printer::JUSTIFY_LEFT);
//				$printer->text("Change Return Policy");
				$printer->setEmphasis(true);
				$printer->feed();
				$printer->feed();
				$printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("THANK YOU!");
            }
			else
            {
                $printer->text("----------------------------------");
                $printer->feed();
                $printer->text('Signature');
                $printer->feed();
                $printer->feed();
                $printer->feed();
                $printer->text("----------------------------------");
                $printer->feed();
                $printer->text('Date');
            }
            $printer->feed();
            $printer->barcode($sale->id."", Printer::BARCODE_CODE39);
            /*dd($items);*/
            /* $printer -> feed();*/
            return redirect()->route('sale_receipt', ['sale_id' => $sale_id]);

        } Catch (\Exception $e) {
			/*if($e->getCode() == 0 ) {
			    return redirect()->route('print_sale',['sale_id'=>$sale->id, "print_type"=>$request->print_type,'driver'=>1]);
            }*/
			//dd($e);
            return redirect()->back()->with(["error" => $e->getMessage()]);
        } finally {
            if (isset($printer)) {
                $printer->cut();
                $printer->pulse();
                $printer->close();
            }
        }

    }

    public function popOpenCashDrawer(){

        $counter_id = Cookie::get('counter_id',null);
        if(is_null($counter_id)){
            return redirect()->route('new_sale')->with(["error" => "Select a counter first."]);
        }

        $counter = Counter::where("id",$counter_id)->first();
        $ip_address = $counter->printer_ip;
        $port = $counter->printer_port;

        try{
            if($counter->printer_connection_type && $counter->printer_connection_type==\App\Enumaration\PrinterConnectionType::USB_CONNECTION) {
                $connector = new WindowsPrintConnector($counter->name);
            }
            else {
                $ip_address = $counter->printer_ip;
                $port = $counter->printer_port;

                $connector = new NetworkPrintConnector($ip_address, $port);
            }
            $printer = new Printer($connector);
        } Catch (\Exception $e)
        {
			dd( $e->getTrace());
            return redirect()->route('new_sale')->with(["error" => $e->getMessage()]);
        } finally{
            if (isset($printer)) {
                $printer->pulse();
                $printer->close();
                return redirect()->route('new_sale');
            }
        }

    }

    public function testPrint($counter_id)
    {


        $settings = SettingsSingleton::get();

        $counter = Counter::where("id",$counter_id)->first();
        try{
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
            $printer->text("Order\n");

            $sale = new \StdClass();
            $sale->id = 12345;
            $sale->created_at = "1/19/2018";

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Receipt\n");
            $printer->selectPrintMode();
            $printer->text(  $sale->created_at. "\n");
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $printer->text($settings['company_name'] . "\n");
            $printer->selectPrintMode();
			$printer->text("Order No." . $sale->id . "\n");

			if($settings['address_line_1']!=""||$settings['address_line_1']!=null)
				$printer->text(wordwrap($settings['address_line_1'] . "\n",43,"\n",false));
			if($settings['address_line_2']!=""||$settings['address_line_2']!=null)
				$printer->text(wordwrap($settings['address_line_2'] . "\n",43,"\n",false));

            if($settings['phone']!=""||$settings['phone']!=null) {
                $printer->text('Phone: '.$settings['phone'] . "\n");
                $printer->selectPrintMode();
            }
            if($settings['website']!=""||$settings['website']!=null) {
                $printer->text('Website: '.$settings['website'] . "\n");
                $printer->selectPrintMode();
            }
            $printer->text("Cashier: " . Auth::user()->name . "\n");
            $printer->selectPrintMode();
            $printer->text("------------------------------------------\n");

            $header = new \App\Model\Printer\Item("Qty", "Name", "Unit", "Total");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text($header);

            $toPrint = new \App\Model\Printer\Item(round(5), "TEST ITEM", 100.0, 500.00);

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(false);

            $printer->text($toPrint);
            $printer->text("-------------------------------------------\n");

			$printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("CUSTOMER COPY");
            $printer->feed();
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Change Return Policy");
            $printer->setEmphasis(true);
            $printer->feed();
            $printer->barcode($sale->id, Printer::BARCODE_CODE39);
           // $printer->feed();
            //$printer->text($settings['company_name']." " . $sale->id);
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("THANK YOU!");
            $printer->feed();

            return redirect()->back();
            /*dd($items);*/
            /* $printer -> feed();*/

        }Catch (\Exception $e)
        {
            return redirect()->back()->with(["error" => $e->getMessage()]);
        }

        finally{
            if (isset($printer)) {
                $printer->cut();
                $printer->pulse();
                $printer->close();
            }
        }
    }

    public function PreEditSaleGet($sale_id){
        $sale = Sale::with('Customer','Employee')->where("id",$sale_id)->first();
        if(is_null($sale)) {
            return redirect()->route('new_sale')->with('error',"Sale id not exist or deleted");
        }
        $customers = Customer::all();
        $employees = Employee::all();
        return view('sales.pre_edit_sale',["sale_id"=>$sale_id,"sale"=>$sale,"customers"=>$customers,"employees"=>$employees]);
    }
    
	public function PreEditSalePost(Request $request, $sale_id){

        $sale = Sale::with('Customer','Employee')->where("id",$sale_id)->first();
        $sale->customer_id = $request->customer_id;
        $sale->employee_id = $request->employee_id;
        $sale->comment = $request->comment;
        $sale->save();
        return redirect()->route("sale_pre_edit",["sale_id"=>$sale_id]);
    }

    public function EditSaleGet($sale_id){

        $sales = DB::table('sales')
            ->join('item_sale','sales.id','=','item_sale.sale_id')
            ->join('items','items.id','=','item_sale.item_id')
            ->leftJoin('suppliers','suppliers.id','=','items.supplier_id')
            ->leftJoin('item_price_rule','items.id','=','item_price_rule.item_id')
            ->leftJoin('price_rules','item_price_rule.price_rule_id','=','price_rules.id')
            ->where('sales.deleted_at',null)
            ->where('items.deleted_at',null)
            ->where('sales.id',$sale_id)
            ->select('item_sale.*','items.*','sales.*',
                'suppliers.*','item_price_rule.price_rule_id as price_rule_id','price_rules.*')
            ->get()->toArray();

        $sale_payments = Sale::with('PaymentLogs')->where('id',$sale_id)->first()->PaymentLogs;

        $current_date = new \DateTime('today');
        // Check price rules on specific items

        foreach($sales as $anItem) {


            if(isset($anItem->id)){

                if ($anItem->active){

                    if($anItem->type==1){

                        if($anItem->percent_off>0){

                            $rule_start_date = new \DateTime($anItem->start_date);
                            $rule_expire_date = new \DateTime($anItem->end_date);

                            if(($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) ) {
                                $discountPercentage = $anItem->percent_off;

                                if($discountPercentage>100){
                                    $anItem->discountPercentage = 100;
                                    $anItem->itemPrice = $anItem->selling_price;
                                    $anItem->discountName = $anItem->name;
                                    $anItem->discountAmount = $anItem->itemPrice*($discountPercentage/100);
                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice-$anItem->discountAmount;
                                    $anItem->discountApplicable = true;
                                }else{
                                    $anItem->discountPercentage = $discountPercentage;
                                    $anItem->itemPrice = $anItem->selling_price;
                                    $anItem->discountName = $anItem->name;
                                    $anItem->discountAmount = $anItem->itemPrice*($discountPercentage/100);
                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice-$anItem->discountAmount;
                                    $anItem->discountApplicable = true;
                                }

                            }else{
                                $anItem->discountApplicable = false;
                            }

                            //echo "Item should be discounted by ".$anItem->percent_off." percent";

                        }else if($anItem->fixed_of>0){

                            $rule_start_date = new \DateTime($anItem->start_date);
                            $rule_expire_date = new \DateTime($anItem->end_date);

                            if( ($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) ) {
                                $discountPercentage = ($anItem->fixed_of/$anItem->selling_price)*100;
                                if($discountPercentage>100) {
                                    $anItem->discountPercentage = 100;
                                    $anItem->discountAmount = $anItem->selling_price;
                                    $anItem->discountName = $anItem->name;
                                    $anItem->itemPrice = $anItem->selling_price;
                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice - $anItem->itemPrice;
                                    $anItem->discountApplicable = true;
                                }
                                else{
                                    $anItem->discountPercentage = $discountPercentage;
                                    $anItem->discountAmount = $anItem->fixed_of;
                                    $anItem->discountName = $anItem->name;
                                    $anItem->itemPrice = $anItem->selling_price;
                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice - $anItem->discountAmount;
                                    $anItem->discountApplicable = true;
                                }

                            }else{
                                $anItem->discountApplicable = false;
                            }
                            // echo "Item should be discounted by ".$anItem->fixed_of." dollar";
                        }

                    }

                }


            }
        }


        $customerList = Customer::all();
        //dd($sale->items);
        return view('sales.edit_sale',["sales"=>$sales,"customerList"=>$customerList,"sale_id"=>$sale_id,"payments"=>$sale_payments]);

    }

    public function EditSaleVueGet($sale_id){

        $sales = DB::table('sales')
            ->join('item_sale','sales.id','=','item_sale.sale_id')
            ->join('items','items.id','=','item_sale.item_id')
            ->leftJoin('suppliers','suppliers.id','=','items.supplier_id')
            ->leftJoin('item_price_rule','items.id','=','item_price_rule.item_id')
            ->leftJoin('price_rules','item_price_rule.price_rule_id','=','price_rules.id')
            ->where('sales.deleted_at',null)
            ->where('items.deleted_at',null)
            ->where('sales.id',$sale_id)
            ->select('item_sale.*','items.*','sales.*',
                'suppliers.*','item_price_rule.price_rule_id as price_rule_id','price_rules.*')
            ->get()->toArray();

        $sale_payments = Sale::with('PaymentLogs')->where('id',$sale_id)->first()->PaymentLogs;
        foreach($sale_payments as $aPayment) {
            $aPayment->payment_type = array_search($aPayment->payment_type, \App\Enumaration\PaymentTypes::$TypeList);
        }

        $current_date = new \DateTime('today');
        // Check price rules on specific items

        foreach($sales as $anItem) {

            if(is_null($anItem->price_rule_id))
                $anItem->price_rule_id = 0;

            if(isset($anItem->id)){

                if ($anItem->active){

                    if($anItem->type==1){

                        if($anItem->percent_off>0){

                            $rule_start_date = new \DateTime($anItem->start_date);
                            $rule_expire_date = new \DateTime($anItem->end_date);

                            if(($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) ) {
                                $discountPercentage = $anItem->percent_off;
                                if($discountPercentage>100){
                                    $anItem->discountPercentage = 100;
                                    $anItem->itemPrice = $anItem->selling_price;
                                    $anItem->discountName = $anItem->name;
                                    $anItem->discountAmount = $anItem->itemPrice*($discountPercentage/100);
                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice-$anItem->discountAmount;
                                    $anItem->discountApplicable = true;
                                }else{
                                    $anItem->discountPercentage = $discountPercentage;
                                    $anItem->itemPrice = $anItem->selling_price;
                                    $anItem->discountName = $anItem->name;
                                    $anItem->discountAmount = $anItem->itemPrice*($discountPercentage/100);
                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice-$anItem->discountAmount;
                                    $anItem->discountApplicable = true;
                                }

                            }else{
                                $anItem->discountApplicable = false;
                            }

                            //echo "Item should be discounted by ".$anItem->percent_off." percent";

                        }else if($anItem->fixed_of>0){

                            $rule_start_date = new \DateTime($anItem->start_date);
                            $rule_expire_date = new \DateTime($anItem->end_date);

                            if( ($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) ) {
                                $discountPercentage = ($anItem->fixed_of/$anItem->selling_price)*100;
                                if($discountPercentage>100){
                                    $anItem->discountPercentage = 100;
                                    $anItem->discountAmount = $anItem->selling_price;
                                    $anItem->discountName = $anItem->name;
                                    $anItem->itemPrice = $anItem->selling_price;
                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice - $anItem->itemPrice;
                                    $anItem->discountApplicable = true;
                                }
                                else{
                                    $anItem->discountPercentage = $discountPercentage;
                                    $anItem->discountAmount = $anItem->fixed_of;
                                    $anItem->discountName = $anItem->name;
                                    $anItem->itemPrice = $anItem->selling_price;
                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice - $anItem->discountAmount;
                                    $anItem->discountApplicable = true;
                                }

                            }else{
                                $anItem->discountApplicable = false;
                            }
                            // echo "Item should be discounted by ".$anItem->fixed_of." dollar";
                        }

                    }

                }
            }
        }


        $customerList = Customer::all();
        //dd($sale->items);
        return view('sales.edit_sale_vue',["sales"=>$sales,"customerList"=>$customerList,"sale_id"=>$sale_id,"payments"=>$sale_payments]);

    }

    public function EditSalePost(Request $request, $sale_id){

        $saleInfo = $request->sale_info;
        $productInfos = $request->product_infos;
        $paymentInfos = $request->payment_infos;
        $deletedTransactions = $request->deletedTransactions;
        $sale = new Sale();
        $sale_id = $sale->EditSale($saleInfo, $productInfos, $paymentInfos, $deletedTransactions,$saleInfo['status'], $sale_id);
        echo $sale_id;
    }

    public function DeleteSale($sale_id){
        $cashRegister = new CashRegister();
        $currentCashRegister = $cashRegister->getCurrentActiveRegister();
        if(!is_null($currentCashRegister)) {
            $sale = Sale::where("id",$sale_id)->first();

            $paymentLog = new PaymentLog();
            $paymentLog->addNewPaymentLog(CashRegisterTransactionType::$SALE_REFUND, $sale->total_amount,$sale,$sale->cusotmer_id,null);

            $previous_status = $sale->sale_status;
            $sale->refund_status = true;
            $sale->refund_register_id = $currentCashRegister->id;
            $sale->save();
            $sale->delete();

            SaleStatusLog::changeSaleStatus($sale_id,$previous_status, SaleStatus::$REFUNDED);

            $sale_items = DB::table('item_sale')->where("sale_id",$sale_id)->get();
            foreach($sale_items as $anItem) {
                $item = Item::where("id",$anItem->item_id)->first();
                $item->item_quantity += $anItem->quantity;
                $item->save();
            }
            return redirect()->route("new_sale")->with("success", "Sale ID successfully deleted");
        }else{
            return redirect()->route("sale_pre_edit",["sale_id"=>$sale_id])->with('error',"No cash register is active");
        }
    }

    public function clearSalesData(Request $request) {
        $start_date = $request->start_date_formatted;
        $end_date = $request->end_date_formatted;
        if(!$start_date&&!$end_date){
            return redirect()->back()->with('error','Specify at least one field');
        }
        else {
            if($start_date && !$end_date) {
                CashRegister::whereDate('created_at','>',$start_date)->delete();
                CashRegisterTransaction::whereDate('created_at','>',$start_date)->delete();
                Invoice::whereDate('created_at','>',$start_date)->delete();
                LoyaltyTransaction::whereDate('created_at','>',$start_date)->delete();

                $saleIds= Sale::_eloquentToArray(Sale::whereDate('created_at','>',$start_date)
                    ->select('id')->get(),"id");
                DB::table('customer_transaction_invoice')->whereIn('sale_id',$saleIds)->delete();

                PaymentLog::whereIn("sale_id",$saleIds)->delete();
                DB::table('payment_log_sale')->whereIn("sale_id",$saleIds)->delete();
                DB::table('item_sale')->whereIn("sale_id",$saleIds)->delete();
                Sale::whereIn("id",$saleIds)->delete();
            } else if($end_date && !$start_date) {

                CashRegister::whereDate('created_at','<',$start_date)->delete();
                CashRegisterTransaction::whereDate('created_at','<',$start_date)->delete();
                Invoice::whereDate('created_at','<',$start_date)->delete();
                LoyaltyTransaction::whereDate('created_at','<',$start_date)->delete();

                $saleIds= Sale::_eloquentToArray(Sale::whereDate('created_at','<',$start_date)
                    ->select('id')->get(),"id");
                DB::table('customer_transaction_invoice')->whereIn('sale_id',$saleIds)->delete();

                PaymentLog::whereIn("sale_id",$saleIds)->delete();
                DB::table('payment_log_sale')->whereIn("sale_id",$saleIds)->delete();
                DB::table('item_sale')->whereIn("sale_id",$saleIds)->delete();
                Sale::whereIn("id",$saleIds)->delete();
            }else{
                CashRegister::whereDate('created_at','>',$start_date)->whereDate('created_at','<',$start_date)->delete();
                CashRegisterTransaction::whereDate('created_at','>',$start_date)->whereDate('created_at','<',$start_date)->delete();
                Invoice::whereDate('created_at','>',$start_date)->whereDate('created_at','<',$start_date)->delete();
                LoyaltyTransaction::whereDate('created_at','>',$start_date)->whereDate('created_at','<',$start_date)->delete();

                $saleIds= Sale::_eloquentToArray(Sale::whereDate('created_at','>',$start_date)
                    ->whereDate('created_at','<',$start_date)
                    ->select('id')->get(),"id");
                DB::table('customer_transaction_invoice')->whereIn('sale_id',$saleIds)->delete();

                PaymentLog::whereIn("sale_id",$saleIds)->delete();
                DB::table('payment_log_sale')->whereIn("sale_id",$saleIds)->delete();
                DB::table('item_sale')->whereIn("sale_id",$saleIds)->delete();
                Sale::whereIn("id",$saleIds)->delete();
            }
           return redirect()->back()->with('success','Successfully deleted');
        }
    }

}
