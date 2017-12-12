<?php

namespace App\Http\Controllers;

use App\Enumaration\ItemStatus;
use App\Model\Category;
use App\Model\File;
use App\Model\Item;
use App\Model\ItemKit;
use App\Model\ItemsImage;
use App\Model\Manufacturer;
use App\Model\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ItemKitController extends Controller
{
    public function GetItemKitForm()
    {

        $categoryList = Category::orderBy('category_name')->get();

        $manufacturerList = Manufacturer::all();

        $items =  DB::table('items')
            ->where('item_status','=',ItemStatus::$ACTIVE)
            ->where('product_type','=','0')
            ->get();

        return view('item_kits.new_item_kit',['categoryList'=>$categoryList,'manufacturerList'=>$manufacturerList,'items'=>$items]);
    }

    public function AddItemKit(Request $request)
    {

        $this->validate($request, [
            'item_kit_name' => 'required',
            'item_kit_category' => 'required',
            'cost_price' => 'required|numeric',
            'unit_price' => 'required|numeric'
        ]);

        $item = new ItemKit();
        $item->InsertItemKit($request);

        return redirect()->route('item_kit_list');

    }


    public function GetItemKitList()
    {
        $item_kits = DB::table('item_kits')
            ->leftJoin('categories', 'item_kits.category_id', '=', 'categories.id')
            ->select('item_kits.id as item_kit_id', 'item_kits.*','categories.*')
            ->whereNull('item_kits.deleted_at')
            ->get();

        return view('item_kits.item_kit_list', ["itemKits" => $item_kits]);
    }

    public function EditItemKitGet($itemKitId)
    {

        $items =  DB::table('items')
            ->where('item_status','=',ItemStatus::$ACTIVE)
            ->where('product_type','=','0')
            ->get();


        $categoryList = Category::orderBy('category_name')->get();

        $manufacturerList = Manufacturer::all();

        $itemKitInfo = DB::table('item_kits')
            ->leftJoin('categories', 'item_kits.category_id', '=', 'categories.id')->leftJoin('manufacturers', 'item_kits.manufacturer_id', '=', 'manufacturers.id')->where('item_kits.id', '=', $itemKitId)->select('item_kits.id as item_kit_id', 'item_kits.*'   ,'categories.*','manufacturers.*')->first();

        $itemKit = new ItemKit();
        $selectedItems = $itemKit->getItemKitItems($itemKitId);

        return view('item_kits.item_kit_edit', ['itemKit' => $itemKitInfo, 'categoryList' => $categoryList,'manufacturerList'=>$manufacturerList,'items'=>$items,'selectedItems'=>$selectedItems]);
    }

    public function  EditItemKitPost(Request $request, $itemKitId)
    {

        $this->validate($request, [
            'item_kit_name' => 'required',
            'item_kit_category' => 'required',
            'cost_price' => 'required|numeric',
            'unit_price' => 'required|numeric'
        ]);

        $itemKit = new ItemKit();
        $itemKit->editItemKit($request,$itemKitId);

        return redirect()->route('item_kit_list');

    }

    public function DeleteItemKitGet($itemKitId){

        $itemKit = new ItemKit();

        $itemKit = $itemKit::where("id",$itemKitId)->first();

        $itemKit->delete();

        return redirect()->route('item_kit_list');
    }

    public function DeleteItemKits(Request $request){

        $item_kit_list = $request->id_list;
        if(DB::table('item_kits')->whereIn('id',$item_kit_list)->delete())
            return response()->json(["success"=>true],200);
        return response()->json(["success"=>false],200);

    }



}
