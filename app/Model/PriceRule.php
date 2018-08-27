<?php

namespace App\Model;

use App\Enumaration\ItemStatus;
use App\Enumaration\PriceRuleTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PriceRule extends Model
{
    use SoftDeletes;

    public function Categories(){
        return $this->belongsToMany('App\Model\Category');
    }

    public function Items(){
        return $this->belongsToMany('App\Model\Item')->where('items.item_status','=',ItemStatus::$ACTIVE)->where('items.product_type','=','0');
    }
    public function itemKits(){
        return $this->belongsToMany('App\Model\ItemKit');
    }

    public function InsertPriceRule($request)
    {
        $priceRule = new PriceRule();

        $priceRule->type = PriceRuleTypes::$PRICE_RULE[$request->type];

        $priceRule->name = $request->name ;

        $priceRule->description = $request->description ;

        $priceRule->start_date =$request->start_date;

        $priceRule->end_date =  $request->end_date;

        if(!is_null($request->requires_coupon))
            $priceRule->requires_coupon =  1;
        else
            $priceRule->requires_coupon =  0;


        $priceRule->coupon_code = $request->coupon_code ;

        if(!is_null($request->show_on_reciept))
            $priceRule->show_on_reciept = 1 ;
        else
            $priceRule->show_on_reciept = 0 ;

        if(!is_null($request->active))
            $priceRule->active = 1 ;
        else
            $priceRule->active = 0 ;


        if(!is_null($request->items_to_buy))
            $priceRule->items_to_buy = $request->items_to_buy;
        else
            $priceRule->items_to_buy = 0;


        if(!is_null($request->items_to_get))
            $priceRule->items_to_get =  $request->items_to_get ;
        else
            $priceRule->items_to_get = 0;

        if(!is_null($request->spend_amount))
            $priceRule->spend_amount =  $request->spend_amount;
        else
            $priceRule->spend_amount = 0;

        if(!is_null($request->percent_off))
            $priceRule->percent_off =  $request->percent_off;
        else
            $priceRule->spend_amount =  0;

        if(!is_null($request->fixed_of))
            $priceRule->fixed_of =  $request->fixed_of ;
        else
            $priceRule->fixed_of =  0;

        if(!is_null($request->num_times_to_apply))
            $priceRule->num_times_to_apply =  $request->num_times_to_apply;
        else
            $priceRule->num_times_to_apply =  0;

        if(!is_null($request->unlimited))
            $priceRule->unlimited = 1 ;
        else
            $priceRule->unlimited = 0 ;

        $priceRule->save();

        $priceRule = PriceRule::find($priceRule->id);

        if(!is_null($request->items))
        foreach($request->items as $key=>$item){
            $itemExists = DB::table("item_price_rule")->where('price_rule_id',$priceRule->id)->where('item_id',$item)->first();
            if(!$itemExists)
                $priceRule->Items()->attach($item);
        }

        if(!is_null($request->item_kits))
        foreach($request->item_kits as $key=>$itemKit){
            $priceRule->itemKits()->attach($itemKit);
        }

        if(!is_null($request->categories))
        foreach($request->categories as $key=>$category){
            $priceRule->Categories()->attach($category);
            $itemsInCategory = \App\Model\Item::where("category_id",$category)->get();
            foreach($itemsInCategory as $anIetm){
                $itemExists = DB::table("item_price_rule")->where('price_rule_id',$priceRule->id)->where('item_id',$anIetm)->first();
                $priceRule->Items()->attach($anIetm->id);
            }
        }

    }

    public function EditPriceRule($request, $price_rule_id)
    {

        $priceRule = PriceRule::where("id",$price_rule_id)->first();

        $priceRule->type = PriceRuleTypes::$PRICE_RULE[$request->type];

        $priceRule->name = $request->name ;

        $priceRule->description = $request->description ;

        $priceRule->start_date =$request->start_date;

        $priceRule->end_date =  $request->end_date;

        if(!is_null($request->requires_coupon))
            $priceRule->requires_coupon =  1;
        else
            $priceRule->requires_coupon =  0;


        $priceRule->coupon_code = $request->coupon_code ;

        if(!is_null($request->show_on_reciept))
            $priceRule->show_on_reciept = 1 ;
        else
            $priceRule->show_on_reciept = 0 ;

        if(!is_null($request->active))
            $priceRule->active = 1 ;
        else
            $priceRule->active = 0 ;


        if(!is_null($request->items_to_buy))
            $priceRule->items_to_buy = $request->items_to_buy;
        else
            $priceRule->items_to_buy = 0;


        if(!is_null($request->items_to_get))
            $priceRule->items_to_get =  $request->items_to_get ;
        else
            $priceRule->items_to_get = 0;

        if(!is_null($request->spend_amount))
            $priceRule->spend_amount =  $request->spend_amount;
        else
            $priceRule->spend_amount = 0;

        if(!is_null($request->percent_off)){
            $priceRule->percent_off =  $request->percent_off;
            $priceRule->fixed_of =  0.0 ;
        }
        else
            $priceRule->spend_amount =  0;

        if(!is_null($request->fixed_of)) {
            $priceRule->fixed_of =  $request->fixed_of ;
            $priceRule->percent_off =  0.0;
        }
        else
            $priceRule->fixed_of =  0;

        if(!is_null($request->num_times_to_apply))
            $priceRule->num_times_to_apply =  $request->num_times_to_apply;
        else
            $priceRule->num_times_to_apply =  0;

        if(!is_null($request->unlimited))
            $priceRule->unlimited = 1 ;
        else
            $priceRule->unlimited = 0 ;

        $priceRule->save();

        $items = array();
        if(!is_null($request->items))
        foreach($request->items as $key=>$item){
            array_push($items,$item);
        }
        $priceRule->Items()->sync($items);

        $itemKits = array();
        if(!is_null($request->item_kits))
        foreach($request->item_kits as $key=>$itemKit){
            array_push($itemKits,$itemKit);
        }
        $priceRule->itemKits()->sync($itemKits);

        $categories = array();
        if(!is_null($request->categories))
        foreach($request->categories as $key=>$category){
            array_push($categories,$category);
        }
        $priceRule->Categories()->sync($request->categories);
        if(!is_null($request->categories))
        foreach($request->categories as $key=>$category){
            $priceRule->Categories()->attach($category);
            $itemsInCategory = \App\Model\Item::where("category_id",$category)->get();
            foreach($itemsInCategory as $anIetm){
                $priceRule->Items()->attach($anIetm->id);
            }
        }

    }
}
