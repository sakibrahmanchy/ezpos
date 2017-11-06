<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    //
    public function InsertFile($fileObject){
        $fileExtension = $fileObject->getClientOriginalExtension();
        $destinationPath =  asset('/img');
        $fileNewName = uniqid() .".".$fileExtension;;

        $file = new File();
        $file->type = $fileObject->getMimeType();
        $file->directory = $destinationPath;
        $file->new_name = $fileNewName;
        $file->actual_name = $fileObject->getClientOriginalName();
        $file->save();

        if(  Storage::disk('images')->put($fileNewName, file_get_contents($fileObject)) ){
            return true;
        }
        else{
            return false;
        }
    }

    public function InsertItemFile($fileObject,$item_id){
        $fileExtension = $fileObject->getClientOriginalExtension();
        $destinationPath =  asset('/img');
        $fileNewName = uniqid() .".".$fileExtension;;

        $file = new File();
        $file->type = $fileObject->getMimeType();
        $file->directory = $destinationPath;
        $file->new_name = $fileNewName;
        $file->actual_name = $fileObject->getClientOriginalName();
        $file->save();

        $file_id = $file->id;
        $itemImage = new \App\Model\ItemsImage();
        $itemImage->item_id = $item_id;
        $itemImage->file_id = $file_id;
        $itemImage->save();

        if(  Storage::disk('images')->put($fileNewName, file_get_contents($fileObject)) ){
            return true;
        }
        else{
            return false;
        }
    }

    public function DeleteFile($fileId){

        $file = File::where("id",'=',$fileId)->first();
        $fileName = $file->new_name;
        $fileDirectory = $file->directory;
        unlink($fileDirectory.$fileName);
        $file->delete();
    }
}
