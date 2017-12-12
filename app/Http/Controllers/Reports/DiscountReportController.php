<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountReportController extends Controller
{

    public function ReportDiscountGraphical(){

        $report_name = "item_discount_percentage";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "item_sale.discount_percentage";

        $bindings = array($startDate, $endDate);
        $sql = "Select sum(total_price) as total, item_discount_percentage from item_sale
                join items on items.id = item_sale.item_id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and items.deleted_at is null
                group by item_sale.item_discount_percentage" ;


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_discount_percentage."% ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.discount.graphical',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);
    }


    public function ReportDiscountAjax(Request $request){


        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        $bindings = array($startDate, $endDate);
        $sql = "Select item_discount_percentage, sum(item_sale.quantity) as item_count ,sum(total_price) as subtotal,
                sum(total_price+tax_amount) as total, sum(tax_amount) as tax,  sum(item_profit) as profit
                from item_sale
                join items on items.id = item_sale.item_id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and items.deleted_at is null
                group by item_sale.item_discount_percentage" ;


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_discount_percentage."% ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }



        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return response()->json(['sale'=>$items,'labels'=>$labels,"dataset"=>$values,"info"=>$info], 200);
    }

    public function ReportDiscountSummary(){

        $report_name = "discount_percentage";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.discount_id";

        $bindings = array($startDate, $endDate);
        $sql = "Select item_discount_percentage, sum(item_sale.quantity) as item_count ,sum(total_price) as subtotal,
                sum(total_price+tax_amount) as total, sum(tax_amount) as tax,  sum(item_profit) as profit
                from item_sale
                join items on items.id = item_sale.item_id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and items.deleted_at is null
                group by item_sale.item_discount_percentage" ;


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_discount_percentage."% ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.discount.summary',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);

    }






}
