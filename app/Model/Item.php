<?php

namespace App\Model;

use App\Enumaration\InventoryReasons;
use App\Enumaration\InventoryTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Item extends Model
{
    use SoftDeletes;

    public function PriceRule(){
        return $this->belongsToMany('App\Model\PriceRule')->groupBy('price_rule_id');
    }

    public function Category(){
        return $this->belongsTo('App\Model\Category','category_id');
    }

    public function Sales(){
        return $this->belongsToMany('App\Model\Sale');
    }

    public function Customer() {
        return $this->belongsToMany('App\Model\Customer');
    }

    public function Supplier(){
        return $this->belongsTo('App\Model\Supplier','supplier_id');
    }

    public function InventortLogs(){
        return $this->hasMany('App\Model\InventoryLog');
    }

    public function InsertItem($request){

        $item = new Item();
        if($request->item_id!=0)
            $item->id = $request->item_id;
        if($request->item_status)
            $item->item_status = $request->item_status;
        $item->isbn = $request->isbn;
        $item->product_id = $request->product_id;
        $item->item_name = $request->item_name;
        $item->category_id = $request->item_category;
        $item->supplier_id = $request->item_supplier;
        $item->item_size = $request->size;
        $item->manufacturer_id = $request->item_manufacturer;
        $item->item_reorder_level = $request->reorder_level;
        $item->item_replenish_level = $request->replenish_level;
        $item->days_to_expiration = $request->expire_days;
        $item->description = $request->description;
        if(isset($request->tax_included))
            $item->price_include_tax = true;
        else
            $item->price_include_tax = false;

        if(isset($request->is_service))
            $item->service_item = true;
        else
            $item->service_item = false;
        $item->cost_price = $request->cost_price ;
        $item->selling_price = $request->unit_price;

        $previous_item_quantity = 0;
        $item->item_quantity += intval( $request->quantity_add_minus);


        if(isset($request->product_type))
            $item->product_type = 1;

        $item->save();

        $current_item_quantity = $item->item_quantity;
        $quantity_change = $current_item_quantity - $previous_item_quantity;

        if($quantity_change!=0){

            $inventoryLog = new InventoryLog();
            $inventoryLog->item_id = $item->id;
            $inventoryLog->in_out_quantity = $quantity_change;
            if($quantity_change>0)
                $inventoryLog->type = InventoryTypes::$ADD_INVENTORY;
            else
                $inventoryLog->type = InventoryTypes::$SUBTRACT_INVENTORY;

            $inventoryLog->reason = InventoryReasons::$ITEM_INSERT;
            $inventoryLog->user_id = Auth::user()->id;
            $inventoryLog->save();
        }
    }

    public function editItem($request, $item_id){

        $item = Item::where('id','=',$item_id)->first();

        $item->isbn = $request->isbn;
        $item->product_id = $request->product_id;
        $item->item_name = $request->item_name;
        $item->item_status = $request->item_status;
        $item->category_id = $request->item_category;
        $item->supplier_id = $request->item_supplier;
        $item->item_size = $request->size;
        $item->manufacturer_id = $request->item_manufacturer;
        $item->item_reorder_level = $request->reorder_level;
        $item->item_replenish_level = $request->replenish_level;
        $item->days_to_expiration = $request->expire_days;
        $item->description = $request->description;
        if(isset($request->tax_included))
            $item->price_include_tax = true;
        else
            $item->price_include_tax = false;

        if(isset($request->is_service))
            $item->service_item = true;
        else
            $item->service_item = false;
        $item->cost_price = $request->cost_price ;
        $item->selling_price = $request->unit_price;

        $previous_item_quantity = $item->item_quantity;
        $item->item_quantity += intval( $request->quantity_add_minus);



        $item->save();

        $current_item_quantity = $item->item_quantity;
        $quantity_change = $current_item_quantity - $previous_item_quantity;

        if($quantity_change!=0){
            $inventoryLog = new InventoryLog();
            $inventoryLog->item_id = $item->id;
            $inventoryLog->in_out_quantity = $quantity_change;
            if($quantity_change>0)
                $inventoryLog->type = InventoryTypes::$ADD_INVENTORY;
            else
                $inventoryLog->type = InventoryTypes::$SUBTRACT_INVENTORY;

            $inventoryLog->reason = InventoryReasons::$ITEM_EDIT;
            $inventoryLog->user_id = Auth::user()->id;
            $inventoryLog->save();
        }
    }

    public function getItemImages($item_id){
        $images = DB::table('items')->leftJoin('items_images','items.id','=','items_images.item_id')->
        leftJoin('files','items_images.file_id','=','files.id')->where('items.id','=',$item_id)->get();

        return $images;
    }

    public function DeleteItem($itemId){
        $item = $this::where("id",$itemId)->first();
        $item->delete();
    }
}
