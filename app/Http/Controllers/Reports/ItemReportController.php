<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
use App\Model\ImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemReportController extends Controller
{

    public function ReportItemGraphical(){

        $report_name = "item_name";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "item_sale.itemkit_percentage";

        $bindings = array($startDate, $endDate);
        $sql = "Select sum(total_price) as total, items.item_name from item_sale
                join items on items.id = item_sale.item_id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and items.product_type = '0'
                and items.deleted_at is null
                group by items.item_name" ;


        $items = DB::select($sql,$bindings);

        $labels = array();
        $values = array();
        foreach($items as $aSale){
            $aSale->item_name = str_replace("'"," ",$aSale->item_name);
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $sql = "Select sum(total_price) as subtotal,
                sum(total_price+tax_amount) as total, sum(tax_amount) as tax,  sum(item_profit) as profit
                from item_sale
                join items on items.id = item_sale.item_id
                 where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and product_type = '0'";
        $bindings = array($startDate, $endDate);

        $total = DB::select($sql,$bindings);

        $info["total"] = $total[0]->total;
        $info["subtotal"] = $total[0]->subtotal;
        $info["tax"] = $total[0]->tax;
        $info["profit"] = $total[0]->profit;

        return view('reports.item.graphical',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);
    }


    public function ReportItemAjax(Request $request){


        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        $bindings = array($startDate, $endDate);
        $sql = "Select item_name, sum(item_sale.quantity) as item_count ,sum(total_price) as subtotal,
                sum(total_price+tax_amount) as total, sum(tax_amount) as tax,  sum(item_profit) as profit
                from item_sale
                join items on items.id = item_sale.item_id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                 and items.product_type = '0'
                and items.deleted_at is null
                group by items.item_name" ;


        $items = DB::select($sql,$bindings);

        $labels = array();
        $values = array();
        foreach($items as $aSale){
            $aSale->item_name = str_replace("''"," ",$aSale->item_name);
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $sql = "Select sum(total_price) as subtotal,
                sum(total_price+tax_amount) as total, sum(tax_amount) as tax,  sum(item_profit) as profit
                from item_sale
                join items on items.id = item_sale.item_id
                 where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and product_type = '0'";

        $bindings = array($startDate, $endDate);

        $total = DB::select($sql,$bindings);

        $info["total"] = $total[0]->total;
        $info["subtotal"] = $total[0]->subtotal;
        $info["tax"] = $total[0]->tax;
        $info["profit"] = $total[0]->profit;


        return response()->json(['sale'=>$items,'labels'=>$labels,"dataset"=>$values,"info"=>$info], 200);
    }

    public function ReportItemSummary(){

        $report_name = "item_percentage";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.item_id";

        $bindings = array($startDate, $endDate);
        $sql = "Select item_name, sum(item_sale.quantity) as item_count ,sum(total_price) as subtotal,
                sum(total_price+tax_amount) as total, sum(tax_amount) as tax,  sum(item_profit) as profit
                from item_sale
                join items on items.id = item_sale.item_id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                 and items.product_type = '0'
                and items.deleted_at is null
                group by items.item_name" ;


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            $aSale->item_name = str_replace("''"," ",$aSale->item_name);
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $sql = "Select sum(total_price) as subtotal,
                sum(total_price+tax_amount) as total, sum(tax_amount) as tax,  sum(item_profit) as profit
                from item_sale
                join items on items.id = item_sale.item_id
                 where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and product_type = '0'";

        $bindings = array($startDate, $endDate);

        $total = DB::select($sql,$bindings);

        $info["total"] = $total[0]->total;
        $info["subtotal"] = $total[0]->subtotal;
        $info["tax"] = $total[0]->tax;
        $info["profit"] = $total[0]->profit;

        return view('reports.item.summary',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);

    }


    public function ReportItemImportLog(){

        $importLogs = ImportLog::orderBy('created_at','desc')->with('User')->get();

        return view('reports.item.item_import_log',["logs"=>$importLogs]);

    }


}
