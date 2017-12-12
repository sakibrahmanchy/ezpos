<?php


function UserHasPermission($permission){

    $permissions = \Illuminate\Support\Facades\App::make("user_permissions");

    if(in_array($permission,$permissions))
        return true;
    return false;

}

function UserHasAccessToModule($module){

    $modules = \Illuminate\Support\Facades\App::make("user_permission_categories");

    if(in_array($module,$modules))
        return true;
    return false;
}

?>