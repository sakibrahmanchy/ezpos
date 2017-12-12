@extends('layouts.master')

@section('pageTitle','Close Out Report')

@section('breadcrumbs')
    {!! Breadcrumbs::render('report_closeout') !!}
@stop

@section('content')
    <?php $dateTypes = new \App\Enumaration\DateTypes(); ?>

    <div class="filter-box">
        <div class="row">
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

    <div class="box box-primary nav-tabs-custom" style="padding:20px">

        <div class="se-pre-con text-center hide">
            <img height="30%" width="30%"  src = "{{ asset('img/loader.gif') }}" >
        </div>

        <div class="data">
            <div class="row" id="table_view">
               @include('reports.close_out.summary_table')
            </div>
        </div>
    </div>

@endsection



@section('additionalJS')

 <script>
     var table;
     $(document).ready(function(e) {


         table = $('.table').DataTable({
             "bInfo" : false,
             "bSort": false,
             paging: false,
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
                 url: "{{ route('report_close_out_ajax') }}",
                 data: {
                     report_name: "detail",
                     start_date_formatted: start_date_formatted,
                     end_date_formatted: end_date_formatted
                 }
             }).done(function( data ) {

                 //console.log(data);

                 table.destroy();
                 $("#table_view").html("");
                 $("#table_view").html(data.contents);
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

             });


         });

     });


 </script>



@stop