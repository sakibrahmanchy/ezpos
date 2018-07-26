<?php

namespace App\Http\Controllers;

use App\Model\Customer;
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
}
