<?php

namespace App\Http\Controllers;

use App\Enumaration\PaymentTypes;
use App\Enumaration\SaleStatus;
use App\Enumaration\SaleTypes;
use App\Library\SettingsSingleton;
use App\Model\Counter;
use App\Model\Customer;
use App\Model\CustomerTransaction;
use App\Model\Invoice;
use App\Model\PaymentLog;
use App\Model\Printer\FooterItem;
use App\Model\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function emailInvoice($invoice_id) {

        $invoice = Invoice::where("id",$invoice_id)->with('transactions','customer')->first();
//        return view('customers.emails_invoice_receipt',["invoice" => $invoice, "customer" => $invoice->customer]);
        if (isset($invoice->customer->id)) {
            $customer = Customer::where('id',$invoice->customer_id)->first();
        }

        if (isset($customer->email) && !is_null($customer->email)) {

            Mail::send('customers.emails_invoice_receipt', ["invoice" => $invoice, "customer" => $customer], function ($m) use ($invoice, $customer) {
                $m->from('sales@mg.grimspos.com', 'EZPOS');

                $pdf = PDF::loadView('customers.invoice_receipt_pdf', ["invoice" => $invoice, "customer" => $customer]);
                $m->to($customer->email, $customer->name)->subject('Invoice receipt for due payment');
                $m->attachData($pdf->output(), 'invoice.pdf', ['mime' => 'application/pdf']);
            });

            // check for failures
            if (Mail::failures()) {
                return redirect()->route('customer_invoice', $invoice_id)->with('error', 'Error sending email');
            }

            // otherwise everything is okay ...
            return redirect()->route('customer_invoice', $invoice_id)->with('success', 'Email successfully sent');
        }

        return redirect()->route('customer_invoice', $invoice_id)->with('error', 'Sorry. This customer has no email id.');
    }

    public function DownloadInvoiceReceipt(Request $request, $invoice_id)
    {
        $invoice = Invoice::where("id",$invoice_id)->with('transactions','customer')->first();
        $customer = Customer::where('id',$invoice->customer->id)->first();

        $pdf = PDF::loadView('customers.invoice_receipt_pdf', ["invoice" => $invoice, "customer" => $customer]);
        return $pdf->download('ezpos-sale-receipt.pdf');

    }

    public function getTotalDueForSelectedSales(Request $request) {
        $listOfSaleIds = $request->transaction_list;


        $totalDue =  CustomerTransaction::join('sales','sales.id','=','customer_transactions.sale_id')
            ->where("sale_type",SaleTypes::$SALE)
            ->whereIn('customer_transactions.id',$listOfSaleIds)
            ->where(DB::raw("sale_amount-paid_amount"),'>',0)
            ->sum(DB::raw("sale_amount-paid_amount"));

        $totalDue = number_format($totalDue, 2);

        return response()->json($totalDue);
    }

    public function clearCustomerInvoice(Request $request) {

        $invoice_id = $request->invoice_id;
        $invoice = Invoice::where('id',$invoice_id)->with('Transactions')->first();

        $payment_type = $request->payment_type;

        foreach ($invoice->transactions as $aTransaction) {

            $sale = Sale::where("id",$aTransaction->sale_id)->first();
            if(!is_null($sale)) {
                $saleDue = $sale->due;
                $customer_id = $sale->customer_id;
                $paymentLog = new PaymentLog();
                $paymentLog->addNewPaymentLog(PaymentTypes::$TypeList[$payment_type],$saleDue,$sale,$customer_id, "Due paid for sale ".$sale->id, $invoice_id);

                $customerTransactionInfo = CustomerTransaction::where("id",$aTransaction->id)
                    ->first();

                if(!is_null($customerTransactionInfo)) {
                    $customerTransactionInfo->update([
                        'paid_amount' => $sale->total_amount,
                        'sale_amount' => $sale->total_amount,
                        'customer_id' => $customer_id
                    ]);
                } else {

                    $customerTransactionObj = new CustomerTransaction();
                    $customerTransactionObj->transaction_type = \App\Enumaration\CustomerTransactionType::SALE;
                    $customerTransactionObj->sale_id = $sale->id;
                    $customerTransactionObj->sale_amount = $sale->total_amount;
                    $customerTransactionObj->paid_amount = $sale->total_amount;
                    $customerTransactionObj->customer_id = $customer_id;
                    $customerTransactionObj->cash_register_id = 0;
                    $customerTransactionObj->save();


                    if($saleDue<0)
                        $saleDue = 0;

                    $updateCustomerBalanceQuery = "update customers set account_balance=account_balance+? where id=?";
                    DB::update( $updateCustomerBalanceQuery, [ $saleDue, $customer_id] );
                }

                $sale->sale_status = SaleStatus::$SUCCESS;
                $sale->due = 0;
                $sale->save();
            } else {
                    CustomerTransaction::where("id",$aTransaction->id)
                    ->first()->delete();
            }
        }
        return redirect()->back();
    }

    public function undoInvoiceClear($invoice_id) {
        $invoice = Invoice::where("id",$invoice_id)->with('Transactions')->first();
        PaymentLog::where("invoice_id",$invoice_id)->where("invoice_id","<>",0)->delete();
        foreach ($invoice->transactions as $aTransaction) {
            $sale_id = $aTransaction->sale_id;
            $previousPayment = PaymentLog::where("sale_id",$sale_id)->sum('paid_amount');
            $sale = Sale::where("id",$sale_id)->first();
            $aTransaction->sale_amount = $sale->total_amount;
            $aTransaction->paid_amount = $previousPayment;
            $aTransaction->save();

            $sale->due = $sale->total_amount - $previousPayment;
            $sale->save();
        }

        return redirect()->back();
    }


    public function printInvoiceReceipt($invoice_id) {
        $invoice = Invoice::where("id",$invoice_id)->with('transactions','customer')->first();
//        $customer = Customer::where('id',$invoice->customer->id)->first();\
        $due = 0; $payment = 0;

        try {
            $settings = SettingsSingleton::get();

            $counter_id = 0;
//            if($request->has('counter_id'))
//                $counter_id = intval($request->counter_id);
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
            $printer->text("Invoice Receipt\n");
            $printer->selectPrintMode();
            $printer->text( $invoice->created_at. "\n");
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $printer->text($settings['company_name']. "\n");
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $printer->text("Invoice No." . $invoice->id . "\n");

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
                $customerNameText = "Customer Name: " . $invoice->customer->first_name . " " . $invoice->customer->last_name;
                $printer->text(wordwrap( $customerNameText . "\n",43,"\n",false));
                if($invoice->customer->loyalty_card_number && strlen($invoice->customer->loyalty_card_number)>0)
                {
                    $loyalityCarNumber = $invoice->customer->loyalty_card_number;
                    $loyalityCarNumberMasked = str_repeat('X', strlen($loyalityCarNumber) - 4) . substr($loyalityCarNumber, -4);
                    $printer->text('Loyality Card No: ' . $loyalityCarNumberMasked . "\n");
                }
            }
            $printer->selectPrintMode();

            $printer->text("------------------------------------------\n");
            $header = new \App\Model\Printer\Invoice("Sale Id", "Created at", "Amount Due");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text($header);

            $items = array();
            foreach($invoice->transactions as $aTransaction)
            {
                $due += (  $aTransaction->sale_amount  );
                $payment += ( $aTransaction->paid_amount );

                $toPrint = new \App\Model\Printer\Invoice(
                    $aTransaction->sale_id,
                    $aTransaction->created_at,
                    number_format($aTransaction->sale_amount ,2)
                );

                array_push($items, $toPrint);
            }

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(false);
            foreach ($items as $item) {
                $printer->text($item);
            }

            $printer->text("-------------------------------------------\n");
            $totalDue = new FooterItem('Total Due', number_format($due, 2));

            $printer->setEmphasis(true);
            $printer->text($totalDue);

            $printer->feed();

            if($payment > 0) {
                $paymentText = new FooterItem('Paid Amount', number_format($payment, 2));
                $remainingText = new FooterItem('Remaining Due', number_format($due - $payment, 2));
                $printer->text($paymentText);
                $printer->feed();
                $printer->text($remainingText);
                $printer->feed();
            }

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            if($due > $payment)
            {
                $printer->text("Unpaid\n");
                $printer->selectPrintMode();
            } else{
                $printer->text("Paid\n");
                $printer->selectPrintMode();
            }

            return redirect()->route('customer_invoice', ['invoice_id' => $invoice_id]);

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
}
