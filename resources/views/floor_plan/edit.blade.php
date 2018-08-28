@extends('layouts.master')

@section('pageTitle','Edit Floor Plan')

@section('breadcrumbs')
    {!! Breadcrumbs::render('edit_floor_plan') !!}
@stop   

@section('additionalCSS')
<style type="text/css">
	.scrollbar-measure {
		width: 1px;
		height: 1px;
		overflow: scroll;
		position: absolute;
		top: -9999px;
	}

    .modal-body{
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }
</style>
@stop

@section('content')
<form action="{{ route('post_edit_floor_plan') }}" method="post">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-md-12">
            <div class="filter-box">
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-primary" type="submit">Update</button>
                        <a class="btn btn-default" href="{{ route('floor_plan') }}">Cancel</a>
                        <input type="hidden" name="size_x" value="{{ $column }}">
                        <input type="hidden" name="size_y" value="{{ $row }}">
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
    					@for($i=1; $i<=$row; $i++)
    						<tr>
    							@for($j=1; $j<=$column; $j++)
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
                                            <input type="hidden" name="id[]" value="{{ $sitting->id or '' }}">
    										<input class="name" type="hidden" name="name[]" value="{{ $sitting->name or '' }}">
    										<input class="seat" type="hidden" name="seat[]" value="{{ $sitting->sit_count or '' }}">
    										<input class="logo" type="hidden" name="logo[]" >
    										<input type="hidden" name="position_x[]" value="{{ $j }}">
    										<input type="hidden" name="position_y[]" value="{{ $i }}">
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
</form>

<div class="modal fade" tabindex="-1" role="dialog" id="select_image_from">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header"> 
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> 
				<h4 class="modal-title" id="mySmallModalLabel">Select Image</h4> 
			</div> 

			<div class="modal-body"> 
				<button id="btn_new_icon" class="btn btn-default">Create New Icon</button>
				<button id="btn_show_gallery" class="btn btn-default">Select From Gallery</button>
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
						<label for="recipient-name" class="control-label">Name:</label>
						<input type="text" class="form-control" id="input_name">
					</div>

					<div class="form-group">
						<label for="recipient-name" class="control-label">Number of seats:</label>
						<input type="text" class="form-control" id="input_seat">
					</div>
				</form>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button id="modal_btn_ok" type="button" class="btn btn-primary">Ok</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
                                <img class="img-thumbnail" src="{{ $image->src }}" height="100px" width="100px">
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
                    <img id="img_preview_modal" src="{{ url('/img/floorplan/default.jpg') }}" width="100px">
                </center>

                <br>

                <form class="form-horizontal" id="form" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
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
<script type="text/javascript" src="{{ url('js/jqColorPicker.min.js') }}"></script>
<script type="text/javascript">
	$(function() {
		var select_index = 0;

		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.color').colorPicker({
            cssAddon: '.cp-color-picker {z-index: 1000000;}',
        });

		$('.item_info').click(function() {
			var index = $(".item_info").index(this);
			var name = $('.name:eq('+index+')').val();
			var seat = $('.seat:eq('+index+')').val();

			$('#modal_btn_ok').attr('data-index', index);

			$('#input_name').val(name);
			$('#input_seat').val(seat);
			$('#add_new_modal').modal('show');
		});

		$('#modal_btn_ok').click(function() {
			var index = $(this).attr('data-index');

			var name = $('#input_name').val();
			var seat = $('#input_seat').val();

			if (!$.isNumeric(seat)) {
				alert('Please enter valid seat number.');
				return;
			}

			$('.name:eq('+index+')').val(name);
			$('.seat:eq('+index+')').val(seat);

			$('.span_name:eq('+index+')').html(name);
			$('.span_seat:eq('+index+')').html(seat);

			$('#add_new_modal').modal('hide');
		});

		$('.logoPreview').click(function() {
			select_index = $(".logoPreview").index(this);
			$('#select_image_from').modal('show');
		});

		$('#btn_new_icon').click(function() {
			$('#select_image_from').modal('hide');
			$('#image-modal').modal('show');
		});

		$('#btn_show_gallery').click(function() {
			$('#select_image_from').modal('hide');
			$('#image_gallery').modal('show');
		});

		$('.modal').bind('hidden.bs.modal', function () {
			$(document.body).css('padding-right', 0);
		});

		$('.modal').bind('show.bs.modal', function () {
			if (this.clientHeight <= window.innerHeight) 
				return;
			var scrollbarWidth = measureScrollBar();
      		if (scrollbarWidth) 
      			$(document.body).css('padding-right', scrollbarWidth);
		});

		$('.btn_modal_select').click(function() {
            var src = $(this).find('img').attr('src');
            $('.logoPreview:eq('+select_index+')').attr('src', src);

            convertFileToDataURLviaFileReader(src);
            $('#image_gallery').modal('hide');
            //$('#logo_from').val(1);
        });


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
            
            $('#image-modal').modal('hide');
            
            $('.logoPreview:eq('+select_index+')').attr('src', base64_image);
            $('.logo:eq('+(select_index+1)+')').val(base64_image);
            //$('#logo_from').val(0);
        });

        $('#image-modal').on('show.bs.modal', function (event) {
            $('#modal-error').html('');
            $('#bg_image').val('');
            $('#btn_select').addClass('hide');
            $('#btn_preview').removeClass('hide');
            $('#img_preview_modal').attr('src', '{{ url('/img/floorplan/default.jpg') }}');
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

        function convertFileToDataURLviaFileReader(url) {
            var xhr = new XMLHttpRequest();
            var result = "";
            xhr.onload = function() {
                var reader = new FileReader();
                reader.onloadend = function() {
            		$('.logo:eq('+(select_index+1)+')').val(reader.result);
                }
                reader.readAsDataURL(xhr.response);
            };
            xhr.open('GET', url);
            xhr.responseType = 'blob';
            xhr.send();
        }

        function measureScrollBar() {
			var scrollDiv = document.createElement('div')
			scrollDiv.className = 'scrollbar-measure'
			document.body.appendChild(scrollDiv)
			var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
			document.body.removeChild(scrollDiv)
			return scrollbarWidth
		}
	});
</script>
@stop