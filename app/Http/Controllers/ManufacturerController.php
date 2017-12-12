<?php

namespace App\Http\Controllers;

use App\Model\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    public function GetManufacturerList(){
        $manufacturer = new Manufacturer();
        $manufacturerList = $manufacturer->FetchManufacturers();

        return view('manufacturer_list',['manufacturerMenu'=>$manufacturerList]);
    }

    public function AddManufacturer(Request $request){
        $manufacturer = new Manufacturer();

        $manufacturer->InsertManufacturer($request->manufacturerName);
        return response()->json(['success' => true], 200);
    }

    public function EditManufacturer(Request $request){
        $manufacturer = new Manufacturer();

        $manufacturer->EditManufacturer($request->manufacturerName,$request->manufacturerId);

        return response()->json(['success' => true], 200);
    }

    public function DeleteManufacturer(Request $request){
        $manufacturer = new Manufacturer();

        $manufacturer->DeleteManufacturer($request->manufacturerId);

        return response()->json(['success' => true], 200);
    }
}
