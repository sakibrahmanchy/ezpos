<?php

namespace App\Http\Controllers;

use App\Model\Location;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{

    public function GetLocationForm()
    {
        return view('locations.new_location');
    }

    public function AddLocation(Request $request)
    {
        $this->validate($request,[
            "name"=>"required",
            "printer_ip"=>"required",
            "printer_port"=>"required|numeric"
        ]);

        Location::create($request->except('_token'));

        return redirect()->route('location_list');

    }

    public function GetLocationList()
    {
        $locations = Location::all();

        return view('locations.location_list', ["locations" => $locations]);
    }

    public function EditLocationGet($location_id)
    {

        $locationInfo = Location::where('id',$location_id)->first();

        return view('locations.location_edit', ['location' => $locationInfo]);
    }


    public function  EditLocationPost(Request $request, $location_id)
    {
        $this->validate($request,[
            "name"=>"required",
            "printer_ip"=>"required",
            "printer_port"=>"required|numeric"
        ]);

        $location = Location::where("id", "=", $location_id)->first();

        $location->update($request->except('_token'));

        return redirect()->route('location_list');

    }

    public function DeleteLocationGet($location_id){

        $location = Location::where("id",$location_id)->first();

        $location->delete();

        return redirect()->route('location_list');
    }


    public function DeleteLocations(Request $request){

        $location_list = $request->id_list;
        if(DB::table('locations')->whereIn('id',$location_list)->delete())
            return response()->json(["success"=>true],200);
        return response()->json(["success"=>false],200);

    }

    public function SetDefaultLocation(Request $request){

        $location_id = $request->id;
        $status =  $request->status;
        if(Location::where("id",$location_id)->exists()){
            if($status=="true"){
                Location::query()->update(["isDefault"=>false]);
                Location::where("id",$location_id)->update(["isDefault"=>true]);
                return response()->json(["success"=>true],200);
            }else{
                Location::query()->update(["isDefault"=>false]);
                return response()->json(["success"=>true],200);
            }
        }else{
            return response()->json(["success"=>false],401);
        }

//        $location = Location::where("id",$location_id)->first();
//        if($status){
//
//        }

    }
}

