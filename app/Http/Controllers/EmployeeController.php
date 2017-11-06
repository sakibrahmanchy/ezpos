<?php

namespace App\Http\Controllers;


use App\Enumaration\UserTypes;
use App\Model\PermissionCategory;
use App\Model\PermissionName;
use App\Model\User;
use App\Model\UserPermission;
use App\Model\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Contracts\Support\Collection;

class EmployeeController extends Controller
{
    //

    public function GetEmployeeForm()
    {

        //Load all permissions from database
        $modules = PermissionName::GetAllPermissions();

        return view('employees.new_employee', ['modules' => $modules]);
    }

    public function AddEmployee(Request $request)
    {

        $rules = [
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'repeat_password' => 'required|same:password',
            'username' => 'required'
        ];
        $allInput = $request->all();

        $validator = Validator::make($allInput, $rules);
        if ($validator->fails()) {


            return redirect()->route('new_employee')
                ->withErrors($validator)
                ->withInput($request->input());
        }


        $userCredentials['name'] = $request->first_name;
        $userCredentials['email'] = $request->email;
        $userCredentials['password'] = bcrypt($request->password);
        $userCredentials['user_type'] = UserTypes::$EMPLOYEE;

        $userId = User::create($userCredentials)->id;

        $employeeCredentials['first_name'] = $request->first_name;
        $employeeCredentials['last_name'] = $request->last_name;
        $employeeCredentials['phone'] = $request->phone;
        $employeeCredentials['address_1'] = $request->address_1;
        $employeeCredentials['address_2'] = $request->address_2;
        $employeeCredentials['city'] = $request->city;
        $employeeCredentials['state'] = $request->state;
        $employeeCredentials['zip'] = $request->zip;
        $employeeCredentials['country'] = $request->country;
        $employeeCredentials['comments'] = $request->comments;
        $employeeCredentials['hire_date'] =$request->hire_date ? $request->hire_date : null;
        $employeeCredentials['birth_date'] = $request->birthday ?  $request->birthday : null;
        $employeeCredentials['employee_number'] = $request->employee_number;
        $employeeCredentials['user_id'] = $userId;


        $userImageToken = uniqid();
        $file = $request->file('image');

        if ($file) {
            $image = Image::make($file)->stream(); //Resizing image using Intervention Image
            Storage::disk('employee_user_pictures')->put($userImageToken . '.jpg', $image);  // Storing image in the disk as the name according to user id
            $employeeCredentials['image_token'] = $userImageToken.'.jpg';
        }

        $employeeId = \App\Model\Employee::create($employeeCredentials)->id;
        // echo $employeeId;

        //Access Permissions to user
        if (isset($request->permissions_actions))
            UserPermission::CreateUserPermissions($request->permissions_actions, $userId);

        return redirect()->route('employee_list');



    }


    public function GetEmployeeList()
    {
        $employees = DB::table('employees')
            ->leftJoin('users', 'employees.user_id', '=', 'users.id')->select('employees.id as employee_id', 'employees.*', 'users.email')
            ->get();

        return view('employees.employee_list', ["employees" => $employees]);
    }

    public function EditEmployeeGet($employeeId)
    {

        $employeeInfo = DB::table('employees')
            ->leftJoin('users', 'employees.user_id', '=', 'users.id')->where('employees.id', '=', $employeeId)->select('employees.id as employee_id', 'employees.*', 'users.*')
            ->first();

        // var_dump($employeeInfo);
        $employeeUserId = $employeeInfo->user_id;

        $employeePermissions = UserPermission::GetUserPermissions($employeeUserId);

        return view('employees.employee_edit', ['employee' => $employeeInfo, 'modules' => $employeePermissions]);
    }


    public function  EditEmployeePost(Request $request, $employeeId)
    {
        $employee = Employee::where("id", "=", $employeeId)->first();

        /* var_dump($employee);*/
        $rules = [
            'first_name' => 'required',
            'email' => 'required|email',
            'repeat_password' => 'same:password',
            'username' => 'required'
        ];
        $allInput = $request->all();

        $validator = Validator::make($allInput, $rules);
        if ($validator->fails()) {


            return redirect()->route('employee_edit', ['employee_id' => $employee->id])
                ->withErrors($validator)
                ->withInput($request->input());
        }

        $user = User::where('id', '=', $employee->user_id)->first();


        $user->name = $request->username;
        $user->email = $request->email;
        if ($request->password) $user->password = bcrypt($request->password);
        $user->user_type = UserTypes::$EMPLOYEE;

        $user->save();

        $employee->first_name = $request->first_name;
        $employee->last_name = $request->last_name;
        $employee->phone = $request->phone;
        $employee->address_1 = $request->address_1;
        $employee->address_2 = $request->address_2;
        $employee->city = $request->city;
        $employee->state = $request->state;
        $employee->zip = $request->zip;
        $employee->country = $request->country;
        $employee->comments = $request->comments;
        $employee->hire_date = $request->hire_date ? $request->hire_date : null;
        $employee->birth_date = $request->birthday ?  $request->birthday : null;
        $employee->employee_number = $request->employee_number;
        $employee->user_id = $employee->user_id;

        $userImageToken = uniqid();
        $file = $request->file('image');

        if ($file) {
            $image = Image::make($file)->stream(); //Resizing image using Intervention Image
            Storage::disk('employee_user_pictures')->put($userImageToken . '.jpg', $image);  // Storing image in the disk as the name according to user id
            $employee->image_token = $userImageToken . ".jpg";
        }

        $employee->save();

        //$permissionNames = PermissionName::all();

        if (isset($request->permissions_actions)) {
            $userPermissionCreated = UserPermission::where("user_id","=",$employee->user_id)->first();
            if($userPermissionCreated)
                UserPermission::UpdateUserPermissions($request->permissions_actions, $employee->user_id);
            else
                UserPermission::CreateUserPermissions($request->permissions_actions, $employee->user_id);
        }


        return redirect()->route('employee_list');

    }

    public function DeleteEmployees(Request $request){

        $employee_list = $request->id_list;
        if(DB::table('employees')->whereIn('id',$employee_list)->delete())
            return response()->json(["success"=>true],200);
        return response()->json(["success"=>false],200);

    }





}
