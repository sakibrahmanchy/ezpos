
@extends('layouts.master')

@section('pageTitle','Edit Item')

@section('breadcrumbs')
    {!! Breadcrumbs::render('item_edit',$item->item_id) !!}
@stop

@section('content')
    <div class="box box-primary" style="padding:20px">
        @include('includes.message-block')
        <div class="row" id="form">
            <div class="spinner" id="grid-loader" style="display:none">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
            </div>
            <div class="col-md-12">


                <form action="{{route('item_edit',['item_id'=>$item->item_id])}}" id="item_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    {{-- {{ csrf_field() }}--}}

                    <div class="panel panel-piluku">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="pe-7s-edit"></i>
                                Item Information    					<small>(Fields in red are required)</small>
                            </h3>
                        </div>

                        <div class="panel-body">

                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label for="isbn" class=" col-sm-3 col-md-3 col-lg-2 control-label required">UPC/EAN/ISBN:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="isbn" value="{{ $item->isbn }}" class="form-control" id="isbn" >
                                            <span class="text-danger">{{ $errors->first('isbn') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="product_id" class=" col-sm-3 col-md-3 col-lg-2 control-label ">Product Id:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="product_id" value="{{ $item->product_id }}" class="form-control" id="product_id">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="item_name" class="col-sm-3 col-md-3 col-lg-2 control-label required">Item Name:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input  type="text" name="item_name" value="{{ $item->item_name }}" class="form-control" id="item_name" >
                                            <span class="text-danger">{{ $errors->first('item_name') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="category" class="col-sm-3 col-md-3 col-lg-2 control-label ">Item Status:</label>			<div class="col-sm-9 col-md-9 col-lg-10">

                                            <select  name = "item_status" class="form-control" value = "{{ $item->item_status }}">
                                                <option value = "1"  @if($item->item_status==\App\Enumaration\ItemStatus::$ACTIVE) selected @endif>
                                                    Active
                                                </option>
                                                <option value = "2" @if($item->item_status==\App\Enumaration\ItemStatus::$INACTIVE) selected @endif>
                                                    Inactive
                                                </option>
                                                <option disabled value = "3" @if($item->item_status==\App\Enumaration\ItemStatus::$DRAFTED) selected @endif>
                                                    Drafted
                                                </option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('item_status') }}</span>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="category" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Category:</label>			<div class="col-sm-9 col-md-9 col-lg-10">

                                            <select  name = "item_category" class="form-control" value = "{{ $item->category_id }}">
                                                <option></option><option value="-1" @if($item->category_id==-1) selected @endif>none</option>
                                                @foreach ($categoryList as $aList)
                                                    @if($aList->id==$item->category_id)
                                                    <option value = "{{$aList->id}}" selected>
                                                        {{$aList->category_name}}
                                                    </option>
                                                    @else
                                                        <option value = "{{$aList->id}}">
                                                            {{$aList->category_name}}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{ $errors->first('item_category') }}</span>
                                            @if(UserHasPermission("item_manage_categories"))
                                                <a href ="{{route('category_list')}}" target="_blank">Manage Categories</a>
                                            @endif
                                        </div>
                                    </div>


                                    {{--<div class="form-group">
                                        <label for="tag_names" class="col-sm-3 col-md-3 col-lg-2 control-label ">Tags:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <ul id="myTags">
                                            </ul>
                                        </div>
                                    </div>--}}


                                    <div class="form-group">
                                        <label for="size" class="col-sm-3 col-md-3 col-lg-2 control-label">Size:</label>			<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="size" value="{{ $item->item_size }}" class="form-control" id="size">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="supplier" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Supplier:</label>			<div class="col-sm-9 col-md-9 col-lg-10">

                                            <select  name = "item_supplier" value="{{ $item->supplier_id }}" class="form-control">
                                                <option></option><option value="-1" @if($item->supplier_id==-1) selected @endif>none</option>
                                                @foreach ($supplierList as $aList)
                                                    @if($aList->id==$item->supplier_id)
                                                        <option value = "{{$aList->id}}" selected>
                                                            {{ $aList->company_name }}
                                                        </option>
                                                    @else
                                                        <option value = "{{$aList->id}}">
                                                            {{ $aList->company_name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{ $errors->first('item_supplier') }}</span>

                                        </div>

                                    </div>

                                    <div class="form-group">
                                        <label for="manufacturer" class="col-sm-3 col-md-3 col-lg-2 control-label ">Manufacturer:</label>			<div class="col-sm-9 col-md-9 col-lg-10">

                                            <select  name = "item_manufacturer" {{ $item->manufacturer_id }} class="form-control">
                                                <option></option><option value="-1">none</option>
                                                @foreach ($manufacturerList as $aList)
                                                    @if($aList->id==$item->manufacturer_id)
                                                        <option value = "{{$aList->id}}" selected>
                                                            {{ $aList->manufacturer_name }}
                                                        </option>
                                                    @else
                                                        <option value = "{{$aList->id}}">
                                                            {{ $aList->manufacturer_name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @if(UserHasPermission("item_manage_manufacturers"))
                                                <a href ="{{route('manufacturer_list')}}" target="_blank">Manage Manufacturers</a>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="form-group reorder-input ">
                                        <label for="reorder_level" class="col-sm-3 col-md-3 col-lg-2 control-label wide">Reorder Level:</label>					<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="reorder_level" value="{{ $item->item_reorder_level }}" id="reorder_level" class="form-control form-inps">
                                            <span class="text-danger">{{ $errors->first('reorder_level') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group reorder-input ">
                                        <label for="replenish_level" class="col-sm-3 col-md-3 col-lg-2 control-label wide">Replenish Level:</label>					<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="replenish_level" value="{{ $item->item_replenish_level }}" id="replenish_level" class="form-control form-inps">
                                            <span class="text-danger">{{ $errors->first('replenish_level') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="size" class="col-sm-3 col-md-3 col-lg-2 control-label wide">Days to expiration:</label>					<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" name="expire_days" value="{{ $item->days_to_expiration }}" id="expire_days" class="form-control form-inps">
                                            <span class="text-danger">{{ $errors->first('expire_days') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="description" class="col-sm-3 col-md-3 col-lg-2 control-label wide">Description:</label>					<div class="col-sm-9 col-md-9 col-lg-10">
                                            <textarea name="description" value = "{{ $item->description }}" cols="17" rows="5" id="description" class="form-control  text-area">{{ $item->description }}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="tax_included" class="col-sm-3 col-md-3 col-lg-2 control-label wide">Prices include Tax:</label>					<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="checkbox" value = "{{$item->price_include_tax }}" name="tax_included" value="1" id="tax_included" class="delete-checkbox">
                                            <label for="tax_included"><span></span></label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="is_service" class="col-sm-3 col-md-3 col-lg-2 control-label wide">Is Service Item (Does not have quantity)?:</label>					<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="checkbox" value = "{{$item->service_item }}" name="is_service" value="1" id="is_service" class="delete-checkbox">
                                            <label for="is_service"><span></span></label>
                                        </div>
                                    </div>
                                    <div  class="form-group">
                                        <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">Item Images:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10"><div class="col-sm-9 col-md-9 col-lg-10">
                                                <div class = "dropzone" id ="uploadForm"></div>
                                                <div class  ="alert alert-info text-center">Drag and drop or click in the upload area to select image</div>
                                            </div>
                                        </div>
                                    </div>

                            <div  class="form-group">
                                <label class="col-sm-3 col-md-3 col-lg-2 control-label wide"> </label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            @foreach($images as $anImage)
                                            @if(!is_null($anImage->item_id))
                                            <div class="{{$item->item_id}}ol-sm-6 col-md-4">
                                            <div class="thumbnail">

                                                 <img src = "{{$anImage->directory}}/{{$anImage->new_name}}"  >

                                                    <div class="caption">
                                                    <h6>{{$anImage->actual_name}}</h6>
                                                    <p><a href="{{route('item_image_delete',['item_id'=>$anImage->item_id,'image_id'=>$anImage->file_id])}}" class="btn btn-default" role="button">Delete </a></p>
                                                </div>
                                            </div>
                                           </div>
                                                @endif
                                                @endforeach

                                          </div>
                                       </div>
                               </div>

                                    {{--<div class = "form-group">
                                    <div class="col-sm-3 col-md-3 col-lg-2 control-label wide">
                                        <div class="col-sm-9 col-md-9 col-lg-10"><div class="col-sm-9 col-md-9 col-lg-10">
                                        <div class ="img-responsive">
                                            <div class="row">
                                            @foreach($images as $anImage)
                                                <div class = "col-md-3">
                                                    <img class ="img-thumbnail" src = "{{$anImage->directory}}/{{$anImage->new_name}}" height = "200px" width="200px" >
                                                </div>
                                            @endforeach
                                          </div>
                                        </div>
                                        </div>
                                    </div>
                                    </div>--}}

                                    <br>
                                    <div class="panel-heading pricing-widget">
                                        <i class="pe-7s-wallet"></i> Pricing and Inventory			</div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="cost_price" class="col-sm-3 col-md-3 col-lg-2 control-label required wide">Cost Price (Without Tax):</label>						<div class="col-sm-9 col-md-9 col-lg-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon bg"><span class="">$</span></span>
                                                    <input  type="text" name="cost_price" value = "{{$item->cost_price }}" size="8" id="cost_price" class="form-control form-inps">
                                                    <span class="text-danger">{{ $errors->first('cost_price') }}</span>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="unit_price" class="col-sm-3 col-md-3 col-lg-2 control-label required wide">Selling Price:</label>					<div class="col-sm-9 col-md-9 col-lg-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon bg"><span class="">$</span></span>
                                                    <input  type="text" name="unit_price" value = "{{$item->selling_price }}" size="8" id="unit_price" class="form-control form-inps">
                                                    <span class="text-danger">{{ $errors->first('unit_price') }}</span>
                                                </div>
                                            </div>
                                        </div>


                                        <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">Current Quantity:</label>								<div class="col-sm-9 col-md-9 col-lg-10">
                                            <h5 data-start-quantity="0" class="cur_quantity" id="cur_quantity_location_1"> {{$item->item_quantity}} </h5>
                                            <input type = "hidden" id="current_quantity" value = {{$item->item_quantity}}"">
                                        </div>
                                    </div>


                                    <div class="form-group quantity-input ">
                                        <label class="col-sm-3 col-md-3 col-lg-2 control-label wide">Inventory to add/subtract:</label>							<div class="col-sm-9 col-md-9 col-lg-10">
                                            <input type="text" value = "" name="quantity_add_minus" value="" id="quantity_add_minus_location_1" data-location-id="1" class="form-control form-inps quantity_add_minus">
                                        </div>
                                    </div>

                                </div>


                                <input type="hidden" name  = "item_id" id="item_id" value="{{$item->item_id}}">

                                <div class="form-actions pull-right">
                                    <input type="submit" name="submitf" value="Submit"  class="btn floating-button btn-primary float_right" id = "add">
                                </div>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </div>
                        </div>
                </form>	</div>
        </div>
    </div>
@endsection


@section('additionalJS')
    <script>

        $(document).ready(function(){
            $(".quantity_add_minus").keyup(function()
            {


                if ($(this).val() != '')
                {

                    var location_id = 1;
                    var start_quantity = parseFloat($('#cur_quantity_location_'+location_id).data('start-quantity'));

                    if (!isNaN(parseFloat($(this).val())) && isFinite($(this).val()) && parseFloat($(this).val())!=0)
                    {
                        var quantity_info = parseFloat($(this).val()) > 0 ? '<span class="text-success">+'+$(this).val()+'</span>' : '<span class="text-danger">'+$(this).val()+'</span>';
                        current_quantity = parseInt($("#current_quantity").val());
                        if(!isNaN(current_quantity))
                        {
                            $('#cur_quantity_location_'+location_id).html((current_quantity+parseFloat($(this).val())) + " ("+quantity_info+")");
                        }else{
                            $('#cur_quantity_location_'+location_id).html((start_quantity+parseFloat($(this).val())) + " ("+quantity_info+")");
                        }

                    }
                    else
                    {
                        $('#cur_quantity_location_'+location_id).text($('#cur_quantity_location_'+location_id).data('start-quantity'));
                    }
                }
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });








            item_id = $("#item_id").val();
            Dropzone.autoDiscover = false;
            var myDropzone = new Dropzone("#uploadForm", {
                addRemoveLinks: true,
                url: "{{route('insert_item_file')}}",
                type: "POST",
                autoProcessQueue: false,
                init: function () {
                    this.on("queuecomplete", function () {
                        $("#item_form").submit();
                    }),
                            this.on('sending', function(file, xhr, formData){
                                formData.append('item_id', item_id);
                            });

                },
                headers: {
                    'X-CSRF-Token': $('input[name="_token"]').val()
                },
                success: function(file, response){
                    $("#item_id").val(response.item_id);
                },
                error: function(file, response){
                    console.log(response);
                }
            });





            $('#add').on('click',function(e){
                $val_test = true;
                if( $('input[name="item_name"]').val()==""||$('input[name="item_category"]').val()==""||$('input[name="item_supplier"]').val()==""||$('input[name="cost_price"]').val()==""||$('input[name="unit_price"]').val==""){
                    $val_test = false;
                }
                if($('input[name="reorder_level"]').val()!="")
                {
                    if(isNaN($('input[name="reorder_level"]').val())){
                        $val_test = false;
                    }
                }
                if($('input[name="replenish_level"]').val()!=""){
                    if(isNaN($('input[name="replenish_level"]').val())){
                        $val_test = false;
                    }
                }
                if( $('input[name="expire_days"]').val()!=""){
                    if(isNaN($('input[name="expire_days"]').val())){
                        $val_test = false;
                    }
                }
                if( $('input[name="cost_price"]').val()!=""){
                    if(isNaN($('input[name="cost_price"]').val())){
                        $val_test = false;
                    }
                }
                if( $('input[name="unit_price"]').val()!=""){
                    if(isNaN($('input[name="unit_price"]').val())){
                        $val_test = false;
                    }
                }
                if(!$val_test){
                    $("#item_form").submit();
                }
                else {
                    e.preventDefault();
                    if (myDropzone.getQueuedFiles().length > 0) {
                        myDropzone.processQueue();
                    } else {
                        $("#item_form").submit();
                    }

                }
            });


        });



    function groupSelect(checkBox){
        selectClass = ".permissions_"+checkBox.id;
        if(checkBox.checked == true){
            $(selectClass).prop('checked', 'checked');
        }else{
            $(selectClass).prop('checked', '');
        }
    }
    function loadTempImage(input){
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#image_empty').attr('src', e.target.result) .width(150)
                        .height(200);;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

</script>
@stop
