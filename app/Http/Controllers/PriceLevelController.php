<?php

namespace App\Http\Controllers;

use App\Enumaration\ItemStatus;
use App\Model\Category;
use App\Model\Item;
use App\Model\ItemKit;
use App\Model\PriceLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceLevelController extends Controller
{


    public function GetPriceLevelForm()
    {
        return view('price_levels.new_price_level');
    }

    public function AddPriceLevel(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'percentage' => 'required'
        ]);

        $priceLevel = new PriceLevel();
        $priceLevel->name = $request->name;
        $priceLevel->description = $request->description;
        $priceLevel->percentage = $request->percentage;
        $priceLevel->save();

        return redirect()->route('price_level_list');

    }


    public function GetPriceLevelList()
    {
        $price_levels = PriceLevel::all();

        return view('price_levels.price_level_list', ["price_levels" => $price_levels]);
    }

    public function EditPriceLevelGet($priceLevelId)
    {

        $priceLevel = PriceLevel::where('id', $priceLevelId)->first();

        return view('price_levels.price_level_edit',["price_level"=>$priceLevel]);
    }



    public function  EditPriceLevelPost(Request $request, $priceLevelId)
    {

        $this->validate($request, [
            'name' => 'required',
            'percentage' => 'required'
        ]);
        $priceLevel = PriceLevel::where("id",$priceLevelId)->first();
        $priceLevel->name = $request->name;
        $priceLevel->description = $request->description;
        $priceLevel->percentage = $request->percentage;
        $priceLevel->save();
        return redirect()->route('price_level_list');

    }

    public function DeletePriceLevelGet($priceLevelId){

        $priceLevel = PriceLevel::where("id",$priceLevelId)->first();

        $priceLevel->delete();

        DB::table('price_levels')->where('price_level_id', $priceLevelId)->delete();

        return redirect()->route('price_level_list');
    }

    public function DeletePriceLevels(Request $request){

        $price_level_list = $request->id_list;
        if(DB::table('price_levels')->whereIn('id',$price_level_list)->delete())
            return response()->json(["success"=>true],200);
        return response()->json(["success"=>false],200);

    }



}
