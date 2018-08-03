<?php

namespace App\Http\Controllers;

use App\Enumaration\SaleTypes;
use App\Model\Customer;
use App\Model\CustomerTransaction;
use App\Model\Invoice;
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
            ->where("sales.due",'>',0)
            ->sum('due');

        return response()->json($totalDue);
    }

    public function clearCustomerInvoice($invoice_id, Request $request) {

        $transactionList = Invoice::where('id',$invoice_id)->with('Transactions')->get();

        $payment_type = $request->payment_type;


        $sale_ids = Sale::_eloquentToArray(CustomerTransaction::whereIn("id",$transactionList)->select('sale_id')->get(),"sale_id");

        foreach ($sale_ids as $aSaleId) {

            $sale = Sale::where("id",$aSaleId)->first();
            $saleDue = $sale->due;
            $paymentLog = new PaymentLog();
            $paymentLog->addNewPaymentLog(PaymentTypes::$TypeList[$payment_type],$saleDue,$sale,$customer_id, "Due paid for sale ".$sale->id);
            $customer_id = $sale->customer_id;

            $customerTransactionInfo = CustomerTransaction::where("sale_id",$aSaleId)
                ->where("customer_id",$customer_id)
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
        }
    }
}
