@extends('layouts.app')

@section('breadcrumbs', Breadcrumbs::render('edit_menu_view', $menu->id))

@section('additionalCSS')
<link href="{{ asset('plugin/select2/css/select2.min.css') }}" rel="stylesheet" />

<style type="text/css">
	.category-body {
		margin-bottom: 0px;
	}

	.panel-collapse .panel-body {
		padding: 0px;
	}

	.img-thumbnail {
        max-height: 150px;
    }
</style>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Menu Information</h3>
            </div>
			<form id="form-menu" method="POST" action="{{ route('edit_menu_post', array('menu' => $menu->id)) }}">
				{{ csrf_field() }}

				<div class="box-body">
					<input id="logo_from" type="hidden" name="logo_from" value="">

					<div class="row">
						<div class="col-md-4">
							<div class="form-group{{ $errors->has('logo') ? ' has-error' : '' }}">
						        <label class="control-label" for="logo">Image</label>
					            <img id="logoPreview" src="{{ route('get_menu_logo', ['filename' => $menu->logo_filename]) }}" alt="logo" height="70px" width="70px" />

					            <button class="btn btn-default" type="button" id="get_file" data-toggle="modal" data-target="#image-modal"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span> Create</button>

					            <button class="btn btn-default" type="button" id="get_file" data-toggle="modal" data-target="#image_gallery"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span> Select</button>
					            
					            <input name="logo" type="hidden" id="logo">
						    </div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label>Name</label>
								<input class="form-control" type="text" name="name" value="{{ $menu->name or '' }}">
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label>Description</label>
								<input class="form-control" type="text" name="description" value="{{ $menu->description or '' }}">
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label>Product</label>
								<select class="form-control" id="product_select">
									@foreach ($products as $category => $product)
										<optgroup label="{{ $category }}">
											@foreach($product as $item)
												<option data-category="{{ $category }}" data-id="{{ $item->id }}" value="{{ $item->item_name }}">{{ $item->item_name }}</option>
											@endforeach
										</optgroup>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label>Combo</label>
								<select class="form-control" id="combo_select">
									<option value="">Select Combo</option>
									@foreach ($combos as $combo)
										<option data-id="{{ $combo->id }}" data-index="{{ $loop->index }}" value="{{ $combo->name }}">{{ $combo->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<button class="btn btn-primary" id="btn_submit">Update Menu</button>
						</div>

						<div class="col-md-2 col-md-offset-5">
							<button id="btn_add_product" class="btn btn-primary pull-right">Add Product</button>
						</div>
						
						<div class="col-md-2">
							<button id="btn_add_combo" class="btn btn-primary pull-right">Add Combo</button>
						</div>
					</div>

					<hr>

					<div class="row" id="menu_items_container">
						@if (sizeof($menu_combos) > 0)
							<div class="col-md-4 combo-panel" id="combo-container">
								<div class="panel panel-primary">
									<div class="panel-heading">
										Combo
									</div>

									<div class="panel-body">
										<div class="panel-group combo-panel-body" id="accordion" role="tablist" aria-multiselectable="true">
											@foreach($menu_combos as $combo)
												<div class="panel panel-info combo-item combo{{ $combo->id }}">
													<div class="panel-heading" role="tab" id="heading{{ $combo->id }}">
														<h4 class="panel-title">
															<button class="btn btn-danger btn_delete_combo">X</button>
															<a class="category-title-anchor" role="button" data-toggle="collapse" data-parent="#accordion" href="#{{ $combo->id }}" aria-expanded="true" aria-controls="{{ $combo->id }}">
																<span class="combo_panel_title">{{ $combo->name }}</span>
															</a>
														</h4>
													</div>

													<div id="{{ $combo->id }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{ $combo->id }}">
														<div class="panel-body">
															<ul class="list-group category-body">
																@foreach($combo->categories as $category)
																	<li class="list-group-item">
																		<span class="badge">{{ $category->pivot->quantity }}</span>
																		<span class="category_title">{{ $category->name }}</span>
																	</li>
																@endforeach
															</ul>
														</div>
													</div>

													<input type="hidden" name="combo_id[]" class="input_combo_id" value="{{ $combo->id }}">
												</div>
											@endforeach
										</div>
									</div>
								</div>
							</div>
						@endif

						@foreach ($menu_products as $category => $product)
							<div class="col-md-4 menu_items {{ $category }}">
								<div class="panel panel-primary">
									<div class="panel-heading">
										{{ $category }}
									</div>
									<div class="panel-body">
										<ul class="list-group">
											@foreach($product as $item)
												<li class="list-group-item">
													<button class="btn btn-danger delete_item" data-product_id="{{ $item->id }}">X</button>
													<span class="item_name">{{ $item->item_name }}</span>
													<input type="hidden" name="product_id[]" class="input_product_id" value="{{ $item->id }}">
												</li>
											@endforeach
										</ul>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<template id="templete_menu_item">
	<div class="col-md-4 menu_items">
		<div class="panel panel-primary">
			<div class="panel-heading">
				
			</div>
			<div class="panel-body">
				<ul class="list-group">
					
				</ul>
			</div>
		</div>
	</div>
</template>

<template id="templete_menu_li">
	<li class="list-group-item">
		<button class="btn btn-danger delete_item">X</button>
		<span class="item_name"></span>
		<input type="hidden" name="product_id[]" class="input_product_id">
	</li>
</template>

<template id="templete_combo_panel">
	<div class="col-md-4 combo-panel" id="combo-container">
		<div class="panel panel-primary">
			<div class="panel-heading">
				Combo
			</div>

			<div class="panel-body">
				<div class="panel-group combo-panel-body" id="accordion" role="tablist" aria-multiselectable="true">
					
				</div>
			</div>
		</div>
	</div>
</template>

<template id="templete_combo_category_ui">
	<div class="panel panel-info combo-item">
		<div class="panel-heading" role="tab" id="headingOne">
			<h4 class="panel-title">
				<button class="btn btn-danger btn_delete_combo">X</button>
				<a class="category-title-anchor" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					<span class="combo_panel_title"></span>
				</a>
			</h4>
		</div>

		<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
			<div class="panel-body">
				<ul class="list-group category-body">
					
				</ul>
			</div>
		</div>

		<input type="hidden" name="combo_id[]" class="input_combo_id">
	</div>
</template>

<template id="templete_combo_category_li">
	<li class="list-group-item">
		<span class="badge"></span>
		<span class="category_title"></span>
	</li>
</template>

<div class="modal fade" id="image_gallery" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Gallery</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($images as $image)
                        <div class="col-md-2 text-center item">
                            <a role="button" class="btn_modal_select">
                                <img class="img-thumbnail" src="{{ $image->src }}">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" role="dialog" id="image-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Image</h4>
            </div>
            <div class="modal-body">
                <center>
                    <img id="img_preview_modal" src="{{ url('/images/default.jpg') }}" width="100px">
                </center>

                <br>

                <form class="form-horizontal" id="form" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="" class="col-sm-4 control-label">Background Image</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="file" name="bg_image" id="bg_image" accept="image/*" required>

                            <span id="modal-error" class="help-block" style="color: red">
                                Error log
                            </span>

                            OR
                        </div>
                    </div> 
                    
                    <div class="form-group">

                        <label for="" class="col-sm-4 control-label">Background Color</label>
                        <div class="col-sm-8">
                            <input class="form-control color" type="text" name="bg_color" id="bg_color" value="#{{ $settings['icon_background_color'] }}">
                        </div>
                    </div> 

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Icon Width</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="icon_width" id="icon_width" value="{{ $settings['icon_width'] }}">
                        </div>

                        <label for="" class="col-sm-3 control-label">Icon Height</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="icon_height" id="icon_height" value="{{ $settings['icon_height'] }}">
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Price Text</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="price_text" id="price_text" value="{{ $settings['icon_price_text'] }}">
                        </div>

                        <label for="" class="col-sm-3 control-label">Price Font Size</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="price_font_size" id="price_font_size" value="{{ $settings['icon_price_font_size'] }}">
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Price Color</label>
                        <div class="col-sm-3">
                            <input class="form-control color" type="text" name="price_color" id="price_color" required value="#{{ $settings['icon_price_color'] }}">
                        </div>

                        <label for="" class="col-sm-3 control-label">Price Background Color</label>
                        <div class="col-sm-3">
                            <input class="form-control color" type="text" name="price_bg_color" id="price_bg_color" required value="#{{ $settings['icon_price_background_color'] }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Price Location X</label>
                        <div class="col-sm-3">
                            <select class="form-control" name="price_location_x" id="price_location_x">
                                <option value="left" {{ $settings['icon_price_location_x'] == "left" ? 'selected' : '' }}>Left</option>
                                <option value="right" {{ $settings['icon_price_location_x'] == "right" ? 'selected' : '' }}>Right</option>
                                <option value="center" {{ $settings['icon_price_location_x'] == "center" ? 'selected' : '' }}>Center</option>
                            </select>
                        </div>

                        <label for="" class="col-sm-3 control-label">Price Location Y</label>
                        <div class="col-sm-3">
                            <select class="form-control" name="price_location_y" id="price_location_y">
                                <option value="top" {{ $settings['icon_price_location_y'] == "top" ? 'selected' : '' }}>Top</option>
                                <option value="bottom" {{ $settings['icon_price_location_y'] == "bottom" ? 'selected' : '' }}>Bottom</option>
                                <option value="middle" {{ $settings['icon_price_location_y'] == "middle" ? 'selected' : '' }}>Middle</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Label Text</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="label_text" id="label_text" value="{{ $settings['icon_label_text'] }}">
                        </div>

                        <label for="" class="col-sm-3 control-label">Label Font Size</label>
                        <div class="col-sm-3">
                            <input class="form-control" type="text" name="label_font_size" id="label_font_size" value="{{ $settings['icon_label_font_size'] }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Label Color</label>
                        <div class="col-sm-3">
                            <input class="form-control color" type="text" name="label_color" id="label_color" required value="#{{ $settings['icon_label_color'] }}">
                        </div>

                        <label for="" class="col-sm-3 control-label">Label Background Color</label>
                        <div class="col-sm-3">
                            <input class="form-control color" type="text" name="label_bg_color" id="label_bg_color" required value="#{{ $settings['icon_label_background_color'] }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Label Location X</label>
                        <div class="col-sm-3">
                            <select class="form-control" name="label_location_x" id="label_location_x">
                                <option value="left" {{ $settings['icon_label_location_x'] == "left" ? 'selected' : '' }}>Left</option>
                                <option value="right" {{ $settings['icon_label_location_x'] == "right" ? 'selected' : '' }}>Right</option>
                                <option value="center" {{ $settings['icon_label_location_x'] == "center" ? 'selected' : '' }}>Center</option>
                            </select>
                        </div>

                        <label for="" class="col-sm-3 control-label">Label Location Y</label>
                        <div class="col-sm-3">
                            <select class="form-control" name="label_location_y" id="label_location_y">
                                <option value="top" {{ $settings['icon_label_location_y'] == "top" ? 'selected' : '' }}>Top</option>
                                <option value="bottom" {{ $settings['icon_label_location_y'] == "bottom" ? 'selected' : '' }}>Bottom</option>
                                <option value="middle" {{ $settings['icon_label_location_y'] == "middle" ? 'selected' : '' }}>Middle</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="btn_preview" type="button" class="btn btn-primary">Preview</button>
                <button id="btn_select" type="button" class="btn btn-primary">Select</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@stop

@section('additionalJS')
<script src="{{ asset('plugin/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jqColorPicker.min.js') }}"></script>

<script type="text/javascript">
	$(function() {
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $('.btn_modal_select').click(function() {
            var src = $(this).find('img').attr('src');
            $('#logoPreview').attr("src", src);

            convertFileToDataURLviaFileReader(src);
            $('#image_gallery').modal('hide');
            $('#logo_from').val(1);
        });

		$('.color').colorPicker({
		    cssAddon: '.cp-color-picker {z-index: 1000000;}',
		});

		var selected_items = <?php echo '[' . implode(', ', $menu_products_id) . ']' ?>;
		var form = $('#form-menu');

		$('#product_select').select2({
		  placeholder: 'Select a product'
		});

		$('#btn_add_product').click(function(event) {
			event.preventDefault();

			var product_name = $('#product_select').val();
			var category = $('#product_select').find(':selected').attr('data-category');
			var product_id = $('#product_select').find(':selected').attr('data-id');

			if (selected_items.indexOf(parseInt(product_id)) != -1) {
				alert('Already added');
				return ;
			}

			var menu_li_html = $('#templete_menu_li').html();
			var row_menu_li = $(menu_li_html);
			row_menu_li.find('.item_name').html(product_name);
			row_menu_li.find('.input_product_id').val(product_id);
			row_menu_li.find('.delete_item').attr('data-product_id', product_id);

			if ($('.'+category).length == 0){
				var html = $('#templete_menu_item').html();
				var rowElement = $(html);

				rowElement.addClass(category);
				rowElement.find('.panel-heading').html(category);
				rowElement.find('.list-group').append(row_menu_li);

				$('#menu_items_container').append(rowElement);
			} else {
				$('.'+category).find('.list-group').append(row_menu_li);
			}

			selected_items.push(parseInt(product_id));
		});

		$('#btn_add_combo').click(function(event) {
			event.preventDefault();

			var combo_name = $('#combo_select').val();
			var combo_id = $('#combo_select').find(':selected').attr('data-id');
			var combo_index = $('#combo_select').find(':selected').attr('data-index');
			var combos = "{{ $combos }}";
			combos = JSON.parse(combos.replace(/&quot;/g,'"'));

			if (!combo_name){
				alert("Select a combo");
				return;
			}

			if ($('.combo'+combo_id).length != 0){
				alert("Already added");
				return;
			}

			var html_combo_category_ui = $('#templete_combo_category_ui').html();
			var row_combo_category_ui = $(html_combo_category_ui);
			row_combo_category_ui.addClass('combo'+combo_id);
			row_combo_category_ui.find('.combo_panel_title').html(combo_name);
			row_combo_category_ui.find('.input_combo_id').val(combo_id);
			row_combo_category_ui.find('.panel-heading').attr("id", "heading"+combo_id);
			row_combo_category_ui.find('.category-title-anchor').attr("href", "#"+combo_id);
			row_combo_category_ui.find('.category-title-anchor').attr("aria-controls", combo_id);
			row_combo_category_ui.find('.collapse').attr("id", combo_id);
			row_combo_category_ui.find('.collapse').attr("aria-labelledby", "heading"+combo_id);

			$.each(combos[combo_index].categories, function( index, value ) {
				var html_category_li = $('#templete_combo_category_li').html();
				var row_category_li = $(html_category_li);
				row_category_li.find('.badge').html(value.pivot.quantity);
				row_category_li.find('.category_title').html(value.category_name);

				row_combo_category_ui.find('.category-body').append(row_category_li);
			});

			if ($('.combo-panel').length == 0){
				var html_combo_panel = $('#templete_combo_panel').html();
				var combo_panel = $(html_combo_panel);

				combo_panel.find('.combo-panel-body').html(row_combo_category_ui);
				$('#menu_items_container').append(combo_panel);
			} else {
				$('.combo-panel-body').append(row_combo_category_ui);
			}

		});

		$(document.body).on('click', '.btn_delete_combo' ,function(event){
			if ($('.combo-item').length == 1) {
				$(this).closest('#combo-container').remove();
			} else {
				$(this).closest('.combo-item').remove();
			}
		});

		$(document.body).on('click', '.delete_item' ,function(event){
			event.preventDefault();

			var item_count = $(this).closest("ul").children().length;
			var product_id = $(this).data("product_id");
			
			if (item_count == 1){
				$(this).closest(".menu_items").remove();
			} else {
				$(this).closest("li").remove();
			}

			selected_items.splice(selected_items.indexOf(product_id), 1); 
		});

		$('#btn_submit').click(function(event) {
			event.preventDefault();
			formValidation();
			
			if (!form.valid()) {
				return; 
			} else {
				if ($('.menu_items').length == 0 && $('.combo-item').length == 0){
					alert('Select a product or combo.');
				} else {
					form.submit();
				}
			}
		});

		function formValidation(){
			form.validate({
			    rules: {
			      name: "required",
			      description: {
			      	required: true,
			      	maxlength: 250
			      }
			    },
			    // Specify validation error messages
			    messages: {
			      name: "Please enter name",
			      description: {
			        required: "Please enter description",
			        maxlength: "Please enter no more than 250 characters"
			      }
			    },
		  	});
		}

		function convertFileToDataURLviaFileReader(url) {
            var xhr = new XMLHttpRequest();
            var result = "";
            xhr.onload = function() {
                var reader = new FileReader();
                reader.onloadend = function() {
                    $('#logo').val(reader.result);
                }
                reader.readAsDataURL(xhr.response);
            };
            xhr.open('GET', url);
            xhr.responseType = 'blob';
            xhr.send();
        }

		//Image
		function create_image(){
            var formData = new FormData();
            if ($('#bg_image').val() != ''){
	            formData.append('bg_image', $('#bg_image')[0].files[0]);
	        }

            var other_data = $('#form').serializeArray();
            $.each(other_data,function(key,input){
                formData.append(input.name,input.value);
            });

            $.ajax({
               url : "{{ route('create_product_image') }}",
               type : 'POST',
               data : formData,
               processData: false,  // tell jQuery not to process the data
               contentType: false,  // tell jQuery not to set contentType
               dataType: 'json',
               success : function(data) {
                    if (data.success) {
                        $('#img_preview_modal').attr('src', '');
                        $('#img_preview_modal').attr('src', 'data:image/png;base64,' + data.data);
                        $('#btn_select').removeClass('hide');
                        $('#btn_preview').addClass('hide');
                    } else {
                        $('#modal-error').html(data.data);
                    }
               }
            });
        }

        $('#btn_preview').click(function() {
            create_image();
        });

        $('#bg_image').change(function() {
            readURL(this);
        });

        $('#btn_select').click(function() {
            //$('#img_preview').attr('src', $('#img_preview_modal').attr('src'));
            var base64_image = $('#img_preview_modal').attr('src');
            $('#logoPreview').attr('src', base64_image);
            $('#image-modal').modal('hide');
            $('#logo').val(base64_image);
            $('#logo_from').val(0);
        });

        $('#image-modal').on('show.bs.modal', function (event) {
        	$('#modal-error').html('');
            $('#bg_image').val('');
            $('#btn_select').addClass('hide');
            $('#btn_preview').removeClass('hide');
            $('#img_preview_modal').attr('src', '{{ url('/images/default.jpg') }}');
        });

        $('#bg_image, #icon_width, #icon_height, #price_text, #price_font_size, #price_color, #price_bg_color, #price_location_x, #price_location_y, #label_text, #label_font_size, #label_color, #label_bg_color, #label_location_x, #label_location_y').change(function() {
            $('#btn_select').addClass('hide');
            $('#btn_preview').removeClass('hide');
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#img_preview_modal').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
	});
</script>
@stop