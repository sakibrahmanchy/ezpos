<?php

namespace App\Http\Controllers\Api;

use App\Enumaration\ItemStatus;
use App\Enumaration\UserTypes;
use App\Http\Controllers\Controller;
use App\Library\SettingsSingleton;
use App\Model\CashRegister;
use App\Model\Employee;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;


class ItemController extends Controller
{
    public function GetItemsAutocomplete(Request $request){

        $autoselect = Input::get('autoselect');
        if($autoselect==="true") {

            if(!$request->q)
                return response()->json(["success" => false, "message" => "Query param is required"],422);

            $search_param = (string) Input::get('q');
            $search_param = preg_replace('/[^0-9]/','',$search_param);

            return response()->json(["success" => true, "data" => $this->searchForBarcodeItem($search_param)],200);

        }else{

            if(!$request->q)
                return response()->json(["success" => false, "message" => "Query param is required"],422);

            $search_param = (string) '%'.Input::get('q').'%';

            $items =  $this->generalItemQuery()->where(function($query) use ($search_param) {
                $query->where('item_name','LIKE',$search_param)
                    ->orWhere('isbn','LIKE',$search_param);
            })->get()->toArray();

            $itemsWithItemKits = $items;

            $current_date = new \DateTime('today');

            foreach($itemsWithItemKits as $anItem) {
                $this->getItemAfterPriceRuleValidation($anItem,false,
                    -1, $current_date);
            }

            return response()->json(["success" => true, "data" => $itemsWithItemKits],200);
        }
    }

    public function scanItemByBarcode($barcode_number) {
        return $this->searchForBarcodeItem($barcode_number);
    }

    public function getItemPrice(Request $request) {
        if($request->barcode) {
            $itemInfo = $this->searchForBarcodeItem($request->barcode);
            if(count($itemInfo) == 0) {
                return response()->json([
                    "success" => true,
                    "data" => [],
                    "message" => "No items found with the matched barcode"],200);
            }
            else {
                $barcode_scan_price_required = ($itemInfo[0]->new_price !== -1 ? true : false);
                if($barcode_scan_price_required)
                    return response()->json([
                        "success" => true,
                        "data" => array(
                            "selling_price" => $itemInfo[0]->selling_price,
                            "barcode_scan_price_required" => $barcode_scan_price_required,
                            "price_from_barcode_scan" => $itemInfo[0]->new_price,
                        )],200);
                else
                {
                    if($request->customer_id) {
                        return $this->applyPriceLevelIfAny($request->customer_id, $itemInfo[0]);
                    } else {
                        return $this->applyItemPriceRuleIfAny($itemInfo[0]);
                    }
                }

            }
        } else {
            if($request->item_id) {

                $item = $this->generalItemQuery()->where('items.id', $request->item_id)->first();

                if(!is_null($item)) {
                    if($request->customer_id) {
                        return $this->applyPriceLevelIfAny($request->customer_id, $item);
                    } else {
                        return $this->applyItemPriceRuleIfAny($item);
                    }
                }
                else
                return response()->json([
                    "success" => true,
                    "data" => [],
                    "message" => "No items found with the given item id"],200);

            }  else{
                return response()->json(["success" => false,  "message" => "At least one query param is required"],422);
            }
        }
    }

    public function applyPriceLevelIfAny($customerId, $item) {
        $priceLevel = $this->getPriceLevelByCustomerAndItem($customerId, $item->item_id);
        if(is_null($priceLevel)){
            return response()->json([
                "success" => true,
                "data" => array(
                    "selling_price" => $item->selling_price,
                    "customer_price_level" => false
                )], 200);
        } else {
            $discountPercentage = $priceLevel->percentage;
            $itemPriceAfterApplyingPriceLevel = number_format(percentageLess($item->selling_price,(-1) * $discountPercentage),2);
            return response()->json([
                "success" => true,
                "data" => array(
                    "selling_price" => $item->selling_price,
                    "customer_price_level" => $priceLevel->id,
                    "price_level_percentage"=>$discountPercentage,
                    "price_after_applying_price_level" => $itemPriceAfterApplyingPriceLevel
                )], 200);
        }
    }

    public function applyItemPriceRuleIfAny($item) {
        $priceRule = $this->getPriceRuleByItemId($item->item_id);
        if(!is_null($priceRule)) {
            if($priceRule->percent_off > 0) {
                $discountPercentage = $priceRule->percent_off;
                $itemPriceAfterApplyingPriceRule = number_format(percentageLess($item->selling_price,  $discountPercentage),2);
                return response()->json([
                    "success" => true,
                    "data" => array(
                        "selling_price" => $item->selling_price,
                        "price_rule_id" => $priceRule->id,
                        "price_rule_name" => $priceRule->name,
                        "price_rule_type"=>"percentage",
                        "price_rule_percentage"=>$discountPercentage,
                        "price_after_applying_price_rule" => $itemPriceAfterApplyingPriceRule
                    )], 200);
            } else if($priceRule->fixed_of>0) {
                $itemPriceAfterApplyingPriceRule =  number_format($item->selling_price - $priceRule->fixed_of,2 );
                $itemPriceAfterApplyingPriceRule = ($itemPriceAfterApplyingPriceRule > 0) ? $itemPriceAfterApplyingPriceRule : 0;
                return response()->json([
                    "success" => true,
                    "data" => array(
                        "selling_price" => $item->selling_price,
                        "price_rule" => $priceRule->id,
                        "price_rule_type"=>"fixed",
                        "price_rule_discount_amount"=>$priceRule->fixed_of,
                        "price_after_applying_price_rule" =>$itemPriceAfterApplyingPriceRule
                    )], 200);
            }
        }
        else
            return response()->json([
                "success" => true,
                "data" => array(
                    "selling_price" => $item->selling_price,
                )], 200);
    }

    public function getPriceLevelByCustomerAndItem($customer_id, $item_id) {
        return DB::table('customer_item')
            ->join('price_levels','customer_item.price_level_id','=','price_levels.id')
            ->where('customer_id', $customer_id)
            ->where('item_id',$item_id)
            ->first();
    }

    public function getPriceRuleByItemId($item_id) {
        $dateToday = date('Y-m-d');
        return DB::table('item_price_rule')
            ->join('price_rules','item_price_rule.price_rule_id','=','price_rules.id')
            ->whereDate('start_date','<=',$dateToday)
            ->whereDate('end_date','>=',$dateToday)
            ->where('active',1)
            ->where('item_id',$item_id)
            ->first();
    }

    public function checkAndValidateItemScanPrice($search_param) {
        $scan_price_from_barcode = SettingsSingleton::getByKey('scan_price_from_barcode');
        $item_new_price_from_barcode = -1;
        $priceNeededToBeScanned = false;
        if(strlen($search_param)===12) {
            if($scan_price_from_barcode=="true"){
                $upc_code_prefix_string = $scan_price_from_barcode = SettingsSingleton::getByKey('upc_code_prefix');
                $upc_code_prefix = explode(",",$upc_code_prefix_string);
                foreach($upc_code_prefix as $currentPrefix) {
                    $stringToSearch = substr($search_param,0,strlen($currentPrefix));
                    if($stringToSearch === $currentPrefix){
                        $item_new_price_from_barcode = (int) substr($search_param,6,12);
                        $item_new_price_from_barcode =  ((int)($item_new_price_from_barcode / 10))/100;
                        $search_param = substr($search_param,0,6);
                        $priceNeededToBeScanned = true;
                        break;
                    }
                }
            }
        }

        return array(
            "item_new_price_from_barcode" => $item_new_price_from_barcode,
            "priceNeededToBeScanned" => $priceNeededToBeScanned
        );
    }

    public function generalItemQuery() {
        return DB::table('items')
            ->leftJoin('items_images', 'items.id', '=', 'items_images.item_id')
            ->leftJoin('files', 'files.id', '=', 'items_images.file_id')
            ->leftJoin('item_price_rule','items.id','=','item_price_rule.item_id')
            ->leftJoin('price_rules','item_price_rule.price_rule_id','=','price_rules.id')
            ->leftJoin('suppliers','suppliers.id','=','items.supplier_id')
            ->where('items.deleted_at',null)
            ->where('items.item_status',ItemStatus::$ACTIVE)
            ->select('items.id as item_id','price_rules.id as price_rule_id','items.*','files.*','price_rules.*','suppliers.*')
            ->groupBy('items.item_name')
            ->where('items.product_type','<>',2);
    }

    public function searchForBarcodeItem($search_param){
        if( $search_param != "" || $search_param != null ) {
            $dataAfterScanValidate = $this->checkAndValidateItemScanPrice($search_param);
            $priceNeededToBeScanned = $dataAfterScanValidate["priceNeededToBeScanned"];
            $item_new_price_from_barcode = $dataAfterScanValidate["item_new_price_from_barcode"];
            if($priceNeededToBeScanned) {
                $items =  $this->generalItemQuery()->where(function($query) use ($search_param) {
                    $query->where('isbn','like',"%".$search_param."%");
                })->first();
            }
            else {
                //check if product exists by normal match query
                //it will solve if both query DB with checkdigit-scan with checkdigit, DB without checkdigit-scan without checkdigit
                $items = $this->checkScanDigit($search_param);
            }

            if(!is_null($items) && count($items) !=0) {

                $itemsWithItemKits = array($items);

                $current_date = new \DateTime('today');
                // Check price rules on specific items

                foreach($itemsWithItemKits as $anItem) {
                    $this->getItemAfterPriceRuleValidation($anItem,true,
                        $item_new_price_from_barcode, $current_date);
                }
                return $itemsWithItemKits;
            }
            return [];
        }
        return [];
    }

    public function getItemAfterPriceRuleValidation($anItem, $priceNeededToBeScanned = false,
        $item_new_price_from_barcode = -1, $date) {

        $current_date = $date;

        if(is_null($anItem->price_rule_id))
            $anItem->price_rule_id = 0;
        if(isset($anItem->item_id)){

            $anItem->scan_type = "auto";
            if($priceNeededToBeScanned) {
                $anItem->new_price = $item_new_price_from_barcode;
                $anItem->useScanPrice = true;
            }
            if ($anItem->active){

                if($anItem->type==1){

                    if($anItem->percent_off>0){

                        $rule_start_date = new \DateTime($anItem->start_date);
                        $rule_expire_date = new \DateTime($anItem->end_date);

                        if(($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) ) {
                            $discountPercentage = $anItem->percent_off;

                            if($discountPercentage>100){
                                $anItem->discountPercentage = 100;
                                $anItem->itemPrice = $anItem->selling_price;
                                $anItem->discountName = $anItem->name;
                                $anItem->discountAmount = $anItem->itemPrice*($discountPercentage/100);
                                $anItem->itemPriceAfterDiscount = $anItem->itemPrice-$anItem->discountAmount;
                                $anItem->discountApplicable = true;
                            }else{
                                $anItem->discountPercentage = $discountPercentage;
                                $anItem->itemPrice = $anItem->selling_price;
                                $anItem->discountName = $anItem->name;
                                $anItem->discountAmount = $anItem->itemPrice*($discountPercentage/100);
                                $anItem->itemPriceAfterDiscount = $anItem->itemPrice-$anItem->discountAmount;
                                $anItem->discountApplicable = true;
                            }

                        }else{
                            $anItem->discountApplicable = false;
                        }

                        //echo "Item should be discounted by ".$anItem->percent_off." percent";

                    }else if($anItem->fixed_of>0){

                        $rule_start_date = new \DateTime($anItem->start_date);
                        $rule_expire_date = new \DateTime($anItem->end_date);

                        if( ($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) ) {
                            $discountPercentage = ($anItem->fixed_of/$anItem->selling_price)*100;
                            if($discountPercentage>100){
                                $anItem->discountPercentage = 100;
                                $anItem->discountAmount = $anItem->selling_price;
                                $anItem->discountName = $anItem->name;
                                $anItem->itemPrice = $anItem->selling_price;
                                $anItem->itemPriceAfterDiscount = $anItem->itemPrice - $anItem->itemPrice;
                                $anItem->discountApplicable = true;
                            }
                            else{
                                $anItem->discountPercentage = $discountPercentage;
                                $anItem->discountAmount = $anItem->fixed_of;
                                $anItem->discountName = $anItem->name;
                                $anItem->itemPrice = $anItem->selling_price;
                                $anItem->itemPriceAfterDiscount = $anItem->itemPrice - $anItem->discountAmount;
                                $anItem->discountApplicable = true;
                            }

                        }else{
                            $anItem->discountApplicable = false;
                        }
                        // echo "Item should be discounted by ".$anItem->fixed_of." dollar";
                    }

                }

            }
        }

        return $anItem;
    }

    public function checkScanDigit($search_param) {
        $matchedItem = DB::table('items')
            ->where('isbn','=',$search_param)
            ->where('deleted_at',null)
            ->where('item_status',ItemStatus::$ACTIVE)
            ->where('product_type','<>',2)
            ->first();
        if(!$matchedItem)
        {
            //check DB checkdigit-scan without checkdigit
            $matchedItem = DB::table('items')
                ->where('isbn','like', '_' . $search_param . '_' )
                ->where('deleted_at',null)
                ->where('item_status',ItemStatus::$ACTIVE)
                ->where('product_type','<>',2)
                ->first();
        }

        if(!$matchedItem)
        {
            //check DB without checkdigit-scan with checkdigit
            $cleanedParam = strpos($search_param, 1 , -1);
            $matchedItem = DB::table('items')
                ->where('isbn','=',$cleanedParam)
                ->where('deleted_at',null)
                ->where('item_status',ItemStatus::$ACTIVE)
                ->where('product_type','<>',2)
                ->first();
        }
        if(!$matchedItem)
            return [];

        $items =  DB::table('items')
            ->leftJoin('items_images', 'items.id', '=', 'items_images.item_id')
            ->leftJoin('files', 'files.id', '=', 'items_images.file_id')
            ->leftJoin('item_price_rule','items.id','=','item_price_rule.item_id')
            ->leftJoin('price_rules','item_price_rule.price_rule_id','=','price_rules.id')
            ->leftJoin('suppliers','suppliers.id','=','items.supplier_id')
            /*->where(function($query) use ($search_param) {
                $query->where('isbn','=',$search_param);
            })
            ->where('items.deleted_at',null)
            ->where('items.item_status',ItemStatus::$ACTIVE)*/
            ->select('items.id as item_id','price_rules.id as price_rule_id','items.*','files.*','price_rules.*','suppliers.*')
            ->groupBy('items.item_name')
            //->where('items.product_type','<>',2)
            ->where('items.id',$matchedItem->id)
            ->first();

            return $items;
    }
}
