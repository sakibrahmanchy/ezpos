<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManufacturerReportController extends Controller
{

    public function ReportManufacturerGraphical(){

        $report_name = "item_name";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "item_sale.itemkit_percentage";

        $bindings = array($startDate, $endDate);
        $sql = "Select sum(total_price) as total, IFNULL(manufacturers.manufacturer_name, 'No manufactuer') as item_name from item_sale
                join items on items.id = item_sale.item_id
                left join manufacturers on items.manufacturer_id = manufacturers.id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and items.product_type <> 2
                and items.deleted_at is null
                group by items.manufacturer_id" ;


        $items = DB::select($sql,$bindings);

        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.manufacturer.graphical',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);
    }


    public function ReportManufacturerAjax(Request $request){


        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        $bindings = array($startDate, $endDate);
        $sql = "Select IFNULL(manufacturers.manufacturer_name, 'No manufactuer') as item_name, sum(item_sale.quantity) as item_count ,sum(total_price) as subtotal,
                sum(total_price+tax_amount) as total, sum(tax_amount) as tax,  sum(item_profit) as profit
                from item_sale
                join items on items.id = item_sale.item_id
                left join manufacturers on items.manufacturer_id = manufacturers.id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and items.product_type <> 2
                and items.deleted_at is null
                group by items.manufacturer_id" ;


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

    public function ReportManufacturerSummary(){

        $report_name = "item_percentage";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.item_id";

        $bindings = array($startDate, $endDate);
        $sql = "Select IFNULL(manufacturers.manufacturer_name, 'No manufactuer') as item_name, sum(item_sale.quantity) as item_count ,sum(total_price) as subtotal,
                sum(total_price+tax_amount) as total, sum(tax_amount) as tax,  sum(item_profit) as profit
                from item_sale
                join items on items.id = item_sale.item_id
                left join manufacturers on items.manufacturer_id = manufacturers.id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and items.product_type <> 2
                and items.deleted_at is null
                group by items.manufacturer_id" ;


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.manufacturer.summary',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);

    }






}
