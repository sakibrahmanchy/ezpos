<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
use App\Model\Category;
use App\Model\InventoryLog;
use App\Model\Item;
use App\Model\PaymentLog;
use App\Model\Sale;
use App\Model\Inventory;
use App\Model\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryReportController extends Controller
{

    public function ReportInventoryGraphical(){

        $report_name = "Total";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "item_sale.itemkit_percentage";

        $bindings = array($startDate, $endDate);
        $sql = <<<EOT
        (
            Select sum(total_price) as total, IFNULL(suppliers.company_name, 'No supplier') as item_name from item_sale
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
        )
EOT;

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


    public function ReportInventoryAjax(Request $request){

        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        if($request->report_type == "inventory_summary"){

            $supplier_id = $request->supplier_id;
            $category_id = $request->category_id;
            $stock_type = $request->stock_type;

            if($supplier_id==0 && $category_id ==0 && $stock_type == 0)
                $items = Item::where('product_type',0)->with('supplier','category')->get();
            else if($supplier_id!=0){

                $items = Item::where('product_type',0)->where('supplier_id',$supplier_id)->with('supplier','category')->get();

            }else if($category_id!=0){

                $items = Item::where('product_type',0)->where("category_id",$category_id)->with('supplier','category')->get();

            }else if($stock_type!=0){

                if($stock_type==1){
                    $query = 'items.item_quantity <= items.item_reorder_level';
                }else{
                    $query = 'items.item_quantity <= 0';
                }

                $items = Item::WhereRaw($query)->where('product_type',0)->with('supplier','category')->get();
            }

            return response()->json(['sale'=>$items], 200);

        }
        else if($request->report_type == "inventory_detail"){

            $item_id = $request->item_id;

            if($item_id!=0)
                $sales = InventoryLog::with('item','user')->where('item_id',$item_id)->where("created_at",">=",$startDate)
                    ->where("created_at","<=",$endDate)->get();
            else
                $sales = InventoryLog::with('item','user')->where("created_at",">=",$startDate)
                    ->where("created_at","<=",$endDate)->get();

            return response()->json(['sale'=>$sales], 200);

        }
        else if($request->report_type == "inventory_low"){

            $supplier_id = $request->supplier_id;
            $category_id = $request->category_id;
            $stock_type = $request->stock_type;

            if($supplier_id==0 && $category_id ==0 && $stock_type == 0)
                $items = Item::where(function($query){
                    $query->where('item_quantity','<=',0)
                        ->orWhereRaw('items.item_quantity <= items.item_reorder_level');
                })->where('product_type',0)->with('supplier','category')->get();
            else if($supplier_id!=0){

                $items = Item::where(function($query){
                    $query->where('item_quantity','<=',0)
                        ->orWhereRaw('items.item_quantity <= items.item_reorder_level');
                })->where('product_type',0)->where('supplier_id',$supplier_id)->with('supplier','category')->get();

            }else if($category_id!=0){
                $items = Item::where(function($query){
                    $query->where('item_quantity','<=',0)
                        ->orWhereRaw('items.item_quantity <= items.item_reorder_level');
                })->where('product_type',0)->where("category_id",$category_id)->with('supplier','category')->get();
            }else if($stock_type!=0){
                if($stock_type==1){
                    $query = 'items.item_quantity <= items.item_reorder_level';
                }else{
                    $query = 'items.item_quantity <= 0';
                }

                $items = Item::WhereRaw($query)->where('product_type',0)->with('supplier','category')->get();
            }

            return response()->json(['sale'=>$items], 200);

        }

    }

    public function ReportInventorySummary(){

        $suppliers = Supplier::all();

        $categories = Category::all();

        $items = Item::where('product_type',0)->with('supplier','category')->get();

        $info['totalItems'] = Item::where("product_type",0)->sum('item_quantity');

        $info['inventoryValue'] =  Item::where("product_type",0)->sum(DB::raw('item_quantity * cost_price'));

        $info['inventoryValueByUnit'] = Item::where("product_type",0)->sum(DB::raw('item_quantity * selling_price'));
        if($info['totalItems']!=0)
            $info['weightedAverage'] = $info['inventoryValue']/$info['totalItems'];
        else
            $info['weightedAverage'] = 0;
        return view('reports.inventory.summary',['suppliers'=>$suppliers,'categories'=>$categories,"items"=>$items,"info"=>$info]);


    }

    public function ReportInventoryDetail(){


        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));


        $itemNames = Item::all();

        $items = InventoryLog::with('item','user')->where('created_at','>=',$startDate)->where('created_at','<=',$endDate)->get();

        return view('reports.inventory.detail',
            ["items"=>$itemNames,"sales"=>$items]);

    }


    public function ReportInventoryLow(){

            $suppliers = Supplier::all();
            $categories = Category::all();

            $items = Item::where(function($query){
                   $query->where('item_quantity','<=',0)
                         ->orWhereRaw('items.item_quantity <= items.item_reorder_level');
            })->where('product_type',0)->with('supplier','category')->get();


            return view('reports.inventory.low_inventory',['suppliers'=>$suppliers,'categories'=>$categories,"items"=>$items]);
    }






}
