<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentReportController extends Controller
{

    public function ReportPaymentGraphical(){

        $report_name = "item_name";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "payment_log_id";

        $bindings = array($startDate, $endDate);
        $sql = "Select sum(total_amount) as total, payment_type as item_name from payment_logs
                join payment_log_sale on payment_logs.id = payment_log_sale.payment_log_id
                join sales on sales.id = payment_log_sale.sale_id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                group by payment_type";


        $items = DB::select($sql,$bindings);

        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.payment.graphical',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);
    }


    public function ReportPaymentAjax(Request $request){


        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        $bindings = array($startDate, $endDate);
        $sql = "Select sale_id, payment_logs.created_at as payment_date, sales.created_at as sale_date,
                sum(total_amount) as total_amount, payment_type as item_name from payment_logs
                join payment_log_sale on payment_logs.id = payment_log_sale.payment_log_id
                join sales on sales.id = payment_log_sale.sale_id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                group by payment_type";

        $items = DB::select($sql,$bindings);

        $labels = array();
        $values = array();
        foreach($items as $aSale){

            array_push($labels,$aSale->item_name." ($".$aSale->total_amount.")");
            array_push($values,$aSale->total_amount);
        }



        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);


        return response()->json(['sale'=>$items,'labels'=>$labels,"dataset"=>$values,"info"=>$info], 200);
    }

    public function ReportPaymentSummary(){

        $report_name = "item_percentage";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.item_id";

        $bindings = array($startDate, $endDate);
        $sql = "Select sum(total_amount) as total, payment_type as item_name from payment_logs
                join payment_log_sale on payment_logs.id = payment_log_sale.payment_log_id
                join sales on sales.id = payment_log_sale.sale_id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                group by payment_type" ;


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.payment.summary',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);

    }


    public function ReportPaymentDetail(){

        $report_name = "payment_type";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "payment_type";

        $bindings = array($startDate, $endDate);
        $sql = "Select sale_id, payment_logs.created_at as payment_date, sales.created_at as sale_date,
                sum(total_amount) as total_amount, payment_type as item_name from payment_logs
                join payment_log_sale on payment_logs.id = payment_log_sale.payment_log_id
                join sales on sales.id = payment_log_sale.sale_id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                group by payment_type";

        $sales = DB::select($sql,$bindings);

        $labels = array();
        $values = array();
        foreach($sales as $aSale){

            array_push($labels,$aSale->item_name." ($".$aSale->total_amount.")");
            array_push($values,$aSale->total_amount);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);



        return view('reports.payment.detail',
            ["sales"=>$sales,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);

    }





}
