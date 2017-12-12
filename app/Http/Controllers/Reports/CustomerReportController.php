<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
use App\Model\Customer;
use App\Model\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerReportController extends Controller
{

    public function ReportCustomerGraphical(){

        $report_name = "customer_name";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.customer_id";

        $bindings = array($startDate, $endDate);
        $sql = "Select sum(sub_total_amount) as subtotal ,sum(total_amount) as total,
                sum(tax_amount) as tax, sum(profit) as profit, IFNULL(customers.first_name,'No Customer') as first_name,
                customers.last_name as last_name
                from sales
                left join customers on customers.id = sales.customer_id
                where sales.id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                group by sales.customer_id" ;


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->first_name." ".$aSale->last_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.customer.graphical',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);
    }


    public function ReportCustomerAjax(Request $request){

        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        if(!isset($request->customer_id)){

            $bindings = array($startDate, $endDate);
            $sql = "Select sum(sub_total_amount) as subtotal ,sum(total_amount) as total,
                sum(tax_amount) as tax, sum(profit) as profit, IFNULL(customers.first_name,'No Customer') as first_name,
                customers.last_name as last_name, sum(items_sold) as items_sold
                from sales
                left join customers on customers.id = sales.customer_id
                where sales.id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                group by sales.customer_id" ;


            $items = DB::select($sql,$bindings);

            $labels = array();
            $values = array();
            foreach($items as $aSale){
                array_push($labels,$aSale->first_name."".$aSale->last_name." ($".$aSale->total.")");
                array_push($values,$aSale->total);
            }

            $reportTotal = new ReportTotal();
            $info = $reportTotal->reportInfo($startDate,$endDate);

            return response()->json(['sale'=>$items,'labels'=>$labels,"dataset"=>$values,"info"=>$info], 200);
        }else{

            $customer_id = $request->customer_id;
            $sales = Sale::where('customer_id',$customer_id)->where('created_at','>=',$startDate)->where('created_at','<=',$endDate)->with('customer','paymentLogs','employee')->get();
            $labels = array();
            $values = array();
            foreach($sales as $aSale){

                array_push($labels,$aSale->first_name."".$aSale->last_name." ($".$aSale->total.")");
                array_push($values,$aSale->total);
            }


            $reportTotal = new ReportTotal();
            $info = $reportTotal->reportInfo($startDate,$endDate);

            return response()->json(['sale'=>$sales,'labels'=>$labels,"dataset"=>$values,"info"=>$info], 200);


        }

    }

    public function ReportCustomerSummary(){

        $report_name = "customer_name";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.customer_id";

        $bindings = array($startDate, $endDate);
        $sql = "Select sum(sub_total_amount) as subtotal ,sum(total_amount) as total,
                sum(tax_amount) as tax, sum(profit) as profit, IFNULL(customers.first_name,'No Customer') as first_name,
                customers.last_name as last_name, sum(items_sold) as items_sold
                from sales
                left join customers on customers.id = sales.customer_id
                where sales.id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                group by sales.customer_id" ;


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->first_name."".$aSale->last_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.customer.summary',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);

    }

    public function ReportCustomerDetail(){

        $report_name = "customer_name";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.customer_id";

        $customers = Customer::all();

        $customer_id = "0";
        $sales = Sale::where('customer_id',$customer_id)->where('created_at','>=',$startDate)->where('created_at','<=',$endDate)->with('customer','paymentLogs','employee')->get();

        $labels = array();
        $values = array();
        foreach($sales as $aSale){

            array_push($labels,$aSale->first_name."".$aSale->last_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);



        return view('reports.customer.detail',
            ["sales"=>$sales,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier,"customers"=>$customers]);

    }





}
