
@extends('layouts.master')

@section('pageTitle','Inventory Detail Report')

@section('breadcrumbs')
    {!! Breadcrumbs::render('report_inventory_detail') !!}
@stop

@section('content')
    <?php $dateTypes = new \App\Enumaration\DateTypes(); ?>

    <div class="filter-box">
        <div class="row">
            <div class="col-md-12">
                <div class="form-inline">
                    <div class="form-group" style="float:left">
                        <div class="input-group" >
                            <select class="form-control itemSelect" id="item_id">
                                <option value="0" selected>All Items</option>
                                @foreach($items as $anItem)
                                    <option value="{{ $anItem->id }}">{{ $anItem->item_name }} </option>
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

    <div class="box box-primary nav-tabs-custom" style="padding:20px">

        <div class="se-pre-con text-center hide">
            <img height="30%" width="30%"  src = "{{ asset('img/loader.gif') }}" >
        </div>

        <div class="data">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                    <thead>
                    <tr>
                        <th align="left" class="header">Item Id</th>
                        <th align="left" class="header">Date</th>
                        <th align="left" class="header">Item Name</th>
                        <th align="left" class="header">Category</th>
                        <th align="right" class="header">In/Out Quantity</th>
                        <th align="right" class="header">Reason</th>
                        <th align="right" class="header">User</th>
                    </tr>
                    </thead>
                    <tbody id="data-table">
                        @foreach($sales as $aSale)


                            <tr>
                                <td class="text-center">{{$aSale->item->id}}</td>
                                <td>{{$aSale->created_at}}</td>
                                <td>{{ $aSale->item->item_name }}</td>
                                <td>{{ $aSale->item->category->category_name }}</td>
                                <td class="text-center">{{ $aSale->in_out_quantity }}</td>
                                <td>{!!  $aSale->reason !!}</td>
                                <td>{{  $aSale->user->name }}</td>
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

         table = $('.table').DataTable({
             pageLength:10,
             dom: 'Bfrtip',
             buttons: [
                 'copy', 'csv', 'excel', 'print',
             ],
         });

         $("#item_id").select2();

         $('#order_by,  #start_date_formatted, #end_date_formatted, #item_id').change(function() {
             $('.data').addClass('hide');
             $('.se-pre-con').removeClass('hide');


             var start_date_formatted = $("#start_date_formatted").val();
             var end_date_formatted = $("#end_date_formatted").val();
             var item_id = $("#item_id").val();


             $.ajax({
                 method: "POST",
                 url: "{{ route('report_inventory_ajax') }}",
                 data: {
                     report_type: "inventory_detail",
                     item_id:item_id,
                     start_date_formatted: start_date_formatted,
                     end_date_formatted: end_date_formatted
                 }
             }).done(function( data ) {

                 //console.log(data);


                 tableData="";

                 data.sale.forEach(function(item){

                    var receipt_url = '{{ route("sale_receipt", ":id") }}';
                     receipt_url = receipt_url.replace(':sale_id', item.id);

                     var edit_url = '{{ route("sale_edit", ":sale_id") }}';
                     edit_url = edit_url.replace(':sale_id', item.id);

                    tableData += "<tr>";
                    tableData += "<td>"+item.item.id+"</td>";
                    tableData += "<td>"+ item.created_at +"</td>";
                    tableData+="<td>"+item.item.item_name;
                    tableData+="</td>";
                    tableData+="<td>";
                    if(item.item.category)
                        tableData+=""+item.item.category.category_name+" ";
                    else tableData+="";

                    tableData+="</td>";
                    tableData+="<td>";
                    tableData+=""+item.in_out_quantity;
                    tableData+="</td>";
                    tableData+="<td>";
                    tableData+=""+item.reason;
                    tableData+="</td>";
                    tableData+="<td>";
                    tableData+=""+item.user.name;
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


             });


         });

     });

   </script>



@stop