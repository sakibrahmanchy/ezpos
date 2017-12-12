@extends('layouts.master')

@section('pageTitle','Edit Supplier')

@section('breadcrumbs')
    {!! Breadcrumbs::render('supplier_edit',$supplier->id) !!}
@stop

@section('content')
    <div class="box box-primary" style="padding:20px">
        @include('includes.message-block')
        <div class="row" id="form">
            <div class="col-md-12">
                <form action="{{route('supplier_edit',['supplier_id'=>$supplier->id])}}" id="supplier_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <div class="panel panel-piluku">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="pe-7s-edit"></i>
                                supplier Basic Information <small>(Fields in red are required)</small>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="company_name" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Company Name:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="company_name" value="{{$supplier->company_name}}" class="form-control" id="company_name" >
                                            <span class="text-danger">{{ $errors->first('company_name') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="first_name" class=" col-sm-3 col-md-3 col-lg-2 control-label ">First Name:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="first_name" value="{{$supplier->first_name}}" class="form-control" id="first_name" >
                                            <span class="text-danger">{{ $errors->first('first_name') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="last_name" class=" col-sm-3 col-md-3 col-lg-2 control-label ">Last Name:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="last_name" value="{{$supplier->last_name}}" class="form-control" id="last_name">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="email" class="col-sm-3 col-md-3 col-lg-2 control-label ">E-Mail:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="email" value="{{$supplier->email}}" class="form-control" id="email" >
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone" class="col-sm-3 col-md-3 col-lg-2 control-label ">Phone Number:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="phone" value="{{$supplier->phone}}" class="form-control" id="phone">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="image_id" class="col-sm-3 col-md-3 col-lg-2 control-label ">Select Image:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <ul class="list-unstyled avatar-list">
                                                <li>
                                                    <input type="file" name="image" onchange = "loadTempImage(this)" id="image" class="filestyle" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);"><div class="bootstrap-filestyle input-group"><input type="text" class="form-control " disabled=""> <span class="group-span-filestyle input-group-btn" tabindex="0"><label for="image" class="btn btn-file-upload "><span class="pe-7s-folder"></span> <span class="buttonText">Choose file</span></label></span></div>&nbsp;
                                                </li>
                                                <li>
                                                    @if($supplier->image_location!=null)
                                                        <div id="avatar"><img src="{{asset('img/suppliers/userpictures/'.$supplier->image_location)}}" class="img-polaroid" id="image_empty" alt=""></div>
                                                    @else
                                                        <div id="avatar"><img src="{{asset('img/avatar.png')}}" class="img-polaroid" id="image_empty" alt=""></div>

                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_1" class="col-sm-3 col-md-3 col-lg-2 control-label ">Address 1:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="address_1" value="{{$supplier->address_1}}" class="form-control" id="address_1">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_2" class="col-sm-3 col-md-3 col-lg-2 control-label ">Address 2:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="address_2" value="{{$supplier->address_2}}" class="form-control" id="address_2">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="city" class="col-sm-3 col-md-3 col-lg-2 control-label ">City:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="city" value="{{$supplier->city}}" class="form-control " id="city">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="state" class="col-sm-3 col-md-3 col-lg-2 control-label ">State/Province:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="state" value="{{$supplier->state}}" class="form-control " id="state">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="zip" class="col-sm-3 col-md-3 col-lg-2 control-label ">Zip:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="zip" value="{{$supplier->zip}}" class="form-control " id="zip">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="country" class="col-sm-3 col-md-3 col-lg-2 control-label ">Country:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="country" value="{{$supplier->country}}" class="form-control " id="country">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="comments" class="col-sm-3 col-md-3 col-lg-2 control-label ">Comments:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                            <textarea name="comments" cols="17" rows="5" id="comments" value ="{{$supplier->comments}}" class="form-control text-area"></textarea>
                                        </div>
                                    </div>

                                </div><!-- /col-md-12 -->
                            </div><!-- /row -->

                            <div class="form-group">
                                <label for="supplier_number" class="col-sm-3 col-md-3 col-lg-2 control-label">supplier number:</label>						<div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="text" name="account" value="{{$supplier->supplier_number}}" id="supplier_number" class="form-control">
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