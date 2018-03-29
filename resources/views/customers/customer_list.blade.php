@extends('layouts.master')

@section('pageTitle','Customer List')

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
            <div class="col-md-6">
                <div class="pull-right">
                    @if(UserHasPermission("customer_add_update"))
                        <a href="{{route('new_customer')}}" class="btn btn-primary hidden-sm hidden-xs" title="New Item"><span class=""><i class="fa fa-plus-circle" aria-hidden="true"></i> New Customer</span></a>
                    @endif
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
        <div class = "row">
            <div class="col-md-12 table-responsive">

                <table class="table table-hover " >
                    <thead>
                    <tr >
                        <th></th>
                        <th>Actions</th>
                        <th>Account No.</th>
                        <th>Loyalty No.</th>
                        <th>Person Id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $customer)
                        <tr data-id="{{ $customer->id }}">
                            <td></td>
                            <td><div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i class="pe-7s-pen"></i>
                                        <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        @if(UserHasPermission("customer_add_update"))
                                            <li><a href="{{route('customer_edit',['customer_id'=>$customer->id])}}">Edit Customer</a></li>
                                        @endif
                                        @if(UserHasPermission("customer_delete"))
                                            <li><a href="{{route('customer_delete',['customer_id'=>$customer->id])}}">Delete</a></li>
                                        @endif
                                    </ul>
                                </div></td>
                            <td>{{ $customer->account_number }}</td>
                            <td>@if($customer->loyalty_card_number) {{ $customer->loyalty_card_number }} @else None @endif</td>
                            <td>{{$customer->id}}</td>
                            <td><a href="{{ route('customer_profile',["customer_id"=>$customer->id]) }}">{{$customer->first_name}} {{$customer->last_name}}</a></td>
                            <td>{{$customer->email}}</td>
                            <td>{{$customer->phone}}</td>
                            <td> @if($customer->image_token!=null)
                                    <img src="{{asset('img/customers/userpictures/'.$customer->image_token)}}" height="40" width="40" class="img-polaroid" id="image_empty" alt="" />
                                @else
                                    <img src="{{asset('img/faces/face-0.jpg')}}" class="img-polaroid"  height="40" width="40" id="image_empty" alt="" />
                                @endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

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
                    url: "{{route('customers_delete')}}",
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


        });

        $('input.global_filter').on( 'keyup click', function () {
            filterGlobal();
        } )

    </script>
@stop