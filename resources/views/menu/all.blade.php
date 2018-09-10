@extends('layouts.app')

@section('breadcrumbs', Breadcrumbs::render('all_menu_view'))

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
        	<div class="box-body">
				<div class="row">
					<div class="col-md-6">
						<a class="btn btn-primary" href="{{ route('add_menu_view') }}"><i class="fa fa-plus" aria-hidden="true"></i> Add Menu</a>

						<a class="btn btn-info hide" id="btn_batch_delete">Batch Delete</a>
					</div>

					<div class="col-md-6">
						<div class="form-inline pull-right">
							<div class="form-group" style="margin-right: 20px">
								<label for="show_count"> Show</label>
								<select class="form-control" name="show_count" id="show_count">
									<option value="10" {{ (isset($appends['show']) && $appends['show'] == "10") ? 'selected' : '' }}>10</option>
									<option value="20" {{ (isset($appends['show']) && $appends['show'] == "20") ? 'selected' : '' }}>20</option>
									<option value="30" {{ (isset($appends['show']) && $appends['show'] == "30") ? 'selected' : '' }}>30</option>
									<option value="40" {{ (isset($appends['show']) && $appends['show'] == "40") ? 'selected' : '' }}>40</option>
									<option value="50" {{ (isset($appends['show']) && $appends['show'] == "50") ? 'selected' : '' }}>50</option>
								</select>
							</div>
						</div>
					</div>
				</div>

				<hr>

			    <div class="row">
			        <div class="col-md-12">
			        	<table class="table table-hover">
			        		<thead>
			        			<tr>
			        				<th>
			        					<input type="checkbox" id="checkbox_select_all">
			        				</th>
			        				<th>Logo</th>
									<th>Name</th>
									<th>Description</th>
									<th>Created At</th>
									<th></th>
								</tr>
			        		</thead>
							
							<tbody>
								@foreach ($menus as $menu)
									<tr>
										<td>
											<input type="checkbox" class="item_checkbox" data-id="{{ $menu->id }}">
										</td>
										<td><img class="" src="{{ route('get_menu_logo', ['filename' => $menu->logo_filename]) }}" height="50px" width="50px"></td>
										<td>{{ $menu->name }}</td>
										<td>{{ $menu->description }}</td>
										<td>{{ $menu->created_at }}</td>
										<td class="text-right">
											<div class="dropdown">
												<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
													<span class="glyphicon glyphicon-option-vertical" aria-hidden="true"></span>
												</button>

												<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
													<li><a href="{{ route('edit_menu_view', array('menu' => $menu->id)) }}">Edit</a></li>
													<li><a class="delete" data-id="{{ $menu->id }}" href="#" data-toggle="modal" data-target="#deleteModal">Delete</a></li>
												</ul>
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>

						<div class="pagination"> {{ $menus->appends($appends)->links() }} </div>
			        </div>
		        </div>
	        </div>
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
				<p>Are you sure want to delete?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancel</button>
				<button id="btnDelete" type="button" class="btn btn-outline">Delete</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('additionalJS')
<script type="text/javascript">
	$(function(){
		$.ajaxSetup({
	        headers: {
	            'X-CSRF-Token': '{!! csrf_token() !!}'
	        }
	    });

		$('.delete').click(function(){
			var id = $(this).data('id');
			var index = $(this).closest('tr').index();

			$("#btnDelete").attr("data-id", id);
			$("#btnDelete").attr("data-index", index);
		});

		$('#btnDelete').click(function(){
			//alert($(this).attr('data-id'));\
			var id = $(this).attr('data-id');
			var index = parseInt($(this).attr('data-index'))+1;

			$.ajax({
			  method: "POST",
			  url: "{{route('delete_menu_post')}}",
			  data: { ids: [id] }
			})
			  .done(function( msg ) {
			    $('#deleteModal').modal('toggle');
			    $( 'tr:eq( '+index+' )' ).remove();
			  });
		});

		$('#show_count').change(function(){
        	var filter_url = '{{ Request::url() }}'+'?';

			filter_url += 'show=' + $('#show_count').val();

        	window.location.replace(filter_url);
        });

        $('#checkbox_select_all').change(function() {
        	if ($(this).is(":checked")) {
				$('.item_checkbox').prop('checked', true);
			} else {
				$('.item_checkbox').prop('checked', false);
			}

			var total_check_item = $('.item_checkbox:checkbox:checked').length;
        	
        	if (total_check_item > 1)
        		$('#btn_batch_delete').removeClass('hide');
        	else
        		$('#btn_batch_delete').addClass('hide');
        });

        $('.item_checkbox').change(function() {
        	var total_check_item = $('.item_checkbox:checkbox:checked').length;
        	
        	if (total_check_item > 1)
        		$('#btn_batch_delete').removeClass('hide');
        	else
        		$('#btn_batch_delete').addClass('hide');
        });

        $('#btn_batch_delete').click(function() {
        	selected_index = [];
        	selected_ids = [];
        	var counter = 0;

        	$('.item_checkbox').each(function(i) {
			   if (this.checked) {
			       selected_index[counter] = i;
			       selected_ids[counter++] = $(this).data('id');
			   }
			});

			selected_index.sort();
    		selected_index.reverse();


			$.ajax({
				method: "POST",
				url: "{{ route('delete_menu_post') }}",
				data: { ids: selected_ids }
			}).done(function( msg ) {
				$.each(selected_index, function(index, value) {
	  				var i = value + 1;
					$( 'tr:eq( '+i+' )' ).remove();
				});
	  		});
        });
	})
</script>
@stop