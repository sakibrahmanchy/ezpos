@extends('layouts.master')

@section('pageTitle','Settings')

@section('breadcrumbs')
    {!! Breadcrumbs::render('settings') !!}
@stop

@include('includes.message-block')

@section('content')

    <div class="box box-primary" style="padding:20px">
        <div class="row" id="form">
            <div class="spinner" id="grid-loader" style="display:none">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
            </div>
            <div class="col-md-12">

                <form action="{{ route('save_settings') }}" id="user_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    {{-- {{ csrf_field() }}--}}

                    <div class="panel panel-piluku">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="pe-7s-edit"></i>
                               Settings<small>(Fields in red are required)</small>
                            </h3>
                        </div>

                        <div class="panel-body">

                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label for="first_name" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Company Name:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="company_name" value="{{$settings['company_name']}}" class="form-control" id="company_name" >
                                            <span class="text-danger">{{ $errors->first('company_name') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="image_id" class="col-sm-3 col-md-3 col-lg-2 control-label ">Select Company Logo:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <ul class="list-unstyled avatar-list">
                                                <li>
                                                    <input type="file" name="image" onchange = "loadTempImage(this)" id="image" class="filestyle" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);"><div class="bootstrap-filestyle input-group"><input type="text" class="form-control " disabled=""> <span class="group-span-filestyle input-group-btn" tabindex="0"><label for="image" class="btn btn-file-upload "><span class="pe-7s-folder"></span> <span class="buttonText">Choose file</span></label></span></div>&nbsp;
                                                </li>
                                                <li>
                                                         <div id="avatar"><img src="{{asset('img/logo.png?'.rand())}}" class="img-responsive logo-preview" id="image_empty" alt=""></div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <label for="hire_date" class="col-sm-3 col-md-3 col-lg-2 control-label">Tax Rate:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <div class="input-group date">
                                    <span class="input-group-addon bg">
                                     %
                                    </span>
                                           <input class="form-control" type="text" name="tax_rate" value="{{ $settings['tax_rate'] }}">

                                        </div>
                                    </div><br><br><br>

                                    <label for="address" class="col-sm-3 col-md-3 col-lg-2 control-label">Address:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <div class="input-group date">
                                    <span class="input-group-addon bg ">
                                        <i class="fa fa-map"></i>
                                    </span>
                                            <input class="form-control" type="text" name="address" value="{{ $settings['address'] }}">

                                        </div>
                                    </div><br><br><br>

                                    <label for="phone" class="col-sm-3 col-md-3 col-lg-2 control-label">Phone:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <div class="input-group date">
                                    <span class="input-group-addon bg">
                                     <i class="fa fa-phone"></i>
                                    </span>
                                            <input class="form-control" type="text" name="phone" value="{{ $settings['phone'] }}">

                                        </div>
                                    </div>

                                </div>

                            </div>

                            <br><br>


                            <div class="form-actions pull-right">
                                <input type="submit" value="Submit" id="submitf" class="btn floating-button btn-primary float_right">
                            </div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </div>
                </form>	</div>
        </div>
    </div>

    <script>


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


