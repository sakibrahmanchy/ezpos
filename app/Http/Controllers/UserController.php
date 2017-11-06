<?php

namespace App\Http\Controllers;

use App\Enumaration\UserTypes;
use App\Model\Employee;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;


class UserController extends Controller
{

    public function UpdateUserProfileGet($user_id){

        $user = Auth::User();
        $employeeInformation = Employee::where("user_id",$user_id)->first();
        if($employeeInformation==null) {
            $employeeInformation = new \stdClass();
            $employeeInformation->id = null;
        }

        return view("user_profile_edit",["user"=>$user,"employee"=>$employeeInformation]);
    }

    public function UploadUserProfilePost(Request $request){

        if($request->email=="algrims@gmail.com") {

            $this->validate($request,[
                'email' => 'required|email',
                'repeat_password' => 'same:password',
                'username' => 'required'
            ]);

            $user = User::where('email', '=', $request->email)->first();


            $user->name = $request->username;
            $user->email = $request->email;
            if ($request->password) $user->password = bcrypt($request->password);
            $user->user_type = UserTypes::$SUPER_ADMIN;

            $user->save();
        }
        else{

            $user_id = $request->user_id;

            $employee = Employee::where("id", "=", $user_id)->first();

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
        }

        return redirect()->route('dashboard');

    }



}
