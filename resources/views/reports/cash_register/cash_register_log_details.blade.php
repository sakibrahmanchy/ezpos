@extends('layouts.master')

@section('pageTitle','Cash Register Logs')

@section('content')
    <?php $dateTypes = new \App\Enumaration\DateTypes(); ?>

    <div class="filter-box">
        <div class="row">
            <div class="col-md-12">

                <div class="form-inline">

                    <div class="form-group" style="float:right">
                        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                            <input id="end_date_formatted" name="end_date_formatted" type="text" class="form-control" value="{{ date('Y-m-d') }}">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="float:right">
                        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                            <input id="start_date_formatted" name="start_date_formatted" type="text" class="form-control" value="{{date('Y-m-d')}}">
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

                <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                    <div class="info-seven primarybg-info">
                        <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                        <span class="count" id="cash_sales">{{$info["cash_sales"]}}</span>
                        <p>Total Cash Sales</p>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                    <div class="info-seven primarybg-info">
                        <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                        <span class="count" id="total_shortages">{{$info["total_shortages"]}}</span>
                        <p>Total Shortages</p>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                    <div class="info-seven primarybg-info">
                        <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                        <span class="count" id="total_overages">{{$info["total_overages"]}}</span>
                        <p>Total Overages</p>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                    <div class="info-seven primarybg-info">
                        <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                        <span class="count" id ="total_difference">{{$info["total_difference"]}}</span>
                        <p>Total Difference</p>
                    </div>
                </div>

            </div>

            <div class="row" id="table_view">
                @include('reports.cash_register.details_table')
            </div>

        </div>
    </div>

@endsection



@section('additionalJS')

    <script>
        var table;
        $(document).ready(function(e) {

            countAnimate();
            table = $('.table').DataTable({
                pageLength:10,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print',
                ],
            });

            $(' #start_date_formatted, #end_date_formatted').change(function() {
                $('.data').addClass('hide');
                $('.se-pre-con').removeClass('hide');

                var start_date_formatted = $("#start_date_formatted").val();
                var end_date_formatted = $("#end_date_formatted").val();

                $.ajax({
                    method: "POST",
                    url: "{{ route('cash_register_log_report_details_ajax') }}",
                    data: {
                        report_name:"{{ $report_name }}",
                        start_date_formatted: start_date_formatted,
                        end_date_formatted: end_date_formatted
                    }
                }).done(function( data ) {

                    console.log(data);

                    $("#cash_sales").text(data.info['cash_sales']);
                    $("#total_shortages").text(data.info['total_shortages']);
                    $("#total_overages").text(data.info['total_overages']);
                    $("#total_difference").text(data.info['total_overages']);

                    /* animateNumber($("#cash_sales"),data.info['cash_sales']);*/

                    table.destroy();
                    $("#table_view").html("");
                    $("#table_view").html(data.contents);
                    console.log(data.contents);
                    table = $('.table').DataTable({
                        "bInfo" : false,
                        "bSort": false,
                        paging: false,
                        dom: 'Bfrtip',
                        buttons: [
                            'copy', 'csv', 'excel', 'print',
                        ],
                    });

                    $('.se-pre-con').addClass('hide');
                    $('.data').removeClass('hide');
                    countAnimate();

                });


            });

        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function countAnimate(){

            $('.count').each(function () {
                var value = $(this).text();

                var unformatted = value.replace(",", "");
                /*var value = parseInt($(this).text());*/

                $(this).prop('Counter',0).animate({
                    Counter: unformatted
                }, {
                    duration: 500,
                    easing: 'swing',
                    step: function (now) {
                        if(now>=0)
                            $(this).text("$"+numberWithCommas(now.toFixed(2)));
                        else
                            $(this).text("-$"+numberWithCommas((-1) * now.toFixed(2)));
                    }
                });
            });

        }
    </script>



@stop
