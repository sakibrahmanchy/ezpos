@extends('layouts.master')

@section('pageTitle','Counter List')

@section('breadcrumbs')
    {!! Breadcrumbs::render('counter_list') !!}
@stop
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {display:none;}

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

</style>
@section('content')
    <div class="filter-box">
        <div class="row">
            <div class="col-md-6">
                <div class="input-group pull-left col-md-6">
                    <input type="text" id="global_filter" class="form-control pull-right global_filter" placeholder="Search">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="pull-right">
                    @if(UserHasPermission("counters_add_update"))
                        <a href="{{route('new_counter')}}" class="btn btn-primary hidden-sm hidden-xs" title="New Counter"><i class="fa fa-plus-circle"></i> <span class="">New Counter</span></a>
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
                        <th>Counter Name</th>
                        <th>Starting Id</th>
                        <th>Description</th>
						<th>Printer Connection Type</th>
                        <th>Printer IP</th>
                        <th>Printer Port</th>
                        <th>Default</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($counters as $counter)
                        <tr data-id="{{ $counter->id }}">
                            <td></td>
                            <td><div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i class="pe-7s-pen"></i>
                                        <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        @if(UserHasPermission("counters_add_update"))
                                            <li><a href="{{route('counter_edit',['counter_id'=>$counter->id])}}">Edit Counter</a></li>
                                            <li><a href="{{route('test_print',['counter_id'=>$counter->id])}}">Test Print</a></li>
                                        @endif
                                        @if(UserHasPermission("counters_delete") && !$counter->isDefault)
                                            <li><a href="{{route('counter_delete',['counter_id'=>$counter->id])}}">Delete</a></li>
                                        @endif
                                    </ul>
                                </div></td>
                            <td>{{$counter->name}}</td>
                            <td>{{$counter->counter_code}}</td>
                            <td>{{$counter->description}}</td>
							<td>{{ $counter->printer_connection_type==\App\Enumaration\PrinterConnectionType::USB_CONNECTION ? 'USB Connection' : 'Connected Via Network'}}</td>
                            <td>{{$counter->printer_ip}}</td>
                            <td>{{$counter->printer_port}}</td>
                            <td><label class="switch">
                                    <input type="checkbox" onchange="changeDefault({{ $counter->id }})" id="default-{{ $counter->id }}" {{ $counter->isDefault?'checked':'' }}>
                                    <span class="slider round"></span>
                                </label></td>
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


        function changeDefault(id){

            var status = $("#default-"+id).is(':checked');
            $.ajax({
                url: "{{route('counter_set_default')}}",
                type: "post",
                data: {
                    id: id,
                    status: status
                },
                success: function(response){
                   if(response.success){
                        location.reload();
                   }
                }

            });

        }

        $(document).ready(function(){

            $("[name='my-checkbox']").bootstrapSwitch();
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
                    url: "{{route('counters_delete')}}",
                    type: "post",
                    data: {
                        id_list:id_list
                    },
                    success: function(response){
                        if(response.success){
                            if(response.success){
                                var deletedRows = response.deletedRows;
                                deletedRows.forEach(function(aRow){
                                    table.rows('.selected').remove().draw( false );
                                });
                            }

                        }
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