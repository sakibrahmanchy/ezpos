@extends('layouts.master')

@section('pageTitle','New Supplier')

@section('breadcrumbs')
    {!! Breadcrumbs::render('new_supplier') !!}
@stop

@section('content')
    <div class="box box-primary" style="padding:20px">
        @include('includes.message-block')
        <div class="row" id="form">
            <div class="col-md-12">
                <form action="{{route('new_supplier')}}" id="supplier_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <div class="panel panel-piluku">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="pe-7s-edit"></i>
                               Supplier Information <small>(Fields in red are required)</small>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="company_name" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Company Name:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="company_name" value="{{ old('company_name') }}" class="form-control" id="company_name" >
                                            <span class="text-danger">{{ $errors->first('company_name') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="first_name" class=" col-sm-3 col-md-3 col-lg-2 control-label ">First Name:</label>
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
                                        <label for="email" class="col-sm-3 col-md-3 col-lg-2 control-label ">E-Mail:</label>
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
                                                    <div id="avatar"><img src="{{asset('img\avatar.png')}}" class="img-polaroid" id="image_empty" alt=""></div>
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

                                    <div class="form-group">
                                        <label for="country" class="col-sm-3 col-md-3 col-lg-2 control-label ">Account #:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="account" value="{{ old('account') }}" class="form-control " id="account">
                                        </div>
                                    </div>

                                </div>
                            </div>

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