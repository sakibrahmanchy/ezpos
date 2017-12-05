@extends('layouts.master')

@section('pageTitle','Edit Counter')

@section('breadcrumbs')
    {!! Breadcrumbs::render('counter_edit',$counter->id) !!}
@stop

@section('content')

    <div class="box box-primary" style="padding:20px">
        @include('includes.message-block')
        <div class="row" id="form">
            <div class="col-md-12">
                <form action="{{route('counter_edit',["counter_id"=>$counter->id])}}" id="counter_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="pe-7s-edit"></i>
                            Counter Information   <small>(Fields in red are required)</small>
                        </h3>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Counter Name:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" name="name" value="{{ $counter->name }}" class="form-control" id="name" >
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="col-sm-3 col-md-3 col-lg-2 control-label ">Description:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                        <textarea name="description" cols="17" rows="5" id="description" class="form-control text-area">{{ $counter->description }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="value" class=" col-sm-3 col-md-3 col-lg-2 required control-label ">Printer IP:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" name="printer_ip" value="{{ $counter->printer_ip }}" class="form-control" id="printer_ip">
                                        <span class="text-danger">{{ $errors->first('printer_ip') }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="value" class=" col-sm-3 col-md-3 col-lg-2 required control-label ">Printer Port:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" name="printer_port" value="{{ $counter->printer_port }}" class="form-control" id="printer_port">
                                        <span class="text-danger">{{ $errors->first('printer_port') }}</span>
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
