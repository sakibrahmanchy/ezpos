@extends('layouts.master')

@section('pageTitle','Search Sales')

@include('includes.message-block')

@section('breadcrumbs')
    {!! Breadcrumbs::render('sale_search') !!}
@stop

@section('content')

    <div class="box box-primary" style="padding:20px">
        <div class="panel-heading hidden-print">
            Date Range			</div>
        <div class="panel-body hidden-print">
            <form id = "salesReportGenerator" name="salesReportGenerator" action="{{route('search_sale')}}" method="get" class="form-horizontal form-horizontal-mobiles">
                <input type="hidden" name="isPosted" value="0">

                <div id="report_date_range_complex">
                    <div class="form-group">
                        <label for="complex_radio" class="col-sm-3 col-md-3 col-lg-2 control-label">Custom Range:</label>							<div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="radio" name="report_type" id="complex_radio" value="complex" data-start-checked="checked">
                            <label for="complex_radio"><span></span></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group input-daterange" id="reportrange">
		                                    <span class="input-group-addon bg">
					                           From					                       	</span>
                                        <input type="text" class="datepicker form-control start_date" name="start_date_formatted" id="start_date_formatted" value="09/12/2017"><input type="hidden" id="start_date" name="start_date" value="2017-09-12">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-daterange" id="reportrange1">
		                                    <span class="input-group-addon bg">
			                                    To			                                </span>
                                        <input type="text" class="datepicker form-control end_date" name="end_date_formatted" id="end_date_formatted" value="09/12/2017"><input type="hidden" id="end_date" name="end_date" value="2017-09-12">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                {{--<script type="text/javascript">--}}
                    {{--$('.reports_selected_location_ids_checkboxes').change(function()--}}
                    {{--{--}}
                        {{--var selected_location_ids = [];--}}
                        {{--$("input[name='reports_selected_location_ids[]']:checked").each(function()--}}
                        {{--{--}}
                            {{--selected_location_ids.push($(this).val());--}}
                        {{--});--}}

                        {{--$.post('https://demo.phppointofsale.com/index.php/reports/set_selected_location_ids', {reports_selected_location_ids: selected_location_ids});--}}
                    {{--});--}}
                {{--</script>--}}

                <div class="form-group">
                    <label for="matchType" class="required text-danger col-sm-3 col-md-3 col-lg-2 control-label">Match Type:</label>						<div class="controls col-sm-9 col-md-7 col-lg-7">
                        <select name="matchType" id="matchType" class="form-control">
                            <option value="matchType_All">Match all rules (AND condition)</option>
                            <option value="matchType_Or">Match any rule (OR condition)</option>
                        </select>
                        <em>
                            Help: How do you want the sales report rules you create below to be applied? Choose 'Match all rules' to only see the sales who match every rule you create. Choose 'Match any rule' to see the sales who match one or more rules.							</em>
                    </div>

                </div>

               {{-- <div class="form-group">
                    <label for="export_excel" class="col-sm-3 col-md-3 col-lg-2 control-label">Export to Excel:</label>						<div class="controls col-sm-9 col-md-9 col-lg-9">
                        <input type="checkbox" name="export_excel" value="1" id="export_excel">
                        <label for="export_excel"><span></span></label>						</div>
                </div>--}}

                <div class="table-responsive">
                    <table class="table conditions custom-report">
                        <tbody id="resumable">

                                   <tr class="duplicate">
                                        <td class="field">
                                            <select name="field[]" class="selectField span7 form-control">
                                                <option value="0">Please Select</option>
                                                <option value="1" rel="customers">Customer Name</option>
                                               {{-- <option value="2" rel="itemsSN">Item Serial number</option>--}}
                                                <option value="3" rel="employees">Employee Name</option>
                                                <option value="4" rel="itemsCategory">Item Category</option>
                                                <option value="5" rel="suppliers">Supplier Name</option>
                                                {{--<option value="6" rel="saleType">Sale Type</option>--}}
                                                <option value="7" rel="saleAmount">Sale Amount</option>
                                                <option value="8" rel="itemsKitName">Item Kit Name</option>
                                                <option value="9" rel="itemsName">Item Name/UPC/Product ID</option>
                                                <option value="10" rel="saleID">Sale ID</option>
                                               {{-- <option value="11" rel="paymentType">Payment Type</option>--}}
                                             {{--   <option value="12" rel="saleItemDescription">Sale Item Description</option>--}}
                                                <option value="13" rel="salesPerson">Sales person</option>
                                              {{--  <option value="14" rel="itemsTag">Tag</option>--}}
                                                <option value="15" rel="manufacturer">Manufacturer</option>

                                            </select>
                                        </td>

                                        <td class="condition">
                                            <select name="condition[]" class="selectCondition form-control" disabled="">
                                                <option value="1" selected>is</option>
                                                <option value="2">is not</option>
                                                <option value="7">Greater Than (&gt;)</option>
                                                <option value="8">Less Than (&lt;)</option>
                                                <option value="9">Equal To (=)</option>
                                            </select>
                                        </td>
                                        <td class="value"><input type="text" name="value[]" w="" data-value="" value="" class="form-control" disabled=""></td>
                                        <td class="actions">
                                                <span class="actionCondition">
                                                AND									</span>
                                            <a class="btn btn-primary AddCondition" href="#" title="Add Condition">+</a>
                                            <a class="btn btn-primary DelCondition" href="#" title="Remove Condition">-</a>
                                        </td>
                                    </tr>
                                {{--@endif--}}
                        </tbody></table>
                </div>

                <div class="form-actions text-center">
                    <button name="generate_report" onclick="submitForm()" value="1" id="generate_report" class="submit_button btn btn-primary btn-lg">Submit</button>
                </div>


            </form>


        </div>
    </div>
    @if(isset($items))
    <div class="card  table-resonsive">

         @if($items)
         <table class="table tableResult">

            <thead>
                <th>Sale Id</th>
                <th>Date</th>
                <th>Sold By</th>
                <th>Sold To</th>
                <th>Subtotal</th>
                <th>Total</th>
                <th>Tax</th>
                <th>Discounts</th>
            </thead>
             <tbody>
             @foreach($items as $anItem)
                <tr>
                    <td><a href="{{route('sale_receipt',['sale_id'=>$anItem->sale_id])}}">
                            <span class="glyphicon glyphicon-print"></span></a>
                        @if(UserHasPermission('sale_refund'))
                             <a href="{{route('sale_pre_edit',['id'=>$anItem->sale_id])}}"><span class="glyphicon glyphicon-edit"></span></a>
                        @endif
                        EZPOS {{$anItem->sale_id}}</td>
                    <td> {{$anItem->sale_create_date}}</td>
                    <td> {{$anItem->name}}</td>
                    <td> {{$anItem->first_name}} {{$anItem->last_name}}</td>
                    <td> {{$anItem->sub_total_amount}}</td>
                    <td> {{$anItem->total_amount}}</td>
                    <td> {{$anItem->tax_amount}}</td>
                    <td> {{number_format($anItem->total_sales_discount,2)}}</td>
                </tr>
             @endforeach
             </tbody>
         </table>
         @endif

    </div>
    @endif


@endsection

@section('additionalJS')
<script>


    function submitForm(){

    }

    (function($)
    {
        if(localStorage.getItem("resumableData") != null){

        }

        @if(isset($_GET['matchType']))
            $("#matchType").val("<?php echo $_GET['matchType'] ?>");
        @endif

        var setA = false;
        @if(isset($_GET['report_type']))

            @if($_GET['report_type']=='simple')

                var selected_date = "<?php echo $_GET['report_date_range_simple']; ?>";
                $(".dates").each(function(i){

                        if($(this).val()==selected_date){
                            $(this).prop('selected', true);

                        }
                });

            @elseif($_GET['report_type']=='complex')
                   $("#complex_radio").prop('checked',true);
                    setA = true;
                    $("#start_date_formatted").datepicker( "setDate",  "<?php echo $_GET['start_date_formatted'] ?>" );
                    $("#end_date_formatted").datepicker( "setDate",  "<?php echo $_GET['end_date_formatted'] ?>");
            @endif



        @endif

        if(!setA) {
                $("#start_date_formatted").datepicker("setDate", new Date());
                $("#end_date_formatted").datepicker("setDate", new Date());
            }

        $.fn.tokenize = function(inputId)
        {
            console.log(inputId);
            w = $("#field-value-"+inputId).attr("data-value");

            var propertyToSearch = {
                "customers": "first_name",
                "employees": "name",
                "itemsCategory": "category_name",
                "suppliers": "company_name",
                "itemsKitName": "item_kit_name",
                "itemsName": "item_name",
                "salesPerson": "first_name",
                "manufacturer": "manufacturer_name"
            }

           // var settings = $.extend({}, {prePopulate: false}, inputId);
            return this.each(function()
            {
                $(this).tokenInput('{{route('search_sale')}}?act=autocomplete&w='+w,
                        {
                            theme: "facebook",
                            queryParam: "term",
                            extraParam: "w",
                            hintText: "Type in a search term",
                            noResultsText: "No results",
                            searchingText: "Searching...",
                            preventDuplicates: true,
                            propertyToSearch: propertyToSearch[w]
                        });
            });
        }
    })(jQuery);

    $(function() {

        table = $('.tableResult').DataTable({
            "bInfo" : false,
            "bSort": false,
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print',
            ],
        });

        var prepopulate = {"field": [[[]]]};
        var sInput = $("<input />").attr({
            "type": "text",
            "name": "value[]",
            "w": "",
            "value": "",
            "class": "form-control"
        });
        $(".selectField").each(function (i) {

            if ($(this).val() == 0) {
                $(this).parent().parent().children("td.condition").children(".selectCondition").prop("disabled", true);
                $(this).parent().parent().children("td.value").html("").append(sInput.prop("disabled", true));
            } else {

                if ($(this).val() != 2 && $(this).val() != 6 && $(this).val() != 7 && $(this).val() != 10 && $(this).val() != 12) {
                    $(this).parent().parent().children("td.value").children("input").attr("w", $("option:selected", $(this)).attr('rel')).tokenize($(this).val());
                }
                if ($(this).val() == 6) {
                    $(this).parent().parent().children("td.value").html("").append($("<input />").attr({
                        "type": "hidden",
                        "name": "value[]",
                        "value": "",
                        "class": "form-control"
                    }));
                }
                disableConditions($(this), false);
            }
        });
    });


    $(document).on('change', "#matchType", function(){
        if ($(this).val() == 'matchType_All')
        {
            $("#matched_items_only").prop('disabled', false);
            $(".actions span.actionCondition").html("AND");
        }
        else
        {
            $("#matched_items_only").prop('checked', false);
            $("#matched_items_only").prop('disabled', true);
            $(".actions span.actionCondition").html("OR");
        }
    });


    $(document).on('click', "a.AddCondition", function(e){
        var sInput = $("<input />").attr({"type": "text", "name": "value[]", "w":"", "value":"", "class":"form-control"});
        $('.conditions tr.duplicate:last').clone().insertAfter($('.conditions tr.duplicate:last'));
        $("input", $('.conditions tr.duplicate:last')).parent().html("").append(sInput).children("input").tokenize();
        $("option", $('.conditions tr.duplicate:last select')).removeAttr("disabled").removeAttr("selected").first().prop("selected", true);

        $('.conditions tr.duplicate:last').trigger('change');
        e.preventDefault();
    })

    $(document).on('click', "a.DelCondition", function(e){
        if ($(this).parent().parent().parent().children().length > 1)
            $(this).parent().parent().remove();

        e.preventDefault();
    })

    $(document).on('change', ".selectField", function(){
        var sInput = $("<input />").attr({"type": "text","id":"field-value-"+($(this).val()), "name": "value[]", "w":"", "value":"", "class":"form-control"});
        var field = $(this);
       // console.log($(this).val());
        // Remove Value Field
        field.parent().parent().children("td.value").html("");
        if ($(this).val() == 0)
        {
            field.parent().parent().children("td.condition").children(".selectCondition").prop("disabled", true);
            field.parent().parent().children("td.value").append(sInput.prop("disabled", true));
        }
        else
        {
            field.parent().parent().children("td.condition").children(".selectCondition").removeAttr("disabled");
            if ($(this).val() == 2 || $(this).val() == 7 || $(this).val() == 10 || $(this).val() == 12)
            {
                field.parent().parent().children("td.value").append(sInput);
            }
            else
            {
                if ($(this).val() == 6)
                {
                    field.parent().parent().children("td.value").append($("<input />").attr({"type": "hidden", "name": "value[]", "value":"", "class":"form-control"}));
                }
                else
                {
                   /* field.parent().parent().children("td.value").append(sInput.attr("w", $("option:selected", field).attr('rel'))).children("input").tokenize();*/

                    field.parent().parent().children("td.value").append(sInput.attr("data-value", $("option:selected", field).attr('rel'))).children("input").tokenize($(this).val());

                }
            }
            disableConditions(field, true);
        }
    });

    function disableConditions(elm, q) {
        var allowed1 = ['1', '2'];
        var allowed2 = ['7', '8', '9'];
        var allowed3 = ['10', '11'];
        var allowed4 = ['1', '2', '7', '8', '9'];
        var allowed5 = ['1'];
        var disabled = elm.parent().parent().children("td.condition").children(".selectCondition");

        if (q == true)
            $("option", disabled).removeAttr("selected");

        $("option", disabled).prop("disabled", true);
        $("option", disabled).each(function() {
            if (elm.val() == 11 && $.inArray($(this).attr("value"), allowed5) != -1) {
                $(this).removeAttr("disabled");
            }else if (elm.val() == 10 && $.inArray($(this).attr("value"), allowed4) != -1) {
                $(this).removeAttr("disabled");
            } else if (elm.val() == 6 && $.inArray($(this).attr("value"), allowed3) != -1) {
                $(this).removeAttr("disabled");
            } else if (elm.val() == 7 && $.inArray($(this).attr("value"), allowed2) != -1) {
                $(this).removeAttr("disabled");
            } else if (elm.val() != 6 && elm.val() != 7 && elm.val() != 10 && elm.val() != 11 && $.inArray($(this).attr("value"), allowed1) != -1) {
                $(this).removeAttr("disabled");
            }
        });

        if (q == true)
            $("option:not(:disabled)", disabled).first().prop("selected", true);
    }

    window.onpageshow = function(evt) {
        // If persisted then it is in the page cache, force a reload of the page.
        if (evt.persisted) {
            document.body.style.display = "none";
            location.reload();
        }
    };
</script>
@stop