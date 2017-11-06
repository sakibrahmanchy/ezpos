<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PermissionCategory extends Model
{
    //
    public function Permissions(){
        $this->hasMany('App\Model\Permission');
    }

    public function AddPermissionCategory($data){
        $permissionCategory = new PermissionCategory();
        $permissionCategory->permission_category_name = $data['permission_category_name'];
        $permissionCategory->permission_category_description = $data['permission_category_description'];
        $permissionCategory->save();
        return $permissionCategory->id;
    }

    public function UpdatePermission($data){
        $permissionCategory = PermissionCategory::where('permission_category_name','=',$data['permission_category_name'])->first();
        $permissionCategory->permission_category_name = $data['permission_category_name'];
        $permissionCategory->permission_category_description = $data['permission_category_description'];
        $permissionCategory->save();
        return $permissionCategory->id;
    }
}
