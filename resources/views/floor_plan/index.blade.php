@extends('layouts.master')

@section('pageTitle','Floor Plan')

@section('breadcrumbs')
	{!! Breadcrumbs::render('floor_plan') !!}
@stop


@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="filter-box">
			<div class="row">
				<div class="col-md-6">
					<a class="btn btn-primary" id="btn_new">New Floor Plan</a>
					<a class="btn btn-primary" href="{{ route('edit_floor_plan') }}">Edit Floor Plan</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-md-12">
		<div class="box box-primary">
			<div class="box-body">
				<table class="table table-bordered">
					@for($i=1; $i<=$size_y; $i++)
						<tr>
							@for($j=1; $j<=$size_x; $j++)
								<?php
									$sitting = null;
									foreach($sittings as $item) {
										if ($item->position_x == $j && $item->position_y == $i){
											$sitting = $item;
											break;
										}
									}
								?>
								<td class="item">
									@if ($sitting)			
										<center>
											<img class="logoPreview pull-center" src="{{ route('get_sitting_logo', ['sitting' => $sitting->id]) }}" height="100px" width="100px">
										</center>
									@else
										<center>
											<img class="logoPreview pull-center" src="{{ url('img/floorplan/default.jpg') }}" height="100px" width="100px">
										</center>
									@endif						
		 
		 							<div class="item_info">
		 								<b>Name: </b><span class="span_name">{{ $sitting->name or '' }}</span><br>
										<b>No. of Seat: </b><span class="span_seat">{{ $sitting->sit_count or '' }}</span>
		 							</div>
								</td>
							@endfor
						</tr>
					@endfor
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="add_new_modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Floor Plan</h4>
			</div>

			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="recipient-name" class="control-label">Grid Size X:</label>
						<input type="text" class="form-control" id="input_x">
					</div>

					<div class="form-group">
						<label for="recipient-name" class="control-label">Grid Size Y:</label>
						<input type="text" class="form-control" id="input_y">
					</div>
				</form>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button id="modal_btn_create" type="button" class="btn btn-primary">Create</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@stop

@section('additionalJS')
<script type="text/javascript">
	$(function() {
		$('#btn_new').click(function() {
			$('#add_new_modal').modal('show');
		});

		$('#modal_btn_create').click(function() {
			var input_x = $('#input_x').val();
			var input_y = $('#input_y').val();

			if (input_x == "") {
				alert('Please enter Grid Size X.');
				return;
			}

			if (input_y == "") {
				alert('Please enter Grid Size Y.');
				return;
			}

			if (!$.isNumeric(input_x)) {
				alert('Please enter valid Grid Size X.');
				return;
			}

			if (!$.isNumeric(input_y)) {
				alert('Please enter valid Grid Size Y.');
				return;
			}

			window.location = "{{ route('new_floor_plan') }}?x=" + input_x + "&y=" + input_y;
		});
	});
</script>
@stop