@extends('layouts.master')

@section('pageTitle','Customer Assign Price Levels')

@section('breadcrumbs')
        {!! Breadcrumbs::render('customer_list') !!}
@stop

@section('content')
    <div class="filter-box">
        <div class="row">
            <div class="col-md-6">
                <div class="input-group pull-left" style="width: 30%;">
                    <input type="text" id="global_filter" class="form-control pull-right global_filter" placeholder="Search">

                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row hidden" id="selectButtonHolder" style="margin-top:10px">
            <div class="col-md-12">
                <div class="input-group">
                    <select class="form-control" id="price_level">
                        @foreach($priceLevels as $aPriceLevel)
                            <option value="{{ $aPriceLevel->id }}">{{ $aPriceLevel->name }}</option>
                        @endforeach
                    </select><br><br>
                    <button style="margin-right:5px" class="btn btn-primary" id="applyButton">Apply</button>
                    <button style="margin-right:5px" class="btn btn-danger" id="deleteButton">Delete</button>
                    <button style="margin-right:5px" class="btn btn-default" id="selectAllButton">Select All</button>
                    <button class="btn btn-default" id="clearAllButton">Clear All</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="modal-body">
            <div class="box box-info" id="add_balance_section">
                <div class="box-body">
                    <table  class="table table-bordered table-hover table-striped" id="tableAll">
                        <thead>
                        <tr>
                            <th>Actions</th>
                            <th>Item Name</th>
                            <th>Price Level</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($allItems as $item)
                            <tr data-id="{{ $item->id }}">
                                <td></td>
                                <td>{{$item->item_name}}</td>
                                <td>
                                   @if($customer->items->contains($item->id))
                                       @php
                                            $priceLevelId = DB::table('customer_item')
                                            ->where("customer_id",$customer->id)
                                            ->where("item_id",$item->id)->first()->price_level_id;
                                            $priceLevel = \App\Model\PriceLevel::where("id",$priceLevelId)->first();
                                       @endphp
                                       {{ $priceLevel->name }}( {{ $priceLevel->percentage }}%)
                                   @else
                                        No price level assigned
                                   @endif
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('additionalJS')
    <script>

        var table;

        function filterGlobal (tableActive) {

            $(tableActive).DataTable().search(
                $('#global_filter').val(),
                $('#global_regex').prop('checked'),
                $('#global_smart').prop('checked')
            ).draw();
        }

        $(document).ready(function(){

            table = $("#tableAll").DataTable({

                pageLength:10,
                columnDefs: [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets:   0
                }],
                select: {
                    style:    'multi',
                    selector: 'td:first-child'
                },
                dom:"Bt<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-4'l><'col-sm-8'p>>",
            });



            $('input.global_filter').on( 'keyup click', function () {
                filterGlobal("#tableAll");
            });

            $("#tableAll").DataTable().on( 'select', function ( e, dt, type, indexes ) {

                if ( type === 'row' ) {
                    $('#selectButtonHolder').removeClass('hidden');
                }

            });

            $("#tableAll").DataTable().on( 'deselect', function ( e, dt, type, indexes ) {
                var count_rows =  $("#tableAll").DataTable().rows('.selected').data().length;
                if(count_rows==0){
                    $('#selectButtonHolder').addClass('hidden');
                }
            } );

            $('#selectAllButton').click( function () {

                $("#tableAll").DataTable().rows({ page: 'current' }).select();

            });

            $('#clearAllButton').click( function () {

                $("#tableAll").DataTable().rows({ page: 'current' }).deselect();

            } );

            $('#deleteButton').click( function () {
                $("#deleteModal").modal('toggle');
            });

            $('#applyButton').click(function(){

                var table = $("#tableAll").DataTable();

                var id_list = $.map(table.rows('.selected').nodes(), function (item) {
                    return $(item).attr("data-id");
                });

                var price_level_id = $("#price_level").val();
                $.ajax({
                    url: "{{route('customer_assign_price_level')}}",
                    type: "post",
                    data: {
                        id_list:id_list,
                        price_level_id: price_level_id,
                        customer_id: "{{ $customer->id }}"
                    },
                    success: function(response){
                        location.reload();
                    }
                });
            });

            $('#deleteButton').click(function(){

                var table = $("#tableAll").DataTable();

                var id_list = $.map(table.rows('.selected').nodes(), function (item) {
                    return $(item).attr("data-id");
                });

                var price_level_id = $("#price_level").val();
                $.ajax({
                    url: "{{route('customer_remove_price_level')}}",
                    type: "post",
                    data: {
                        id_list:id_list,
                        price_level_id: price_level_id,
                        customer_id: "{{ $customer->id }}"
                    },
                    success: function(response){
                        location.reload();
                    }
                });
            });
        });


    </script>
@stop