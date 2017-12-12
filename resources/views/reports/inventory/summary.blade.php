
@extends('layouts.master')

@section('pageTitle','Inventory Summary Report')

@section('content')
    <?php $dateTypes = new \App\Enumaration\DateTypes(); ?>

    <style>
        .small-text{
            font-size: 15px;
        }
    </style>

    <div class="filter-box">
        <div class="row">
            <div class="form-inline">
                <div class="form-group" style="float:right">
                    <div class="input-group" >
                        <select class="form-control categorySelect" id="category_id">
                            <option value="0" selected>All</option>
                            @foreach($categories as $aCategory)
                                <option value="{{ $aCategory->id }}">{{ $aCategory->category_name }} </option>
                            @endforeach
                        </select>
                        <div class="input-group-addon">
                            <b>Category</b>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="float:right">
                    <div class="input-group" >
                        <select class="form-control customerSelect" id="supplier_id">
                            <option value="0" selected>All</option>
                            @foreach($suppliers as $aSupplier)
                                <option value="{{ $aSupplier->id }}">{{ $aSupplier->company_name }} </option>
                            @endforeach
                        </select>
                        <div class="input-group-addon">
                            <b>Supplier</b>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="float:left">
                    <div class="input-group" >
                        <select class="form-control customerSelect" id="stock_type">
                            <option value="0" selected>All</option>
                            <option value = "1">In Stock</option>
                            <option value = "2">Out of Stock</option>
                        </select>
                        <div class="input-group-addon">
                            <b>Stock</b>
                        </div>
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
            <div class="row">

                <div class="col-md-12">

                    <div class="form-inline">

                        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                            <div class="info-seven primarybg-info" >
                                <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                                <span class="" id="sub_total_amount">{{(int) $info["totalItems"]}}</span>
                                <p class="small-text">Total Items in Inventory</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                            <div class="info-seven primarybg-info">
                                <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                                <span class="count" id="total_amount">{{$info["inventoryValue"]}}</span>
                                <p class="small-text">Total Inventory Value</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                            <div class="info-seven primarybg-info">
                                <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                                <span class="count" id="tax_amount">{{$info["weightedAverage"]}}</span>
                                <p class="small-text">Weighted Average Cost Price</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-12 col-sm-6 summary-data">
                            <div class="info-seven primarybg-info">
                                <div class="logo-seven"><i class="glyphicon glyphicon-globe"></i></div>
                                <span class="count" id ="profit">{{$info["inventoryValueByUnit"]}}</span>
                                <p class="small-text">Total Inventory Value By Unit Price</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="table-responsive">
                <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                    <thead>
                    <tr>
                        <th align="left" class="header">Id</th>
                        <th align="left" class="header">Item Name</th>
                        <th align="right" class="header">Category</th>
                        <th align="right" class="header">Supplier</th>
                        <th align="right" class="header">Product ID</th>
                        <th align="right" class="header">Description</th>
                        <th align="right" class="header">Cost Price</th>
                        <th align="right" class="header">Selling Price</th>
                        <th align="right" class="header">Count</th>
                        <th align="right" class="header">Reorder Level</th>
                        <th align="right" class="header">Replenish Level</th>

                    </tr>
                    </thead>
                    <tbody id="data-table">
                    @foreach($items as $aSale)
                        <tr>
                            <td>{{ $aSale->id }}</td>
                            <td>{{ $aSale->item_name }}</td>
                            <td>
                                {{$aSale->category->category_name}}
                            </td>
                            <td>
                                {{$aSale->supplier->company_name}}
                            </td>
                            <td>{{$aSale->product_id}}</td>
                            <td>{{$aSale->description}}</td>
                            <td>${{$aSale->cost_price}}</td>
                            <td>${{$aSale->selling_price}}</td>
                            @if($aSale->item_quantity<=0)
                                <td class="text-center"><span style="font-size:15px;" class="label label-danger">{{$aSale->item_quantity}}</span></td>
                            @elseif($aSale->item_quantity<$aSale->item_reorder_level)
                                <td class="text-center"><span style="font-size:15px;" class="label label-warning">{{$aSale->item_quantity}}</span></td>
                            @else
                                <td class="text-center"><span style="font-size:15px;" class="label label-success">{{$aSale->item_quantity}}</span></td>
                            @endif
                            <td>@if($aSale->item_reorder_level!=null) {{$aSale->item_reorder_level}} @else Not Set @endif   </td>
                            <td>@if($aSale->item_replenish_level!=null) {{$aSale->item_replenish_level}} @else Not Set @endif   </td>
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

            $('#supplier_id,  #category_id, #stock_type').change(function() {

                $('.data').addClass('hide');
                $('.se-pre-con').removeClass('hide');

                var supplier_id = $("#supplier_id").val();
                var category_id = $("#category_id").val();
                var stock_type = $("#stock_type").val();

                $.ajax({
                    method: "POST",
                    url: "{{ route('report_inventory_ajax') }}",
                    data: {
                        report_type: "inventory_summary",
                        supplier_id: supplier_id,
                        category_id: category_id,
                        stock_type: stock_type
                    }
                }).done(function( data ) {

                    tableData="";
                    data.sale.forEach(function(item){

                        tableData += "<tr>";
                        tableData+=" <td> "+ item.id +"</td>";
                        tableData+=" <td> "+ item.item_name +"</td>";
                        tableData+="<td>";
                        tableData+=item.category.category_name;
                        tableData+="</td>";
                        tableData+="<td>";
                        tableData+=item.supplier.company_name;
                        tableData+="</td>";
                        tableData+="<td>"+item.product_id+"</td>";
                        tableData+="<td>"+item.description+"</td>";
                        tableData+="<td>"+item.cost_price+"</td>";
                        tableData+="<td>"+item.selling_price+"</td>";
                        if(item.item_quantity<=0){

                            tableData+='<td class="text-center"><span style="font-size:15px;" class="label label-danger">';
                            tableData+= parseInt(item.item_quantity);
                            tableData+="</span></td>";

                        }else if(item.item_quantity<item.item_reorder_level){

                            tableData+='<td class="text-center"><span style="font-size:15px;" class="label label-warning">';
                            tableData+= parseInt(item.item_quantity);
                            tableData+="</span></td>";

                        }else{

                            tableData+='<td class="text-center"><span style="font-size:15px;" class="label label-success">';
                            tableData+= parseInt(item.item_quantity);
                            tableData+="</span></td>";
                        }

                        if(item.item_reorder_level!=null)
                            tableData+='<td>'+item.item_reorder_level+'</td>';
                        else
                            tableData+='<td>Not Set</td>';
                        if(item.item_replenish_level!=null)
                            tableData+='<td>'+item.item_replenish_level+'</td>';
                        else
                            tableData+='<td>Not Set</td>';
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