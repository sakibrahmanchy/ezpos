@extends('layouts.master')

@section('pageTitle','Edit Gift Card')

@section('breadcrumbs')
    {!! Breadcrumbs::render('gift_card_edit',$gift_card->id) !!}
@stop

@section('content')
    <div class="box box-primary" style="padding:20px">
        @include('includes.message-block')
        <div class="row" id="form">
            <div class="col-md-12">
                <form action="{{route('gift_card_edit',["gift_card_id"=>$gift_card->id])}}" id="gift_card_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="pe-7s-edit"></i>
                            Gift Card Information    					<small>(Fields in red are required)</small>
                        </h3>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label for="gift_card_number" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Gift Card Number:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" name="gift_card_number" value="{{ $gift_card->gift_card_number }}" class="form-control" id="gift_card_number" >
                                        <span class="text-danger">{{ $errors->first('gift_card_number') }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="col-sm-3 col-md-3 col-lg-2 control-label ">Description:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <textarea name="description" cols="17" rows="5" id="description" class="form-control text-area">{{ $gift_card->description }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="value" class=" col-sm-3 col-md-3 col-lg-2 required control-label ">Value:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" name="value" value="{{ $gift_card->value }}" class="form-control" id="value">
                                        <span class="text-danger">{{ $errors->first('value') }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="customer_id" class="col-sm-3 col-md-3 col-lg-2 control-label ">Customer:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <select name="customer_id"  id="customer_id" class="form-control">
                                            <option value="0"></option>
                                            @foreach($customers as $aCustomer)
                                                @if($aCustomer->id==$gift_card->customer_id)
                                                    <option selected value="{{ $aCustomer->id }}">{{ $aCustomer->first_name }} {{ $aCustomer->last_name }}</option>
                                                @else
                                                    <option value="{{ $aCustomer->id }}">{{ $aCustomer->first_name }} {{ $aCustomer->last_name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="taxable" class="col-sm-3 col-md-3 col-lg-2 control-label ">Active:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                            @if($gift_card->status)
                                                <input type="checkbox" name="status" value="1" checked  id="status">
                                            @else
                                                <input type="checkbox" name="status" value="1"   id="status">
                                            @endif
                                        <label for="taxable"><span></span></label>
                                    </div>
                                </div>

                                <div class="form-actions pull-right">
                                    <input type="submit" name="submitf" value="Submit" id="submitf" class="btn floating-button btn-primary float_right">
                                </div>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </div>
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