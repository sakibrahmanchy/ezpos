<?php

namespace App\Http\Controllers;

use App\Model\File;
use App\Model\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input as Input;
class FileController extends Controller
{
    //
    public function InsertFile(Request $request){
        $file = new File();
        $fileUploadStatus = $file->InsertFile(Input::file('file'));
        if($fileUploadStatus)  return response()->json(['success' => true], 200);
        else  return response()->json(['success' => false], 400);
    }

    public function DeleteFile($fileId){
        $file = new File();
        if($file->DeleteFile($fileId)){
            echo "Deleted Successfully";
        }

    }

    public function InsertItemFile(Request $request){

        if(Input::get('item_id')!=0) $item_id = Input::get('item_id');
        else {
            $item = Item::orderBy('id', 'desc')->first();


            if (is_null($item)) {
                $item_id = 1;
            } else
                $item_id = $item->id + 1;
        }
        $file = new File();
        $fileUploadStatus = $file->InsertItemFile(Input::file('file'),$item_id);
        if($fileUploadStatus)  return response()->json(['success' => true,'item_id'=>$item_id], 200);
        else  return response()->json(['success' => false], 400);
    }







}
