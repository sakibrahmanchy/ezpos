
@extends('layouts.master')

@section('pageTitle','Sale Summary Report')

@section('breadcrumbs')
    {!! Breadcrumbs::render('report_sale_summary') !!}
@stop

@section('content')
    <?php $dateTypes = new \App\Enumaration\DateTypes(); ?>

    <div class="filter-box">
        <div class="row">
            <div class="col-md-12">

                <div class="form-inline">

                    <div class="form-group" style="float:left">
                        <div class="input-group" >
                            <select class="form-control saleSelect" id="sale_type">
                                <option value="{{ \App\Enumaration\SaleTypes::$SALE  }}" selected>Sale</option>
                                <option value="{{ \App\Enumaration\SaleTypes::$RETURN  }}" >Return</option>
                            </select>
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-user"></span>
                            </div>
                        </div>
                    </div>

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

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                    <thead>
                    <tr>
                        <th align="left" class="header">Date</th>
                        <th align="left" class="header">Subtotal</th>
                        <th align="right" class="header">Total</th>
                        <th align="left" class="header">Tax</th>
                        <th align="left" class="header">Profit</th>
                        <th align="left" class="header">Counter</th>
                    </tr>
                    </thead>
                    <tbody id="data-table">
                        @foreach($sales as $aSale)
                            <tr>
                                <td>{{ $aSale->item_name }} </td>
                                <td>
                                    @if($aSale->subtotal>=0)
                                        ${{$aSale->subtotal}}
                                    @else
                                        -${{ (-1) * $aSale->subtotal }}
                                    @endif
                                </td>
                                <td>
                                    @if($aSale->total>=0)
                                        ${{$aSale->total}}
                                    @else
                                        -${{ (-1) * $aSale->total }}
                                    @endif
                                </td>
                                <td>
                                    @if($aSale->tax>=0)
                                        ${{$aSale->tax}}
                                    @else
                                        -${{ (-1) * $aSale->tax }}
                                    @endif
                                </td>
                                <td>
                                    @if($aSale->profit>=0)
                                        ${{$aSale->profit}}
                                    @else
                                        -${{ (-1) * $aSale->profit }}
                                    @endif
                                </td>
                                <td>
                                    {{$aSale->counter_name}}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

         $('#sale_type,   #start_date_formatted, #end_date_formatted').change(function() {
             $('.data').addClass('hide');
             $('.se-pre-con').removeClass('hide');


             var sale_type = $("#sale_type").val();
             var start_date_formatted = $("#start_date_formatted").val();
             var end_date_formatted = $("#end_date_formatted").val();

             $.ajax({
                 method: "POST",
                 url: "{{ route('report_sale_ajax') }}",
                 data: {
                     report_name:"{{ $report_name }}",
                     modifier:"{{ $modifier }}",
                     sale_type: sale_type,
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

                 tableData="";
                 data.sale.forEach(function(item){
                     tableData += "<tr>";
                     tableData+=" <td> "+ item.item_name +"</td>";
                     tableData+="<td>";
                     if(item.subtotal>=0)
                         tableData+="$"+item.subtotal;
                     else
                         tableData+="-$"+(-1) * item.subtotal;
                     tableData+="</td>";
                     tableData+="<td>";
                     if(item.total>=0)
                         tableData+="$"+item.total;
                     else
                         tableData+="-$"+ (-1) * item.total;
                     tableData+="</td>";
                     tableData+="<td>";
                     if(item.tax>=0)
                         tableData+="$"+item.tax;
                     else
                         tableData+="-$"+ (-1) * item.tax;
                     tableData+="</td>";
                     tableData+="<td>";
                     if(item.profit>=0)
                         tableData+="$"+item.profit;
                     else
                         tableData+="-$"+ (-1) * item.profit;
                     tableData+="</td>";
                     tableData+="<td>"+item.counter_name+"</td>";
                     tableData+="</tr>";
                 });

                 table.destroy();
                 $("#data-table").html(tableData);
                 table = $('.table').DataTable({
                     pageLength:10,
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