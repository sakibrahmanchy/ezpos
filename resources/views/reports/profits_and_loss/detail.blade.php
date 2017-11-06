
@extends('layouts.master')

@section('pageTitle','Profit and Loss Detail Report')

@section('breadcrumbs')
    {!! Breadcrumbs::render('report_profit_and_loss_detail') !!}
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
                <div class="table-responsive col-md-6">
                    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                        <thead>
                            <h3>Sales</h3>
                        </thead>
                        <tbody id="data-table-sales">
                            @foreach($sales as $aSale)
                                <tr>
                                    <td>{{ $aSale->payment_type}}</td>
                                    <td>
                                        @if($aSale->paid_amount>=0)
                                            ${{$aSale->paid_amount}}
                                        @else
                                            -${{ (-1) * $aSale->paid_amount }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive col-md-6">
                    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                        <thead>
                        <h3>Profit</h3>
                        </thead>
                        <tbody id="data-table-profit">
                          <tr>
                              <td>Total</td>
                              <td>
                                  @if($info['profit']>=0)
                                      ${{$info['profit']}}
                                  @else
                                      -${{ (-1) * $info['profit'] }}
                                  @endif
                              </td>
                          </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive col-md-6" >
                    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                        <thead>
                        <h3>Returns</h3>
                        </thead>
                        <tbody id="data-table-returns">
                        @foreach($returns as $aSale)
                            <tr>
                                <td>{{ $aSale->payment_type}}</td>
                                <td>
                                    @if($aSale->paid_amount>=0)
                                        ${{$aSale->paid_amount}}
                                    @else
                                        -${{ (-1) * $aSale->paid_amount }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive col-md-6">
                    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                        <thead>
                        <h3>Discounts</h3>
                        </thead>
                        <tbody id="data-table-discounts">
                        <tr>
                            <td>Discount</td>
                            <td>
                                @if($discount>=0)
                                    ${{$discount}}
                                @else
                                    -${{ (-1) * $discount }}
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive col-md-6">
                    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                        <thead>
                        <h3>Taxes</h3>
                        </thead>
                        <tbody id="data-table-tax">
                        <tr>
                            <td>Taxes</td>
                            <td>
                                @if($info["tax"]>=0)
                                    ${{$info["tax"]}}
                                @else
                                    -${{ (-1) * $info["tax"] }}
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive col-md-6">
                    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                        <thead>
                        <h3>Total Sales - Returns - Discounts - Taxes</h3>
                        </thead>
                        <tbody id="data-table-total">
                        <tr>
                            <td>Total</td>
                            <td>
                                @if($total>=0)
                                    ${{$total}}
                                @else
                                    -${{ (-1) * $total }}
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>








@endsection



@section('additionalJS')

 <script>

     $(document).ready(function(e) {


         $(' #start_date_formatted, #end_date_formatted').change(function() {
             $('.data').addClass('hide');
             $('.se-pre-con').removeClass('hide');

             var start_date_formatted = $("#start_date_formatted").val();
             var end_date_formatted = $("#end_date_formatted").val();

             $.ajax({
                 method: "POST",
                 url: "{{ route('report_profit_loss_ajax') }}",
                 data: {
                     report_name: "detail",
                     start_date_formatted: start_date_formatted,
                     end_date_formatted: end_date_formatted
                 }
             }).done(function( data ) {

                 //console.log(data);

                 console.log(data);
                 tableData="";
                 data.sales.forEach(function(item){
                     tableData+="<tr><td>"+item.payment_type+"</td>";
                     if(item.paid_amount>=0)
                         tableData += "<td>$"+item.paid_amount+"</td>";
                     else
                         tableData += "<td>-$"+(-1) * item.paid_amount+"</td>";
                     tableData += "</tr>";
                 });
                 if(tableData=="")
                     tableData+="<tr><td>&nbsp;</td><td>&nbsp; </td></tr>";
                 $("#data-table-sales").html(tableData);

                 tableData="";
                 data.returns.forEach(function(item){
                     tableData+="<tr><td>"+item.payment_type+"</td>";
                     if(item.paid_amount>=0)
                         tableData += "<td>$"+item.paid_amount+"</td>";
                     else
                         tableData += "<td>-$"+(-1) * item.paid_amount+"</td>";
                     tableData += "</tr>";
                 });
                 if(tableData=="")
                     tableData+="<tr><td>&nbsp; </td><td>&nbsp; </td></tr>";
                 $("#data-table-returns").html(tableData);

                 tableData="";
                 tableData+="<tr><td>Discount</td>";
                 if(data.discount>=0)
                     tableData += "<td>$"+data.discount+"</td>";
                 else
                     tableData += "<td>-$"+(-1) * data.discount+"</td>";
                 tableData += "</tr>";
                 $("#data-table-discounts").html(tableData);

                 tableData="";
                 tableData+="<tr><td>Taxes</td>";
                 if(data.info['tax']>=0)
                     tableData += "<td>$"+data.info['tax']+"</td>";
                 else
                     tableData += "<td>-$"+(-1) * data.info['tax']+"</td>";
                 tableData += "</tr>";
                 $("#data-table-tax").html(tableData);

                 tableData="";
                 tableData+="<tr><td>Total</td>";
                 if(data.total>=0)
                     tableData += "<td>$"+data.total+"</td>";
                 else
                     tableData += "<td>-$"+(-1) * data.total+"</td>";
                 tableData += "</tr>";
                 $("#data-table-total").html(tableData);

                 tableData="";
                 tableData+="<tr><td>Profit</td>";
                 if(data.info['profit']>=0)
                     tableData += "<td>$"+data.info['profit']+"</td>";
                 else
                     tableData += "<td>-$"+(-1) * data.info['profit']+"</td>";
                 tableData += "</tr>";
                 $("#data-table-profit").html(tableData);


                 $('.se-pre-con').addClass('hide');
                 $('.data').removeClass('hide');


             });


         });

     });


 </script>



@stop