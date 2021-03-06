<?php

namespace App\Http\Controllers;

use App\Model\Counter;

use App\Model\Employee;
use App\Model\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use \App\Enumaration\PrinterConnectionType;
use Illuminate\Validation\Rule;

class CounterController extends Controller
{

    public function GetCounterForm()
    {
        return view('counters.new_counter');
    }

    public function AddCounter(Request $request)
    {
        $connectViaNetwork = $request->printer_connection_type == PrinterConnectionType::CONNECT_VIA_NETWORK;

        $this->validate($request,[
            "name" => "required",
			"printer_connection_type" => "required",
            'printer_ip' =>  $connectViaNetwork ? 'required|ip' : '',
            "printer_port" =>  $connectViaNetwork ? 'required|numeric' : '',
            "starting_id" => "required|numeric|unique:counters"
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

        $user = Auth::user();

        if($user->id!=1) {
            $employee = Employee::where('user_id',$user->id)->first();
            $counters = Counter::join('counter_employee','counters.id','=','counter_employee.counter_id')
                       ->select(DB::raw('counters.*'))
            ->where('employee_id',$employee->id)->get();
        } else {
            $counters = Counter::all();
        }


        return response()->json(["counters" => $counters],200);
    }

    public function EditCounterGet($counter_id)
    {

        $counterInfo = Counter::where('id',$counter_id)->first();

        return view('counters.counter_edit', ['counter' => $counterInfo]);
    }


    public function  EditCounterPost(Request $request, $counter_id)
    {
        //dd($request->all());
        $connectViaNetwork = $request->printer_connection_type == PrinterConnectionType::CONNECT_VIA_NETWORK;

        $this->validate($request,[
            "name" => "required",
			"printer_connection_type" => "required",
            'printer_ip' =>  $connectViaNetwork ? 'required|ip' : '',
            "printer_port" =>  $connectViaNetwork ? 'required|numeric' : '',
            "starting_id" => [
								'required',
								'numeric',
								Rule::unique('counters')->ignore($counter_id)
							]
        ]);

        $counter = Counter::where("id", "=", $counter_id)->first();

        $counter->update($request->except('_token'));

        return redirect()->route('counter_list');

    }

    public function DeleteCounterGet($counter_id){

        $counter = Counter::where("id",$counter_id)->first();

        if(!$counter->isDefault)
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
	
	public function SetCounterAjax($counter_id, Request $request){

        $counter = Counter::where("id",$counter_id)->first();

		
        return response(json_encode($counter))->withCookie(cookie('counter_name', $counter->name, 45000))
                                                        ->withCookie(cookie('counter_id', $counter->id, 45000));

    }
}
