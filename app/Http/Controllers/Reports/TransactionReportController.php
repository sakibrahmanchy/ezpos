<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Model\Customer;
use Illuminate\Http\Request;

class TransactionReportController extends Controller{

    public function detailedReports(Request $request){

        $firstDayOfThisMonth = new \DateTime('first day of this month');
        $firstDayOfThisMonth->format('Y-m-d');
        $startDate =  $firstDayOfThisMonth->format('Y-m-d');
        $endDate = date('Y-m-d');
        if(isset($request->customer_id)){
            $customer_id = $request->customer_id;

            $customer = new Customer();
            $openingDue = $customer->getBalance($customer_id,$startDate);
            $closingDue = $customer->getBalance($customer_id,$endDate);

            $transactionHistory = Customer::with('transactions','transactionSum')
                ->where('id',$customer_id)->get();


            $info = ["startDate"=>$startDate,"endDate"=>$endDate,"openingDue"=>$openingDue,
                     "closingDue"=>$closingDue,"transactionHistory"=>$transactionHistory];
            return view("reports.transactions.specific_detail",["info"=>$info,"customer_id_found"=>true]);
        }else {
            $customers = Customer::all();
            return view("reports.transactions.specific_detail",["customers"=>$customers,"customer_id_found"=>false]);
        }

    }

    public function getOpeningBalance($date) {

    }

    public function detailedReportsAjax(Request $request){

        $startDate = $request->start_date_formatted;
        $endDate = $request->end_date_formatted;
        $customer_id = $request->customer_id;
        $customer = new Customer();
        $openingDue = $customer->getBalance($customer_id,$startDate);
        $closingDue = $customer->getBalance($customer_id,$endDate);

        $transactionHistory = Customer::with('transactions','transactionSum')
            ->where('id',$customer_id)->get();

        $info = ["startDate"=>$startDate,"endDate"=>$endDate,"openingDue"=>$openingDue,
                 "closingDue"=>$closingDue,"transactionHistory"=>$transactionHistory];
        $view = view('reports.transactions.specific_detail_table',["info"=>$info]);
        $contents = (string) $view->render();

        return response()->json(["contents"=>$contents],200);
    }

}