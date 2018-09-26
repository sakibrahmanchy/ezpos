<?php

namespace App\Model;

use App\Enumaration\CashRegisterTransactionType;
use App\Enumaration\InventoryReasons;
use App\Enumaration\InventoryTypes;
use App\Enumaration\LotyaltyTransactionType;
use App\Enumaration\PaymentTransactionTypes;
use App\Enumaration\PaymentTypes;
use App\Enumaration\SaleStatus;
use App\Enumaration\SaleTypes;
use App\Library\SettingsSingleton;
use App\Model\Item;
use App\Model\PaymentLog;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Model\LoyaltyTransaction;
use App\Model\CustomerTransaction;
use App\Model\CashRegister;
use DB;

class Sale extends Model
{
    use SoftDeletes;
    public $incrementing = false;
    public $keyType = "string";

    public function Items(){
        return $this->belongsToMany('App\Model\Item')->withPivot('quantity','unit_price',
            'total_price','discount_amount','item_discount_percentage','is_price_taken_from_barcode')->with('pricerule', 'Category','Supplier');
    }

    public function ItemKits(){
        return $this->belongsToMany('App\Model\ItemKit','item_kit_sale','item_kit_id','sale_id')->withPivot('quantity','unit_price',
            'total_price','discount_amount','item_discount_percentage')->with('PriceRule');
    }

    public function PaymentLogs(){
        return $this->belongsToMany('App\Model\PaymentLog')->where('payment_type','<>',PaymentTypes::$TypeList['Due']);
    }

    public function PaymentLogsWithDue(){
        return $this->belongsToMany('App\Model\PaymentLog');
    }

    public function Customer(){
        return $this->belongsTo('App\Model\Customer');
    }

    public function Employee(){
        return $this->belongsTo('App\Model\User');
    }

    public function counter(){
        return $this->belongsTo('App\Model\Counter');
    }

    public function InsertSale($saleInfo, $productInfos,$paymentInfos , $saleStatus){

        if($saleStatus!=1)
            session()->put('success','Sale has been successfully suspended');

		if(isset($saleInfo["cash_register_id"])) {
            $activeRegister = CashRegister::where("id",$saleInfo["cash_register_id"])->first();
        } else {
            $cashRegister = new CashRegister();
            $activeRegister = $cashRegister->getCurrentActiveRegister();
        }

                $sale = $this->insertSaleInfo($saleInfo,$saleStatus, $activeRegister->id);

            $sale_id = $sale->id;

            $this->insertItemsInSale($productInfos,$sale,$saleStatus);

            if(!is_null($paymentInfos))
                $this->insertSalePaymentInfos($paymentInfos,$sale);

            if( $saleInfo['customer_id'] != 0){
                //Check if customer has a loyalty card or not
                $this->addCustomerLoyaltyBalance($sale->customer_id, $sale_id, $sale->total_amount);
                $this->InsertIntoCustomerAccount($sale->customer_id, $sale_id, $sale->total_amount, $sale->due, $sale->cash_register_id,$sale->status );
            }

            return $sale_id;


    }
	
	
	public function InsertIntoCustomerAccount( $customer_id, $sale_id, $sale_amount, $due_amount, $cash_register_id, $sale_status )
	{
		$paid_amount = $sale_amount - $due_amount;
		/*if( $sale_status==\App\Enumaration\SaleStatus::$LAYAWAY )
		{
			$paid_amount = 0;
			$due_amount = $sale_amount;
		}*/
		$customerTransactionObj = new CustomerTransaction();
		$customerTransactionObj->transaction_type = \App\Enumaration\CustomerTransactionType::SALE;
		$customerTransactionObj->sale_id = $sale_id;
		$customerTransactionObj->sale_amount = $sale_amount;
		$customerTransactionObj->paid_amount = $paid_amount;
		$customerTransactionObj->customer_id = $customer_id;
		$customerTransactionObj->cash_register_id = $cash_register_id;
		$customerTransactionObj->save();
		
		
		if($due_amount<0)
			$due_amount = 0;
		
		$updateCustomerBalanceQuery = "update customers set account_balance=account_balance+? where id=?";
		DB::update( $updateCustomerBalanceQuery, [ $due_amount, $customer_id] );
	}
	
	public function UpdateCustomerAccount( $customer_id, $sale_id, $sale_amount, $due_amount, $cash_register_id, $sale_status )
	{
		$paid_amount = $sale_amount - $due_amount;
		/*if( $sale_status==\App\Enumaration\SaleStatus::$LAYAWAY )
		{
			$paid_amount = 0;
			$due_amount = $sale_amount;
		}*/
		$customerTrandactionInfo = CustomerTransaction::where('sale_id', $sale_id )
							->where('transaction_type' , \App\Enumaration\CustomerTransactionType::SALE)
							->first();

		if(is_null($cash_register_id)) {
		    $cash_register = new CashRegister();
		    $cash_register_id = $cash_register->getCurrentActiveRegister()->id;
        }
		if($customerTrandactionInfo)
		{
			if(!$customer_id)
				$customerTrandactionInfo->delete();
			else
				$customerTrandactionInfo->update([
					'paid_amount' => $paid_amount,
					'sale_amount' => $sale_amount,
					'customer_id' => $customer_id
				]);
		}
		else
			$this->InsertIntoCustomerAccount( $customer_id, $sale_id, $sale_amount, $due_amount, $cash_register_id, $sale_status );
	}

    public function generateRandomNumber($digits) {
        return rand(pow(10, $digits-1), pow(10, $digits)-1);
    }


    public function getMaxSaleIdByCounter($counterId) {
		$counter = Counter::where("id",$counterId)->first();
		/*$nextCounter = Counter::where('starting_id','>',$counter->starting_id)
										->orderBy('starting_id', 'desc')
										->first();
		
        $saleId = Sale::withTrashed()
						->where("counter_id",$counterId);
		if($nextCounter)
			$saleId = $saleId->where('id','<',$nextCounter->starting_id);
		$saleId	= $saleId->max("id");*/
        
		$saleId = Sale::withTrashed()
						->where("counter_id",$counterId)
						->whereNotIn(DB::raw('id+1'),function($q) use ($counterId)
								{
								   $q->from('sales')
									->select('id');
								})
						->where('id','>=', $counter->starting_id )
						->min('id');

        if(is_null($saleId) || $saleId == 0 || $saleId < $counter->starting_id)
           return $counter->starting_id;
        return $saleId+1;
    }

    /**
     * @return bigInteger value related to current counter
     */
    public function generateSalesID($counterId = null) {
        //Previous algo, not currently active
//        $timestamp = date("Ymdhs",time());
//        $randomNumber = $this->generateRandomNumber(3);
//        $counterCode = Counter::where("id",$counterId)->first()->counter_code;
        if(is_null($counterId)) {
            $counterId = Cookie::get("counter_id");
            $counter = Counter::where("id",$counterId)->first();
            if(is_null($counter))
                return redirect()->route('new_sale')->with('Error', 'No counters Selected');
        }

        return $this->getMaxSaleIdByCounter($counterId);
    }

    public function insertSaleInfo($saleInfo,$saleStatus, $registerId){

        $sale = new Sale();
        $sale->id = $this->generateSalesID(isset($saleInfo['counter_id']) ? $saleInfo['counter_id'] : null);
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
        if(isset($saleInfo['counter_id'])) {
            $sale->counter_id = $saleInfo['counter_id'];
        }
        else {
            if (Cookie::get('counter_id') !== false) {
                $sale->counter_id = Cookie::get("counter_id");
            }
            else
                $sale->counter_id = 0;

        }

        $sale->comment = $saleInfo["comment"];
        $sale->cash_register_id = $registerId;
        $sale->total_sales_discount = $saleInfo['total_sales_discount'];
        $sale->save();

        SaleStatusLog::changeSaleStatus($sale->id,null,$saleStatus);

        //Inserting due as a transaction
        self::insertDueAsPaymentInSale($sale);

        return $sale;

    }

    public static function insertDuePaymentInPaymentLog($sale) {
        $paymentLogObject = new \App\Model\PaymentLog();
        $comments =   "Due for sale: ".$sale->id;
        $paymentLogObject->addNewPaymentLog( PaymentTypes::$TypeList["Due"], $sale->due,$sale,$sale->customer_id, $comments);
    }

    public static function insertDueAsPaymentInSale($sale) {
        if($sale->due < 0) {
            if(!self::checkIfDueAlreadyExistForSale($sale->id)) {
                self::insertDuePaymentInPaymentLog($sale);
            } else {
                PaymentLog::where("sale_id",$sale->id)
                    ->where("payment_type",PaymentTypes::$TypeList["Due"])
                    ->delete();
                self::insertDuePaymentInPaymentLog($sale);
            }
        }
    }

    public function insertItemsInSale($productInfos,$sale,$saleStatus) {

        foreach($productInfos as $aProductInfo) {

            $item_id = $aProductInfo['item_id'];
            $item_quantity = $aProductInfo['quantity'];
            $item_type = $aProductInfo["item_type"];

            if($item_id==0) {

                $keyId = "discount-01!XcQZc003ab";

                $item = Item::where("product_id",$keyId)->first();

                if(is_null($item)){

                    $itemId = $this->InsertDiscountAsAnItemInItemsTable($keyId,$aProductInfo["unit_price"]);

                }else
                    $itemId = $item->id;

                $aProductInfo['item_id'] = $itemId;

                $sale->Items()->attach([$itemId=>$aProductInfo]);

            } else {

                if($saleStatus!=SaleStatus::$ESTIMATE) {

                    if($item_type=="item"){

                        $item = Item::where("id",$item_id)->first();
                        $quantity_change = $this->decreaseItemQuantity($item,$item_quantity);

                        if($quantity_change!=0){
                            $this->itemChangeInventoryLog($item_id,$sale->id,$quantity_change);
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
                                    $this->itemChangeInventoryLog($item_id,$sale->id,$quantity_change);
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
    }

    public function insertPaymentInfo($aPaymentInfo, $sale) {

        $sale_id = $sale->id;

        $paymentLogObject = new \App\Model\PaymentLog();
        $comments =   "Sales for sale: ".$sale_id;
        $insertedPaymentLog = $paymentLogObject->addNewPaymentLog( PaymentTypes::$TypeList[$aPaymentInfo["payment_type"]], $aPaymentInfo["paid_amount"],$sale,$sale->customer_id, $comments);

//        switch($aPaymentInfo["payment_type"]){
//            case 'Cash':
//                $cashRegisterTransaction = new CashRegisterTransaction();
//                $cashRegisterTransaction->newCashRegisterTransaction($sale_id,$aPaymentInfo["paid_amount"],
//                    CashRegisterTransactionType::$CASH_SALES,$insertedPaymentLog->id);
//                break;
//            case 'Check':
//                $cashRegisterTransaction = new CashRegisterTransaction();
//                $cashRegisterTransaction->newCashRegisterTransaction($sale_id,$aPaymentInfo["paid_amount"],
//                    CashRegisterTransactionType::$CHECK_SALES,$insertedPaymentLog->id);
//                break;
//
//            case 'Debit Card':
//                $cashRegisterTransaction = new CashRegisterTransaction();
//                $cashRegisterTransaction->newCashRegisterTransaction($sale_id,$aPaymentInfo["paid_amount"],
//                    CashRegisterTransactionType::$DEBIT_CARD_SALES,$insertedPaymentLog->id);
//                break;
//
//            case 'Credit Card':
//                $cashRegisterTransaction = new CashRegisterTransaction();
//                $cashRegisterTransaction->newCashRegisterTransaction($sale_id,
//                    $aPaymentInfo["paid_amount"],CashRegisterTransactionType::$CREDIT_CARD_SALES,$insertedPaymentLog->id);
//                break;
//
//            case 'Gift Card':
//                $cashRegisterTransaction = new CashRegisterTransaction();
//                $cashRegisterTransaction->newCashRegisterTransaction($sale_id,$aPaymentInfo["paid_amount"],
//                    CashRegisterTransactionType::$GIFT_CARD_SALES,$insertedPaymentLog->id);
//                break;
//
//            case 'Loyalty Card':
//                $cashRegisterTransaction = new CashRegisterTransaction();
//                $cashRegisterTransaction->newCashRegisterTransaction($sale_id,$aPaymentInfo["paid_amount"],
//                    CashRegisterTransactionType::$LOYALTY_CARD_SALES,$insertedPaymentLog->id);
//                break;
//
//            default:
//                break;
//        }

        if(strpos($aPaymentInfo["payment_type"],"Loyalty Card")!==false){
            $loyaltyTransaction = new LoyaltyTransaction();
            $loyaltyTransaction->NewLoyaltyTransaction($sale->customer_id,$aPaymentInfo["paid_amount"],LotyaltyTransactionType::$DEBIT_BALANCE,$sale_id);
        }

        return $insertedPaymentLog;
    }

    public function insertSalePaymentInfos($paymentInfos,$sale) {
        foreach($paymentInfos as $aPaymentInfo){
            $this->insertPaymentInfo($aPaymentInfo, $sale);
        }
    }


    public function addCustomerLoyaltyBalance($customer_id, $sale_id, $sale_total){
        $customerLoyalty = new LoyaltyTransaction();
        if($customerLoyalty->CustomerHasLoyalty($customer_id)) {
            $creditAmount = $customerLoyalty->IncreaseCustomerLoyalty($customer_id,$sale_total);
            $loyaltyTransaction = new LoyaltyTransaction();
            $loyaltyTransaction->NewLoyaltyTransaction($customer_id,$creditAmount,LotyaltyTransactionType::$CREDIT_BALANCE,$sale_id);
        }
    }



    public function editSale($saleInfo, $productInfos,$paymentInfos, $deletedTransactions, $saleStatus, $sale_id) {

        if($saleStatus!=1)
            session()->put('success','Sale has been successfully suspended');

        $sale = Sale::where('id',$sale_id)->first();
        $previousStatus = $sale->sale_status;

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
        $sale->counter_id = Cookie::get("counter_id");
        $sale->comment = $saleInfo["comment"];
        $sale->total_sales_discount = $saleInfo["total_sales_discount"];

        $sale->save();

        SaleStatusLog::changeSaleStatus($sale->id,$previousStatus,$saleStatus);

        self::insertDueAsPaymentInSale($sale);

        $sale_id = $sale->id;

        foreach($productInfos as $aProductInfo){

            $item_id = $aProductInfo['item_id'];
            $item_quantity = $aProductInfo['quantity'];
            $item_type = $aProductInfo["item_type"];
            if($item_id==0){
                $keyId = "discount-01!XcQZc003ab";
                $item = Item::where("product_id",$keyId)->first();

                if(is_null($item)) {

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

                } else
                    $itemId = $item->id;

                $aProductInfo['item_id'] = $itemId;

                $productInfos[0]['item_id'] = $itemId;

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

                    } else if($item_type=="item-kit"){
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
               if($payment_id==0) {
                   $insertedPaymentLog = $this->insertPaymentInfo($aPaymentInfo, $sale);
                   $payment_id = $insertedPaymentLog->id;
                }
                array_push($payments, $payment_id);
            }
            $sale->paymentLogs()->sync($payments);
        }

        self::deleteItemsFromPaymentLogWhichAreNoLongerRequired($sale_id);

        if( $saleInfo['customer_id'] != 0){
            //Check if customer has a loyalty card or not
            $this->addCustomerLoyaltyBalance($sale->customer_id, $sale_id, $sale->total_amount);
        }




        foreach ($deletedTransactions as $aDeletedTransaction) {
            $cashRegisterTransaction = new CashRegisterTransaction();
            $cashRegisterTransaction->deleleCashRegisterTransactionByPaymentLogId($aDeletedTransaction);
        }

		
		$this->UpdateCustomerAccount( $sale->customer_id, $sale_id, $sale->total_amount, $sale->due, $sale->cash_register_id, $sale->status );
		
        return $sale_id;


    }

    public function DeleteSale($saleId){
        $sale = $this::where("id",$saleId)->first();
        $sale->delete();
    }

    public function InsertDiscountAsAnItemInItemsTable($keyId, $unitPrice){

        $item = new Item();
        $item->product_id=$keyId;
        $item->item_name = "discount";
        $item->category_id = 0;
        $item->supplier_id = 0;
        $item->product_type = 2;
        $item->cost_price = $unitPrice;
        $item->selling_price = $unitPrice;
        $item->save();

        return $item->id;
    }

    public function decreaseItemQuantity($item,$quantityToLess){
        $item->item_quantity -= $quantityToLess;
        $previous_item_quantity = $item->item_quantity;
        $item->save();
        $current_item_quantity = $item->item_quantity;
        $quantity_change = $current_item_quantity - $previous_item_quantity;
        return $quantity_change;
    }

    public function itemChangeInventoryLog($item_id,$sale_id, $quantity_change) {
        $inventoryLog = new InventoryLog();
        $inventoryLog->item_id = $item_id;
        $inventoryLog->in_out_quantity = $quantity_change;
        if($quantity_change>0)
            $inventoryLog->type = InventoryTypes::$ADD_INVENTORY;
        else
            $inventoryLog->type = InventoryTypes::$SUBTRACT_INVENTORY;

        $inventoryLog->reason = InventoryReasons::$SALEORRETURN." (<a href=". route('sale_receipt',["sale_id"=>$sale_id]) .">EZPOS ".$sale_id."</a>)";
        $inventoryLog->user_id = Auth::user()->id;
        $inventoryLog->save();
    }

    public static function checkIfDueAlreadyExistForSale($sale_id) {
        if(\App\Model\PaymentLog::where("sale_id",$sale_id)->where("payment_type",\App\Enumaration\PaymentTypes::$TypeList["Due"])->count()>0)
            return true;
        return false;
    }

    public static function getListSalesFromPaymentLogWithMultipleDue() {
        return DB::select('select sale_id from 
                            (
                              select sale_id, count(*) as total_count from `payment_logs` 
                              where `payment_type` = 9 
                              group by `sale_id`
                            ) as count_info 
                            where count_info.total_count > 1'
                           );
    }

    public static function _eloquentToArray($eloquentArray, $column_name) {
        $arrayToPush = array();
        foreach ($eloquentArray as $anArrayElement) {
            array_push($arrayToPush,$anArrayElement->{$column_name});
        }
        return $arrayToPush;
    }

    public static function deleteItemsFromPaymentLogWhichAreNoLongerRequired($sale_id) {

        /*$paymentLogsRequiredForThisSale = self::_eloquentToArray(DB::table("payment_log_sale")->where('sale_id',$sale_id)->select('payment_log_id')->get(),
            "payment_log_id");


        $allPaymentLogsForThisSale = self::_eloquentToArray(PaymentLog::where("sale_id",$sale_id)->select("id")->get(), "id");

        $paymentLogWhichAreNoLongerRequired = array_diff($allPaymentLogsForThisSale,$paymentLogsRequiredForThisSale);

        PaymentLog::whereIn("id",$paymentLogWhichAreNoLongerRequired)->delete();*/



        $query = "Delete from payment_logs where sale_id=? and id not in(select payment_log_id from payment_log_sale where sale_id=?) and payment_type!=?";
        DB::delete($query, [$sale_id, $sale_id, PaymentTypes::$TypeList['Due']]);
    }

    public static function getDeletedSales($cashRegisterId) {
        $deletedSales = Sale::withTrashed()->whereNotNull("refund_register_id");

        if($cashRegisterId != 0)
            $deletedSales = $deletedSales->where("refund_register_id",$cashRegisterId);

        return $deletedSales->get();
    }

}
