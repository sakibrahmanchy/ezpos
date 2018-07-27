@extends('layouts.master')

@section('pageTitle','New Employee')

@section('breadcrumbs')
    {!! Breadcrumbs::render('new_employee') !!}
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
                <form action="{{route('new_employee')}}" id="employee_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <div class="panel panel-piluku">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="pe-7s-edit"></i>
                                Employee Basic Information    <small>(Fields in red are required)</small>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="first_name" class="required col-sm-3 col-md-3 col-lg-2 control-label ">First Name:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control" id="first_name" >
                                            <span class="text-danger">{{ $errors->first('first_name') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="last_name" class=" col-sm-3 col-md-3 col-lg-2 control-label ">Last Name:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control" id="last_name">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="email" class="col-sm-3 col-md-3 col-lg-2 control-label required">E-Mail:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="email" value="{{ old('email') }}" class="form-control" id="email" >
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone" class="col-sm-3 col-md-3 col-lg-2 control-label ">Phone Number:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" id="phone">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="image_id" class="col-sm-3 col-md-3 col-lg-2 control-label ">Select Image:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <ul class="list-unstyled avatar-list">
                                                <li>
                                                    <input type="file" onchange = "loadTempImage(this)" name="image" id="image" class="filestyle" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);"><div class="bootstrap-filestyle input-group"><input type="text" class="form-control " disabled=""> <span class="group-span-filestyle input-group-btn" tabindex="0"><label for="image" class="btn btn-file-upload "><span class="pe-7s-folder"></span> <span class="buttonText">Choose file</span></label></span></div>&nbsp;
                                                </li>
                                                <li>
                                                    <div id="avatar"><img height="200px" width="200px" src="{{asset('img\avatar.png')}}" class="img-polaroid" id="image_empty" alt=""></div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_1" class="col-sm-3 col-md-3 col-lg-2 control-label ">Address 1:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="address_1" value="{{ old('address_1') }}" class="form-control" id="address_1">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_2" class="col-sm-3 col-md-3 col-lg-2 control-label ">Address 2:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="address_2" value="{{ old('address_2') }}" class="form-control" id="address_2">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="city" class="col-sm-3 col-md-3 col-lg-2 control-label ">City:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="city" value="{{ old('city') }}" class="form-control " id="city">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="state" class="col-sm-3 col-md-3 col-lg-2 control-label ">State/Province:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="state" value="{{ old('state') }}" class="form-control " id="state">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="zip" class="col-sm-3 col-md-3 col-lg-2 control-label ">Zip:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="zip" value="{{ old('zip') }}" class="form-control " id="zip">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="country" class="col-sm-3 col-md-3 col-lg-2 control-label ">Country:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="country" value="{{ old('country') }}" class="form-control " id="country">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="comments" class="col-sm-3 col-md-3 col-lg-2 control-label ">Comments:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <textarea name="comments" cols="17" rows="5" id="comments" class="form-control text-area">{{ old('comments') }}</textarea>
                                        </div>
                                    </div>

                                </div><!-- /col-md-12 -->
                            </div><!-- /row -->

                            <div class="form-group offset1">
                                <label for="hire_date" class="col-sm-3 col-md-3 col-lg-2 control-label text-info wide">Hire date:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <div class="input-group date">
                                        <span class="input-group-addon bg">
                                           <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="text" name="hire_date" value="" id="hire_date" class="form-control datepicker">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group offset1">
                                <label for="birthday" class="col-sm-3 col-md-3 col-lg-2 control-label text-info wide">Birthday:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <div class="input-group date">
                                        <span class="input-group-addon bg">
                                           <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="text" name="birthday" value="" id="birthday" class="form-control datepicker">

                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="employee_number" class="col-sm-3 col-md-3 col-lg-2 control-label">Employee number:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="text" name="employee_number" value="" id="employee_number" class="form-control">
                                </div>
                            </div>

                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="pe-7s-power"></i> Employee Login Info</h3>
                            </div>
                            <div class="form-group"><br>
                                <label for="username" class="col-sm-3 col-md-3 col-lg-2 control-label required">Username:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="text" name="username" value="" id="username" class="form-control" >
                                    <span class="text-danger">{{ $errors->first('username') }}</span>
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
                                <label for="password" class="col-sm-3 col-md-3 col-lg-2 control-label">Password:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="password" name="password" value="" id="password" class="form-control" autocomplete="off" >
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="repeat_password" class="col-sm-3 col-md-3 col-lg-2 control-label">Password Again:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="password" name="repeat_password" value="" id="repeat_password" class="form-control" autocomplete="off" >
                                    <span class="text-danger">{{ $errors->first('repeat_password') }}</span>
                                </div>
                            </div>

                            <div class="panel-heading">
                               <h3 class="panel-title"><i class = "pe-7s-info"></i>  Employee Permissions and Access<br></h3>
                            </div>

                            <br><br>
                            <div class="form-group">
                                <label for="counter_permissions" class="col-sm-3 col-md-3 col-lg-2 control-label">List of accessible counters:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <select class="form-control" id="counter_permissions" name="counter_permissions[]" multiple>
                                        @foreach($counters as $aCounter)
                                            <option value="{{ $aCounter->id }}">{{ $aCounter->name }}</option>
                                        @endforeach
                                    </select><br>
                                    <input  type="checkbox" id="checkbox" >&nbsp; Select All
                                </div>

                            </div>

                            <p class="text-center">Check the boxes below to grant access to modules</p>
                            <div class="panel-body form-group">

                                <ul id="permission_list" class="list-unstyled">
                                    @foreach($modules as $module)
                                        <input type="checkbox" name="permissions[]" value="{{$module['name']}}" id="{{$module['category_id']}}" class="module_checkboxes" onchange="groupSelect(this);">
                                        <label for="permissions{{$module['name']}}"><span></span></label>						<span class="text-success">{{$module['name']}}</span>
                                        <span class="text-warning">{{$module['description']}}</span>
                                        <li>
                                            <ul class="list-unstyled list-permission-actions">
                                                @foreach($module['permissions'] as $permission)
                                                    <li>
                                                        <input type="checkbox" name="permissions_actions[]" value="{{$permission['permission_token']}}"  class="permissions_{{$permission["permission_category_id"]}}" id="permissions_actions  {{$permission['permission_token']}}">
                                                        <label for="permissions_actions{{$permission['permission_name']}}"><span></span></label>								<span class="text-info">{{$permission['permission_name']}}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>


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
@stop

@section('additionalJS')
<script>

    $(document).ready(function(){

        $('.datepicker').datepicker({
            orientation: "bottom",
            autoclose: true,
            format: 'yyyy/mm/dd'
        });
        $("#counter_permissions").select2();

        $("#checkbox").click(function(){
            if($("#checkbox").is(':checked') ){
                $("#counter_permissions > option").prop("selected","selected");
                $("#counter_permissions").trigger("change");
            }else{
                $("#counter_permissions > option").removeAttr("selected");
                $("#counter_permissions").trigger("change");
            }
        });

    });



    function groupSelect(checkBox){
        selectClass = ".permissions_"+checkBox.id;
        if(checkBox.checked == true){
            $(selectClass).prop('checked', 'checked');
        }else{
            $(selectClass).prop('checked', '');
        }
    }
    function loadTempImage(input){
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#image_empty').attr('src', e.target.result) .width(150)
                        .height(200);;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }



</script>


@endsection


