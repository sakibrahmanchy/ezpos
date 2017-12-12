<?php

namespace App\Http\Controllers\Reports;

use App\Enumaration\SaleTypes;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
use App\Model\PaymentLog;
use App\Model\Sale;
use App\Model\ProfitLoss;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitLossReportController extends Controller
{

    public function ReportProfitLossSummary(){


        $info = Sale::select(DB::raw("sum(total_amount) as total, sum(profit) as profit, sum(tax_amount) as tax"))->first();
        $return = Sale::where("sale_type",SaleTypes::$RETURN)->sum("total_amount");
        $discount = DB::table('item_sale')
                 ->sum("discount_amount");

        return view('reports.profits_and_loss.summary',["info"=>$info,"return"=>$return,"discount"=>$discount]);
    }


    public function ReportProfitLossAjax(Request $request){

        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));


        if(isset($request->report_name)){


            $saleIds = Sale::where('sale_type',SaleTypes::$SALE)->select('id')
                        ->where('created_at','>=',$startDate)
                        ->where('created_at','<=',$endDate)
                        ->get();

            $sales = DB::table('payment_logs')
                ->selectRaw('payment_type, sum(paid_amount) as paid_amount')
                ->join('payment_log_sale','payment_logs.id','=','payment_log_sale.payment_log_id')
                ->whereIn('sale_id',$saleIds)
                ->groupBy('payment_type')->get();

            $returnIds = Sale::where('sale_type',SaleTypes::$RETURN)->select('id')
                ->where('created_at','>=',$startDate)
                ->where('created_at','<=',$endDate)->get();

            $returns = DB::table('payment_logs')
                ->selectRaw('payment_type, sum(paid_amount) as paid_amount')
                ->join('payment_log_sale','payment_logs.id','=','payment_log_sale.payment_log_id')
                ->whereIn('sale_id',$returnIds)
                ->groupBy('payment_type')->get();

            $info = Sale::select(DB::raw("sum(total_amount) as total, sum(profit) as profit, sum(tax_amount) as tax"))
                    ->where('created_at','>=',$startDate)
                    ->where('created_at','<=',$endDate)
                    ->first();

            $return = Sale::where("sale_type",SaleTypes::$RETURN)
                    ->where('created_at','>=',$startDate)
                    ->where('created_at','<=',$endDate)->sum("total_amount");

            $saleIds = Sale::select('id')
                ->where('created_at','>=',$startDate)
                ->where('created_at','<=',$endDate)
                ->get();

            $discount = DB::table('item_sale')
                ->whereIn("sale_id",$saleIds)
                ->sum("discount_amount");

            $total = $info["total"]-$return-$discount-$info["tax"];

            return response()->json(['sales'=>$sales,"returns"=>$returns,"info"=>$info,
                "return"=>$return,"discount"=>$discount,"total"=>$total],200);

        }
        else{



            $info = DB::table('sales')
                ->select(DB::raw("sum(sales.total_amount) as total"), DB::raw("sum(profit) as profit"),
                    DB::raw("sum(tax_amount) as tax"))
                ->where("created_at",">=",$startDate)
                ->where("created_at","<=",$endDate)
                ->first();


            $return = Sale::where("sale_type",SaleTypes::$RETURN)
                ->where('created_at','>=',$startDate)->where('created_at','<=',$endDate)->sum("total_amount");

            $discount = DB::table('item_sale')
                ->whereIn('sale_id',function($query) use($startDate, $endDate){
                    $query->select('id')->from(with (new Sale)->getTable())
                        ->where('created_at','>=',$startDate)->where('created_at','<=',$endDate);
                })
                ->sum("discount_amount");


            return response()->json(["info"=>$info,"return"=>$return,"discount"=>$discount],200);

        }



    }



    public function ReportProfitLossDetail(){

        $saleIds = Sale::where('sale_type',SaleTypes::$SALE)->select('id')->get();

        $sales = DB::table('payment_logs')
                ->selectRaw('payment_type, sum(paid_amount) as paid_amount')
                ->join('payment_log_sale','payment_logs.id','=','payment_log_sale.payment_log_id')
                ->whereIn('sale_id',$saleIds)
                ->groupBy('payment_type')->get();

        $returnIds = Sale::where('sale_type',SaleTypes::$RETURN)->select('id')->get();

        $returns = DB::table('payment_logs')
            ->selectRaw('payment_type, sum(paid_amount) as paid_amount')
            ->join('payment_log_sale','payment_logs.id','=','payment_log_sale.payment_log_id')
            ->whereIn('sale_id',$returnIds)
            ->groupBy('payment_type')->get();

        $info = Sale::select(DB::raw("sum(total_amount) as total, sum(profit) as profit, sum(tax_amount) as tax"))->first();
        $return = Sale::where("sale_type",SaleTypes::$RETURN)->sum("total_amount");
        $discount = DB::table('item_sale')
            ->sum("discount_amount");

        $total = $info["total"]-$return-$discount-$info["tax"];

        return view('reports.profits_and_loss.detail',
            ['sales'=>$sales,"returns"=>$returns,"info"=>$info,"return"=>$return,"discount"=>$discount,"total"=>$total]);
    }






}
