@extends('layouts.master')

@section('pageTitle','Item Kit List')

@section('breadcrumbs')
    {!! Breadcrumbs::render('item_kit_list') !!}
@stop

@section('content')
    <div class="filter-box">
        <div class="row">
            <div class="col-md-6">
                 <div class="input-group col-md-6 col-sm-6 pull-left">
                    <input type="text" id="global_filter" class="form-control pull-right global_filter" placeholder="Search">

                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 " >
                <div class="pull-right">
                    <div class="pull-right-btn">
                        @if(UserHasPermission("itemkit_add_update"))
                            <a href="{{route('new_item_kit')}}" class="btn btn-primary hidden-sm hidden-xs" title="New Item">
                            <i class="fa fa-plus-circle"></i>
                            <span class="">
                                New Item Kit
                            </span>
                            </a>
                        @endif
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

    <div class="box box-primary nav-tabs-custom" style="padding:20px">
        <div class="table-responsive">
            <table class="table table-hover ">
                <thead>
                    <tr>
                        <th></th>
                        <th>Actions</th>
                        <th>Item Kit Id</th>
                        <th>Item Kit Name</th>
                        <th>UPC/EAN/ISBN</th>
                        <th>Cost Price</th>
                        <th>Selling Price</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itemKits as $itemKit)
                        <tr data-id="{{ $itemKit->item_kit_id }}">
                            <td></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                        <i class="pe-7s-pen"></i>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(UserHasPermission("itemkit_add_update"))
                                            <li>
                                                <a href="{{route('item_kit_edit',['item_kit_id'=>$itemKit->item_kit_id])}}">
                                                    Edit
                                                </a>
                                            </li>
                                        @endif
                                        @if(UserHasPermission("itemkit_delete"))
                                            <li>
                                                <a href="{{route('item_kit_delete',['item_kit_id'=>$itemKit->item_kit_id])}}">
                                                    Delete
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                            <td> {{$itemKit->item_kit_id}} </td>
                            <td>{{$itemKit->item_kit_name}}</td>
                            <td>{{$itemKit->isbn}}</td>
                            <td>{{$itemKit->cost_price}}</td>
                            <td>{{$itemKit->selling_price}}</td>
                            <td>{{$itemKit->category_name}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal modal-danger fade" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete</h4>
                </div>
                <div class="modal-body">
                    <p>You are requesting for a delete operation, which cannot be reverted. Are you sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancel</button>
                    <button id="confirmDelete" type="button" class="btn btn-outline">Delete</button>
                </div>
            </div>
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
                url: "{{route('item_kits_delete')}}",
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