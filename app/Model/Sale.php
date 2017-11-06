<?php

namespace App\Model;

use App\Enumaration\InventoryReasons;
use App\Enumaration\InventoryTypes;
use App\Enumaration\SaleStatus;
use App\Model\Item;
use App\Model\PaymentLog;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Sale extends Model
{
    use SoftDeletes;

    public function Items(){
        return $this->belongsToMany('App\Model\Item')->withPivot('quantity','unit_price',
            'total_price','discount_amount','item_discount_percentage')->with('pricerule', 'Category','Supplier');
    }

    public function ItemKits(){
        return $this->belongsToMany('App\Model\ItemKit','item_kit_sale','item_kit_id','sale_id')->withPivot('quantity','unit_price',
            'total_price','discount_amount','item_discount_percentage')->with('PriceRule');
    }

    public function PaymentLogs(){
        return $this->belongsToMany('App\Model\PaymentLog');
    }

    public function Customer(){
        return $this->belongsTo('App\Model\Customer');
    }

    public function Employee(){
        return $this->belongsTo('App\Model\User');
    }

    public function InsertSale($saleInfo, $productInfos,$paymentInfos , $saleStatus){

        $sale = new Sale();
        $sale->employee_id = Auth::user()->id;
        $sale->customer_id = $saleInfo['customer_id'];
        $sale->sub_total_amount = $saleInfo['subtotal'];
        $sale->tax_amount = $saleInfo['tax'];
        $sale->total_amount = $saleInfo['total'];
        $sale->sales_discount = $saleInfo['discount'];
        $sale->sale_status = $saleStatus;
        $sale->due = $saleInfo['due'];
        $sale->profit = $saleInfo['profit'];
        $sale->items_sold = $saleInfo['items_sold'];
        $sale->sale_type = $saleInfo['sale_type'];

        $sale->save();

        $sale_id = $sale->id;

        foreach($productInfos as $aProductInfo){

            $item_id = $aProductInfo['item_id'];
            $item_quantity = $aProductInfo['quantity'];
            $item_type = $aProductInfo["item_type"];
            if($item_id==0){
                $keyId = "discount-01!XcQZc003ab";
                $item = Item::where("product_id",$keyId)->first();

                if(is_null($item)){

                        $item = new Item();
                        $item->product_id=$keyId;
                        $item->item_name = "discount";
                        $item->category_id = 0;
                        $item->supplier_id = 0;
                        $item->product_type = 2;
                        $item->cost_price = $aProductInfo["unit_price"];
                        $item->selling_price = $aProductInfo["unit_price"];
                        $item->save();
                        $itemId = $item->id;

                }else
                    $itemId = $item->id;

                    $aProductInfo['item_id'] = $itemId;
                    $sale->Items()->attach([$itemId=>$aProductInfo]);



            }else{
                if($saleStatus!=SaleStatus::$ESTIMATE){
                    if($item_type=="item"){
                        $item = Item::where("id",$item_id)->first();
                        $previous_item_quantity = $item->item_quantity;
                        $item->item_quantity -= $item_quantity;
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

                            $inventoryLog->reason = InventoryReasons::$SALEORRETURN." (<a href=". route('sale_receipt',["sale_id"=>$sale_id]) .">EZPOS ".$sale_id."</a>)";
                            $inventoryLog->user_id = Auth::user()->id;
                            $inventoryLog->save();
                        }

                    }else if($item_type=="item-kit"){
                        $itemKit = ItemKit::where("id",$item_id)->first();
                        $itemKitProduct = ItemKit::where("id",$itemKit->id)->get();
                        foreach($itemKitProduct as $anItem){

                            $item = Item::where("id",$anItem->item_id)->where('deleted_at','null')->first();
                            if(!is_null($item)&&!isEmpty($item)){
                                $previous_item_quantity = $item->item_quantity;
                                $item->item_quantity -= $item_quantity;
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

                                    $inventoryLog->reason = InventoryReasons::$SALEORRETURN." (<a href=". route('sale_receipt',["sale_id"=>$sale_id]) .">EZPOS ".$sale_id."</a>)";
                                    $inventoryLog->user_id = Auth::user()->id;
                                    $inventoryLog->save();
                                }

                            }


                        }

                    }


                }

                if($item_type=='item')
                    $sale->Items()->attach([$item_id=>$aProductInfo]);
                else if($item_type=='item-kit'){
                    unset($aProductInfo['item_id']);
                    $sale->ItemKits()->attach([$item_id=>$aProductInfo]);
                }

            }

        }

        if(!is_null($paymentInfos))
        foreach($paymentInfos as $aPaymentInfo){

            $paymentLog = new PaymentLog();

            $paymentLog->payment_type = $aPaymentInfo["payment_type"];
            $paymentLog->paid_amount = $aPaymentInfo["paid_amount"];

            $paymentLog->save();

            $sale->paymentLogs()->attach($paymentLog);

        }
        return $sale_id;


    }

    public function editSale($saleInfo, $productInfos,$paymentInfos , $saleStatus, $sale_id){

        $sale = Sale::where('id',$sale_id)->first();

        $sale->employee_id = Auth::user()->id;
        $sale->customer_id = $saleInfo['customer_id'];
        $sale->sub_total_amount = $saleInfo['subtotal'];
        $sale->tax_amount = $saleInfo['tax'];
        $sale->total_amount = $saleInfo['total'];
        $sale->sales_discount = $saleInfo['discount'];
        $sale->sale_status = $saleStatus;
        $sale->due = $saleInfo['due'];
        $sale->profit = $saleInfo['profit'];
        $sale->items_sold = $saleInfo['items_sold'];
        $sale->sale_type = $saleInfo['sale_type'];

        $sale->save();

        $sale_id = $sale->id;

        foreach($productInfos as $aProductInfo){

            $item_id = $aProductInfo['item_id'];
            $item_quantity = $aProductInfo['quantity'];
            $item_type = $aProductInfo["item_type"];
            if($item_id==0){
                $keyId = "discount-01!XcQZc003ab";
                $item = Item::where("product_id",$keyId)->first();

                if(is_null($item)){

                    $item = new Item();
                    $item->product_id=$keyId;
                    $item->item_name = "discount";
                    $item->category_id = 0;
                    $item->supplier_id = 0;
                    $item->product_type = 2;
                    $item->cost_price = $aProductInfo["unit_price"];
                    $item->selling_price = $aProductInfo["unit_price"];
                    $item->save();
                    $itemId = $item->id;

                }else
                    $itemId = $item->id;

                $aProductInfo['item_id'] = $itemId;

                $productInfos[0]['item_id'] = $itemId;

            }else{
                if($saleStatus!=SaleStatus::$ESTIMATE){
                    echo $item_id;
                    if($item_type=="item"){

                        $item = Item::where("id",$item_id)->first();
                        $previous_item_quantity = $item->item_quantity;
                        $item->item_quantity -= $item_quantity;
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

                            $inventoryLog->reason = InventoryReasons::$SALEORRETURN." (<a href=". route('sale_receipt',["sale_id"=>$sale_id]) .">EZPOS ".$sale_id."</a>)";
                            $inventoryLog->user_id = Auth::user()->id;
                            $inventoryLog->save();
                        }

                    }else if($item_type=="item-kit"){
                        $itemKit = ItemKit::where("id",$item_id)->first();
                        $itemKitProduct = ItemKit::where("id",$itemKit->id)->get();
                        foreach($itemKitProduct as $anItem){

                            $item = Item::where("id",$anItem->item_id)->where('deleted_at','null')->first();
                            if(!is_null($item)&&!isEmpty($item)){

                                $previous_item_quantity = $item->item_quantity;
                                $item->item_quantity -= $item_quantity;
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

                                    $inventoryLog->reason = InventoryReasons::$SALEORRETURN." (<a href=". route('sale_receipt',["sale_id"=>$sale_id]) .">EZPOS ".$sale_id."</a>)";
                                    $inventoryLog->user_id = Auth::user()->id;
                                    $inventoryLog->save();
                                }
                            }


                        }

                    }


                }


            }

        }

        $sale->items()->sync($productInfos);


        if(!is_null($paymentInfos)){

            $payments = array();
            foreach($paymentInfos as $aPaymentInfo){

                $payment_id = (int) $aPaymentInfo["payment_id"];
               if($payment_id==0){

                    $paymentLog = new PaymentLog();

                    $paymentLog->payment_type = $aPaymentInfo["payment_type"];
                    $paymentLog->paid_amount = $aPaymentInfo["paid_amount"];

                    $paymentLog->save();

                    $payment_id = $paymentLog->id;
                }

                array_push($payments, $payment_id);
            }

            $sale->paymentLogs()->sync($payments);
        }

        return $sale_id;


    }

    public function DeleteSale($saleId){
        $sale = $this::where("id",$saleId)->first();
        $sale->delete();
    }
}
