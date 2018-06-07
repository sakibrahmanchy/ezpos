@extends('layouts.master')

@section('pageTitle','Gift Card List')

@section('breadcrumbs')
    {!! Breadcrumbs::render('gift_card_list') !!}
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
                    @if(UserHasPermission("gift_cards_add_update"))
                        <a href="{{route('new_gift_card')}}" class="btn btn-primary hidden-sm hidden-xs" title="New Customer"><i class="fa fa-plus-circle"></i> <span class="">New Gift Card</span></a>
                    @endif
                </div>
            </div>
        </div>
        <div class="row hidden" id="selectButtonHolder" style="margin-top:10px">
            <div class="col-md-12">
                <div class="input-group">
                    @if(UserHasPermission("price_rules_delete"))
                        <button style="margin-right:5px" class="btn btn-danger" id="deleteButton">Delete Row(s)</button>
                    @endif
                    <button style="margin-right:5px" class="btn btn-default" id="selectAllButton">Select All</button>
                    <button class="btn btn-default" id="clearAllButton">Clear All</button>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-primary" style="padding:20px">
        <div class = "row">
            <div class="col-md-12 table-responsive">

                <table class="table">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Actions</th>
                        <th>Gift Card Number</th>
                        <th>Value</th>
                        <th>Description</th>
                        <th>Customer Name</th>
                        <th>Active/Inactive</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($gift_cards as $gift_card)
                        <tr data-id="{{ $gift_card->id }}">
                            <td></td>
                            <td><div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i class="pe-7s-pen"></i>
                                        <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        @if(UserHasPermission("gift_cards_add_update"))
                                            <li><a href="{{route('gift_card_edit',['gift_card_id'=>$gift_card->id])}}">Edit Gift Card</a></li>
                                        @endif
                                        @if(UserHasPermission("gift_cards_delete"))
                                            <li><a href="{{route('gift_card_delete',['gift_card_id'=>$gift_card->id])}}">Delete</a></li>
                                        @endif
                                    </ul>
                                </div></td>
                            <td>{{$gift_card->gift_card_number}}</td>
                            <td>${{$gift_card->value}}</td>
                            <td>{{$gift_card->description}}</td>
                            @if($gift_card->customer!=null)
                                <td>{{$gift_card->customer->first_name}} {{$gift_card->customer->last_name}}</td>
                            @else
                                <td></td>
                            @endif
                            <td> @if($gift_card->status==\App\Enumaration\GiftCardStatus::$ACTIVE)
                                    <span class="label label-success">Active</span>
                                @else
                                    <span class="label label-danger">Inactive</span>

                                @endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
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
                            columns: [2,3,4,5,6]
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: [2,3,4,5,6]
                        }

                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [2,3,4,5,6]
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [2,3,4,5,6]
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

                console.log(id_list);
                $.ajax({
                    url: "{{route('gift_cards_delete')}}",
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