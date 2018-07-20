<?php

namespace App\Http\Controllers;

use App\Enumaration\SaleTypes;
use App\Model\Customer;
use App\Model\Item;
use App\Model\ItemKit;
use App\Model\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function getDashBoard(){

        $page_data = [
            'page_title' => 'Dashboard',
            'page_subtile' => ''
        ];

        $info["total_sales"] = Sale::count('id');
        $info["total_customers"] = Customer::count('id');
        $info["total_items"] = Item::where('product_type',0)->count('id');
        $info["total_item_kits"] = ItemKit::count('id');

        $startDate = date('Y-m-d',strtotime('-30 day', strtotime(date('Y-m-d'))));
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));

        $bindings = array($startDate, $endDate);

        $sql = "Select  sum(total_amount) as total, CAST(sales.created_at as DATE) as item_name from sales
                where id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.sale_type=".SaleTypes::$SALE."
                    and sales.deleted_at is null
                )
                group by CAST(sales.created_at as DATE)";

        $saleMonthly = DB::select($sql,$bindings);

        $labelMonthly = array();
        $valueMonthly = array();
        foreach($saleMonthly as $aSale){
            array_push($labelMonthly,$aSale->item_name." ($".$aSale->total.")");
            array_push($valueMonthly,$aSale->total);
        }

        $startDate = date('Y-m-d',strtotime('-7 day', strtotime(date('Y-m-d'))));
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));

        $bindings = array($startDate, $endDate);

        $sql = "Select  sum(total_amount) as total, CAST(sales.created_at as DATE) as item_name from sales
                where id in (
                    select DISTINCT id from sales where
                    sales.created_at >=  ?
                    and sales.created_at <= ?
                    and sales.sale_type=".SaleTypes::$SALE."
                    and sales.deleted_at is null
                )
                group by CAST(sales.created_at as DATE)";

        $saleWeekly = DB::select($sql,$bindings);

        $labelWeekly = array();
        $valueWeekly = array();
        foreach($saleWeekly as $aSale){
            array_push($labelWeekly,$aSale->item_name." ($".$aSale->total.")");
            array_push($valueWeekly,$aSale->total);
        }


        return view('dashboard',["info"=>$info,
                                 "labelMonthly"=>$labelMonthly,"valueMonthly"=>$valueMonthly,
                                 "labelWeekly"=>$labelWeekly,"valueWeekly"=>$valueWeekly])->with($page_data);
    }

}
