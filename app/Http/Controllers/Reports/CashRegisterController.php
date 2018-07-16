<?php

namespace App\Http\Controllers\Reports;

use App\Model\CashRegisterTransaction;
use App\Enumaration\CashRegisterTransactionType;
use App\Http\Controllers\Controller;
use App\Model\CashRegister;
use App\Model\PaymentLog;
use Illuminate\Http\Request;

class CashRegisterController extends Controller{

    public function detailedReports(){

        $report_name = "register_log_details";
        $reportTotal = new ReportTotal();
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $info = $reportTotal->reportInfo($startDate,$endDate);

        $cashRegisters = CashRegister::with('OpenedByUser','ClosedByUser',
            'CashRegisterTransactions','Counter','additionSum','subtractionSum','saleSum')
            ->whereDate('opening_time', '>=', $startDate)
            ->whereDate('closing_time', '<=', $endDate)->get();

        $info = $this->totalDetails($startDate,$endDate);

        //dd($cashRegisters[6]->subtractionSum[0]->aggregate);
        return view('reports.cash_register.cash_register_log_details',
            ["info"=>$info,
                "report_name"=>$report_name,"cash_registers"=>$cashRegisters]);
    }

    public function detailedReportsAjax(Request $request){

        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        $cashRegisters = CashRegister::with('OpenedByUser','ClosedByUser',
            'CashRegisterTransactions','Counter','additionSum','subtractionSum','saleSum')
            ->where('opening_time', '>=', $startDate)
            ->where('closing_time', '<=', $endDate)->get();

        $info = $this->totalDetails($startDate,$endDate);


        $view = view('reports.cash_register.details_table',["info"=>$info,"cash_registers"=>$cashRegisters]);

        $contents = (string) $view->render();

        return response()->json(["contents"=>$contents,"info"=>$info],200);

    }

    public function totalDetails($startDate,$endDate)
    {
        $cashSales = PaymentLog::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->where("payment_type",CashRegisterTransactionType::$CASH_SALES)->sum('paid_amount');

        $cashRegisters = CashRegister::with('CashRegisterTransactions','additionSum','subtractionSum','saleSum')
            ->whereDate('opening_time', '>=', $startDate)
                        ->whereDate('closing_time', '<=', $endDate)->get();

        $total_shortages = 0;
        $total_overages = 0;
        $total_difference = 0;

        foreach($cashRegisters as $aCashRegister){

            if(isset($aCashRegister->saleSum[0]))
                $saleSum = $aCashRegister->saleSum[0]->aggregate;
            else
                $saleSum = 0;
            if(isset($aCashRegister->additionSum[0]))
                $additionSum = $aCashRegister->additionSum[0]->aggregate;
            else
                $additionSum = 0;
            if(isset($aCashRegister->subtractionSum[0]))
                $subtractionSum = $aCashRegister->subtractionSum[0]->aggregate;
            else
                $subtractionSum = 0;

            $difference = ($aCashRegister->closing_balance) - ($aCashRegister->opening_balance + $saleSum + $additionSum - $subtractionSum);



            if($difference>=0)
                $total_overages += $difference;

            if($difference<0)
                $total_shortages += $difference;

            $total_difference += $difference;
        }

        $data['cash_sales'] = number_format($cashSales,2);
        $data['total_shortages'] = number_format($total_shortages,2);
        $data['total_overages'] =number_format( $total_overages,2);
        $data['total_difference'] = number_format($total_difference,2);

        return $data;
    }
}