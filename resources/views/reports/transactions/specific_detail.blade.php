@extends('layouts.master')

@section('pageTitle','Customer Specific Transaction Report')

@section('content')
    @if(!$customer_id_found)
        <div class="row">
            <div class="modal-body">
                <div class="box box-info" id="select_customer_modal">
                    <form method="get" action="{{ route('report_transaction_details') }}">
                        <div class="box-header with-border" style="background: #00c0ef; color:white;">
                            <h3 class="box-title" >Select Customer</h3>
                        </div>
                        <div class="box-body">
                            <label for="customer_id">Customer</label>
                            <select class="form-control" name="customer_id" id="customer_id">
                                <option value="" >Select customer</option>
                                @foreach($customers as $aCustomer)
                                    <option value="{{ $aCustomer->id }}">{{ $aCustomer->first_name }} {{ $aCustomer->last_name }}</option>
                                @endforeach
                            </select><br>
                            <input type="submit" class="btn btn-info pull-right" name="submit" value="Submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $("#select_customer_modal").modal();
        </script>
    @else
        <div class="filter-box">
            <div class="row">
                <div class="col-md-12">

                    <div class="form-inline">

                        <div class="form-group" style="float:right">
                            <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                <input id="end_date_formatted" name="end_date_formatted" type="text" class="form-control" value="{{ $info['endDate'] }}">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="float:right">
                            <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                <input id="start_date_formatted" name="start_date_formatted" type="text" class="form-control" value="{{ $info['startDate'] }}">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                            To
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary" style="padding:20px">

            <div class="se-pre-con text-center hide">
                <img height="30%" width="30%"  src = "{{ asset('img/loader.gif') }}" >
            </div>

            <div class="data">
                <div class="row">

                </div>

                <div class="table-responsive">
                    <p class="pull-right label label-default" style="font-size: 20px">Opening Due: ${{$info['openingDue']}}</p>
                    @include('reports.transactions.specific_detail_table')
                    <p class="pull-right label label-default" style="font-size: 20px">Closing Due: ${{$info['closingDue']}}</p>
                </div>
            </div>
        </div>
    @endif


@endsection


@section('additionalJS')
    @if($customer_id_found)
    <script>
        var table;
        $(document).ready(function(e) {
            $(' #start_date_formatted, #end_date_formatted').change(function() {
                $('.data').addClass('hide');
                $('.se-pre-con').removeClass('hide');

                var start_date_formatted = $("#start_date_formatted").val();
                var end_date_formatted = $("#end_date_formatted").val();

                $.ajax({
                    method: "POST",
                    url: "{{ route('report_transaction_details_ajax') }}",
                    data: {
                        report_name: "detail",
                        start_date_formatted: start_date_formatted,
                        end_date_formatted: end_date_formatted,
                        customer_id: "{{ $_GET['customer_id'] }}"
                    }
                }).done(function( data ) {

                    //console.log(data);

                    $("#table_view").html("");
                    $("#table_view").html(data.contents);


                    $('.se-pre-con').addClass('hide');
                    $('.data').removeClass('hide');

                });


            });

        });


    </script>
    @endif
@stop