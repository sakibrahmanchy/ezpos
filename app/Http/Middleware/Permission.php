<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Permission
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



//        dd($request->route()->getName());

        if($user->user_type==UserTypes::$EMPLOYEE||$user->user_type==UserTypes::$SUPER_ADMIN)
            return $next($request);
        else
            return redirect()->back();
    }
}
