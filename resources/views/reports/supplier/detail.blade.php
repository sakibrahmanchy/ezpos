
@extends('layouts.master')

@section('pageTitle','Supplier Detail Report')

@section('breadcrumbs')
    {!! Breadcrumbs::render('report_supplier_detail') !!}
@stop

@section('content')
    <?php $dateTypes = new \App\Enumaration\DateTypes(); ?>

    <div class="filter-box">
        <div class="row">
            <div class="col-md-12">

                <div class="form-inline">

                    <div class="form-group" style="float:left">
                        <div class="input-group" >
                            <select class="form-control supplierSelect" id="supplier_id">
                                <option value="0" selected>No Supplier</option>
                                @foreach($suppliers as $aSupplier)
                                    <option value="{{ $aSupplier->id }}">{{ $aSupplier->company_name }} </option>
                                @endforeach
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
                        <span class="count" id="sub_total_amount">{{$info["subtotal"]}}</span>
                        <p>Subtotal</p>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                    <div class="info-seven primarybg-info">
                        <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                        <span class="count" id="total_amount">{{$info["total"]}}</span>
                        <p>Total</p>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                    <div class="info-seven primarybg-info">
                        <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                        <span class="count" id="tax_amount">{{$info["tax"]}}</span>
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
                        <th align="left" class="header">Sale Id</th>
                        <th align="left" class="header">Date</th>
                        <th align="left" class="header">Sold By</th>
                        <th align="left" class="header">Sold To</th>
                        <th align="right" class="header">Subtotal_amount</th>
                        <th align="right" class="header">Total</th>
                        <th align="right" class="header">Tax</th>
                        <th align="right" class="header">Profit</th>
                        <th align="right" class="header">Payments</th>
                        <th align="right" class="header">Number of items sold</th>
                    </tr>
                    </thead>
                    <tbody id="data-table">
                        @foreach($sales as $aSale)


                            <tr>
                                <td><a href="{{route('sale_receipt',['sale_id'=>$aSale->id])}}">
                                        <span class="glyphicon glyphicon-print"></span></a>
                                    <a href="{{route('sale_edit',['id'=>$aSale->id])}}"><span class="glyphicon glyphicon-edit"></span></a>
                                        EZPOS {{$aSale->id}}</td>
                                <td>{{ $aSale->created_at }}</td>
                                <td>{{ $aSale->employee->name }}</td>
                                <td>{{ $aSale->customer->first_name }} {{ $aSale->customer->last_name }}</td>
                                <td>
                                    @if($aSale->sub_total_amount>=0)
                                        ${{$aSale->sub_total_amount}}
                                    @else
                                        -${{ (-1) * $aSale->sub_total_amount }}
                                    @endif
                                </td>
                                <td>
                                    @if($aSale->total_amount>=0)
                                        ${{$aSale->total_amount}}
                                    @else
                                        -${{ (-1) * $aSale->total_amount }}
                                    @endif
                                </td>
                                <td>
                                    @if($aSale->tax_amount>=0)
                                        ${{$aSale->tax_amount}}
                                    @else
                                        -${{ (-1) * $aSale->tax_amount }}
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

                                    @foreach($aSale->paymentLogs as $aPayment)
                                        <span style="color: #0976b4;">{{$aPayment->payment_type}}</span>: ${{$aPayment->paid_amount}}<br>
                                    @endforeach

                                </td>

                                <td>
                                        {{ (int) $aSale->items_sold}}
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

         $("#supplier_id").select2();

         $('#order_by,  #start_date_formatted, #end_date_formatted, #supplier_id').change(function() {
             $('.data').addClass('hide');
             $('.se-pre-con').removeClass('hide');


             var order_by = $("#order_by").val();
             var start_date_formatted = $("#start_date_formatted").val();
             var end_date_formatted = $("#end_date_formatted").val();
             var supplier_id = $("#supplier_id").val();


             $.ajax({
                 method: "POST",
                 url: "{{ route('report_supplier_ajax') }}",
                 data: {
                     supplier_id:supplier_id,
                     report_name:"{{ $report_name }}",
                     modifier:"{{ $modifier }}",
                     order_by: "{{ $report_type }}",
                     start_date_formatted: start_date_formatted,
                     end_date_formatted: end_date_formatted
                 }
             }).done(function( data ) {

                 //console.log(data);

                 $("#sub_total_amount").text(data.info['subtotal']);
                 $("#total_amount").text(data.info['total']);
                 $("#tax_amount").text(data.info['tax']);
                 $("#profit").text(data.info['profit']);

                 /* animateNumber($("#sub_total_amount"),data.info['sub_total_amount']);*/

                 tableData="";
                 data.sale.forEach(function(item){

                    var receipt_url = '{{ route("sale_receipt", ":id") }}';
                     receipt_url = receipt_url.replace(':sale_id', item.id);

                     var edit_url = '{{ route("sale_edit", ":sale_id") }}';
                     edit_url = edit_url.replace(':sale_id', item.id);

                    tableData += "<tr>";
                    tableData += "<td><a  href='"+ receipt_url +"'><span class='glyphicon glyphicon-print'></span></a>  <a   href='"+ edit_url +"'><span class='glyphicon glyphicon-edit'></span></a> EZPOS "+ item.id +"</td>";
                    tableData += "<td>"+ item.created_at +"</td>";

                    if(item.employee.name!=null)
                        tableData+="<td>"+item.employee.name;
                    else
                        tableData+=" <td> No Employee ";
                    tableData+="</td>";
                    tableData+="<td>";

                    if(item.customer!=undefined){
                         if(item.customer.first_name!=null)
                             tableData+=""+item.customer.first_name+" ";
                         if(item.customer.last_name!=null)
                             tableData+=item.customer.last_name;
                    }else{
                         tableData+="No Customer";
                    }
                    tableData+="</td>";

                    tableData+="<td>";
                    if(item.sub_total_amount>=0)
                        tableData+="$"+item.sub_total_amount;
                    else
                        tableData+="-$"+(-1) * item.sub_total_amount;
                    tableData+="</td>";
                    tableData+="<td>";
                    if(item.total_amount>=0)
                        tableData+="$"+item.total_amount;
                    else
                        tableData+="-$"+ (-1) * item.total_amount;
                    tableData+="</td>";
                    tableData+="<td>";
                    if(item.tax_amount>=0)
                        tableData+="$"+item.tax_amount;
                    else
                        tableData+="-$"+ (-1) * item.tax_amount;
                    tableData+="</td>";
                    tableData+="<td>";
                    if(item.profit>=0)
                       tableData+="$"+item.profit;
                    else
                        tableData+="-$"+ (-1) * item.profit;
                    tableData+="</td>";
                     tableData+="<td>";
                    var payments = item.payment_logs;

                     payments.forEach(function(payment){
                        tableData+=payment.payment_type+": $"+payment.paid_amount+"<br>";

                     });
                     tableData+="</td>";
                     tableData+="<td>";
                     tableData+= parseInt(item.items_sold);
                     tableData+="</td>";
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