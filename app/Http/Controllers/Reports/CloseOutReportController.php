<?php

namespace App\Http\Controllers\Reports;

use App\Enumaration\SaleTypes;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
use App\Model\Item;
use App\Model\PaymentLog;
use App\Model\Sale;
use App\Model\CloseOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CloseOutReportController extends Controller
{

    public function ReportCloseOutSummary(){


        $info = Sale::select(DB::raw("sum(sub_total_amount) as subtotal, sum(total_amount) as total, sum(items_sold) as items_sold,
                                sum(profit) as profit, sum(tax_amount) as tax, count(*) as no_transactions"))->first();

        $infoSales = Sale::select(DB::raw("sum(sub_total_amount) as subtotal, sum(total_amount) as total, sum(items_sold) as items_sold,
                                sum(profit) as profit, sum(tax_amount) as tax, count(*) as no_transactions"))
                                ->where("sale_type",SaleTypes::$SALE)->first();

        $infoReturns = Sale::select(DB::raw("sum(sub_total_amount) as subtotal, sum(total_amount) as total, sum(items_sold) as items_sold,
                                sum(profit) as profit, sum(tax_amount) as tax, count(*) as no_transactions"))
            ->where("sale_type",SaleTypes::$RETURN)->first();


        $return = Sale::where("sale_type",SaleTypes::$RETURN)->sum("total_amount");


        $discount = DB::table('item_sale')
                 ->sum("discount_amount");

        $numberOfDiscounts = DB::Table('item_sale')->where('discount_amount','>',0)->count('discount_amount');


        $saleIds = Sale::select('id')->get();

        $sales = DB::table('payment_logs')
            ->selectRaw('payment_type, sum(paid_amount) as paid_amount')
            ->join('payment_log_sale','payment_logs.id','=','payment_log_sale.payment_log_id')
            ->whereIn('sale_id',$saleIds)
            ->groupBy('payment_type')->get();

        $saleIds = Sale::where('sale_type',SaleTypes::$SALE)->select('id')
            ->get();

        $salePayments = DB::table('payment_logs')
            ->selectRaw('payment_type, sum(paid_amount) as paid_amount')
            ->join('payment_log_sale','payment_logs.id','=','payment_log_sale.payment_log_id')
            ->whereIn('sale_id',$saleIds)
            ->groupBy('payment_type')->get();

        $saleIds = Sale::where('sale_type',SaleTypes::$RETURN)->select('id')
            ->get();

        $returnPayments = DB::table('payment_logs')
            ->selectRaw('payment_type, sum(paid_amount) as paid_amount')
            ->join('payment_log_sale','payment_logs.id','=','payment_log_sale.payment_log_id')
            ->whereIn('sale_id',$saleIds)
            ->groupBy('payment_type')->get();

        $inventoryItems = Item::sum('item_quantity');
        $totalInventoryValues = Item::where("product_type",0)->sum(DB::raw('item_quantity * cost_price'));

        return view('reports.close_out.summary',["info"=>$info,"return"=>$return,"discount"=>$discount,
                     "sales"=>$sales,"infoSales"=>$infoSales,"infoReturns"=>$infoReturns, "salePayments"=>$salePayments,
                     "returnPayments"=>$returnPayments,"numberOfDiscounts"=>$numberOfDiscounts,
                      "inventoryItems"=>$inventoryItems,"totalInventoryValues"=>$totalInventoryValues]);
    }


    public function ReportCloseOutAjax(Request $request){

        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        $info = Sale::select(DB::raw("sum(sub_total_amount) as subtotal, sum(total_amount) as total, sum(items_sold) as items_sold,
                                sum(profit) as profit, sum(tax_amount) as tax, count(*) as no_transactions"))->where('created_at','>=',$startDate)
            ->where('created_at','<=',$endDate)->first();

        $infoSales = Sale::select(DB::raw("sum(sub_total_amount) as subtotal, sum(total_amount) as total, sum(items_sold) as items_sold,
                                sum(profit) as profit, sum(tax_amount) as tax, count(*) as no_transactions"))->where('created_at','>=',$startDate)
            ->where('created_at','<=',$endDate)
            ->where("sale_type",SaleTypes::$SALE)->first();

        $infoReturns = Sale::select(DB::raw("sum(sub_total_amount) as subtotal, sum(total_amount) as total, sum(items_sold) as items_sold,
                                sum(profit) as profit, sum(tax_amount) as tax, count(*) as no_transactions"))->where('created_at','>=',$startDate)
            ->where('created_at','<=',$endDate)
            ->where("sale_type",SaleTypes::$RETURN)->first();

        $return = Sale::where("sale_type",SaleTypes::$RETURN)
            ->where('created_at','>=',$startDate)
            ->where('created_at','<=',$endDate)
            ->sum("total_amount");


        $saleIds = Sale::select('id')
            ->where('created_at','>=',$startDate)
            ->where('created_at','<=',$endDate)
            ->get();

        $discount = DB::table('item_sale')
            ->whereIn('sale_id',$saleIds)
            ->sum("discount_amount");

        $numberOfDiscounts = DB::Table('item_sale')->where('discount_amount','>',0)->count('discount_amount');

        $sales = DB::table('payment_logs')
            ->selectRaw('payment_type, sum(paid_amount) as paid_amount')
            ->join('payment_log_sale','payment_logs.id','=','payment_log_sale.payment_log_id')
            ->whereIn('sale_id',$saleIds)
            ->groupBy('payment_type')->get();

        $saleIds = Sale::where('sale_type',SaleTypes::$SALE)
            ->where('created_at','>=',$startDate)
            ->where('created_at','<=',$endDate)
            ->select('id')
            ->get();

        $salePayments = DB::table('payment_logs')
            ->selectRaw('payment_type, sum(paid_amount) as paid_amount')
            ->join('payment_log_sale','payment_logs.id','=','payment_log_sale.payment_log_id')
            ->whereIn('sale_id',$saleIds)
            ->groupBy('payment_type')->get();

        $saleIds = Sale::where('sale_type',SaleTypes::$RETURN)
            ->where('created_at','>=',$startDate)
            ->where('created_at','<=',$endDate)
            ->select('id')
            ->get();

        $returnPayments = DB::table('payment_logs')
            ->selectRaw('payment_type, sum(paid_amount) as paid_amount')
            ->join('payment_log_sale','payment_logs.id','=','payment_log_sale.payment_log_id')
            ->whereIn('sale_id',$saleIds)
            ->groupBy('payment_type')->get();

        $inventoryItems = Item::sum('item_quantity');
        $totalInventoryValues = Item::where("product_type",0)->sum(DB::raw('item_quantity * cost_price'));

        $view = view('reports.close_out.summary_table',["info"=>$info,"return"=>$return,"discount"=>$discount,
            "sales"=>$sales,"infoSales"=>$infoSales,"infoReturns"=>$infoReturns, "salePayments"=>$salePayments,
            "returnPayments"=>$returnPayments,"numberOfDiscounts"=>$numberOfDiscounts,
            "inventoryItems"=>$inventoryItems,"totalInventoryValues"=>$totalInventoryValues]);

        $contents = (string) $view->render();


        return response()->json(["contents"=>$contents],200);

    }









}
