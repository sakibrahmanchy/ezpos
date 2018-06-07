
@extends('layouts.master')

@section('pageTitle','Price Level List')

@section('breadcrumbs')
    {!! Breadcrumbs::render('price_level_list') !!}
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
            <div class="col-md-6">
                <div class="pull-right">
                    <div class="buttons-list">
                        <div class="pull-right-btn">
                            @if(UserHasPermission("price_level_add_update"))
                                <a href="{{route('new_price_level')}}" class="btn btn-primary hidden-sm hidden-xs" title="New Price Level"><i class="fa fa-plus-circle"></i> <span class="">New Price Level</span></a>
                            @endif
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
                    @if(UserHasPermission("price_levels_delete"))
                        <button style="margin-right:5px" class="btn btn-danger" id="deleteButton">Delete Row(s)</button>
                    @endif
                    <button style="margin-right:5px" class="btn btn-default" id="selectAllButton">Select All</button>
                    <button class="btn btn-default" id="clearAllButton">Clear All</button>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary nav-tabs-custom" style="padding:20px">
        <div class="table-responsive">
            <table  class="table table-hover " >
                <thead>
                <tr>
                    <th></th>
                    <th>Actions</th>
                    <th>Price Level Name</th>
                    <th>Description</th>
                    <th>Percentage</th>
                </tr>
                </thead>
                <tbody>
                @foreach($price_levels as $price_level)
                    <tr data-id="{{ $price_level->id }}">
                        <td></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i class="pe-7s-pen"></i>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="{{route('price_level_edit',['price_level_id'=>$price_level->id])}}">Edit</a></li>
                                    <li><a href="{{route('price_level_delete',['price_level_id'=>$price_level->id])}}">Delete</a></li>
                                </ul>
                            </div></td>
                        <td>{{$price_level->name}}</td>
                        <td>{{$price_level->description}}</td>
                        <td>
                            @if($price_level->percentage>0)
                             +{{ $price_level->percentage }}%
                            @elseif($price_level->percentage<0)
                                {{ $price_level->percentage }}%
                            @else
                                0
                            @endif
                        </td>
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
                    {
                        extend: 'pdf',
                        footer: true,
                        exportOptions: {
                            columns: [2,3,4]
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: [2,3,4]
                        }

                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [2,3,4]
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [2,3,4]
                        }
                    },
                    {
                        extend: 'colvis',
                        footer: false
                    }
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

                $.ajax({
                    url: "{{route('price_levels_delete')}}",
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