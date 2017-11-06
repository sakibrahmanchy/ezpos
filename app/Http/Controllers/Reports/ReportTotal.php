<?php


namespace app\Http\Controllers\Reports;


use Illuminate\Support\Facades\DB;

class ReportTotal
{

    public function reportInfo($start_date,$end_date){

        $sql = "Select sum(sub_total_amount) as subtotal, sum(tax_amount) as tax ,sum(total_amount) as total, sum(profit) as profit
                    from sales where sales.created_at >= '".$start_date."'
                     and sales.created_at <= '".$end_date."'
                     and sales.deleted_at is null";


        $result = DB::select($sql);

        $data['subtotal'] = number_format($result[0]->subtotal,2);
        $data['tax'] = number_format($result[0]->tax,2);
        $data['total'] =number_format( $result[0]->total,2);
        $data['profit'] = number_format($result[0]->profit,2);

        return $data;
    }






}