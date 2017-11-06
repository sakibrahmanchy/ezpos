<?php

namespace App\Http\Controllers\Reports;

use App\Enumaration\SaleStatus;
use App\Enumaration\SaleTypes;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
use App\Model\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReportController extends Controller
{

    public function ReportSaleGraphical(){

        $report_name = "Total";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "sales.created";

        $bindings = array($startDate, $endDate);
        $sql = "Select  sum(total_amount) as total, CAST(sales.created_at as DATE) as item_name from sales
                where id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.sale_type=".SaleTypes::$SALE."
                    and sales.deleted_at is null
                )
                group by CAST(sales.created_at as DATE)";


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.sale.graphical',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);
    }


    public function ReportSaleAjax(Request $request){


        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));
        $sale_type = $request->sale_type;

        $bindings = array($startDate, $endDate);
        $sql = "Select id, CAST(sales.created_at as DATE) as item_name,
                sum(sub_total_amount) as subtotal,
                sum(total_amount) as total,
                sum(tax_amount) as tax,
                sum(profit) as profit
                from sales
                where id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and sales.sale_type=".$sale_type."
                group by CAST(sales.created_at as DATE)";

        $items = DB::select($sql,$bindings);

        $labels = array();
        $values = array();
        foreach($items as $aSale){

            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }



        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);


        return response()->json(['sale'=>$items,'labels'=>$labels,"dataset"=>$values,"info"=>$info], 200);
    }

    public function ReportSaleSummary(){

        $report_name = "Total";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.item_id";

        $bindings = array($startDate, $endDate);
        $sql = "Select id, CAST(sales.created_at as DATE) as item_name,
                sum(sub_total_amount) as subtotal,
                sum(total_amount) as total,
                sum(tax_amount) as tax,
                sum(profit) as profit
                from sales
                where id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and sales.sale_type=".SaleTypes::$SALE."
                group by CAST(sales.created_at as DATE)" ;


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.sale.summary',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);

    }


    public function ReportSaleDetail(){

        $report_name = "Total";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "payment_type";

        $sales = Sale::where('created_at','>=',$startDate)->where('sale_type',SaleTypes::$SALE)
        ->where('created_at','<=',$endDate)->with('customer','paymentLogs','employee')->get();

        $labels = array();
        $values = array();
        foreach($sales as $aSale){

            array_push($labels,$aSale->first_name."".$aSale->last_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);


        return view('reports.sale.detail',
            ["sales"=>$sales,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);

    }

    public function ReportSaleDetailAjax(Request $request){

        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));
        $sale_type = $request->sale_type;

        $sales = Sale::where('created_at','>=',$startDate)->where('sale_type',$sale_type)
            ->where('created_at','<=',$endDate)->with('customer','paymentLogs','employee')->get();

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

    public function ReportSaleHourlyAjax(Request $request){

        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));
        $elapse = 1;
        $sale_type = $request->sale_type;


        $bindings = array($startDate, $endDate);
        $sql = "Select  sum(total_amount) as total,floor(hour(created_at) / $elapse) AS item_name,
                DATE_FORMAT(date(created_at) + interval $elapse * (hour(created_at) div $elapse) hour, '%H:%i')  as starttime,
                DATE_FORMAT(date(created_at) + interval $elapse * ((hour(created_at) div $elapse) + 1) hour, '%H:%i') as endtime,
                sum(sub_total_amount) as subtotal, sum(tax_amount) as tax, sum(profit) as profit
                from sales
                where id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and sales.sale_type=".$sale_type."
                group by item_name";


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            //array_push($labels,$aSale->starttime." - ".$aSale->endtime." ($".$aSale->total.")");
            array_push($labels,$aSale->starttime." - ".$aSale->endtime." ");
            array_push($values,$aSale->total);
        }




        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return response()->json(['sale'=>$items,'labels'=>$labels,"dataset"=>$values,"info"=>$info], 200);
    }

    public function ReportSaleGraphicalHourly(){

        $report_name = "Total";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "sales.created";

        $elapse = 1;

        $bindings = array($startDate, $endDate);
        $sql = "Select  sum(total_amount) as total,floor(hour(created_at) / $elapse) AS item_name,
                DATE_FORMAT(date(created_at) + interval $elapse * (hour(created_at) div $elapse) hour, '%H:%i')  as starttime,
                DATE_FORMAT(date(created_at) + interval $elapse * ((hour(created_at) div $elapse) + 1) hour, '%H:%i') as endtime
                from sales
                where id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and sales.sale_type=".SaleTypes::$SALE."
                group by item_name";


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->starttime." - ".$aSale->endtime." ");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.sale.graphical_hourly',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);
    }

    public function ReportSaleSummaryHourly(){

        $report_name = "Total";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "sales.created";

        $elapse = 1;

        $bindings = array($startDate, $endDate);
        $sql = "Select  sum(total_amount) as total,floor(hour(created_at) / $elapse) AS item_name,
                DATE_FORMAT(date(created_at) + interval $elapse * (hour(created_at) div $elapse) hour, '%H:%i')  as starttime,
                DATE_FORMAT(date(created_at) + interval $elapse * ((hour(created_at) div $elapse) + 1) hour, '%H:%i') as endtime,
                sum(sub_total_amount) as subtotal, sum(tax_amount) as tax, sum(profit) as profit
                from sales
                where id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and sales.sale_type=".SaleTypes::$SALE."
                group by item_name";


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->starttime." - ".$aSale->endtime." ");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.sale.summary_hourly',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);
    }

    public function ReportSuspendedDetail(){

        $report_name = "Total";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "payment_type";

        $sales = Sale::where('created_at','>=',$startDate)->where('created_at','<=',$endDate)
                    ->where(function ($query){
                        $query->where('sale_status',SaleStatus::$ESTIMATE)
                        ->oRWhere('sale_status',SaleStatus::$LAYAWAY);
                    })->with('customer','paymentLogs','employee')->get();


        $labels = array();
        $values = array();
        foreach($sales as $aSale){

            array_push($labels,$aSale->first_name."".$aSale->last_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);


        return view('reports.suspended_sale.detail',
            ["sales"=>$sales,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);

    }

    public function ReportSuspendedDetailAjax(Request $request){

        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        $sales = Sale::where('created_at','>=',$startDate)->where('created_at','<=',$endDate)
            ->where(function ($query){
                $query->where('sale_status',SaleStatus::$ESTIMATE)
                    ->oRWhere('sale_status',SaleStatus::$LAYAWAY);
            })->with('customer','paymentLogs','employee')->get();



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
