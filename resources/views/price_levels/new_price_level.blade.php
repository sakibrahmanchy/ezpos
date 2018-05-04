@extends('layouts.master')

@section('pageTitle','New Price Level')

@section('breadcrumbs')
    {!! Breadcrumbs::render('new_price_level') !!}
@stop

@section('content')
    <div class="box box-primary" style="padding:20px">
        @include('includes.message-block')
        <div class="row" id="form">
            <div class="col-md-12">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="pe-7s-edit"></i>
                        Price Level Information <small>(Fields in red are required)</small>
                    </h3>
                </div>

                <form action="{{route('new_price_level')}}" id="item_kit_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="name" class="col-sm-3 col-md-3 col-lg-2 control-label required wide">Level Name:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="text" name="name" value="{{ old('name') }}" id="name" required="required" class="form-control form-inps">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="col-sm-3 col-md-3 col-lg-2 control-label">Description:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <textarea name="description" cols="30" rows="4" id="description" class="form-control text-area" value="">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="percentage" class="col-sm-3 col-md-3 col-lg-2 control-label wide required">Percentage:</label>					<div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="text" name="percentage" value="" id="percentage" class="form-control form-inps" step="any">
                            </div>
                        </div>

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-controls">
                            <ul class="list-inline pull-right">
                                <li>
                                    <input type="submit" name="submitf" value="Submit" id="submitf" class=" btn btn-primary">
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('additionalJS')
    <script>



    </script>
@stop