@extends('layouts.master')

@section('pageTitle','New Price Rule')

@section('breadcrumbs')
    {!! Breadcrumbs::render('price_rule_edit',$priceRule->id) !!}
@stop

@section('content')
     <div class="box box-primary" style="padding:20px">
        @include('includes.message-block')
        <div class="row" id="form">
            <div class="col-md-12">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="pe-7s-edit"></i>
                        Price Rule Information <small>(Fields in red are required)</small>
                    </h3>
                </div>

                <form action="{{route('price_rule_edit',['price_rule_id'=>$priceRule->id])}}" id="item_kit_form" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="type" class="col-sm-3 col-md-3 col-lg-2 control-label  required wide">Rule Type:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <select  name="type" class="form-control form-inps" id="type" required>
                                    <option value="0">Select A Rule Type</option>
                                    <option @if($priceRule->type==1) selected ="selected"  @endif value="simple_discount">Simple Discount</option>
                                </select>
                                <span class="text-danger">{{ $errors->first('type') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="col-sm-3 col-md-3 col-lg-2 control-label required wide">Rule Name:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="text" name="name" value="{{$priceRule->name}}" id="name" required="required" class="form-control form-inps">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="col-sm-3 col-md-3 col-lg-2 control-label">Description:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <textarea name="description" cols="30" rows="4" id="description" class="form-control text-area" value="{{$priceRule->description}}"></textarea>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="start_date" class="col-sm-3 col-md-3 col-lg-2 control-label required text-info wide">Start Date:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <div class="input-group date" data-date="">
                                    <span class="input-group-addon bg"><i class="fa fa-calendar"></i></span>
                                    <input  name="start_date" value="{{$priceRule->start_date}}" id="start_date" required="required" class="form-control datepicker">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="end_date" class="col-sm-3 col-md-3 col-lg-2 control-label required text-info wide">End Date:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <div class="input-group date" data-date="">
                                    <span class="input-group-addon bg"><i class="fa fa-calendar"></i></span>
                                    <input  name="end_date" value="{{$priceRule->end_date}}" id="end_date" class="form-control form-inps datepicker" required="required">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="active" class="col-sm-3 col-md-3 col-lg-2 control-label">Active:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                @if($priceRule->active)
                                    <input type="checkbox" name="active" value="{{$priceRule->active}}" id="active" checked>
                                @else
                                    <input type="checkbox" name="active" value="{{$priceRule->active}}" id="active">
                                @endif
                                <label for="active"><span></span></label>
                            </div>
                        </div>

                        <span id="select_fields" class="hidden">
                            <div class="form-group">
                                <label for="item-select" class=" col-sm-3 col-md-3 col-lg-2 control-label " >Add Items:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <select name = "items[]" class="item-select form-control" multiple="multiple" data-placeholder= "Select items" >
                                        @foreach($items as $anItem)
                                            @if(in_array($anItem->id,$selectedItems))
                                                 <option selected id ="option-{{$anItem->id}}" value="{{$anItem->id}}">{{$anItem->item_name}}</option>
                                            @else
                                                <option id ="option-{{$anItem->id}}" value="{{$anItem->id}}">{{$anItem->item_name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="item-select" class=" col-sm-3 col-md-3 col-lg-2 control-label " name = "item_kits[]">Add Item Kits:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <select name = "item_kits[]" class="item-select form-control" multiple="multiple" data-placeholder= "Select item kits" >
                                        @foreach($itemKits as $anItemKit)
                                            @if(in_array($anItemKit->id,$selectedItemKits))
                                                <option selected id ="option-{{$anItemKit->id}}" value="{{$anItemKit->id}}">{{$anItemKit->item_kit_name}}</option>
                                            @else
                                                <option id ="option-{{$anItemKit->id}}" value="{{$anItemKit->id}}">{{$anItemKit->item_kit_name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="item-select" class=" col-sm-3 col-md-3 col-lg-2 control-label " name = "categories[]">Add Categories:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <select name = "categories[]" class="item-select form-control" multiple="multiple" data-placeholder= "Select categories" >
                                        @foreach($categories as $aCategory)
                                            @if(in_array($aCategory->id,$selectedCategories))
                                                <option selected id ="option-{{$aCategory->id}}" value="{{$aCategory->id}}">{{$aCategory->category_name}}</option>
                                            @else
                                                <option id ="option-{{$aCategory->id}}" value="{{$aCategory->id}}">{{$aCategory->category_name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </span>

                        <div id="items_to_buy_field" class="form-group hidden">
                            <label for="items_to_buy" class="col-sm-3 col-md-3 col-lg-2 control-label required wide">Quantity to Buy:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="text" name="items_to_buy" value="{{$priceRule->items_to_buy}}" id="items_to_buy" class="form-control form-inps items_to_buy">
                            </div>
                        </div>

                        <div id="items_to_get_field" class="form-group hidden">
                            <label for="items_to_get" class="col-sm-3 col-md-3 col-lg-2 control-label required wide">Quantity to Get:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="text" name="items_to_get" value="{{$priceRule->items_to_get}}" id="items_to_get" class="form-control form-inps items_to_get">
                            </div>
                        </div>

                        <div id="spend_amount_field" class="form-group hidden">
                            <label for="spend_amount" class="col-sm-3 col-md-3 col-lg-2 control-label required wide">Spend Amount:</label>
                            <div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="text" name="spend_amount" value="{{$priceRule->spend_amount}}" id="spend_amount" class="form-control form-inps">
                            </div>
                        </div>

                        <span id="discount_fields" class="hidden">

                            <div class="form-group">
                                <label for="percent_off" class="col-sm-3 col-md-3 col-lg-2 control-label wide">Percent Off:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="text" name="percent_off" value="{{$priceRule->percent_off}}" id="percent_off" class="form-control form-inps" step="any">
                                </div>
                            </div>

                            <div class="form-group">
                                <h4 class="text-center">OR</h4>
                            </div>

                            <div class="form-group">
                                <label for="fixed_off" class="col-sm-3 col-md-3 col-lg-2 control-label wide">Fixed Off:</label>
                                <div class="col-sm-9 col-md-9 col-lg-10">
                                    <input type="text" name="fixed_of" value="{{$priceRule->fixed_of}}" id="fixed_off" class="form-control form-inps" step="any">
                                </div>
                            </div>

                        </span>


                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-controls">
                            <ul class="list-inline pull-right">
                                <li>
                                    <input type="submit" name="submitf" value="Submit" id="submitf" class=" btn btn-primary">
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>

     </div>
@endsection

@section('additionalJS')
    <script>
        if($('#requires_coupon').is(':checked'))
        {
            $('#coupon_code_field').removeClass('hidden');
            $('#coupon_code_field_checkbox').removeClass('hidden');
        }


        jQuery(document).on("click", "#add_row", function(){





            var last_row_id= $('#price_break_rule_tbl tbody tr:last').attr('id');
            new_row_id = parseInt(last_row_id)+1;
            var new_row='<tr id="'+new_row_id+'">';
            new_row+='<td><a onclick="deleteRow('+new_row_id+')"><i class="ion-close-circled text-danger" title="Delete"></i></a></td>';
            new_row+='<td><input type="text" name="qty_to_buy[]" class="qty_to_buy form-control" /></td>';
            new_row+='<td><input type="text" name="flat_unit_discount[]" class="unit_discount form-control" /></td>';
            new_row+='<td><input type="text" name="percent_unit_discount[]" class="unit_discount form-control" /></td>';
            new_row+='</tr>';

            $("#price_break_rule_tbl tbody").append(new_row);
        });

        function deleteRow(id)
        {
            var elem = document.getElementById(id); // getElementById requires the ID
            elem.parentNode.removeChild(elem);
            return false;
        }


        //validation and submit handling
        var ruleID = '1';
        var type = $('#type').val();

        $(document).ready(function()
        {


            $(".item-select").select2({
                width: '100%',
            });


            /*date_time_picker_field($('.datepicker'), JS_DATE_FORMAT);*/

            display_rule_type_options(type);

        });

        function display_rule_type_options(type)
        {
            switch (type)
            {
                case "simple_discount":
                    //show
                    $('#select_fields, #discount_fields, #unlimited_field').toggleClass('hidden',false);
                    //hide
                    $('#items_to_buy_field, #items_to_get_field, #spend_amount_field, #price_breaks_table').toggleClass('hidden', true);
                    break;
                case "buy_x_get_y_free":
                    //show
                    $('#select_fields, #items_to_buy_field, #items_to_get_field, #unlimited_field').toggleClass('hidden',false);
                    //hide
                    $('#discount_fields, #spend_amount_field, #price_breaks_table').toggleClass('hidden', true);
                    break;
                case "buy_x_get_discount":
                    //show
                    $('#select_fields, #items_to_buy_field, #unlimited_field, #discount_fields').toggleClass('hidden',false);
                    //hide
                    $('#items_to_get_field, #spend_amount_field, #price_breaks_table').toggleClass('hidden', true);
                    break;
                case "spend_x_get_discount":
                    //show
                    $('#spend_amount_field, #discount_fields, #unlimited_field').toggleClass('hidden',false);
                    //hide
                    $('#select_fields, #items_to_buy_field, #items_to_get_field, #price_breaks_table').toggleClass('hidden', true);
                    break;
                case "advanced_discount":

                    if(!$('#unlimited').is(':checked'))
                    {
                        $('#unlimited').trigger('click');
                    }
                    //show
                    $('#select_fields, #price_breaks_table').toggleClass('hidden',false);
                    //hide
                    $('#items_to_buy_field, #items_to_get_field, #spend_amount_field, #discount_fields, #unlimited_field').toggleClass('hidden', true);
                    break;
                default:
                    //hide
                    $('#select_fields, #items_to_buy_field, #items_to_get_field, #spend_amount_field, #discount_fields, #unlimited_field, #price_breaks_table').toggleClass('hidden', true);
                    break;
            }
        }

        if($('#num_times_to_apply').val() == 0)
        {
            $('#unlimited').prop('checked', true);
        }

        if(!$('#unlimited').is(":checked"))
        {
            if($('#num_times_to_apply').val() === undefined)
            {
                $('#num_times_to_apply').val(1);
            }

            $('#times_to_apply').toggleClass('hidden', false);
        }

        $('#requires_coupon').on('change', function() {
            if($(this).is(":checked"))
            {
                $('#coupon_code_field').removeClass('hidden');
                $('#coupon_code_field_checkbox').removeClass('hidden');
            }
            else
            {
                $('#coupon_code_field').addClass('hidden');
                $('#coupon_code_field_checkbox').addClass('hidden');
                $('#coupon_code').val('');
            }

        });

        $("#unlimited").on('change', function() {
            if($(this).is(":checked"))
            {
                $('#times_to_apply').toggleClass('hidden', true);
                $('#num_times_to_apply').val(0);
            } else {
                if($('#num_times_to_apply').val() <= 0 || $('#num_times_to_apply').val() === undefined)
                {
                    $('#num_times_to_apply').val(1);
                }

                $('#times_to_apply').toggleClass('hidden', false);
            }
        });

        $('#type').on('change',function(event){
            event.preventDefault();
            //clear all data

            $(this).closest('form').find("input[type=text]").each(function(){
                if($(this).attr("id") !== 'name' && $(this).attr("id") !== 'start_date' && $(this).attr("id") !== 'end_date')
                {
                    $(this).val("");
                }
            });

            var type = $('#type').val();
            display_rule_type_options(type);
        });

        $("#percent_off, #fixed_off").on("keyup", function (e) {
            var id = $(this).attr("id");
            var val = $(this).val();
            if(e.which == 9)
            {
                return;
            }

            if(val < 0 || (isNaN(val) && val != '.'))
            {
                $(this).val('');
            }
            else
            {
                if(id == 'fixed_off')
                {
                    $('#percent_off').val('');
                }
                if(id == 'percent_off')
                {
                    $('#fixed_off').val('');
                }
            }
        });

        $("#price_break_rule_tbl tbody").on("keyup", ".unit_discount", function (e) {
            var row = $(this).closest('tr');
            var n = $(this).attr("name");
            var val = $(this).val();

            if(e.which == 9)
            {
                return;
            }

            if(val < 0 || (isNaN(val) && val != '.'))
            {
                $(this).val('');
            }
            else
            {
                if(n == 'flat_unit_discount[]')
                {
                    var other = row.find("input[name='percent_unit_discount[]']");
                }
                if(n == 'percent_unit_discount[]')
                {
                    var other = row.find("input[name='flat_unit_discount[]']");
                }

                other.val('');
            }

        });


    </script>
@stop