<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencyDenominationController extends Controller
{
    public function addNewCurrencyDenominator(Request $request){
        $denomintaionNames = $request->denominations_names;
        $denomintaionValues = $request->denominations_values;
        for($i = 0; $i<sizeof($denomintaionNames); $i++){
            $denominationKeyPair[$denomintaionNames[$i]] = $denomintaionValues[$i];
            $array = array(
                "denomination_name"=>$denomintaionNames[$i],
                "denomination_value"=>$denomintaionValues[$i]
            );
            CurrencyDenomination::create($array);
        }
    }
}
