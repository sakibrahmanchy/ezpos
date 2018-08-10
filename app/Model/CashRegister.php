<?php

namespace App\Model;

use App\Enumaration\CashRegisterTransactionType;
use App\Enumaration\PaymentTypes;
use App\Enumaration\SaleTypes;
use FontLib\Header;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CashRegister extends Model
{

    protected $fillable = ['opening_balance','closing_balance','current_balance','counter_id',
        'opened_by','closed_by','opening_time','closing_time'];


    public function OpenedByUser(){
        return $this->belongsTo('App\Model\User','opened_by','id');
    }

    public function ClosedByUser(){
        return $this->belongsTo('App\Model\User','closed_by','id');
    }

    public function CashRegisterTransactions(){
//        return $this->hasMany('App\Model\CashRegisterTransaction','cash_register_id','id');
        return $this->hasMany('App\Model\PaymentLog','cash_register_id','id');
    }

    public function PaymentLogs(){
        return $this->hasMany('App\Model\PaymentLog','cash_register_id','id');
    }

    public function additionSum(){
            return $this->PaymentLogs()
            ->selectRaw('cash_register_id, sum(paid_amount) as aggregate')
            ->where("payment_type",CashRegisterTransactionType::$ADD_BALANCE)
            ->groupBy('cash_register_id');
    }

    public function subtractionSum(){
        return $this->PaymentLogs()
            ->selectRaw('cash_register_id, sum(paid_amount) as aggregate')
            ->where("payment_type",CashRegisterTransactionType::$SUBTRACT_BALANCE)
            ->groupBy('cash_register_id');
    }

    public function saleSum(){
        return $this->PaymentLogs()
            ->selectRaw('cash_register_id, sum(paid_amount) as aggregate')
            ->where("payment_type",CashRegisterTransactionType::$CASH_SALES)
            ->groupBy('cash_register_id');
    }

    public function Counter(){
        return $this->belongsTo('App\Model\Counter','counter_id','id');
    }


    public function getCurrentActiveRegister(){
        $activeRegister =  $this->orderBy('opening_time', 'desc')
            ->where('closing_balance',null)
            ->where( 'user_id',Auth::id() )->first();

        return $activeRegister;
    }

    public function getTotalAddedAmountInActiveRegister(){

        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){
            $cashRegisterId = $cash_register->id;
            $addedTotal = PaymentLog::where("cash_register_id",$cashRegisterId)
                ->where("payment_type",CashRegisterTransactionType::$ADD_BALANCE)->sum("paid_amount");
            return $addedTotal;
        }
        return 0;
    }

    public static function isThereAnyOtherRegistersThatAreOpenedByTheUser($user_id) {
        return is_null(CashRegister::where('user_id',$user_id)->whereNotNull('created_at')->first());
    }

    public function getActiveRegisterOpeningBalance(){
        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){
            $cashRegisterId = $cash_register->id;
            $opening_balance = CashRegister::where("id",$cashRegisterId)->first()->opening_balance;
            return $opening_balance;
        }
        return 0;
    }

    public function getTotalSubtractedAmountInActiveRegister(){

        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){
            $cashRegisterId = $cash_register->id;
            $subtractedTotal = PaymentLog::where("cash_register_id",$cashRegisterId)
                ->where("payment_type",CashRegisterTransactionType::$SUBTRACT_BALANCE)
                ->sum("paid_amount");
            return $subtractedTotal;
        }
        return 0;
    }

    public function getPreviousClosingBalance(){
        $previousCashRegister = $this->orderBy('opening_time', 'desc')->where('closing_balance','<>',null)->where( 'user_id',Auth::id() )->first();
        if(!is_null($previousCashRegister))
            return $previousCashRegister->closing_balance;
        else
            return 0.0;
    }

    public function addCashToRegister($amount, $comment){
        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){

            $cash_register->current_balance += $amount;
            if($cash_register->save()){
                $paymentLog = new PaymentLog;
                $paymentLog->addNewPaymentLog(CashRegisterTransactionType::$ADD_BALANCE,$amount,null,null,$comment);
                return true;
            }
        }
        return false;
    }

    public function subtractCashFromRegister($amount, $comment){
        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){

            $cash_register->current_balance -= $amount;
            if($cash_register->save()){
                $paymentLog = new PaymentLog;
                $paymentLog->addNewPaymentLog(CashRegisterTransactionType::$SUBTRACT_BALANCE,$amount,null,null,$comment);
                return true;
            }
        }
        return false;
    }

    public function getTotalSaleInCurrentRegister($transactionType,$sale_status = array()){
        $cash_register = $this->getCurrentActiveRegister();
        if(!is_null($cash_register)){
            $total_sales_in_register = PaymentLog::where("payment_logs.cash_register_id",$cash_register->id)
                ->join('sales','sales.id','=','payment_logs.sale_id')
                ->whereNull('sales.refund_register_id')
                ->where('sale_type',SaleTypes::$SALE)
                ->whereIn("payment_logs.sale_status",$sale_status)
                ->where("payment_type",$transactionType)
                ->sum('paid_amount');
            return $total_sales_in_register;
        }
        return 0;

    }

    public function getRefundedSalesInCashRegister($register_id) {
        $refundedSales = Sale::withTrashed()->where("refund_register_id",$register_id)->get();
        return $refundedSales;
    }

    public function getRefundedSalesAmountInCashRegister($register_id) {
        $refundedSalesAmount = Sale::withTrashed()->where("refund_register_id",$register_id)->sum('total_amount');
        return $refundedSalesAmount;
    }

    public static function generateTransactionData($paymentLogList, $cashRegister, $saleStatus) {
        $allTransactionArr = [];;

        $cashAmount = 0;
        $chequeAmount = 0;
        $creditCardAmount = 0;
        $debitCardAmount = 0;
        $giftCardAmount = 0;
        $loyalityAmount = 0;
        $changedDue = 0;

        foreach($paymentLogList as $aPaymentLog)
        {
            if($aPaymentLog->sale_status == $saleStatus) {
                if( $aPaymentLog->payment_type==PaymentTypes::$TypeList["Cash"] )
                    $cashAmount += floatval($aPaymentLog->paid_amount);
                else if($aPaymentLog->payment_type==PaymentTypes::$TypeList["Check"])
                    $chequeAmount += floatval($aPaymentLog->paid_amount);
                else if($aPaymentLog->payment_type==PaymentTypes::$TypeList["Credit Card"])
                    $creditCardAmount += floatval($aPaymentLog->paid_amount);
                else if($aPaymentLog->payment_type==PaymentTypes::$TypeList["Debit Card"])
                    $debitCardAmount += floatval($aPaymentLog->paid_amount);
                else if($aPaymentLog->payment_type==PaymentTypes::$TypeList["Gift Card"])
                    $giftCardAmount += floatval($aPaymentLog->paid_amount);
                else if($aPaymentLog->payment_type==PaymentTypes::$TypeList["Loyalty Card"])
                    $loyalityAmount += floatval($aPaymentLog->paid_amount);
                else if($aPaymentLog->payment_type==PaymentTypes::$TypeList["Due"])
                    $changedDue += floatval($aPaymentLog->paid_amount);
            }

            if( $cashAmount > 0 )
            {
                $cashAmount  += $changedDue ;
                $allTransactionArr[] = [
                    'sale_id' => $aPaymentLog->sale_id,
                    'created_at' => $aPaymentLog->created_at,
                    'payment_type' => \App\Enumaration\CashRegisterTransactionType::$CASH_SALES,
                    'amount' => $cashAmount
                ];
            }
            if( $chequeAmount > 0 )
            {
                $allTransactionArr[] = [
                    'sale_id' => $aPaymentLog->sale_id,
                    'created_at' => $aPaymentLog->created_at,
                    'payment_type' => \App\Enumaration\CashRegisterTransactionType::$CHECK_SALES,
                    'amount' => $chequeAmount
                ];
            }
            if( $creditCardAmount > 0 )
            {
                $allTransactionArr[] = [
                    'sale_id' => $aPaymentLog->sale_id,
                    'created_at' => $aPaymentLog->created_at,
                    'payment_type' => \App\Enumaration\CashRegisterTransactionType::$CREDIT_CARD_SALES,
                    'amount' => $creditCardAmount
                ];
            }
            if( $debitCardAmount > 0 )
            {
                $allTransactionArr[] = [
                    'sale_id' => $aPaymentLog->sale_id,
                    'created_at' => $aPaymentLog->created_at,
                    'payment_type' => \App\Enumaration\CashRegisterTransactionType::$DEBIT_CARD_SALES,
                    'amount' => $debitCardAmount
                ];
            }
            if( $giftCardAmount > 0 )
            {
                $allTransactionArr[] = [
                    'sale_id' => $aPaymentLog->sale_id,
                    'created_at' => $aPaymentLog->created_at,
                    'payment_type' => \App\Enumaration\CashRegisterTransactionType::$GIFT_CARD_SALES,
                    'amount' => $giftCardAmount
                ];
            }
            if( $loyalityAmount > 0 )
            {
                $allTransactionArr[] = [
                    'sale_id' => $aPaymentLog->sale_id,
                    'created_at' => $aPaymentLog->created_at,
                    'payment_type' => \App\Enumaration\CashRegisterTransactionType::$LOYALTY_CARD_SALES,
                    'amount' => $loyalityAmount
                ];
            }
        }


        foreach( $paymentLogList as $aCashRegisterTransaction )
        {
            $addedAmount = 0;
            $subtractedAmount = 0;

            if($aCashRegisterTransaction->payment_type ==\App\Enumaration\CashRegisterTransactionType::$ADD_BALANCE)
            {
                $addedAmount += floatval(($aCashRegisterTransaction->paid_amount));
                $allTransactionArr[] = [
                    'created_at' => $aCashRegisterTransaction->created_at,
                    'payment_type' => \App\Enumaration\CashRegisterTransactionType::$ADD_BALANCE,
                    'amount' => $addedAmount
                ];
            }
            if($aCashRegisterTransaction->payment_type ==\App\Enumaration\CashRegisterTransactionType::$SUBTRACT_BALANCE)
            {
                $subtractedAmount += floatval(($aCashRegisterTransaction->paid_amount));
                $allTransactionArr[] = [
                    'created_at' => $aCashRegisterTransaction->created_at,
                    'payment_type' => \App\Enumaration\CashRegisterTransactionType::$SUBTRACT_BALANCE,
                    'amount' => $subtractedAmount
                ];
            }
        }

        usort($allTransactionArr,
            function ( $a, $b ) {
                return strtotime($a["created_at"]) >= strtotime($b["created_at"]);
            }
        );

        return $allTransactionArr;

    }

    public static function getPaymentAmountTotalList($cashRegisterId, $sale_status = array()) {
        return PaymentLog::join('sales','sales.id','=','payment_logs.sale_id')
            ->where('payment_logs.cash_register_id','=',$cashRegisterId)
            ->whereIn('payment_logs.sale_status',$sale_status)
            ->where('sale_type',SaleTypes::$SALE)
            ->whereNull('refund_register_id')
            ->groupBy('payment_logs.payment_type')
            ->select(DB::raw('payment_type, sum(paid_amount) as total_paid_amount'))
            ->get();

    }

    public static function generatePaymentAmount($cashRegisterId, $sale_status = array()) {

        $paymentAmountTotalList = CashRegister::getPaymentAmountTotalList($cashRegisterId,$sale_status);

        $cashTotal = 0;
        $checkTotal = 0;
        $creditCardAmountTotal = 0;
        $debitCardAmountTotal = 0;
        $giftCardAmountTotal = 0;
        $loyalityAmountTotal = 0;
        $changedDue = 0;

        foreach( $paymentAmountTotalList as $aPaymentTotal )
        {
            if($aPaymentTotal->payment_type==PaymentTypes::$TypeList['Cash'])
                $cashTotal = floatval($aPaymentTotal->total_paid_amount);
            if($aPaymentTotal->payment_type==PaymentTypes::$TypeList['Check'])
                $checkTotal = floatval($aPaymentTotal->total_paid_amount);
            else if($aPaymentTotal->payment_type==PaymentTypes::$TypeList['Credit Card'])
                $creditCardAmountTotal = floatval($aPaymentTotal->total_paid_amount);
            else if($aPaymentTotal->payment_type==PaymentTypes::$TypeList['Debit Card'])
                $debitCardAmountTotal = floatval($aPaymentTotal->total_paid_amount);
            else if($aPaymentTotal->payment_type==PaymentTypes::$TypeList['Gift Card'])
                $giftCardAmountTotal = floatval($aPaymentTotal->total_paid_amount);
            else if($aPaymentTotal->payment_type==PaymentTypes::$TypeList['Loyalty Card'])
                $loyalityAmountTotal = floatval($aPaymentTotal->total_paid_amount);
            else if($aPaymentTotal->payment_type==PaymentTypes::$TypeList['Due'])
                $changedDue = floatval($aPaymentTotal->total_paid_amount);
        }

        $paymentInfo = array(
            "cashTotal" => $cashTotal + $changedDue,
            "checkTotal" => $checkTotal,
            "creditCardTotal" => $creditCardAmountTotal,
            "debitCardTotal" => $debitCardAmountTotal,
            "giftCardTotal" => $giftCardAmountTotal,
            "loyalityTotal" => $loyalityAmountTotal,
            "changedDue" => $changedDue
        );

        return $paymentInfo;
    }


}
