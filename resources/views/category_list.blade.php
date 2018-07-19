

@extends('layouts.master')

@section('pageTitle','Manage Categories')

@section('breadcrumbs')
    {!! Breadcrumbs::render('item_categories') !!}
@stop

@section('content')
    <div class="box box-primary" style="padding:50px">
        <div class = "row">
            <div class="col-md-12">
                    <a href = "javascript:void(0)" onclick="OpenAddCategoryDialog(this)" id = "0">[Add root category]</a>
                    {!! $categoryMenu !!}
            </div>
        </div>
    </div>
@endsection

<!-- Add Category Modal -->
<div id="addCategoryModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create Category</h4>
            </div>
            <div class="modal-body">
                <label for = "category-name">Category Name:</label>
                <input type = "text" class="form-control" name = "category-name" id = "category-name">
                <input type="hidden" name="category-id" id="category-id" >
            </div>
            <div class="modal-footer">
                <button onclick ="addCategory()" type="button" class="btn btn-info" data-dismiss="modal">Add</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Category</h4>
                </div>
                <div class="modal-body">
                    <label for = "category-name">Catgory Name:</label>
                    <input type = "text" class="form-control" name = "edit-category-name" id = "edit-category-name">
                    <input type="hidden" name="edit-category-id" id="edit-category-id" >
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

            if(sessionStorage.getItem("status")!=""){

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

  function OpenAddCategoryDialog(parenCategory){

      parentId = parenCategory.id+"";

       $("#category-id").attr("value",parentId);
      $("#addCategoryModal").modal('show')

  }

    function OpenEditCategoryDialog(parenCategory){

        parentId = parenCategory.id+"";

        categoryName = parenCategory.getAttribute('data-value');


        $("#edit-category-name").attr("value",categoryName);
        $("#edit-category-id").attr("value",parentId);
        $("#editCategoryModal").modal('show');

    }

  function addCategory(){
      categoryName = $("#category-name").val();
      parent = $("#category-id").val();
      $.ajax({
          method: "POST",
          url: "{{ route('new_category') }}",
          data:{ categoryName: categoryName, parent:parent},

      }).done(function( response ) {

          sessionStorage.setItem('status', 'success');
          sessionStorage.setItem('message', 'Category successfully added');

          location.href = "{{route('category_list')}}";

      });

  }

  function editCategory(){
      categoryName = $("#edit-category-name").val();
      categoryId = $("#edit-category-id").val();

      $.ajax({
          method: "POST",
          url: "{{ route('edit_category') }}",
          data:{ categoryName: categoryName, categoryId:categoryId},

      }).done(function( response ) {


         sessionStorage.setItem('status', 'success');
          sessionStorage.setItem('message', 'Category successfully updated');

          location.href = "{{route('category_list')}}";

      });

  }

   function deleteCategory(category){
        categoryId = category.id;
        $.ajax({
            method: "POST",
            url: "{{ route('delete_category') }}",
            data:{categoryId:categoryId},

        }).done(function( response ) {


            sessionStorage.setItem('status', 'success');
            sessionStorage.setItem('message', 'Category successfully deleted');

            location.href = "{{route('category_list')}}";

        });

    }

</script>