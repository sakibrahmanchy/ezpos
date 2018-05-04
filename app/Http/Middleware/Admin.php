<?php

namespace App\Http\Middleware;

use App\Enumaration\RouteTokens;
use App\Enumaration\UserTypes;
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
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        $permissions = DB::table('user_permissions')
            ->join('permission_names','user_permissions.permission_id','=','permission_names.id')
            ->where('user_id',$user->id)
            ->where('status',1)
            ->select('permission_token','permission_category_id')
            ->get();

        $permissionCategoryList = array();

        $permissionList= array();

        foreach($permissions as $aPermission){
            $permission_token = $aPermission->permission_token;
            if(!in_array($aPermission->permission_category_id, $permissionCategoryList))
                array_push($permissionCategoryList, $aPermission->permission_category_id);
            array_push($permissionList, $permission_token);
        }

        App::instance('user_permissions', $permissionList);
        App::instance('user_permission_categories', $permissionCategoryList);

        $route_name = $request->route()->getName();
        $auto_permitted = ["dashboard","user_profile_edit","user_profile_save","report_dashboard"];
        if( in_array($route_name,$auto_permitted)){
            return $next($request);
        }

        if($route_name=='change_settings'||$route_name=='save_settings'){
            if($user->user_type==UserTypes::$SUPER_ADMIN){
                return $next($request);
            }else{
                return redirect()->route('error-401');
            }
        }



        $route_token = RouteTokens::$ROUTE_TOKENS[$request->route()->getName()];
        if($user->user_type==UserTypes::$EMPLOYEE||$user->user_type==UserTypes::$SUPER_ADMIN)
            if(in_array($route_token,$permissionList))
                return $next($request);
        else
            return redirect()->route('error-401');
    }
}
