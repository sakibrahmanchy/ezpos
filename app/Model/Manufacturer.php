<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    public function InsertManufacturer($manufacturer_name){
        $manufacturer = new Manufacturer();
        $manufacturer->manufacturer_name = $manufacturer_name;
        $manufacturer->save();
    }

    public function EditManufacturer($manufacturer_name,$manufacturer_id){

        $manufacturer = Manufacturer::where('id',$manufacturer_id)->first();
        $manufacturer->manufacturer_name = $manufacturer_name;
        $manufacturer->save();
    }


    public function DeleteManufacturer($manufacturer_id){
        $manufacturer = Manufacturer::find($manufacturer_id);
        $manufacturer->delete();

    }

    public function FetchManufacturers(){
        $manufacturerList = Manufacturer::orderBy('manufacturer_name')->get();
        return $manufacturerList;
    }

}
