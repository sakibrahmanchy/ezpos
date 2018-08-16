@extends('layouts.master')

@section('pageTitle','Clear Sales Data')

@include('includes.message-block')

@section('breadcrumbs')
    {!! Breadcrumbs::render('sale_search') !!}
@stop

@section('content')

    <div class="box box-primary" style="padding:20px">
        <div class="panel-heading hidden-print">
            Select date range to delete data			</div>
        <div class="panel-body hidden-print">
            <form id = "salesReportGenerator" name="salesReportGenerator" action="{{route('clear_sales_data')}}" method="post" class="form-horizontal form-horizontal-mobiles">
                {{ csrf_field() }}
                <input type="hidden" name="isPosted" value="0">
                <div id="report_date_range_complex">
                    <div class="form-group">
                        <div class="col-sm-9 col-md-9 col-lg-10 col-md-offset-1">

                            <label for="complex_radio"><span></span></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-daterange" id="reportrange">
		                                    <span class="input-group-addon bg">
					                           From					                       	</span>
                                        <input type="text" class="datepicker form-control start_date" name="start_date_formatted" id="start_date_formatted" value="09/12/2017"><input type="hidden" id="start_date" name="start_date" value="2017-09-12">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-daterange" id="reportrange1">
		                                    <span class="input-group-addon bg">
			                                    To			                                </span>
                                        <input type="text" class="datepicker form-control end_date" name="end_date_formatted" id="end_date_formatted" value="09/12/2017"><input type="hidden" id="end_date" name="end_date" value="2017-09-12">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="form-actions text-center">
                    <button name="generate_report" onclick="submitForm()" value="1" id="generate_report" class="submit_button btn btn-primary btn-lg">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('additionalJS')
    <script>
        $(document).ready(function() {

        });


    </script>
@stop