<?php
/**
 * Created by PhpStorm.
 * User: ByteLab
 * Date: 8/9/2018
 * Time: 12:22 PM
 */
namespace App\Model\Api;

use App\Enumaration\PaymentTypes;
use Illuminate\Database\Eloquent\Model;

class ProcessOrder extends Model
{
    private $data;

    /**
     * ProcessOrder constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }


    public function processCustomerAndGetId() {

        if(isset($this->data->customer_id) && CheckNull($this->data->customer_id))
            return $this->data->customer_id;

        if(CheckNull($this->data->customer_name)) {
            $customer_id = DB::table('customers')->InsertGetId([
                'first_name' => $this->data->customer_name,
                'address_1' => $this->data->customer_address,
                'phone' => $this->data->customer_phone
            ]);

            return $customer_id;
        }
        return 0;
    }

    public function processPaymentInfo() {
//        dd($this->data->all());
        return array(
            "payment_type" => PaymentTypes::$TypeList[$this->data->payment_method],
            "paid_amount" => $this->data->paid
        );
    }

    public function processItems($items) {
        $processedItems = array();

        foreach( $this->data->items as $anItem ) {
            $item_id =  $anItem->id;

            $currentProcessedItem['item_id'] = $item_id;
            $currentProcessedItem['unit_price'] = $anItem->perUnitPrice;
            $currentProcessedItem['cost_price'] = $anItem->stock_price;
            $currentProcessedItem['total_price'] = $anItem->totalPrice;
            $currentProcessedItem['quantity'] = $anItem->quantity;
            $currentProcessedItem['9'] = $this->getItemDiscountAmountByItemId($item_id);
            $currentProcessedItem['price_rule_id'] = $this->getPriceRuleIdByItemId($item_id);
            $currentProcessedItem['item_type'] = $this->getItemTypeByItemId($item_id);
            $currentProcessedItem['item_discount_percentage'] = $anItem->quantity;
            $currentProcessedItem['sale_discount_amount'] = $anItem->quantity;
            $currentProcessedItem['item_profit'] = $anItem->quantity;
            $currentProcessedItem['tax_amount'] = $anItem->quantity;
            $currentProcessedItem['tax_rate'] = $anItem->quantity;
            $currentProcessedItem['is_price_taken_from_barcode'] = false;
        }
    }

    private function getItemDiscountAmountByItemId($item_id) {
        return 0.00;
    }

    private function getPriceRuleIdByItemId($item_id) {
        return 0;
    }

    private function getItemTypeByItemId($item_id) {
        return "item ";
    }

}