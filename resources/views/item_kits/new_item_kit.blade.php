
@extends('layouts.master')

@section('pageTitle','New Item Kit')

@section('breadcrumbs')
    {!! Breadcrumbs::render('new_item_kit') !!}
@stop

@section('content')
    <div class="box box-primary" style="padding:20px">
        @include('includes.message-block')
        <div class="row" id="form">
            <div class="col-md-12">
                <form action="{{route('new_item_kit')}}" id="item_kit_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="pe-7s-edit"></i>
                            Item Kit Information <small>(Fields in red are required)</small>
                        </h3>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="item_selector" class=" col-sm-3 col-md-3 col-lg-2 control-label ">Add Items:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <select class="item-selector  col-md-10" onchange="appendMenuItem(this)">
                                            <option id = "option-0" value="0" selected="selected">Select an item</option>
                                            @foreach($items as $anItem)
                                                <option id ="option-{{$anItem->id}}" value="{{$anItem->id}}">{{$anItem->item_name}}</option>
                                            @endforeach
                                        </select>
                                        <label class ="alert alert-info text-center">Item Kits are made up of 1 or more items to see as a group. Add your first item using this field.</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="items_added"  class=" col-sm-3 col-md-3 col-lg-2 control-label ">Items Added:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <table style="font-size:12px" class="table table-striped table-hover table-responsive" >
                                            <thead>
                                            <tr>
                                                <th>Actions</th>
                                                <th>Item</th>
                                                <th>Quantity</th>
                                            </tr>
                                            </thead>
                                            <tbody id = "selected-items">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="isbn" class=" col-sm-3 col-md-3 col-lg-2 control-label ">UPC/EAN/ISBN:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" name="isbn" value="{{ old('isbn') }}" class="form-control" id="isbn" >
                                        <span class="text-danger">{{ $errors->first('isbn') }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="product_id" class=" col-sm-3 col-md-3 col-lg-2 control-label ">Product Id:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input type="text" name="product_id" value="{{ old('product_id') }}" class="form-control" id="product_id">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="item_kit_name" class="col-sm-3 col-md-3 col-lg-2 control-label required">Item Kit Name:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <input  type="text" name="item_kit_name" value="{{ old('item_kit_name') }}" class="form-control" id="item_kit_name" >
                                        <span class="text-danger">{{ $errors->first('item_kit_name') }}</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="category" class="required col-sm-3 col-md-3 col-lg-2 control-label ">Category:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <select  name = "item_kit_category" class="form-control" value = "{{ old('item_kit_category') }}">
                                            <option></option><option>none</option>
                                            @foreach ($categoryList as $aList)
                                                <option value = "{{$aList->id}}">
                                                    {{$aList->category_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{ $errors->first('item_kit_category') }}</span>
                                        @if(UserHasPermission("item_manage_categories"))
                                            <a href ="{{route('category_list')}}" target="_blank">Manage Categories</a>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="manufacturer" class="col-sm-3 col-md-3 col-lg-2 control-label ">Manufacturer:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <select  name = "item_kit_manufacturer" {{ old('item_kit_manufacturer') }} class="form-control">
                                            <option></option><option>none</option>
                                            @foreach ($manufacturerList as $aList)
                                                <option value = "{{$aList->id}}">
                                                    {{$aList->manufacturer_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if(UserHasPermission("item_manage_manufacturers"))
                                            <a href ="{{route('manufacturer_list')}}" target="_blank">Manage Manufacturers</a>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="col-sm-3 col-md-3 col-lg-2 control-label wide">Description:</label>
                                    <div class="col-sm-9 col-md-9 col-lg-10">
                                        <textarea name="description" value = "{{ old('description') }}" cols="17" rows="5" id="description" class="form-control  text-area"></textarea>
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="cost_price" class="col-sm-3 col-md-3 col-lg-2 control-label required wide">Cost Price (Without Tax):</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <div class="input-group">
                                                <span class="input-group-addon bg"><span class="">$</span></span>
                                                <input  type="text" name="cost_price" value = "{{old('cost_price')}}" size="8" id="cost_price" class="form-control form-inps">
                                                <span class="text-danger">{{ $errors->first('cost_price') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="unit_price" class="col-sm-3 col-md-3 col-lg-2 control-label required wide">Selling Price:</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <div class="input-group">
                                                <span class="input-group-addon bg"><span class="">$</span></span>
                                                <input  type="text" name="unit_price" value = "{{old('unit_price')}}" size="8" id="unit_price" class="form-control form-inps">
                                                <span class="text-danger">{{ $errors->first('unit_price') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name  = "item_kit_id" id="item_kit_id" value="0">

                                <div class="form-actions pull-right">
                                    <input type="submit" name="submitf" value="Submit"  class="btn floating-button btn-primary float_right" id = "add">
                                </div>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection



@section('additionalJS')
    <script>

        $(document).ready(function(){

            $(".item-selector").select2();
        });
        function appendMenuItem(sel){
            id = sel.options[sel.selectedIndex].id;
            curr_id = sel.value;
            elementId = "append-option-"+curr_id;

            if(document.getElementById(elementId) == null)
            {
                $("#selected-items").append('<tr  id = "append-option-'+curr_id+'" ><td><a onclick ="DeleteItem('+curr_id+')" href="javascript:void(0)">Delete</a> </td><td>'+sel.options[sel.selectedIndex].text+'</td><td><input type ="text" name ="quantity[]"</td> <input  type ="hidden" name = "selected_value[]" value = "'+curr_id+'"></tr>');
                $("#selected-items").append('');

            }

        }

        function DeleteItem(item){
            id = "#append-option-"+item;
            $(id).remove();

        }

    </script>
@stop