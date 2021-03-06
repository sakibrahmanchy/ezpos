<?php

use Illuminate\Database\Seeder;
use App\Model\User;
use App\Model\PermissionName;
use App\Model\UserPermission;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if(!(User::where("email","algrims@gmail.com")->exists()))
        {
            $employee = new User();
            $employee->name = "Algrims";
            $employee->email = "algrims@gmail.com";
            $employee->password = bcrypt("123456");
            $employee->user_type = \App\Enumaration\UserTypes::$SUPER_ADMIN;
            $employee->save();

            $permissions =  \Illuminate\Support\Facades\DB::table('permission_names')->select('permission_token')->get()->toArray();
            $permissionList= array();

            foreach($permissions as $aPermission){
                $permission_token = $aPermission->permission_token;
                array_push($permissionList, $permission_token);
            }

            UserPermission::CreateUserPermissions($permissionList, $employee->id);
        }else{
            $employee = User::where("email","algrims@gmail.com")->first();
            $employeePermissions = UserPermission::where("user_id",$employee->id)
                ->pluck('permission_id')->toArray();

            $permissions =  \Illuminate\Support\Facades\DB::table('permission_names')->pluck('id')->toArray();
            //$permissionList= array();
            $newPermissionsToAdd = array_diff($permissions,$employeePermissions);

            if(!is_null($newPermissionsToAdd))
                UserPermission::AddNewPermissions($newPermissionsToAdd,$employee->id);
        }

    }
}
