<?php

namespace App\Http\Controllers;

use App\Model\Counter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class CounterController extends Controller
{

    public function GetCounterForm()
    {
        return view('counters.new_counter');
    }

    public function AddCounter(Request $request)
    {
        $this->validate($request,[
            "name"=>"required",
            "printer_ip"=>"required",
            "printer_port"=>"required|numeric"
        ]);

        Counter::create($request->except('_token'));

        return redirect()->route('counter_list');

    }

    public function GetCounterList()
    {
        $counters = Counter::all();

        return view('counters.counter_list', ["counters" => $counters]);
    }

    public function GetCounterListAjax(){

        $counters = Counter::all();

        return response()->json(["counters" => $counters],200);
    }

    public function EditCounterGet($counter_id)
    {

        $counterInfo = Counter::where('id',$counter_id)->first();

        return view('counters.counter_edit', ['counter' => $counterInfo]);
    }


    public function  EditCounterPost(Request $request, $counter_id)
    {
        $this->validate($request,[
            "name"=>"required",
            "printer_ip"=>"required",
            "printer_port"=>"required|numeric"
        ]);

        $counter = Counter::where("id", "=", $counter_id)->first();

        $counter->update($request->except('_token'));

        return redirect()->route('counter_list');

    }

    public function DeleteCounterGet($counter_id){

        $counter = Counter::where("id",$counter_id)->first();

        $counter->delete();

        return redirect()->route('counter_list');
    }


    public function DeleteCounters(Request $request){

        $counter_list = $request->id_list;
        $deletedRows = array();
        $counters = Counter::whereIn("id",$counter_list)->get();

        foreach($counters as $aCounter){
            if(!$aCounter->isDefault){
                array_push($deletedRows, $aCounter->id);
                $aCounter->delete();
            }
        }

        return response()->json(["success"=>true,"deletedRows"=>$deletedRows],200);

    }

    public function SetDefaultCounter(Request $request){

        $counter_id = $request->id;
        $status =  $request->status;
        if(Counter::where("id",$counter_id)->exists()){
            if($status=="true"){
                Counter::query()->update(["isDefault"=>false]);
                Counter::where("id",$counter_id)->update(["isDefault"=>true]);
                return response()->json(["success"=>true],200);
            }else{
                Counter::query()->update(["isDefault"=>false]);
                return response()->json(["success"=>true],200);
            }
        }else{
            return response()->json(["success"=>false],401);
        }
    }

    public function SetCounter($counter_id, Request $request){

        $counter = Counter::where("id",$counter_id)->first();

        return redirect()->back()->withCookie(cookie('counter_name', $counter->name, 45000))
                                                        ->withCookie(cookie('counter_id', $counter->id, 45000));

    }
}
