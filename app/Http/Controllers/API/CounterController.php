<?php
/**
 * Created by PhpStorm.
 * User: ByteLab
 * Date: 8/8/2018
 * Time: 11:19 AM
 */

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Model\Counter;

class CounterController extends Controller {

    public function GetCounterList()
    {
        $counters = Counter::all();
        if(!is_null($counters))
            return response()->json(['success'=>true, 'data'=>$counters], 200);

        return response()->json(['success'=>false, 'data'=>null],500);
    }
}