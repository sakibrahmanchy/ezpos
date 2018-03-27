<?php

namespace  App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserPermission extends Model
{
    //
    public function PermissionCategory(){
        return $this->belongsToMany('App\Model\PermissionCategory')->withTimestamps();
    }

    public function Users(){
        return $this->belongsToMany('App\Model\User')->withTimestamps();
    }

    public static function GetUserPermissions($userId)
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

                $usersAccess = UserPermission::where("user_id", '=', $userId)->where("permission_id", '=', $aPermission->id)->first();

                if (!isset($usersAccess->status)) {
                    $permission['userAccess'] = "0";
                } else {
                    $permission['userAccess'] = $usersAccess->status;
                }

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

    public static function CreateUserPermissions($permissionList, $userId) {

        $permissionNames = PermissionName::all();
        foreach ($permissionNames as $aPermission) {

            $userPermission = new UserPermission();
            $userPermission->permission_id = $aPermission->id;
            $userPermission->user_id = $userId;

            $permissionMatched = false;
            foreach ($permissionList as $permission_token_to_match) {

                if ($aPermission->permission_token == $permission_token_to_match) {
                    $permissionMatched = true;
                }
            }

            if ($permissionMatched)
                $userPermission->status = 1;
            else
                $userPermission->status = 0;

            $userPermission->save();
        }
    }

    public static function UpdateUserPermissions($permissionList, $userId)
    {
        // Get all permissions by user id
        $userPermissions = DB::table('user_permissions')
            ->leftJoin('permission_names', 'user_permissions.permission_id', '=', 'permission_names.id')
            ->where('user_id', '=', $userId)
            ->get();

        // Update all user permissions to 0
        foreach ($userPermissions as $aUserPermission) {
            $userPermission = UserPermission::where("user_id", '=', $aUserPermission->user_id)->where('permission_id', '=', $aUserPermission->permission_id)->first();
            $userPermission->status = 0;
            $userPermission->save();
        }

        // If user is provided with permissions, update permission
        foreach ($permissionList as $permission_token_to_match) {
            foreach ($userPermissions as $aUserPermission) {
                $userPermission = UserPermission::where("user_id", '=', $userId)->where("permission_id", '=', $aUserPermission->id)->first();
                if ($aUserPermission->permission_token == $permission_token_to_match) {
                    // echo $permission_token_to_match.'<br>';
                    $userPermission->status = 1;
                    $userPermission->save();
                    break;
                }
            }
        }
    }


    public static function AddNewPermissions($permissionList, $userId){
        //Iterate over the permissionList
        foreach ($permissionList as $aPermissionId) {
            // Get permission info from database
            $permissionInfo = PermissionName::where("id",$aPermissionId)->first();

            if(!is_null($permissionInfo)){
                // Create a new user permission over the permission token\
                $userPermission = new UserPermission();
                $userPermission->permission_id = $permissionInfo->id;
                $userPermission->user_id = $userId;
                $userPermission->status = 1;
                $userPermission->save();
            }
        }
        //End of iteration
    }
}
