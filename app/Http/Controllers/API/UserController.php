<?php

namespace App\Http\Controllers\Api;

use App\Enumaration\UserTypes;
use App\Http\Controllers\Controller;
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
    public function __construct(){
        $this->content = array();
    }

    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $this->content['token'] =  $user->createToken('EZPOS Mobile App')->accessToken;
            $status = 200;
        }
        else{
            $this->content['error'] = "Unauthorized";
            $status = 401;
        }
        return response()->json($this->content, $status);
    }

    public function loginByPin(){

       $user = User::where("pin",request('pin'))->first();
        if(!is_null($user)) {
            if(Auth::loginUsingId($user->id)){
                $user = Auth::user();
                $this->content['token'] =  $user->createToken('EZPOS Mobile App')->accessToken;
                $status = 200;
            }
            else{
                $this->content['error'] = "Unauthorized";
                $status = 401;
            }
        }
        else{
            $this->content['error'] = "Unauthorized";
            $status = 401;
        }

        return response()->json($this->content, $status);
    }

}
