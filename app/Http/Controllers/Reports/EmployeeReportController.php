<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SaleController;
use App\Model\Employee;
use App\Model\Sale;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeReportController extends Controller
{

    public function ReportEmployeeGraphical(){

        $report_name = "employee_name";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.employee_id";

        $bindings = array($startDate, $endDate);
        $sql = "Select sum(sub_total_amount) as subtotal ,sum(total_amount) as total,
                sum(tax_amount) as tax, sum(profit) as profit, IFNULL(users.name,'No Employee') as first_name
                from sales
                left join users on users.id = sales.employee_id
                where sales.id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                group by sales.employee_id" ;


        $items = DB::select($sql,$bindings);


        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->first_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.employee.graphical',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);
    }


    public function ReportEmployeeAjax(Request $request){

        $startDate = $request->start_date_formatted;
        $endDate =  $request->end_date_formatted;
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate)));

        if(!isset($request->employee_id)){

            $bindings = array($startDate, $endDate);
            $sql = "Select sum(sub_total_amount) as subtotal ,sum(total_amount) as total,
                sum(tax_amount) as tax, sum(profit) as profit, IFNULL(users.name,'No Employee') as first_name, sum(items_sold) as items_sold
                from sales
                left join users on users.id = sales.employee_id
                where sales.id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                group by sales.employee_id" ;


            $items = DB::select($sql,$bindings);

            $labels = array();
            $values = array();
            foreach($items as $aSale){
                array_push($labels,$aSale->first_name."".$aSale->last_name." ($".$aSale->total.")");
                array_push($values,$aSale->total);
            }

            $reportTotal = new ReportTotal();
            $info = $reportTotal->reportInfo($startDate,$endDate);

            return response()->json(['sale'=>$items,'labels'=>$labels,"dataset"=>$values,"info"=>$info], 200);
        }else{

            $employee_id = $request->employee_id;
            $sales = Sale::where('employee_id',$employee_id)->where('created_at','>=',$startDate)->where('created_at','<=',$endDate)->with('customer','paymentLogs','employee')->get();

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

    public function ReportEmployeeSummary(){

        $report_name = "employee_name";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.employee_id";

        $bindings = array($startDate, $endDate);
        $sql = "Select sum(sub_total_amount) as subtotal ,sum(total_amount) as total,
                sum(tax_amount) as tax, sum(profit) as profit, IFNULL(users.name,'No Employee') as first_name, sum(items_sold) as items_sold
                from sales
                left join users on users.id = sales.employee_id
                where sales.id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.deleted_at is null
                )
                group by sales.employee_id" ;

        $items = DB::select($sql,$bindings);

        $labels = array();
        $values = array();
        foreach($items as $aSale){
            array_push($labels,$aSale->first_name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);

        return view('reports.employee.summary',
            ["sales"=>$items,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier]);

    }

    public function ReportEmployeeDetail(){

        $report_name = "employee_name";
        $modifier = "";
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        $orderBy = "items.employee_id";

        $employees = User::all();

        $user_id = "1";
        $sales = Sale::where('employee_id',$user_id)->where('created_at','>=',$startDate)->where('created_at','<=',$endDate)->with('customer','paymentLogs','employee')->get();

        $labels = array();
        $values = array();
        foreach($sales as $aSale){

            array_push($labels,$aSale->employee->name." ($".$aSale->total.")");
            array_push($values,$aSale->total);
        }

        $reportTotal = new ReportTotal();
        $info = $reportTotal->reportInfo($startDate,$endDate);



        return view('reports.employee.detail',
            ["sales"=>$sales,"labels"=> $labels,
                "datasets"=>$values,"info"=>$info, "report_type"=>$orderBy,
                "report_name"=>$report_name, "modifier"=>$modifier,"employees"=>$employees]);

    }




}
