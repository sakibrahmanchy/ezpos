<?php

namespace App\Http\Controllers;

use App\Library\SettingsSingleton;
use App\Model\CurrencyDenomination;
use App\Model\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class SettingsController extends Controller
{

    public function GetSettings(){
        $denominators = CurrencyDenomination::all();
        return view("settings",["settings","denominators"=>$denominators]);
    }


    public function SaveSettings(Request $request){
        $this->validate($request,[
            "company_name"=>"required",
            'upc_code_prefix' => 'required_with:scan_price_from_barcode,on',
            "tax_rate" => "required",
            "customer_loyalty_percentage" => "required"
        ]);

        $settingsChange =$request->except(['_token','image','denomination_name','denomination_value']);

        foreach($settingsChange as $key=>$value){
            SettingsSingleton::set($key,$value);
        }

        $file = $request->file('image');

        if ($file) {

            $image = Image::make($file)->stream();
            Storage::disk('images')->put("logo" . '.png', $image);
        }

        CurrencyDenomination::truncate();

        $denomintaionNames = $request->denomination_name;
        $denomintaionValues = $request->denomination_value;
        
        if(!empty($denomintaionNames))
        for($i = 0; $i<sizeof($denomintaionNames); $i++){
            if(is_null($denomintaionNames[$i]))
                $denomintaionNames[$i] = "";
            $array = array(
                "denomination_name"=>$denomintaionNames[$i],
                "denomination_value"=>$denomintaionValues[$i]
            );
            CurrencyDenomination::create($array);
        }


        return redirect()->route('change_settings');

    }

}
