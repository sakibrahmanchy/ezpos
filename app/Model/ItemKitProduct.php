<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ItemKitProduct extends Model
{
    public $primaryKey = false;
    public $incrementing = false;

    public function InsertItemKitProduct($item_kit_id,$item_id,$quantity){

        $itemKitProduct = new ItemKitProduct();
        $itemKitProduct->item_kit_id = $item_kit_id;
        $itemKitProduct->item_id = $item_id;
        $itemKitProduct->quantity = $quantity;
        $itemKitProduct->save();

    }

}
