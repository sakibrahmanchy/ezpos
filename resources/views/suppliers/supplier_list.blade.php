@extends('layouts.master')

@section('pageTitle','Supplier List')

@section('breadcrumbs')
    {!! Breadcrumbs::render('supplier_list') !!}
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
                            <a style="margin-right: 10px" href="{{route('new_supplier')}}" class="btn btn-primary" title="New Supplier"><i class="fa fa-plus-circle"></i> <span class="">New Supplier</span></a>
                            <div class="dropdown pull-right ">
                                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <span class="fa fa-cogs" aria-hidden="true">
                                </span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li>
                                        <a href="{{ route('supplier_import_excel') }}">
                                            Import Suppliers
                                        </a>
                                    </li>
                                </ul>
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
        <div class = "row">
            <div class="col-md-12 table-responsive">

                <table class="table">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Actions</th>
                        <th>Person Id</th>
                        <th>Company Name</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($suppliers as $supplier)
                        <tr data-id="{{ $supplier->id }}">
                            <td class="td"></td>
                            <td><div class="dropdown">
                                        <a href="{{route('supplier_edit',['supplier_id'=>$supplier->id])}}">Edit</a>
                                </div></td>
                            <td>{{$supplier->id}}</td>
                            <td>{{$supplier->company_name}}</td>
                            <td>{{$supplier->last_name}}</td>
                            <td>{{$supplier->first_name}} </td>
                            <td>{{$supplier->email}}</td>
                            <td>{{$supplier->phone}}</td>
                            <td> @if($supplier->image_location!=null)
                                    <div id="avatar"><img src="{{asset('img/suppliers/userpictures/'.$supplier->image_location)}}" height="40" width="40" class="img-polaroid" id="image_empty" alt=""></div>
                                @else
                                    <div id="avatar"><img src="{{asset('img/faces/face-0.jpg')}}" class="img-polaroid"  height="40" width="40" id="image_empty" alt=""></div>

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
                            columns: [2,3,4,5,6,7]
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: [2,3,4,5,6,7]
                        }

                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [2,3,4,5,6,7]
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [2,3,4,5,6,7]
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