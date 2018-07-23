<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SaleStatusLog extends Model
{
    protected $fillable = ['sale_id','previous_status','current_status', 'user_id'];

    public static function changeSaleStatus($sale_id, $previousStatus, $currentStatus) {
        self::create([
            "sale_id" => $sale_id,
            "previous_status" => $previousStatus,
            "current_status" => $currentStatus,
            "user_id" => Auth::user()->id
        ]);
    }
}
