<?php

namespace App\Http\Middleware;

use App\Enumaration\RouteTokens;
use App\Enumaration\UserTypes;
use App\Model\Counter;
use App\Model\Employee;
use App\Model\Sale;
use App\Model\User;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    const AUTO_PERMITTED = ["dashboard","user_profile_edit","user_profile_save","report_dashboard"];

    public function handle($request, Closure $next)
    {

        $user = Auth::user();

        if(!is_null($user)) {

            $permissions = self::getUserPermissionsFromDB($user->id);
            $permissionListWithCategory = self::generatePermissionListWithCategory($permissions);

            $permissionList = $permissionListWithCategory["permissionList"];
            $permissionCategoryList = $permissionListWithCategory["categoryList"];


            App::instance('user_permissions', $permissionList);
            App::instance('user_permission_categories', $permissionCategoryList);
            App::instance('user_counter_list', $permissionCategoryList);

            $route_name = $request->route()->getName();
            if(!self::routeNeedsPermission($route_name))
                return $next($request);

            $route_token = RouteTokens::$ROUTE_TOKENS[$route_name];

            if($user->user_type==UserTypes::$EMPLOYEE||$user->user_type==UserTypes::$SUPER_ADMIN) {
                if(in_array($route_token,$permissionList)){
                    return $next($request);
                }
                else
                    return redirect()->route('error-401');
            }
                else
                    return redirect()->route('error-401');
        } else{
            return redirect()->route('pin_log_in');
        }

    }

    public static function getUserPermissionsFromDB($user_id) {

        return DB::table('user_permissions')
            ->join('permission_names','user_permissions.permission_id','=','permission_names.id')
            ->where('user_id',$user_id)
            ->where('status',1)
            ->select('permission_token','permission_category_id')
            ->get();
    }

    public static function getUsersAccessibleCounters($user_id) {
        if($user_id == 1) {
            return Sale::_eloquentToArray(DB::table('counters')
                ->select('id')
                ->get(), "id");
        }else{
            $employee_id = Employee::where("user_id",$user_id)->first()->user_id;
            return Sale::_eloquentToArray(DB::table('counter_employee')
                ->where('employee_id',$employee_id)
                ->select('counter_id')
                ->get(), "counter_id");
        }

    }

    public static function generatePermissionListWithCategory($permissions) {
        $permissionCategoryList = array();

        $permissionList= array();
        foreach($permissions as $aPermission){
            $permission_token = $aPermission->permission_token;
            if(!in_array($aPermission->permission_category_id, $permissionCategoryList))
                array_push($permissionCategoryList, $aPermission->permission_category_id);
            array_push($permissionList, $permission_token);
        }

        $permissionListWithCategory = array(
            "categoryList" => $permissionCategoryList,
            "permissionList" => $permissionList
        );

        return $permissionListWithCategory;
    }

    public static function routeNeedsPermission($route_name) {
        if( in_array($route_name,self::AUTO_PERMITTED) || self::isInSettingsPage($route_name)){
            return false;
        }
        return true;
    }

    public static function isInSettingsPage($route_name) {
        $user = Auth::user();
        if($route_name=='change_settings'||$route_name=='save_settings'){
            if($user->user_type==UserTypes::$SUPER_ADMIN || UserHasPermission("update_settings_table_data")){
                return true;
            }else{
                return false;
            }
        }
        return false;
    }
}
