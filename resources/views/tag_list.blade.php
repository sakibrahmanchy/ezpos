

@extends('layouts.master')

@section('pageTitle','Manage Tags')

@section('content')


    <div class = "row">
        <div class="col-md-12">
            <div class = "jumbotron">
                <a href = "javascript:void(0)" onclick="OpenAddTagDialog()" >[Add tag]</a><br><ul><br>
                @foreach($tagMenu as $aTag)
                    {{$aTag->tag_name}}  <a class="child" href="javascript:void(0)" id = "{{$aTag->id}}" data-value = "{{$aTag->tag_name}}" onclick="OpenEditTagDialog(this)" >[Edit]</a> <a class="child" href="javascript:void(0)" id = "{{$aTag->id}}" onclick = "deleteTag(this)">[Delete]</a><br>
                @endforeach
                </ul>
                <a href = "javascript:void(0)" onclick="OpenAddTagDialog()" >[Add tag]</a><br>
            </div>

        </div>
    </div>
    @endsection




            <!-- Add Tag Modal -->
    <div id="addTagModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create Tag</h4>
                </div>
                <div class="modal-body">
                    <label for = "category-name">Tag Name:</label>
                    <input type = "text" class="form-control" name = "tag-name" id = "tag-name">
                </div>
                <div class="modal-footer">
                    <button onclick ="addTag()" type="button" class="btn btn-info" data-dismiss="modal">Add</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Edit Tag Modal -->
    <div id="editTagModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Tag</h4>
                </div>
                <div class="modal-body">
                    <label for = "category-name">Tag Name:</label>
                    <input type = "text" class="form-control" name = "edit-tag-name" id = "edit-tag-name">
                    <input type="hidden" name="edit-tag-id" id="edit-tag-id" >
                </div>
                <div class="modal-footer">
                    <button onclick ="editCategory()" type="button" class="btn btn-info" data-dismiss="modal">Save</button>
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
                    timer: 4000
                });
                sessionStorage.setItem("status","");
            }

        };

        function OpenAddTagDialog(){

            $("#addTagModal").modal('show')
        }

        function OpenEditTagDialog(tag){

            tagId = tag.id+"";

            tagName = tag.getAttribute('data-value');

            $("#edit-tag-name").attr("value",tagName);
            $("#edit-tag-id").attr("value",tagId);
            $("#editTagModal").modal('show');
        }

        function addTag(){
            tagName = $("#tag-name").val();

            $.ajax({
                method: "POST",
                url: "{{ route('new_tag') }}",
                data:{ tagName: tagName},

            }).done(function( response ) {

                sessionStorage.setItem('status', 'success');
                sessionStorage.setItem('message', 'Tag successfully added');

                location.href = "{{route('tag_list')}}";

            });

        }

        function editCategory(){
            tagName = $("#edit-tag-name").val();
            tagId = $("#edit-tag-id").val();

            $.ajax({
                method: "POST",
                url: "{{ route('edit_tag') }}",
                data:{ tagName: tagName, tagId:tagId},

            }).done(function( response ) {


                sessionStorage.setItem('status', 'success');
                sessionStorage.setItem('message', 'Tag successfully updated');

                location.href = "{{route('tag_list')}}";

            });

        }

        function deleteTag(tag){
            tagId = tag.id;
            $.ajax({
                method: "POST",
                url: "{{ route('delete_tag') }}",
                data:{tagId:tagId},

            }).done(function( response ) {

                sessionStorage.setItem('status', 'success');
                sessionStorage.setItem('message', 'Tag successfully deleted');

                location.href = "{{route('tag_list')}}";

            });

        }

    </script>