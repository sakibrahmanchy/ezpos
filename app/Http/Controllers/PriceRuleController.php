<?php

namespace App\Http\Controllers;

use App\Enumaration\ItemStatus;
use App\Enumaration\PriceRuleTypes;
use App\Model\Category;
use App\Model\Item;
use App\Model\ItemKit;
use App\Model\PriceRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceRuleController extends Controller
{


    public function GetPriceRuleForm()
    {
        $categoryList = Category::orderBy('category_name')->get();

        $itemKitsList = ItemKit::all();

        $itemList =  DB::table('items')
            ->where('item_status','=',ItemStatus::$ACTIVE)
            ->where('product_type','=','0')
            ->get();

        return view('price_rule.new_price_rule',['categories'=>$categoryList,'itemKits'=>$itemKitsList,'items'=>$itemList]);
    }

    public function AddPriceRule(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|not_in:0',
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $priceRule = new PriceRule();
        $priceRule->InsertPriceRule($request);

        return redirect()->route('price_rule_list');

    }


    public function GetPriceRuleList()
    {
        $price_rules = PriceRule::all();

        return view('price_rule.price_rule_list', ["price_rules" => $price_rules]);
    }

    public function EditPriceRuleGet($priceRuleId)
    {

        $priceRule = PriceRule::where('id', $priceRuleId)->with('categories','items','itemKits')->first();

        $selectedCategories = array();

        foreach($priceRule->categories as $aCategory){
            array_push($selectedCategories,$aCategory->id);
        }

        $selectedItems = array();

        foreach($priceRule->items as $anItem){
            array_push($selectedItems,$anItem->id);
        }

        $selectedItemKits = array();

        foreach($priceRule->itemKits as $anItemKit){
            array_push($selectedItemKits,$anItemKit->id);
        }

        $categoryList = Category::orderBy('category_name')->get();

        $itemKitsList = ItemKit::all();

        $itemList =  DB::table('items')
            ->where('item_status','=',ItemStatus::$ACTIVE)
            ->where('product_type','=','0')
            ->get();

        return view('price_rule.price_rule_edit', ["priceRule" => $priceRule,'categories'=>$categoryList,'itemKits'=>$itemKitsList,'items'=>$itemList,'selectedCategories'=> $selectedCategories, 'selectedItems'=>$selectedItems, 'selectedItemKits'=>$selectedItemKits]);
    }



    public function  EditPriceRulePost(Request $request, $priceRuleId)
    {

        $this->validate($request, [
            'type' => 'required|not_in:0',
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $priceRule = new PriceRule();
        $priceRule->EditPriceRule($request,$priceRuleId);
        return redirect()->route('price_rule_list');

    }

    public function DeletePriceRuleGet($priceRuleId){

        $priceRule = new PriceRule();

        $priceRule = $priceRule::where("id",$priceRuleId)->first();

        $priceRule->delete();

        DB::table('item_price_rule')->where('price_rule_id', $priceRuleId)->delete();

        DB::table('item_kit_price_rule')->where('price_rule_id', $priceRuleId)->delete();

        return redirect()->route('price_rule_list');
    }

    public function DeletePriceRules(Request $request){

        $price_rule_list = $request->id_list;
        if(DB::table('price_rules')->whereIn('id',$price_rule_list)->delete())
            return response()->json(["success"=>true],200);
        return response()->json(["success"=>false],200);

    }



}
