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

function UserHasAccessToCounter($counter) {
    $userCounterList = \Illuminate\Support\Facades\App::make("user_counter_list");
    if(in_array($counter,$userCounterList))
        return true;
    return false;
}

function CheckNull($data) {
    if(!is_null($data) && $data != "")
        return true;
    return false;
}

function getPercentage($amount, $percentage) {
    return ($amount * ($percentage / 100));
}

function percentageLess($amount, $percentage) {
    return $amount - getPercentage($amount,$percentage);
}

function percentageMore($amount, $percentage) {
    return $amount + getPercentage($amount,$percentage);
}


?>