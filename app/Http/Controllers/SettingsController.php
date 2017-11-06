<?php

namespace App\Http\Controllers;

use App\Library\SettingsSingleton;
use App\Model\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class SettingsController extends Controller
{

    public function GetSettings(){
        return view("settings",["settings"]);
    }


    public function SaveSettings(Request $request){
        $this->validate($request,[
            "company_name"=>"required"
        ]);

        $settingsChange =$request->except(['_token','image']);

        foreach($settingsChange as $key=>$value){
            SettingsSingleton::set($key,$value);
        }

        $file = $request->file('image');

        if ($file) {

            $image = Image::make($file)->stream();
            Storage::disk('images')->put("logo" . '.png', $image);
        }

        return redirect()->route('change_settings');

    }

}
