<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PermissionName extends Model
{
    //

    public static function AddPermission($data){

        $permission = new PermissionName();
        $permission->permission_name = $data['permission_name'];
        $permission->permission_token = $data['permission_token'];
        $permission->permission_category_id = $data['permission_category_id'];
        $permission->permission_description = "";
        $permission->save();
        return $permission->id;

    }

    public static function UpdatePermission($data){

        $permission = PermissionName::where('permission_token','=',$data['permission_token'])->first();
        $permission->permission_name = $data['permission_name'];
        $permission->permission_token = $data['permission_token'];
        $permission->permission_category_id = $data['permission_category_id'];
        $permission->permission_description = "";
        $permission->save();
        return $permission->id;

    }

    public static function GetAllPermissions()
    {
        $permissionsCategories = PermissionCategory::all();

        $modules = array();


        foreach ($permissionsCategories as $aPermissionCategory) {
            $categories[] = $aPermissionCategory;

            $categorySpecificPermissions = PermissionName::where('permission_category_id', '=', $aPermissionCategory->id)->get();

            $permissions = array();
            foreach ($categorySpecificPermissions as $aPermission) {

                $permission['permission_name'] = $aPermission->permission_name;
                $permission['permission_category_id'] = $aPermission->permission_category_id;
                $permission['permission_token'] = $aPermission->permission_token;


                array_push($permissions, $permission);
            }

            $module["category_id"] = $aPermissionCategory->id;
            $module["name"] = $aPermissionCategory->permission_category_name;
            $module["description"] = $aPermissionCategory->permission_category_description;
            $module["permissions"] = $permissions;


            array_push($modules, $module);
        }

        return $modules;
    }
}
