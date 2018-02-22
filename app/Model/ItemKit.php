<?php

namespace App\Model;

use App\Enumaration\ItemStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\DB;

class ItemKit extends Model
{
    use SoftDeletes;

    public function PriceRule(){
        return $this->belongsToMany('App\Model\PriceRule');
    }

    public function Sales(){
        return $this->belongsToMany('App\Model\Sale','item_kit_sale','item_kit_id','sale_id');
    }

    public function InsertItemKit($request){

        $item_kit = new ItemKit();
        $item_kit->isbn = $request->isbn;
        $item_kit->product_id = $request->product_id;
        $item_kit->item_kit_name = $request->item_kit_name;
        $item_kit->category_id = $request->item_kit_category;
        if(!is_null($request->item_kit_manufacurer))
            $item_kit->manufacturer_id = $request->item_kit_manufacturer;
        else
            $item_kit->manufacturer_id = 0;
        $item_kit->item_kit_description = $request->description;
        if(!is_null($request->tax_included))
            $item_kit->price_include_tax = true;
        else
            $item_kit->price_include_tax = false;
        $item_kit->cost_price = $request->cost_price ;
        $item_kit->selling_price = $request->unit_price;
        $item_kit->save();

        $item_kit_id = $item_kit->id;

        $selectedItems = $request->selected_value;
        $quantities = $request->quantity;

        $itemKitProduct = new ItemKitProduct();

        for($index = 0; $index<sizeof($selectedItems); $index++){
            $mergedArray[$selectedItems[$index]] = $quantities[$index];
        }

        if(isset($mergedArray))
        foreach($mergedArray as $item => $quantity){

            if(!is_null($quantity))
                 $itemKitProduct->InsertItemKitProduct($item_kit_id,$item,$quantity);
            else
                $itemKitProduct->InsertItemKitProduct($item_kit_id,$item,0);
        }


        //Insert Item Kit as an Item
        $itemToInsert = new \stdClass();
        $itemToInsert->item_id =0;
        $itemToInsert->isbn = $request->isbn;
        $itemToInsert->product_id = $request->product_id;
        $itemToInsert->item_name = $request->item_kit_name;
        $itemToInsert->item_status = ItemStatus::$ACTIVE;
        $itemToInsert->item_category = $request->item_kit_category;
        $itemToInsert->item_supplier = "";
        $itemToInsert->size = "";
        if(!is_null($request->item_kit_manufacurer))
            $itemToInsert->item_manufacturer = $request->item_kit_manufacturer;
        else
            $itemToInsert->item_manufacturer = 0;
        $itemToInsert->description = $request->description;
        if(!is_null($request->tax_included))
            $itemToInsert->tax_included = true;
        else
            $itemToInsert->tax_included = false;
        $itemToInsert->is_service = false ;
        $itemToInsert->cost_price = $request->cost_price ;
        $itemToInsert->selling_price = $item_kit->selling_price ;
        $itemToInsert->unit_price = $request->unit_price;
        $itemToInsert->item_status = ItemStatus::$ACTIVE;
        $itemToInsert->reorder_level = null;
        $itemToInsert->replenish_level = null;
        $itemToInsert->expire_days = null;
        $itemToInsert->quantity_add_minus = null;
        $itemToInsert->product_type=1;
        $item = new Item();
        $item->InsertItem($itemToInsert);

    }

    public function editItemKit($request, $item_kit_id){

        $item_kit = ItemKit::where('id','=',$item_kit_id)->first();

        $item_kit->isbn = $request->isbn;
        $item_kit->product_id = $request->product_id;
        $item_kit->item_kit_name = $request->item_kit_name;
        $item_kit->category_id = $request->item_kit_category;
        if(!is_null($request->item_kit_manufacturer))
            $item_kit->manufacturer_id = $request->item_kit_manufacturer;
        else
            $item_kit->manufacturer_id = 0;
        $item_kit->item_kit_description = $request->description;
        if(!is_null($request->tax_included))
            $item_kit->price_include_tax = true;
        else
            $item_kit->price_include_tax = false;
        $item_kit->cost_price = $request->cost_price ;
        $item_kit->selling_price = $request->unit_price;
        $item_kit->save();

        $item_kit_id = $item_kit->id;


        $selectedItems = $request->selected_value;
        $quantities = $request->quantity;
        $itemKitProduct = new ItemKitProduct();

        for($index = 0; $index<sizeof($selectedItems); $index++){
            $mergedArray[$selectedItems[$index]] = $quantities[$index];
        }

        DB::table('item_kit_products')
            ->where('item_kit_id',$item_kit_id)
            ->delete();

        if(isset($mergedArray))
            foreach($mergedArray as $item => $quantity){

                if(!is_null($quantity))
                    $itemKitProduct->InsertItemKitProduct($item_kit_id,$item,$quantity);
                else
                    $itemKitProduct->InsertItemKitProduct($item_kit_id,$item,0);
            }

        $item = Item::where("item_name",$item_kit->item_kit_name)
            ->where("product_type",1)->where("created_at",$item_kit->created_at)->first();


        if(!is_null($item)){


            $itemToInsert = new \stdClass();
            $itemToInsert->item_id =0;
            $itemToInsert->isbn = $item_kit->isbn;
            $itemToInsert->product_id = $item_kit->product_id;
            $itemToInsert->item_name = $item_kit->item_kit_name;
            $itemToInsert->item_status = ItemStatus::$ACTIVE;
            $itemToInsert->item_category = $item_kit->item_kit_category;
            $itemToInsert->item_supplier = "";
            $itemToInsert->size = "";
            if(!is_null($item_kit->item_kit_manufacurer))
                $itemToInsert->item_manufacturer = $item_kit->item_kit_manufacturer;
            else
                $itemToInsert->item_manufacturer = 0;
            $itemToInsert->description = $item_kit->description;
            if(!is_null($item_kit->tax_included))
                $itemToInsert->tax_included = true;
            else
                $itemToInsert->tax_included = false;
            $itemToInsert->is_service = false ;
            $itemToInsert->cost_price = $item_kit->cost_price ;
            $itemToInsert->unit_price = $item_kit->selling_price;
            $itemToInsert->item_status = ItemStatus::$ACTIVE;
            $itemToInsert->reorder_level = null;
            $itemToInsert->replenish_level = null;
            $itemToInsert->expire_days = null;
            $itemToInsert->quantity_add_minus = null;
            $itemToInsert->product_type=1;
            $item->editItem($itemToInsert, $item->id);
        }

    }

    public function getItemKitItems($item_kit_id){
        $items = DB::table('item_kits')->leftJoin('item_kit_products','item_kits.id','=','item_kit_products.item_kit_id')->
        leftJoin('items','item_kit_products.item_id','=','items.id')->where('item_kits.id','=',$item_kit_id)->get();

        return $items;
    }
}
