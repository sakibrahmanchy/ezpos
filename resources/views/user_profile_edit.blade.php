@extends('layouts.master')

@section('pageTitle','Edit Profile')

@section('breadcrumbs')
    {!! Breadcrumbs::render('user_profile',$employee->id) !!}
@stop

@section('content')

    <div class="box box-primary" style="padding:20px">
        @include('includes.message-block')
        <div class="row" id="form">
            <div class="spinner" id="grid-loader" style="display:none">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
            </div>
            <div class="col-md-12">



                <form action="{{route('user_profile_save')}}" id="user_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    {{-- {{ csrf_field() }}--}}
                    @if($user->email!="algrims@gmail.com")
                    <div class="panel panel-piluku">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="pe-7s-edit"></i>
                                User Basic Information    					<small>(Fields in red are required)</small>
                            </h3>
                        </div>

                        <div class="panel-body">

                            <div class="row">
                                <div class="col-md-12">
                                   <input type="hidden" value="{{ $employee->id }}"   name="user_id">

                                    <div class="form-group">
                                        <label for="first_name" class="required col-sm-3 col-md-3 col-lg-2 control-label ">First Name:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="first_name" value="{{$employee->first_name}}" class="form-control" id="first_name" >
                                            <span class="text-danger">{{ $errors->first('first_name') }}</span>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="last_name" class=" col-sm-3 col-md-3 col-lg-2 control-label ">Last Name:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="last_name" value="{{$employee->last_name}}" class="form-control" id="last_name">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="email" class="col-sm-3 col-md-3 col-lg-2 control-label required">E-Mail:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="email" value="{{$user->email}}" class="form-control" id="email" >
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone" class="col-sm-3 col-md-3 col-lg-2 control-label ">Phone Number:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="phone" value="{{$employee->phone}}" class="form-control" id="phone">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="image_id" class="col-sm-3 col-md-3 col-lg-2 control-label ">Select Image:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <ul class="list-unstyled avatar-list">
                                                <li>
                                                    <input type="file" name="image" onchange = "loadTempImage(this)" id="image" class="filestyle" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);"><div class="bootstrap-filestyle input-group"><input type="text" class="form-control " disabled=""> <span class="group-span-filestyle input-group-btn" tabindex="0"><label for="image" class="btn btn-file-upload "><span class="pe-7s-folder"></span> <span class="buttonText">Choose file</span></label></span></div>&nbsp;
                                                </li>
                                                <li>
                                                    @if($employee->image_token!=null)
                                                        <div id="avatar"><img src="{{asset('img/employees/userpictures/'.$employee->image_token)}}" class="img-polaroid" id="image_empty" alt=""></div>
                                                    @else
                                                        <div id="avatar"><img src="{{asset('img/avatar.png')}}" class="img-polaroid" id="image_empty" alt=""></div>

                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                    </div>




                                    <div class="form-group">
                                        <label for="address_1" class="col-sm-3 col-md-3 col-lg-2 control-label ">Address 1:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="address_1" value="{{$employee->address_1}}" class="form-control" id="address_1">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_2" class="col-sm-3 col-md-3 col-lg-2 control-label ">Address 2:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="address_2" value="{{$employee->address_2}}" class="form-control" id="address_2">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="city" class="col-sm-3 col-md-3 col-lg-2 control-label ">City:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="city" value="{{$employee->city}}" class="form-control " id="city">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="state" class="col-sm-3 col-md-3 col-lg-2 control-label ">State/Province:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="state" value="{{$employee->state}}" class="form-control " id="state">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="zip" class="col-sm-3 col-md-3 col-lg-2 control-label ">Zip:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="zip" value="{{$employee->zip}}" class="form-control " id="zip">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="country" class="col-sm-3 col-md-3 col-lg-2 control-label ">Country:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="country" value="{{$employee->country}}" class="form-control " id="country">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="comments" class="col-sm-3 col-md-3 col-lg-2 control-label ">Comments:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <textarea name="comments" cols="17" rows="5" id="comments" value ="{{$employee->comments}}" class="form-control text-area"></textarea>
                                        </div>
                                    </div>

                                </div><!-- /col-md-12 -->
                            </div><!-- /row -->

                            <div class="form-group offset1">
                                <label for="hire_date" class="col-sm-3 col-md-3 col-lg-2 control-label text-info wide">Hire date:</label>						<div class="col-sm-9 col-md-9 col-lg-10">
                                    <div class="input-group date">
                                    <span class="input-group-addon bg">
                                       <i class="fa fa-calendar"></i>
                                    </span>
                                        <input  name="hire_date" value="{{$employee->hire_date}}" id="hire_date" class="form-control datepicker">

                                    </div>
                                </div>
                            </div>


                            <div class="form-group offset1">
                                <label for="birthday" class="col-sm-3 col-md-3 col-lg-2 control-label text-info wide">Birthday:</label>						<div class="col-sm-9 col-md-9 col-lg-10">
                                    <div class="input-group date">
                                    <span class="input-group-addon bg">
                                       <i class="fa fa-calendar"></i>
                                    </span>
                                        <input  name="birthday" value="{{$employee->birth_date}}" id="birthday" class="form-control datepicker">

                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="user_number" class="col-sm-3 col-md-3 col-lg-2 control-label">User number:</label>						<div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="text" name="user_number" value="{{$employee->user_number}}" id="user_number" class="form-control">
                                </div>
                            </div>
                            @endif

                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="pe-7s-power"></i> User Login Info</h3>					</div>
                            <div class="form-group"><br>
                                <label for="username" class="col-sm-3 col-md-3 col-lg-2 control-label required">Username:</label>					<div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="text" name="username" value="{{$user->name}}" id="username" class="form-control" >
                                    <span class="text-danger">{{ $errors->first('username') }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-sm-3 col-md-3 col-lg-2 control-label required">E-Mail:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="text" name="email" value="{{$user->email}}" class="form-control" id="email" >
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="col-sm-3 col-md-3 col-lg-2 control-label">User pin(4 digit max.):</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="password" name="pin" pattern="[0-9]{4}" maxlength="4" class="form-control">
                                    <span class="text-danger">{{ $errors->first('pin') }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="col-sm-3 col-md-3 col-lg-2 control-label">Password:</label>					<div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="password" name="password" value="" id="password" class="form-control" autocomplete="off" >
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="repeat_password" class="col-sm-3 col-md-3 col-lg-2 control-label">Password Again:</label>					<div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="password" name="repeat_password" value="" id="repeat_password" class="form-control" autocomplete="off" >
                                    <span class="text-danger">{{ $errors->first('repeat_password') }}</span>
                                </div>
                            </div>



                            <input type="hidden" name="redirect_code" value="0">

                            <div class="form-actions pull-right">
                                <input type="submit" name="submitf" value="Submit" id="submitf" class="btn floating-button btn-primary float_right">
                            </div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection


