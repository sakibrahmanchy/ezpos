
@extends('layouts.master')

@section('pageTitle','Supplier Graphical Report')

@section('breadcrumbs')
    {!! Breadcrumbs::render('report_supplier_graphical') !!}
@stop

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
                            <span class="count" id="subtotal">{{$info["subtotal"]}}</span>
                        <p>Subtotal</p>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                    <div class="info-seven primarybg-info">
                        <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                            <span class="count" id="total">{{$info["total"]}}</span>
                                    <p>Total</p>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                    <div class="info-seven primarybg-info">
                        <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                            <span class="count" id="tax">{{$info["tax"]}}</span>
                                        <p>Tax</p>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                    <div class="info-seven primarybg-info">
                        <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                            <span class="count" id ="profit">{{$info["profit"]}}</span>
                                    <p>Profit</p>
                    </div>
                </div>
            </div>

            <div class="row"><canvas id="canvas" width="200" height="70"></canvas></div>
        </div>
    </div>






@endsection



@section('additionalJS')

 <script>
     usedIndex = [];
     var myChart;
     $(document).ready(function(e){

        countAnimate();

         var dataset = JSON.parse('<?php echo json_encode($datasets) ?>')
         backgroundColor = [];
         dataset.forEach(function(element){
             backgroundColor.push(getRandomColor());
         });

         var ctx = document.getElementById("canvas");
         myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                 labels:  JSON.parse('<?php echo json_encode($labels) ?>'),
                 datasets: [{
                     label:JSON.parse('<?php echo json_encode($report_name) ?>'),
                     data:  dataset,
                     backgroundColor:backgroundColor,

                     borderWidth: 1
                 }]
             },
             options: {

             }
         });

     });


     $('#order_by,  #start_date_formatted, #end_date_formatted').change(function() {
        $('.se-pre-con').removeClass('hide');
        $('.data').addClass('hide');
        myChart.destroy();

         var order_by = $("#order_by").val();
         var start_date_formatted = $("#start_date_formatted").val();
         var end_date_formatted = $("#end_date_formatted").val();

         $.ajax({
             method: "POST",
             url: "{{ route('report_supplier_ajax') }}",
             data: {
                 report_name:"{{ $report_name }}",
                 modifier:"{{ $modifier }}",
                 order_by: "{{ $report_type }}",
                 start_date_formatted: start_date_formatted,
                 end_date_formatted: end_date_formatted
             }
         }).done(function( data ) {

             //console.log(data);

            $("#subtotal").text(data.info['subtotal']);
            $("#total").text(data.info['total']);
            $("#tax").text(data.info['tax']);
            $("#profit").text(data.info['profit']);

            /* animateNumber($("#subtotal"),data.info['subtotal']);*/
             $('.se-pre-con').addClass('hide');
             $('.data').removeClass('hide');
             countAnimate();


             //Create Chart
             var dataset = data.dataset;
             backgroundColor = [];
             dataset.forEach(function(element){
                 backgroundColor.push(getRandomColor());
             });

             var ctx = document.getElementById("canvas");
             myChart = new Chart(ctx, {
                 type: 'pie',
                 data: {
                     labels:  data.labels,
                     datasets: [{
                         label: JSON.parse('<?php echo json_encode($report_name) ?>'),
                         data:  dataset,
                         backgroundColor:backgroundColor,
                         borderWidth: 1
                     }]
                 },
                 options: {

                 }
             });

         });


     });



     function getRandomColor() {
         var color = randomColor({
             luminosity: 'light',
             hue: 'blue'
         });
         return color;
     }


     function animateNumber($el, value){

         $({percentage: 0}).stop(true).animate({percentage: value}, {
             duration : 2000,
             easing: "easeOutExpo",
             step: function () {
                 // percentage with 1 decimal;

                 var percentageVal = Math.round(this.percentage * 10) / 10;

                 $el.text(percentageVal);
             }
         }).promise().done(function () {
             // hard set the value after animation is done to be
             // sure the value is correct
             $el.text(value);
         });
     }

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