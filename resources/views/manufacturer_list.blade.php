

@extends('layouts.master')

@section('pageTitle','Manage Manufacturers')

@section('breadcrumbs')
    {!! Breadcrumbs::render('item_manufacturers') !!}
@stop

@section('content')

    <div class="box box-primary" style="padding:50px">
        <div class = "row">
            <div class="col-md-12">
                    <a href = "javscript:void(0)" onclick="OpenAddManufacturerDialog()" >[Add manufacturer]</a><br><ul><br>
                    @foreach($manufacturerMenu as $aManufacturer)
                        {{$aManufacturer->manufacturer_name}}  <a class="child" href="javascript:void(0)" id = "{{$aManufacturer->id}}" data-value = "{{$aManufacturer->manufacturer_name}}" onclick="OpenEditManufacturerDialog(this)" >[Edit]</a> <a class="child" href="javascript:void(0)" id = "{{$aManufacturer->id}}" onclick = "deleteManufacturer(this)">[Delete]</a><br>
                    @endforeach
                    </ul>
            </div>
        </div>
    </div>
    @endsection




            <!-- Add Manufacturer Modal -->
    <div id="addManufacturerModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create Manufacturer</h4>
                </div>
                <div class="modal-body">
                    <label for = "category-name">Manufacturer Name:</label>
                    <input type = "text" class="form-control" name = "manufacturer-name" id = "manufacturer-name">
                </div>
                <div class="modal-footer">
                    <button onclick ="addManufacturer()" type="button" class="btn btn-info" data-dismiss="modal">Add</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Edit Manufacturer Modal -->
    <div id="editManufacturerModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Manufacturer</h4>
                </div>
                <div class="modal-body">
                    <label for = "category-name">Manufacturer Name:</label>
                    <input type = "text" class="form-control" name = "e dit-manufacturer-name" id = "edit-manufacturer-name">
                    <input type="hidden" name="edit-manufacturer-id" id="edit-manufacturer-id" >
                </div>
                <div class="modal-footer">
                    <button onclick ="editManufacturer()" type="button" class="btn btn-info" data-dismiss="modal">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>




    <script>
        window.onload = function() {

            if(sessionStorage.getItem("status")!=""&&sessionStorage.getItem("status")!=null){

                $.notify({
                    icon: 'pe-7s-gift',
                    message: sessionStorage.getItem('message')

                },{
                    type: 'success',
                    timer: 2000
                });
                sessionStorage.setItem("status","");
            }

        };

        function OpenAddManufacturerDialog(){

            $("#addManufacturerModal").modal('show')
        }

        function OpenEditManufacturerDialog(manufacturer){

            manufacturerId = manufacturer.id+"";

            manufacturerName = manufacturer.getAttribute('data-value');

            $("#edit-manufacturer-name").attr("value",manufacturerName);
            $("#edit-manufacturer-id").attr("value",manufacturerId);
            $("#editManufacturerModal").modal('show');
        }

        function addManufacturer(){
            manufacturerName = $("#manufacturer-name").val();

            $.ajax({
                method: "POST",
                url: "{{ route('new_manufacturer') }}",
                data:{ manufacturerName: manufacturerName},

            }).done(function( response ) {

                sessionStorage.setItem('status', 'success');
                sessionStorage.setItem('message', 'Manufacturer successfully added');

                location.href = "{{route('manufacturer_list')}}";

            });

        }

        function editManufacturer(){
            manufacturerName = $("#edit-manufacturer-name").val();
            manufacturerId = $("#edit-manufacturer-id").val();

            $.ajax({
                method: "POST",
                url: "{{ route('edit_manufacturer') }}",
                data:{ manufacturerName: manufacturerName, manufacturerId:manufacturerId},

            }).done(function( response ) {


                sessionStorage.setItem('status', 'success');
                sessionStorage.setItem('message', 'Manufacturer successfully updated');

                location.href = "{{route('manufacturer_list')}}";

            });

        }

        function deleteManufacturer(manufacturer){
            manufacturerId = manufacturer.id;
            $.ajax({
                method: "POST",
                url: "{{ route('delete_manufacturer') }}",
                data:{manufacturerId:manufacturerId},

            }).done(function( response ) {

                sessionStorage.setItem('status', 'success');
                sessionStorage.setItem('message', 'Manufacturer successfully deleted');

                location.href = "{{route('manufacturer_list')}}";

            });

        }

    </script>