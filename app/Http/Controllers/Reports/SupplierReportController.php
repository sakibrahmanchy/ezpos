<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
use App\Model\PaymentLog;
use App\Model\Sale;
use App\Model\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierReportController extends Controller
{

    public function ReportSupplierGraphical(){

        $report_name = "Total";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "item_sale.itemkit_percentage";

        $bindings = array($startDate, $endDate);
        $sql = "Select sum(total_price) as total, IFNULL(suppliers.company_name, 'No supplier') as item_name from item_sale
                join items on items.id = item_sale.item_id
                left join suppliers on items.supplier_id = suppliers.id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and items.product_type <> 2
                and items.deleted_at is null
                group by items.supplier_id" ;


        $items = DB::select($sql,$bindings);

        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.supplier.graphical',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);
    }


    public function ReportSupplierAjax(Request $request){


        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        if(!isset($request->supplier_id)){

            $bindings = array($startDate, $endDate);
            $sql = "Select IFNULL(suppliers.company_name, 'No supplier') as item_name, sum(item_sale.quantity) as item_count ,sum(total_price) as subtotal,
                sum(total_price+tax_amount) as total, sum(tax_amount) as tax,  sum(item_profit) as profit
                from item_sale
                join items on items.id = item_sale.item_id
                left join suppliers on items.supplier_id = suppliers.id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and items.product_type <> 2
                and items.deleted_at is null
                group by items.supplier_id" ;


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

        }else{

            $supplier_id = $request->supplier_id;


            $sales = Sale::with(['items' => function($query ) use ($supplier_id)  {
            }, 'items.supplier' => function($query) use ($supplier_id) {
                $query->where('id', $supplier_id);
            },'employee','customer','paymentLogs'])->where("created_at",">=",$startDate)
                ->where("created_at","<=",$endDate)->get();


            $labels = array();
            $values = array();
            foreach($sales as $aSale){

                array_push($labels,$aSale->company_name."($".$aSale->total.")");
                array_push($values,$aSale->total);
            }


            $reportTotal = new ReportTotal();
            $info = $reportTotal->reportInfo($startDate,$endDate);


            return response()->json(['sale'=>$sales,'labels'=>$labels,"dataset"=>$values,"info"=>$info], 200);

        }

    }

    public function ReportSupplierSummary(){

        $report_name = "Total";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.item_id";

        $bindings = array($startDate, $endDate);
        $sql = "Select IFNULL(suppliers.company_name, 'No supplier') as item_name, sum(item_sale.quantity) as item_count ,sum(total_price) as subtotal,
                sum(total_price+tax_amount) as total, sum(tax_amount) as tax,  sum(item_profit) as profit
                from item_sale
                join items on items.id = item_sale.item_id
                left join suppliers on items.supplier_id = suppliers.id
                where sale_id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                and items.product_type <> 2
                and items.deleted_at is null
                group by items.supplier_id" ;


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.supplier.summary',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);

    }

    public function ReportSupplierDetail(){

        $report_name = "Total";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.item_id";

        $suppliers = Supplier::all();
        $supplier_id = 0;
        $items = Sale::with(['items' => function($query ) use ($supplier_id)  {
        }, 'items.supplier' => function($query) use ($supplier_id) {
            $query->where('id', $supplier_id);
        },'employee','customer','paymentLogs'])->where("created_at",">=",$startDate)
            ->where("created_at","<=",$endDate)->get();

        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->item_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.supplier.detail',
            ["suppliers"=>$suppliers,"sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);



    }






}
