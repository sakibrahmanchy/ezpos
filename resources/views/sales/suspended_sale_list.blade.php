
<style>
    thead {
        table-layout: fixed;
        word-wrap: break-word;
    }

</style>

@extends('layouts.master')

@section('pageTitle','Suspended Sale List')

@section('breadcrumbs')
    {!! Breadcrumbs::render('supplier_list') !!}
@stop

@section('content')

    <style>
        td{
            white-space: nowrap;
        }
    </style>

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
            <div class="col-md-6">
                <div class="pull-right">
                    <div class="buttons-list">
                        <div class="pull-right-btn">

                            <div class="piluku-dropdown btn-group">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row hidden" id="selectButtonHolder" style="margin-top:10px">
            <div class="col-md-12">
                <div class="input-group">
                    <button style="margin-right:5px" class="btn btn-danger" id="deleteButton">Delete Row(s)</button>
                    <button style="margin-right:5px" class="btn btn-default" id="selectAllButton">Select All</button>
                    <button class="btn btn-default" id="clearAllButton">Clear All</button>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary" style="padding:20px">
        <div class="card table-responsive">

            <table  class="table table-hover " >
                <thead>
                <tr>
                    <th></th>
                    <th>Suspended Sale Id</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Amount Paid</th>
                    <th>Amount Due</th>
                    <th>Unsuspend</th>
                    <th>Sales Receipt</th>
                </tr>
                </thead>
                <tbody>
                @foreach($suspended_sales as $suspended_sale)
                    <tr>
                        <td></td>
                        <td>EZPOS {{$suspended_sale->id}} </td>
                        <td>{{$suspended_sale->created_at}}</td>
                        <td>
                            @if($suspended_sale->sale_status==\App\Enumaration\SaleStatus::$LAYAWAY)
                                Lay Away
                            @else
                                Estimate
                            @endif
                        </td>
                        <td></td>
                        <td>
							{{ implode(',', $suspended_sale->item_names ) }}
                        </td>

                        <td>{{$suspended_sale->total_amount}}</td>
                        <td>{{  $suspended_sale->total_amount-$suspended_sale->due }}</td>
                        <td>{{$suspended_sale->due}}</td>
                        <td><a class="btn btn-default" href="{{ route("sale_edit",["sale_id"=>$suspended_sale->id]) }}">Unsuspend</a></td>
                        <td><a href="{{route('sale_receipt',["sale_id"=>$suspended_sale->id])}}" class="btn btn-default">Receipt</a></td>

                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>

@endsection


@section('additionalJS')
    <script>
        function filterGlobal () {
            $('.table').DataTable().search(
                $('#global_filter').val(),
                $('#global_regex').prop('checked'),
                $('#global_smart').prop('checked')
            ).draw();
        }

        $(document).ready(function(){

            table = $('.table').DataTable({

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
                buttons: [
                    'copy', 'csv', 'excel', 'print','colvis'
                ],
            });

            table.on( 'select', function ( e, dt, type, indexes ) {
                if ( type === 'row' ) {
                    $('#selectButtonHolder').removeClass('hidden');
                }

            });

            table.on( 'deselect', function ( e, dt, type, indexes ) {
                var count_rows =  table.rows('.selected').data().length;
                if(count_rows==0){
                    $('#selectButtonHolder').addClass('hidden');
                }
            } );

            $('#selectAllButton').click( function () {

                table.rows({ page: 'current' }).select();

            });

            $('#clearAllButton').click( function () {

                table.rows({ page: 'current' }).deselect();

            } );

            $('#deleteButton').click( function () {
                $("#deleteModal").modal('toggle');
            });

            $('#confirmDelete').click(function(){

                var id_list = $.map(table.rows('.selected').nodes(), function (item) {
                    return $(item).attr("data-id");
                });

                console.log(id_list);
                $.ajax({
                    url: "{{route('suppliers_delete')}}",
                    type: "post",
                    data: {
                        id_list:id_list
                    },
                    success: function(response){
                        if(response.success)
                            table.rows('.selected').remove().draw( false );
                        $("#deleteModal").modal('toggle');
                        $('#selectButtonHolder').addClass('hidden');
                    }

                });
            });

            $('input.global_filter').on( 'keyup click', function () {
                filterGlobal();
            } )

        });

    </script>
@stop