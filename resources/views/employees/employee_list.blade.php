
@extends('layouts.master')

@section('pageTitle','Employee List')

@section('breadcrumbs')
    {!! Breadcrumbs::render('employee_list') !!}
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
                    @if(UserHasPermission("employees_add_update"))
                        <a href="{{route('new_employee')}}" class="btn btn-primary hidden-sm hidden-xs" title="New Employee"><i class="fa fa-plus-circle"></i> <span class="">New Employee</span></a>
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
            <div class="col-md-12  table-responsive">
            <table class="table" id="table">
                <thead>
                <tr>
                    <th></th>
                    <th>Actions</th>
                    <th>Person Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($employees as $employee)
                    <tr data-id="{{ $employee->id }}">
                        <td></td>
                        <td><div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i class="pe-7s-pen"></i>
                                    <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                @if(UserHasPermission("employees_add_update"))
                                    <li><a href="{{route('employee_edit',['employee_id'=>$employee->employee_id])}}">Edit Employee</a></li>
                                        <li><a href="{{route('clone_employee',['employee_id'=>$employee->employee_id])}}">Clone Employee</a></li>
                                @endif
                                </ul>
                            </div></td>
                        <td>{{$employee->id}}</td>
                        <td>{{$employee->first_name}} {{$employee->last_name}}</td>
                        <td>{{$employee->email}}</td>
                        <td>{{$employee->phone}}</td>
                        <td> @if($employee->image_token!=null)
                                <div id="avatar"><img src="{{asset('img/employees/userpictures/'.$employee->image_token)}}" height="40" width="40" class="img-polaroid" id="image_empty" alt=""></div>
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
                    url: "{{route('employees_delete')}}",
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

            $('#table thead th').each( function () {
                var title = $(this).text();
                //console.log(title);
                var differentCases = {
                    '': '',
                    'Actions': '',
                    {{--'Item Status': '<select class="form-control"><option value="{{ \App\Enumaration\ItemStatus::$ACTIVE }}" selected>Active</option><option value="{{ \App\Enumaration\ItemStatus::$INACTIVE }}">Inactive</option></select>',--}}

                };

                if(differentCases[title] === undefined) {
                    $(this).html( '<input class="form-control" type="text" placeholder="Search '+title+'" />' );
                } else {
                    $(this).html( differentCases[title] );
                }

            } );


            // Apply the search
            table.columns().every( function () {
                var that = this;
                console.log(this);
                $( 'input', this.header() ).on( 'keyup change', function () {

                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );

        });

    </script>
    @stop