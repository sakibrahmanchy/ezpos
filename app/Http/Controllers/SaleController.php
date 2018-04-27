<?php

namespace App\Http\Controllers;

use App\Enumaration\DateTypes;
use App\Enumaration\SaleStatus;
use app\Http\Controllers\Reports\ReportTotal;
use App\Library\SettingsSingleton;
use App\Model\CashRegister;
use App\Model\Category;
use App\Model\Counter;
use App\Model\Customer;
use App\Model\Employee;
use App\Model\Item;
use App\Model\ItemKit;
use App\Model\Manufacturer;
use App\Model\Printer\FooterItem;
use App\Model\Sale;
use App\Model\Supplier;
use App\Model\User;
use Faker\Provider\tr_TR\DateTime;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
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


            return view('sales.new_sale', ['customerList' => $customerList]);
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

        $sale = new Sale();
        $sale->InsertSale($saleInfo, $productInfos, $saleInfo['status']);

    }

    public function GetSuspendedSale()
    {


    }

    public function GetSaleReceipt($sale_id)
    {

        $sale = Sale::where("id", $sale_id)->with('items', 'paymentlogs', 'customer', 'counter')->first();


        /*return response()->json(['sale'=>$sale], 200);*/
        if ($sale == null)
            return redirect()->back()->with(["error" => "Sale id not found!"]);
        else
            return view('sales.sale_receipt', ["sale" => $sale]);

    }

    public function GetSuspendedSales()
    {

        $suspendedSales = Sale::where('sale_status', SaleStatus::$ESTIMATE)->orWhere('sale_status', SaleStatus::$LAYAWAY)->with('items', 'paymentlogs')->get();

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

        $sale = Sale::orderBy('id', 'desc')->first();
        if (!is_null($sale))
            $sale_id = $sale->id;
        else
            return redirect()->back()->with(["error" => "No sale found!"]);

        return redirect()->route('sale_receipt', ['sale_id' => $sale_id]);
    }


    public function DownloadSaleReceipt($sale_id)
    {


        $sale = Sale::where("id", $sale_id)->with(['items' => function ($query) {
            $query->where('items.product_type', '<>', 2);
        }, 'paymentlogs', 'customer'])->first();

        $pdf = PDF::loadView('sales.sale_receipt_pdf', ["sale" => $sale]);
        return $pdf->download('ezpos-sale-receipt.pdf');
    }

    public function MailSaleReceipt($sale_id)
    {

        $sale = Sale::where("id", $sale_id)->with(['items' => function ($query) {
            $query->where('items.product_type', '<>', 2);
        }, 'paymentlogs', 'customer'])->first();


        $customer = new \stdClass();
        if (isset($sale->customer->id)) {
            $customer->name = $sale->customer->name;
            $customer->email = $sale->customer->email;
        }

        if (isset($customer->email) && !is_null($customer->email)) {

            Mail::send('sales.emails_sales_receipt', ["sale" => $sale], function ($m) use ($sale, $customer) {
                $m->from('sales@ezpos.com', 'EZPOS');

                $pdf = PDF::loadView('sales.sale_receipt_pdf', ["sale" => $sale]);
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
                    ->select(DB::raw(('*, COUNT(*) as item_count')))
                    ->groupBy('sales.id');


                $items = $results->get();

                return view('sales.search_sale', ["dateTypes" => $dateTypes, "items" => $items]);


            } else
                return view('sales.search_sale', ["dateTypes" => $dateTypes]);
        }

    }

    public function printSaleReciept($sale_id)
    {

        $sale = Sale::where("id", $sale_id)->with('items', 'paymentlogs', 'customer')->first();
        if ($sale == null)
            return redirect()->route('new_sale')->with(["error" => 'Sale id not found']);

            $created_at = date("d/m/Y H:i:s a",strtotime($sale->created_at));
        try {
            $settings = SettingsSingleton::get();

            $counter = $sale->counter;
            $ip_address = $counter->printer_ip;
            $port = $counter->printer_port;

            $connector = new NetworkPrintConnector($ip_address, $port);

            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Receipt\n");
            $printer->selectPrintMode();
            $printer->text( $created_at. "\n");
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $printer->text($settings['company_name'] . " No." . $sale->id . "\n");
            $printer->selectPrintMode();
            $printer->text($settings['address'] . "\n\n");
            $printer->selectPrintMode();
            $printer->text("Cashier: " . Auth::user()->name . "\n");
            $printer->selectPrintMode();
            $printer->text("------------------------------------------\n");

            $header = new \App\Model\Printer\Item("Qty", "Name", "Unit", "Total");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text($header);

            $items = array();
            foreach ($sale->items as $anItem) {
                $item_name = "";
                if($anItem->pivot->is_price_taken_from_barcode)
                    $item_name = $anItem->item_name.'@'.$anItem->pivot->unit_price.'/'.$anItem->item_size;
                else
                  $item_name = $anItem->item_name;
                $toPrint = new \App\Model\Printer\Item(round($anItem->pivot->quantity), $item_name, $anItem->pivot->unit_price , $anItem->pivot->total_price);
                array_push($items, $toPrint);
            }

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(false);
            foreach ($items as $item) {
                $printer->text($item);
            }

            $printer->text("-------------------------------------------\n");


            $subtotal = new FooterItem('Subtotal', $sale->sub_total_amount);
            if($settings["tax_rate"]>0)
                $tax = new FooterItem('VAT (' . $settings['tax_rate'] . '%)', $sale->tax_amount);
            $total = new FooterItem('Total', $sale->total_amount);
            $due = new FooterItem('Due', $sale->due);

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text($subtotal);
            $printer->setEmphasis(false);
            if($settings["tax_rate"]>0)
                $printer->text($tax);
            $printer->setEmphasis(true);
            $printer->text($total);
            $printer->setEmphasis(false);
            $printer->text($due);
            $printer->selectPrintMode();
            $printer->feed();
            $printer->feed();


            if (!empty($sale->paymentlogs)) {
                $printer->text("-------------------------------------------\n");
                $printer->setEmphasis(true);
                $printer->text("Payments");
                $printer->feed();
                $printer->setEmphasis(false);
                foreach ($sale->paymentlogs as $aPayment) {
                    $payment = new FooterItem($aPayment->payment_type, $aPayment->paid_amount);
                    $printer->text($payment);
                }
            }
            $printer->feed();
            $printer->feed();
            $printer->setBarcodeHeight(64);
            $printer->setBarcodeWidth(2);
            $printer->setEmphasis(true);
            $printer->text("Change Return Policy");
            $printer->setEmphasis(true);
            $printer->feed();
            $printer->barcode($sale->id, Printer::BARCODE_CODE39);
            $printer->feed();
            $printer->text($settings['company_name']." " . $sale->id);
            $printer->feed();
            /*dd($items);*/
            /* $printer -> feed();*/
            return redirect()->route('sale_receipt', ['sale_id' => $sale_id]);

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


    public function popOpenCashDrawer(){

        $counter_id = Cookie::get('counter_id',null);
        if(is_null($counter_id)){
            return redirect()->route('new_sale')->with(["error" => "Select a counter first."]);
        }

        $counter = Counter::where("id",$counter_id)->first();
        $ip_address = $counter->printer_ip;
        $port = $counter->printer_port;

        try{
            $connector = new NetworkPrintConnector($ip_address, $port);
            $printer = new Printer($connector);
        } Catch (\Exception $e)
        {
            return redirect()->route('new_sale')->with(["error" => $e->getMessage()]);
        } finally{
            if (isset($printer)) {
                $printer->pulse();
                $printer->close();
            }
        }

    }

    public function testPrint($counter_id)
    {


        $settings = SettingsSingleton::get();

        $counter = Counter::where("id",$counter_id)->first();
        $ip_address = $counter->printer_ip;
        $port = $counter->printer_port;

        try{
            $connector = new NetworkPrintConnector($ip_address, $port);

            $printer = new Printer($connector);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Order\n");

            $sale = new \StdClass();
            $sale->id = 12345;
            $sale->created_at = "1/19/2018";


            //$printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            //$printer->text($settings['company_name'] . " " . $sale->id . "\n");
            //$printer->text("------------------------------------------\n");
            $printer->selectPrintMode();
            $printer->text($sale->created_at . "\n");
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $printer->text($settings['company_name'] . " " . $sale->id . "\n");
            $printer->selectPrintMode();
            $printer->text($settings['address'] . "\n\n");
            $printer->selectPrintMode();
            $printer->text("Employee: " . Auth::user()->name . "\n");
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

            $subtotal = new FooterItem('Subtotal', 500.00);
            $tax = new FooterItem('VAT (15%)', 75.00);
            $total = new FooterItem('Total', 575.00);
            $due = new FooterItem('Due', 0.00);

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text($subtotal);
            $printer->setEmphasis(false);
            $printer->text($tax);
            $printer->setEmphasis(true);
            $printer->text($total);
            $printer->setEmphasis(false);
            $printer->text($due);
            $printer->selectPrintMode();
            $printer->feed();
            $printer->feed();

            $printer->feed();
            $printer->feed();
            $printer->setBarcodeHeight(64);
            $printer->setBarcodeWidth(2);
            $printer->setEmphasis(true);
            $printer->text("Change Return Policy");
            $printer->setEmphasis(true);
            $printer->feed();
            $printer->barcode($sale->id, Printer::BARCODE_CODE39);
            $printer->feed();
            $printer->text("EZPOS " . $sale->id);
            $printer->feed();
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



    public function EditSaleGet($sale_id){

        $sales = DB::table('sales')
            ->join('item_sale','sales.id','=','item_sale.sale_id')
            ->join('items','items.id','=','item_sale.item_id')
            ->leftJoin('payment_log_sale','sales.id','=','payment_log_sale.sale_id')
            ->leftJoin('payment_logs','payment_log_sale.payment_log_id','=','payment_logs.id')
            ->leftJoin('suppliers','suppliers.id','=','items.supplier_id')
            ->leftJoin('item_price_rule','items.id','=','item_price_rule.item_id')
            ->leftJoin('price_rules','item_price_rule.price_rule_id','=','price_rules.id')
            ->where('sales.deleted_at',null)
            ->where('items.deleted_at',null)
            ->where('item_sale.sale_id',$sale_id)
            ->get()->toArray();

        $current_date = new \DateTime('today');
        // Check price rules on specific items

        foreach($sales as $anItem) {

            if(isset($anItem->id)){

                if ($anItem->active){

                    if($anItem->unlimited||$anItem->num_times_to_apply>0)
                    {

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
        }



        $customerList = Customer::all();
        //dd($sale->items);
        return view('sales.edit_sale',["sales"=>$sales,"customerList"=>$customerList,"sale_id"=>$sale_id]);

    }

    public function EditSalePost(Request $request, $sale_id){

        $saleInfo = $request->sale_info;
        $productInfos = $request->product_infos;
        $paymentInfos = $request->payment_infos;
        $sale = new Sale();
        $sale_id = $sale->EditSale($saleInfo, $productInfos, $paymentInfos, $saleInfo['status'], $sale_id);
        echo $sale_id;
    }



}
