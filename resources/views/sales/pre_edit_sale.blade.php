@extends('layouts.master')

@section('pageTitle','Edit Sale')

@section('breadcrumbs')
    {{--{!! Breadcrumbs::render('new_sale') !!}--}}
    <span><label class="label label-primary pull-right counter-name"><b>{{ \Illuminate\Support\Facades\Cookie::get('counter_name') }}</b></label></span>
    <br><br>
    <a href="javascript:void(0)"  onclick="changeCounter()" class="pull-right">Change Location</a>
    <br>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-piluku">
                <div class="panel-heading">
                    Edit Sale EZPOS {{ $sale_id }}			</div>
                <div class="panel-body">
                    <form action="{{ route("sale_pre_edit_post",["sale_id"=>$sale_id]) }}" id="sales_edit_form" class="form-horizontal" method="post" accept-charset="utf-8" novalidate="novalidate">


                        <div class="form-group">
                            <label for="sales_receipt" class="col-sm-3 col-md-3 col-lg-2 control-label ">Sales Receipt:</label>					<div class="col-sm-9 col-md-9 col-lg-10 sale-s">
                                <a href="{{ route('sale_receipt',["sale_id"=>$sale_id]) }}" target="_blank">EZPOS {{ $sale_id }}</a>					</div>
                        </div>

                        <div class="form-group">
                            <label for="customer" class="col-sm-3 col-md-3 col-lg-2 control-label ">Customer:</label>					<div class="col-sm-9 col-md-3 col-lg-3 sale-s">

                                <select name="customer_id" id="customer_id">
                                    <option value ="0">No Customer</option>
                                    @foreach($customers as $aCustomer)
                                        <option value="{{ $aCustomer->id }}" @if(!is_null($sale->Customer)) {{ $aCustomer->id == $sale->Customer->id ? 'selected' : '' }} @endif>{{ $aCustomer->first_name }} {{ $aCustomer->last_name }}</option>
                                    @endforeach
                                </select>
                                &nbsp;
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="customer" class="col-sm-3 col-md-3 col-lg-2 control-label ">Customer:</label>					<div class="col-sm-9 col-md-3 col-lg-3 sale-s">

                                <select  name="employee_id" id="employee_id">
                                    <option value ="0">No Employee</option>
                                    @foreach($employees as $anEmployee)
                                        <option value="{{ $anEmployee->id }}" @if(!is_null($sale->Employee)) {{ $anEmployee->id == $sale->Employee->id ? 'selected' : '' }} @endif>{{ $anEmployee->first_name }} {{ $anEmployee->last_name }}</option>
                                    @endforeach
                                </select>
                                &nbsp;
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="comment" class="col-sm-3 col-md-3 col-lg-2 control-label ">Comment:</label>					<div class="col-sm-9 col-md-3 col-lg-3">
                                <textarea name="comment" cols="23" rows="4" id="comment" class="form-control text-area">{{ $sale->comment }}</textarea>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-sm-9 col-md-3 col-md-offset-2">
                                <input type="submit" name="submit_edit" value="Save" id="submit_edit" class=" btn btn-primary">
                            </div>
                            <br><br>

                            <div class="col-sm-9 col-md-3 col-md-offset-2">
                                <a  class="btn btn-primary" href="{{ route('sale_edit',["sale_id"=>$sale_id]) }}">Change Sale</a>
                            </div>
                            <br><br>
                            <div class="col-sm-9 col-md-3 col-md-offset-2">
                                <a  class=" btn btn-danger" href="{{ route('sale_delete',["sale_id"=>$sale_id]) }}">Delete Entire Sale</a>
                            </div>
                        </div>

                    </form>




                </div>
            </div>
        </div>
    </div>
@endsection
@section('additionalJS')
    <script>
        $("#customer_id").select2();
        $("#employee_id").select2();

    </script>
@stop
