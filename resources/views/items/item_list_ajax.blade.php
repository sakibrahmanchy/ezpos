
@extends('layouts.master')

@section('pageTitle','Item List')

@section('breadcrumbs')
    {!! Breadcrumbs::render('item_list') !!}
@stop

@section('content')
    <div class="filter-box">
        <div class="row">
            <div class="col-md-6">
                <div class="input-group pull-left col-md-6 col-sm-6">
                    <input type="text" id="global_filter" class="form-control pull-right global_filter" placeholder="Search">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="pull-right">
                    @if(UserHasPermission("item_add_update"))
                        <a href="{{route('new_item')}}" class="btn btn-primary" title="New Item"><span class=""><i class="fa fa-plus-circle" aria-hidden="true"></i> New Item</span></a>
                        &nbsp; <div class="dropdown pull-right ">
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <span class="fa fa-cogs" aria-hidden="true">
                                </span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <li>
                                    <a href="{{ route('item_import_excel') }}">
                                        Import Items
                                    </a>
                                </li>
                                <li>
                                    <a  href="{{ route('category_list') }}" >
                                        Manage Categories
                                    </a>
                                </li>
                                <li>
                                    <a  href="{{ route('manufacturer_list') }}" >
                                        Manage Manufacturers
                                    </a>
                                </li>
                            </ul>
                        </div>
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
    <div class="box box-primary nav-tabs-custom" style="padding:20px">
        <ul class="nav nav-tabs" role="tablist" id="tabs">
            <li role="presentation" data-val="0" class="active tabButtons" id="allTab"><a href="#all" aria-controls="home" role="tab" data-toggle="tab">All</a></li>
            <li role="presentation" data-val="1" class="tabButtons" id="activeTab"><a href="#active" aria-controls="profile" role="tab" data-toggle="tab" >Active</a></li>
            <li role="presentation" data-val="2" class="tabButtons" id="inactiveTab"><a href="#inactive" aria-controls="messages" role="tab" data-toggle="tab" >Inactive</a></li>
            <li role="presentation" data-val="3" class="tabButtons" id="draftsTab"><a href="#draft" aria-controls="settings" role="tab" data-toggle="tab" >Drafts</a></li>
            <input type="hidden" id="item_status" value="0" />

        </ul>
        <div class="tab-content" style="margin:10px">
            <div role="tabpanel" class="tab-pane active table-responsive" id="all">
                <table  class="table table-bordered table-hover table-striped" id="tableAll">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Actions</th>
                        <th>Product Id</th>
                        <th>Item Name</th>
                        <th>Item Status</th>
                        <th>Supplier</th>
                        <th>UPC/EAN/ISBN</th>
                        <th>Quantity</th>
                        <th>Size</th>
                        <th>Cost Price</th>
                        <th>Selling Price</th>
                        <th>Category</th>
                        <th>Reorder Level</th>
                        <th>Replenish Level</th>
                        <th>Expire Date</th>
                        <th>Prices Include Tax</th>
                        <th>Is Service Item</th>
                    </tr>
                    </thead>
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
    <script type="text/javascript">

        $(function() {

            $(".tabButtons").on('click',function(){
                $("#item_status").val($(this).attr('data-val'));
                table.draw();
            });

            var table = $('#tableAll').DataTable( {
                "processing": true,
                "serverSide": true,
                'createdRow': function( row, data, dataIndex ) {
                    $(row).attr('data-id', data.item_id);
                },
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
                            columns: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16]
                        },
                        orientation : 'landscape',
                        pageSize : 'LEGAL',
                        titleAttr : 'PDF'
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16]
                        }

                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]
                        },
                    },
                    {
                        extend: 'print',
                        footer: false
                    },
                    {
                        extend: 'colvis',
                        footer: false
                    }
                ],
                "ajax": {
                    url: '{{ route('all_items_data') }}',
                    type: 'GET',
                    data: function ( d ) {
                        return $.extend( {}, d, {
                            "item_status" : $('#item_status').val(),
                        });
                    }
                },
                "fnDrawCallback": function () {
                    $('#datable_length').prepend($('#tableAll_length'));
                },
                "language": {
                    "lengthMenu": 'Shows <select class="form-control">'+
                    '<option value="10">10</option>'+
                    '<option value="25">25</option>'+
                    '<option value="100">100</option>'+
                    '<option value="-1">All</option>'+
                    '</select>'
                },
                "lengthMenu": [[10, 25, 100, -1], [10, 25, 100, "All"]],
                "pageLength": 10,
                "order": [[ 2, "desc" ]],
                "columns": [
                    {
                        "data": null,
                        "defaultContent": ""
                    },
                    {
                        "data": function (data) {
                            var url = '{{ route("item_edit", ":item_id") }}';
                            url = url.replace(':item_id', data.item_id);

                            var dataToReturn = "";
                            @if(UserHasPermission('item_add_update'))
                                dataToReturn += `<a href=`+url+`>Edit</a>`;
                            @endif
                                return dataToReturn;
                        },
                        defaultContent: ""
                    },
                    {
                        "data": "product_id",
                    },
                    {
                        "data": "item_name"
                    },
                    {
                        "data": function (data, type, dataToSet) {

                            var statusMessage = "";
                            var labelType = "";
                            switch(data.item_status){
                                case "1":
                                    statusMessage = "ACTIVE";
                                    labelType = "success";
                                    break;
                                case "2":
                                    statusMessage = "INACTIVE";
                                    labelType = "danger";
                                    break;
                                case "3":
                                    statusMessage = "DRAFTED";
                                    labelType = "warning";
                                    break;
                                default:
                                    statusMessage = "DRAFTED";
                                    labelType = "warning";
                                    break;

                            }

                            return '<label class="label label-'+labelType+'">'+statusMessage+'</label>';
                        },
                    },
                    {
                        "data": "supplier",
                    },
                    {
                        "data": "upc",
                    },
                    {
                        "data": "quantity",
                    },
                    {
                        "data": "size",
                    },
                    {
                        "data": function(data) {
                            return "$"+data.cost_price
                        }
                    },
                    {
                        "data": function(data) {
                            return "$"+data.selling_price
                        }
                    },
                    {
                        "data": "category",
                    },
                    {
                        "data": "reorder_level",
                    },
                    {
                        "data": "replenish_level",
                    },
                    {
                        "data": "expire_date",
                    },
                    {
                        "data": function (data, type, dataToSet) {

                            var statusMessage = "";
                            switch(data.price_include_tax){
                                case "1":
                                    statusMessage = "Yes";
                                    break;
                                default:
                                    statusMessage = "No";
                                    break;

                            }

                            return statusMessage;
                        },
                    },
                    {
                        "data": function (data, type, dataToSet) {

                            var statusMessage = "";
                            switch(data.service_item){
                                case "1":
                                    statusMessage = "Yes";
                                    break;
                                default:
                                    statusMessage = "No";
                                    break;

                            }

                            return statusMessage;
                        },
                    },
                ]
            });

            $('#global_filter').keyup(function() {
                table.search($(this).val()).draw() ;
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

            $('#confirmDelete').click(function(){

                var table = $("#tableAll").DataTable();

                var id_list = $.map(table.rows('.selected').nodes(), function (item) {
                    return $(item).attr("data-id");
                });

                $.ajax({
                    url: "{{route('items_delete')}}",
                    type: "post",
                    data: {
                        id_list:id_list
                    },
                    success: function(response){
                        if(response.success)
                            $("#tableAll").DataTable().rows('.selected').remove().draw( false );
                        $("#deleteModal").modal('toggle');
                        table.draw();
                    }

                });
            });


            $('#tableAll thead th').each( function () {
                var title = $(this).text();
                //console.log(title);
                if(title !== "Actions" && title !== "") {
                    $(this).html( '<input class="form-control" type="text" placeholder="Search '+title+'" />' );
                }

            } );


            // Apply the search
            table.columns().every( function () {
                var that = this;

                $( 'input', this.header() ).on( 'keyup change', function () {

                    if ( that.search() !== this.value ) {
                        console.log('change detected');
                        that
                        .search( this.value )
                        .draw();
                    }
                } );
            } );

        });


    </script>
@stop

