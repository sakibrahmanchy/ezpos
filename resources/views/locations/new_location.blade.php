@extends('layouts.master')

@section('pageTitle','New Location')

@section('breadcrumbs')
    {!! Breadcrumbs::render('new_location') !!}
@stop

@section('content')

    <div class="box box-primary" style="padding:20px">
        @include('includes.message-block')
        <div class="row" id="form">
            <div class="col-md-12">
                <form action="{{route('new_location')}}" id="location_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="pe-7s-edit"></i>
                            Location Information   <small>(Fields in red are required)</small>
                        </h3>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Location Name:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" id="name" >
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address" class="col-sm-3 col-md-3 col-lg-2 control-label ">Address:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                        <textarea name="address" cols="17" rows="5" id="address" class="form-control text-area">{{ old('address') }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="phone" class="col-sm-3 col-md-3 col-lg-2 control-label ">Phone:</label>	<div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" id="phone" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="value" class=" col-sm-3 col-md-3 col-lg-2 required control-label ">Printer IP:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" name="printer_ip" value="{{ old('printer_ip') }}" class="form-control" id="printer_ip">
                                        <span class="text-danger">{{ $errors->first('printer_ip') }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="value" class=" col-sm-3 col-md-3 col-lg-2 required control-label ">Printer Port:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" name="printer_port" value="{{ old('printer_port') }}" class="form-control" id="printer_port">
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
