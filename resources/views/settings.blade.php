@extends('layouts.master')

@section('pageTitle','Settings')

@section('breadcrumbs')
    {!! Breadcrumbs::render('settings') !!}
@stop

@include('includes.message-block')

@section('content')
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {display:none;}

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

    </style>


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
                                    </div>
                                    <br><br><br>

                                    <label for="phone" class="col-sm-3 col-md-3 col-lg-2 control-label">Phone:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <div class="input-group date">
                                            <span class="input-group-addon bg">
                                                <i class="fa fa-phone"></i>
                                            </span>
                                            <input class="form-control" type="text" name="phone" value="{{ $settings['phone'] }}">
                                        </div>
                                    </div>
                                    <br><br><br>

                                    <label for="phone" class="col-sm-3 col-md-3 col-lg-2 control-label">Customer Loyalty Percentage:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <div class="input-group date">
                                    <span class="input-group-addon bg">
                                     <i class="fa fa-percent    "></i>
                                    </span>
                                            <input class="form-control" type="text" name="customer_loyalty_percentage" value="{{ $settings['customer_loyalty_percentage'] }}">
                                        </div>
                                    </div>
                                    <br><br><br>

                                    <label for="phone" class="col-sm-3 col-md-3 col-lg-2 control-label">Negative Inventory:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <label class="switch">
                                            <input type="checkbox" onchange="negativeInventory()" id="negative_inventory" @if($settings['negative_inventory']=="true") checked @else  @endif}}>
                                            <span class="slider round"></span>
                                        </label>
                                        <input type="hidden" name="negative_inventory" id="negative_inventory_value" value="{{ $settings['negative_inventory'] }}"/>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <br><br>
                        <div class="form-group" data-keyword="currency">
                            <label class="col-sm-3 col-md-3 col-lg-2 control-label ">Currency Denominations:</label>						<div class="table-responsive col-sm-9 col-md-4 col-lg-4">
                                <table id="currency_denoms" class="table">
                                    <thead>
                                    <tr>
                                        <th>Denomination</th>
                                        <th>Currency Value</th>
                                        <th>Delete</th>
                                    </tr>
                                    </thead>

                                    <tbody>

                                    @foreach($denominators as $aDenominator)
                                        <tr>
                                            <td><input type="text" name="denomination_name[]" class="form-control" value="{{ $aDenominator->denomination_name }}"></td>
                                            <td><input type="text" name="denomination_value[]" class="form-control" value="{{ $aDenominator->denomination_value }}"></td>
                                            <td><a class="delete_currency_denom text-primary" href="javascript:void(0);">Delete</a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <a href="javascript:void(0);" id="add_denom" onclick="addNewDenominator()">Add currency denomination</a>
                            </div>
                        </div>


                        <div class="form-actions pull-right">
                            <input type="submit" value="Submit" id="submitf" class="btn floating-button btn-primary float_right">
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
            </div>
            </form>	</div>
    </div>
    </div>
@endsection


@section('additionalJS')
    <script>

        function addNewDenominator(){
            console.log("Aa");
            $("#currency_denoms tbody").append('<tr><td><input type="text" class="form-control" name="denomination_name[]" value="" /></td><td><input type="text" class="form-control" name="denomination_value[]" value="" /></td><td>&nbsp;</td></tr>');
        }

        $(".delete_currency_denom").click(function()
        {
            $(this).parent().parent().remove();
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

        function negativeInventory(){
            var status = $("#negative_inventory").is(':checked');
            $("#negative_inventory_value").val(status);
        }

    </script>
@stop


