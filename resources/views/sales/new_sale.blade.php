@extends('layouts.master')

@section('pageTitle','Sales')

@section('breadcrumbs')
    {!! Breadcrumbs::render('new_sale') !!}
    <span><label class="label label-primary pull-right counter-name"><b>{{ \Illuminate\Support\Facades\Cookie::get('counter_name') }}</b></label></span>
    <br><br>
    <a href="javascript:void(0)"  onclick="changeCounter()" class="pull-right">Change Location</a>
    <br>
@stop

@section('content')

    {{--Sale config--}}
        <?php $tax_rate = $settings['tax_rate'] ; ?>
    {{--Sale config--}}

    <div class="row">
        <div class="col-sm-7 panel-margin " >
        <div class = "search section">

                <div class="input-group">
                   <a href="{{route('new_item')}}" target="_blank" class="input-group-addon" id="sizing-addon2" style="background-color:#337ab7;color:white;border:solid #337ab7 1px; "><strong>+</strong></a>
                    <input type="text"  class="form-control" id = "item-names">
                    <div class="input-group-btn bs-dropdown-to-select-group">
                        <button type="button" class="btn btn-primary dropdown-toggle as-is bs-dropdown-to-select" data-toggle="dropdown">
                            <span data-bind="bs-drp-sel-label">Sale</span>
                          {{--  <input type="hidden" name="selected_value" data-bind="bs-drp-sel-value" value="">--}}

                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu" style="">
                            <li data-value="1"><a onclick="convertToSale()"  href="#">Sale</a></li>
                            <li data-value="2"><a onclick="convertToReturn()" href="#">Return</a></li>{{--
                            <li data-value="3"><a href="#">Store Account Payment</a></li>--}}
                        </ul>
                    </div>
                </div>
            <input type="checkbox" checked  id = "auto_select" style="margin-left:10px"> <b>Add automatically to cart when item found.</b>

            </div>

        <br>

        <div class="card table-responsive" >
            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Discount(%)</th>
                    <th class="text-center">Total</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class = "product-descriptions">


                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </div>

       </div>
       <div class ="col-sm-4  " >
            <div class = "card"  >

                <div class="sale-buttons input-group" style = "border-bottom:solid #ddd 1px; padding:10px">

                    <div class="btn-group input-group-btn">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <strong>...</strong>
                        </button>
                        <ul class="dropdown-menu sales-dropdown" role="menu">


                            <li>
                                <a href="{{route('suspended_sale_list')}}" class="" title="Suspended Sales"><i class="ion-ios-list-outline"></i> Suspended Sales</a>								</li>
                            <li>
                                    <a href="{{route('search_sale')}}" class="" title="Search Sales"><i class="ion-search"></i> Search Sales</a>
                            </li>

                            <li>
                                <a href="#look-up-receipt" class="look-up-receipt" data-toggle="modal"><i class="ion-document"></i> Lookup Receipt</a>						</li>

                            <li><a href="{{route('sale_last_receipt')}}"  target="_blank" class="look-up-receipt" title="Lookup Receipt"><i class="ion-document"></i> Show last sale receipt</a></li>
                            <li><a href="{{route('pop_open_cash_drawer')}}"  class="look-up-receipt" title="Lookup Receipt"><i class="ion-document"></i> Pop Open Cash Drawer</a></li>
                            <li><a href="{{ route('add_cash_to_register') }}">Add cash to register</a></li>
                            <li><a href="{{ route('subtract_cash_from_register') }}">Remove cash from register</a></li>
                            <li><a href="{{ route('close_cash_register') }}">Close register</a></li>
                        </ul>
                        <form action="" id="cancel_sale_form" autocomplete="off" method="post" accept-charset="utf-8">

                            <div class="btn-group input-group-btn"  >
                                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <i class="ion-pause"></i>
                                    Suspend Sale								</button>
                                <ul class="dropdown-menu sales-dropdown" id = "sale-type" data-selected-type="sale" role="menu">
                                    <li><a href="#" onclick = "layAwaySale()" id="layaway_sale_button"><i class="ion-pause"></i> Layaway</a></li>
                                    <li><a href="#" onclick = "estimateSale()" id="estimate_sale_button"><i class="ion-help-circled"></i> Estimate</a></li>

                                </ul>
                            </div>
                            <a href="" class="btn btn-danger input-group-addon" id="cancel_sale_button">
                                <i class="ion-close-circled"></i>
                                Cancel Sale				</a>

                        </form>
                    </div>


                </div>

                <!-- If customer is added to the sale -->

                <div class="customer-form">

                    <!-- if the customer is not set , show customer adding form -->
                    <form action="" id="select_customer_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
                        <div class="input-group contacts" style="padding-top:10px;padding-left:10px">

						<a href="{{route('new_customer')}}" target="_blank" class="input-group-addon" id="sizing-addon2" style="background-color:#337ab7;color:white;border:solid #337ab7 1px; "><strong>+</strong></a>
                            <select id="customer" name="customer" class="add-customer-input keyboardLeft ui-autocomplete-input form-control" data-title="Customer Name" placeholder="Type customer name..." autocomplete="off">
                                <option value ="0" selected>Select Customer for sale</option>
                                @foreach($customerList as $aCustomer)
                                    <option value = "{{$aCustomer->id}}">{{$aCustomer->first_name}} {{$aCustomer->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                </div>
            </div>
            <div class = "card" style="font-size:12px;">
                <h4 class="text-center"><strong>Receipt</strong></h4>
                <hr>
                <div class="card">
                    <strong>Subtotal</strong> <span style="float: right"><strong data-subtotal="0" class="subtotal">$0.00</strong></span><br>
                    <strong>+Tax({{ $tax_rate }}%)</strong><span style="float: right"><strong data-tax="0" id="tax">$0.00</strong></span><br>
                    <strong>Discount all items by percent</strong><span style="float: right"><strong id=""><input id ="allDiscountAmount" type ="number" onkeyup="setAllItemToDiscount()" onkeydown="setAllItemToDiscount()"  onchange = "setAllItemToDiscount()"  placeholder="" style="max-width:45px;float: right" value = "0" ></strong></span><br><br>
                    <strong>Discount entire sale</strong><span style="float: right"><strong id=""><input id ="saleDiscountAmount" onkeyup="setSaleToDiscount()" onkeydown="setSaleToDiscount()" type ="number" placeholder="" style="max-width:45px;float: right" value ="0"></strong></span>
                </div>

                    <div class = "card" style="background-color: #778a9b;color:whitesmoke;font-size:20px;">
                        Total <span style="float: right"><strong data-total="0" id = "total"> $0.00</strong></span>
                    </div>
                    <div class = "card" style="background-color: #778a9b;color:whitesmoke;font-size:20px;">
                        Due <span style="float: right"><strong data-due="0" id = "due"> $0.00</strong></span>
                    </div><br>
                <div class="row">
                    {{--<input type="number" id = "paid-amount" name="paid-amount" class="col-md-8 form-control" style="float:left">
                    <button type="button" class="col-md-4 btn btn-success" style="float:right" onclick = "SubmitSales()">
                        Checkout <span class="pe-7s-cart"></span>
                    </button><br><br>--}}


                    <div class="add-payment">

                        <div class="payment-history">

                        </div>
                        <input type = "hidden" name="total-paid-amount" data-value="0">
                        <div style="padding:20px">

                            <div class="side-heading">Add Payment</div>

                            <a tabindex="-1" href="#" class="btn btn-pay select-payment active" data-payment="Cash">
                                Cash				</a>
                            <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Check">
                                Check				</a>
                            <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Debit Card">
                                Debit Card				</a>
                            <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Credit Card">
                                Credit Card				</a>
                            <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Gift Card">
                                Gift Card				</a>
                            <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Loyalty Card">
                                Loyalty Card				</a>

                        </div>


                    <div class="input-group add-payment-form">
                        <select name="payment_type" id="payment_types" class="hidden" data-value="Cash" >
                            <option value="Cash" selected="selected">Cash</option>
                            <option value="Check">Check</option>
                            <option value="Gift Card">Gift Card</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Loyalty Card">Loyalty Card</option>
                        </select>
                        <input type="number" name="amount_tendered" value="0.00" id="amount_tendered" class="add-input numKeyboard form-control" data-title="Payment Amount" onkeydown="this.onchange()" onkeypress="this.onchange()" onfocus="this.onchange()" onkeyup="this.onchange()" onchange="calculateDue()">
                        <input class="hidden form-control" type="text" name="gift_card_number"  id="gift_card_number" class="add-input numKeyboard form-control" >
					<span class="input-group-addon" style="background: #5cb85c; border-color: #4cae4c;">
                        <input class="hidden form-control" type="text" name="loyalty_card_number"  id="loyalty_card_number" class="add-input numKeyboard form-control" >
					<span class="input-group-addon" style="background: #5cb85c; border-color: #4cae4c;">
						<a href="javascript:void(0)" class="hidden" id="add_payment_button" onclick = "addPayment()" style=" color:white;text-decoration:none;">Add Payment</a>
						<a class="javascript:void(0)" id="finish_sale_alternate_button" style=" color:white;text-decoration:none;" onclick = "completeSales()">Complete Sale</a>
					</span>


                    </div>

                </div>
            </div>

           <form id = "saleSubmit" method = "post" action = "{{route('new_sale')}}">

               <input type="hidden" name="_token" value="{{ csrf_token() }}">
           </form>
       </div>
    </div>
    </div>

    <!-- Add Category Modal -->
    <div id="look-up-receipt" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Lookup Receipt</h4>
                </div>
                <div class="modal-body">
                    <input type = "text" class="form-control" name = "receipt-id" id = "receipt-id" placeholder="Sale Id">
                </div>
                <div class="modal-footer">
                    <button onclick ="lookUpReceipt()" type="button" class="btn btn-info" data-dismiss="modal">Look Up Receit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="choose_counter_modal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="chooseCounter">Choose Counter</h4>
                </div>
                <div class="modal-body">
                    <ul class="list-inline choose-counter-home">

                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('additionalJS')
    <script>
        var itemTotalInfo = [];
        var paymentAdded = 0;
        var taxRate = Number("{{ $tax_rate/100}}");


        $(document).ready(function(e){

            checkPaymentType();

            var $item = $('#item-names');

            $("#customer").select2();

            $('.select-payment').on('click mousedown',selectPayment);

            $('#saleDiscountAmount').change(function() {
                setSaleToDiscount();
            });

            $('#saleDiscountAmount').keyup(function() {
                setSaleToDiscount();
            });


            $('#amount_tendered').bind('keypress', function(e) {
                if(e.keyCode==13)
                {
                    e.preventDefault();

                    //Quick complete possible
                    if ($("#finish_sale_alternate_button").is(":visible"))
                    {

                        completeSales();
                    }
                    else
                    {
                       addPayment();
                    }

                }
            });

            if (isEmpty($('.product-descriptions'))) {
                $('.product-descriptions').append('<tr class="no-items"> <td colspan="6"><div class="jumbotron text-center"> <h3>There are no items in the cart [Sales]</h3> </div></td> </tr>');/*
                $('.no-items').css('display','content');*/
                $('.add-payment').hide();

            }

            $item.autocomplete({

                minLength: 0,
                source: function (request, response) {
                    // request.term is the term searched for.
                    // response is the callback function you must call to update the autocomplete's
                    // suggestion list.

                    itemTotalInfo = [];

                    var autoselect = false;
                    if($(("#auto_select")).is(':checked'))
                        autoselect = true;

                    $.ajax({
                        url: "{{route('item_list_autocomplete')}}",
                        data: { q: request.term, autoselect: autoselect },
                        dataType: "json",
                        success: response,
                        error: function () {
                            response([]);
                        }
                    })
                },


                messages: {
                    noResults: '',
                    results: function() {}
                },
                focus: function( event, ui ) {
                    $item.val( ui.item.label );
                    return false;
                },

            });

            $item.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                var a = JSON.stringify(item);

                itemTotalInfo.push(JSON.parse(a));

                    if(item.type=="auto"){
                        console.log(item.item_quantity);
                        autoAddItemTOCart(item);
                        return null;

                    }else{
                        var $li = $('<li class="item-suggestion" data-item-total-info = "'+itemTotalInfo+'" data-id = "'+item.item_id+'" data-sale-price = "'+item.selling_price+'" data-img-src ="{{asset('img')}}/'+item.new_name+'" data-title="'+item.item_name+'" data-item-quantity ="'+item.item_quantity+'" data-item-type="item"  onclick = "addItemToCart(this)">'),
                            $img = $('<img height="50px" width="50px" style="margin-right:10px">');

                        //alert(item.new_name);
                        var  img_src = "default-product.jpg";

                        if(item.new_name!=null){
                            img_src = item.new_name;
                        }
                        if(item.product_type==1){
                            $img.attr({
                                src: '{{asset('img')}}/' + "item-kit.png",
                                alt: item.item_kit_name
                            });
                        }else{
                            $img.attr({
                                src: '{{asset('img')}}/' + img_src,
                                alt: item.item_name
                            });
                        }


                        $li.attr('data-value', item.item_name);
                        $li.append('<a  href="#">');
                        $li.find('a').append($img).append(item.item_name);

                        return $li.appendTo(ul);
                    }




            };

            $( document ).on( 'click', '.bs-dropdown-to-select-group .dropdown-menu li', function( event ) {
                var $target = $( event.currentTarget );
                $target.closest('.bs-dropdown-to-select-group')
                        .find('[data-bind="bs-drp-sel-value"]').val($target.attr('data-value'))
                        .end()
                        .children('.dropdown-toggle').dropdown('toggle');
                $target.closest('.bs-dropdown-to-select-group')
                        .find('[data-bind="bs-drp-sel-label"]').text($target.context.textContent);
                return false;
            });

            selectCounter();

        });



        function addItemPriceToRegister(productId){

            productUniqueId = "#product-"+productId;
            productQuantity = Number($(productUniqueId).val());
            productUnitPriceUniqueId = "#unit-price-" + productId;
            productDiscount = Number($("#discount-"+productId).val())/100;
            productUnitPrice = Number($(productUnitPriceUniqueId).attr("data-unit-price"));
            productDiscountAmount = productUnitPrice *  productDiscount;
            productUnitPrice = productUnitPrice-productDiscountAmount;
            var totalProductPrice = Number(productQuantity * productUnitPrice).toFixed(2);
            if(totalProductPrice>=0)
                $("#total-price-" + productId).text("$" + totalProductPrice);
            else
                $("#total-price-" + productId).text("-$" + (-1) * totalProductPrice);
            $("#total-price-" + productId).attr("data-total-price" , totalProductPrice);
            calculatePrice();

        }

        function calculatePrice(){

            //var subTotal = Number($(".subtotal" ).attr("data-subtotal"));
           var discountItemOnBoard = false,discountItemPrice;
            var subTotal = 0;
            var subReal = 0;
            var saleType = $('#sale-type').attr("data-selected-type");
            $( ".total-price" ).each(function( index ) {

                var current_total = Number($( this ).attr("data-total-price"));

                if(($(this).attr("id"))=="total-price-discount")
                {
                    discountItemOnBoard = true;
                    discountItemPrice = Number($( this ).attr("data-total-price"));
                }

                if(current_total>0||($(this).attr("id"))=="total-price-discount"||saleType=="return"){
                    subReal += (Number($( this ).attr("data-total-price"))) ;
                }

                subTotal += (Number($( this ).attr("data-total-price"))) ;
            });

            subTotal = Number(subTotal.toFixed(2));

            subReal = Number(subReal.toFixed(2));

            var taxToBeReduced = 0 ;
            if(discountItemOnBoard)
            {
                taxToBeReduced = Number(discountItemPrice * taxRate);
            }
            var tax = Number(Number(subTotal * taxRate).toFixed(2)-taxToBeReduced).toFixed(2);
            var realTax = Number(Number(subReal * taxRate).toFixed(2)-taxToBeReduced).toFixed(2);

            realTax = Number(realTax);

            var amountTenderd;

            total = +subTotal + +tax;

            var realTotal = Number(+subReal + realTax);

            total = total.toFixed(2);
            realTotal = realTotal.toFixed(2);

            if(subTotal>=0) $(".subtotal").text("$"+subTotal);
            else $(".subtotal").text("-$"+(-1)* subTotal);
            $(".subtotal").attr("data-subtotal",subReal);

            if(tax>=0)
                $("#tax").text("$"+tax);
            else
                $("#tax").text("-$" + (-1)* tax);

            $("#tax").attr("data-tax",tax);

            if(total>=0)
                $("#total").text("$" + total);
            else
                $("#total").text("-$" + (-1) * total);

            $("#total").attr("data-total",realTotal);
            amountTenderd   = Number($("#amount_tendered").val()).toFixed(2);

            if(amountTenderd==0){
                amountTenderd = total;
            }

            $("#amount_tendered").val(amountTenderd);
            calculateDue();

        }


        function removeProduct(productId){
            console.log(productId);
            Id = "#product-div-"+productId;
            $(Id).remove();
            $("#product-discount-"+productId).remove();
            calculatePrice();
            if (isEmpty($('.product-descriptions'))) {
                $('.product-descriptions').append('<tr class="no-items"> <td colspan="6"><div class="jumbotron text-center"> <h3>There are no items in the cart [Sales]</h3> </div></td> </tr>');
                $('.add-payment').hide();
                /*
                 $('.no-items').css('display','content');*/

            }
        }

        function convertToSale(){

            $('#bs-drp-sel-label').text("Sale");
            $(".quantity").each(function(index){

                var old_value = this.value;
                if(old_value<0){
                    this.value = ((-1) * this.value);
                }

                var product_id = this.id.replace(/[^0-9\.-]+/g, "");
                product_id = product_id.substr(1,product_id.length);
                addItemPriceToRegister(product_id);


            });

        }

        function convertToReturn() {

            $('#bs-drp-sel-label').text("Return");
            $("#sale-type").attr("data-selected-type", "return");
            $(".quantity").each(function (index) {

                var old_value = this.value;
                if (old_value >= 0)
                    this.value = (-1) * this.value;
                var product_id = this.id.replace(/[^0-9\.-]+/g, "");
                product_id = product_id.substr(1, product_id.length);
                addItemPriceToRegister(product_id);

            });

        }

        function autoAddItemTOCart(item){

            if(item.item_quantity<=0&&item.product_type!=1){
                alert("Sorry, product is out of stock.");
            }
            else if(document.getElementById("product-div-"+item.item_id) == null) {

                $('.no-items').remove();
                $('.add-payment').show();

                var itemDescription = '<tr class="product-specific-description" data-index="'+item.item_id+'"  data-item-type="item" id="product-div-' + item.item_id + '" >' +
                    '<td class="col-sm-8 col-md-6">' +
                    '<div class="media">' +
                    '' +
                    '<div class="media-body">' +
                    ' <h6 class="media-heading"><a href="#">' + item.item_name + '</a></h6>';

                if(item.name!=null)
                    itemDescription +=  '<h6 class="media-heading"> by <a href="#">'+ item.name +'</a></h6>';

                if(item.item_quantity != 'undefined'){
                    if(item.item_quantity>10)
                        itemDescription += '<span>Status: </span><span class="text-success"><strong>In Stock</strong></span>';
                    else
                        itemDescription += '<span>Status: </span><span class="text-danger"><strong>Soon will be out of Stock</strong></span>';
                }


                itemDescription += '</div>' +
                    '</div></td>' +
                    ' <td class="col-sm-1 col-md-1" style="text-align: center">' +
                    ' <input type="number" min = "0" class="form-control quantity" id="product-' + item.item_id + '"  onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" onchange = "addItemPriceToRegister(' + item.item_id + ')"  value="1">' +
                    '</td>' +
                    '<td class="col-sm-1 col-md-1 text-center" ><strong data-unit-price = "'+item.selling_price +'" id="unit-price-' + item.item_id + '">$' + item.selling_price + '</strong></td>' +
                    '<td><input  class="form-control discount-amount" type="number" id="discount-'+ item.item_id+'" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" onchange = "addItemPriceToRegister(' + item.item_id + ')"  value="0"></td>' +
                    '<td class="col-sm-1 col-md-1 text-center" ><strong data-total-price = "" id ="total-price-' + item.item_id + '" class="total-price"></strong></td>' +
                    '<td class="col-sm-1 col-md-1">' +
                    '<button type="button" class="btn btn-danger" onclick = "removeProduct(' + item.item_id + ')">' +
                    '<span class="pe-7s-trash"></span> Remove' +
                    '</button></td></tr><input type="hidden" id="cost-price-'+item.item_id+'" value="'+item.cost_price+'" >' ;

                var itemDiscounts = "";
                $(".product-descriptions").append(itemDescription);

                if(item.discountApplicable){

                    itemDiscounts+="<tr id='product-discount-"+item.item_id+"'><td colspan='5' style='padding-left:23px;font-size: 80%;background: aliceblue;'> Discount Offer: <strong>"+item.discountName+"</strong><br>";
                    itemDiscounts+="Item Discount Amount: $<strong>"+item.discountAmount+"</strong></td>";
                    $("#discount-"+item.item_id).val(Number(item.discountPercentage).toFixed(2));
                    itemDiscounts+="<input type ='hidden' id='price-rule-"+item.item_id+"' value = '"+item.item_id+"'>";
                    calculatePrice();

                    //console.log(item.discountAmount);
                }

                itemDiscounts += '</tr>';
                $(".product-descriptions").append(itemDiscounts);

                addItemPriceToRegister(item.item_id);

                $("#item-names").val("");
                $("#item-names").blur();
                $("#item-names").autocomplete("close");
            }


            if($('#sale-type').attr("data-selected-type")=="return"){
                convertToReturn();
            }
        }

        function addItemToCart(item){
        //var itemInfo = $(item).data('item-total-info');
            var index = $('.item-suggestion').index(item);
            if(itemTotalInfo[index].item_quantity<=0&&itemTotalInfo[index].product_type!=1){
                alert("Sorry, product is out of stock.");
            }
            else if(document.getElementById("product-div-"+item.getAttribute("data-id")) == null) {
                $('.no-items').remove();
                $('.add-payment').show();

                var itemDescription = '<tr class="product-specific-description" data-index="'+item.getAttribute("data-id")+'"  data-item-type="'+item.getAttribute("data-item-type")+'" id="product-div-' + item.getAttribute("data-id") + '" data-rule-id="'+itemTotalInfo[index].id+'">' +
                        '<td class="col-sm-8 col-md-6">' +
                        '<div class="media">' +
                        '' +
                        '<div class="media-body">' +
                        ' <h6 class="media-heading"><a href="#">' + item.getAttribute('data-title') + '</a></h6>';

                if(itemTotalInfo[index].company_name!=null)
                    itemDescription +=  '<h6 class="media-heading"> by <a href="#">'+ itemTotalInfo[index].company_name +'</a></h6>';

                if(item.getAttribute('data-item-quantity') != 'undefined'){
                    if(item.getAttribute('data-item-quantity')>10)
                        itemDescription += '<span>Status: </span><span class="text-success"><strong>In Stock</strong></span>';
                    else
                        itemDescription += '<span>Status: </span><span class="text-danger"><strong>Soon will be out of Stock</strong></span>';
                }


                itemDescription += '</div>' +
                '</div></td>' +
                ' <td class="col-sm-1 col-md-1" style="text-align: center">' +
                ' <input type="number" min = "0" class="form-control quantity" id="product-' + item.getAttribute('data-id') + '"  onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" onchange = "addItemPriceToRegister(' + item.getAttribute('data-id') + ')"  value="1">' +
                '</td>' +
                '<td class="col-sm-1 col-md-1 text-center" ><strong data-unit-price = "'+item.getAttribute('data-sale-price') +'" id="unit-price-' + item.getAttribute('data-id') + '">$' + item.sale_price + '</strong></td>' +
                '<td><input  class="form-control discount-amount" type="number" id="discount-'+ item.getAttribute("data-id")+'" onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" onchange = "addItemPriceToRegister(' + item.getAttribute('data-id') + ')"  value="0"></td>' +
                '<td class="col-sm-1 col-md-1 text-center" ><strong data-total-price = "" id ="total-price-' + item.getAttribute('data-id') + '" class="total-price"></strong></td>' +
                '<td class="col-sm-1 col-md-1">' +
                '<button type="button" class="btn btn-danger" onclick = "removeProduct(' + item.getAttribute('data-id') + ')">' +
                '<span class="pe-7s-trash"></span> Remove' +
                '</button></td></tr><input type="hidden" id="cost-price-'+item.getAttribute('data-id')+'" value="'+itemTotalInfo[index].cost_price+'" >' ;

                var itemDiscounts = "";
                $(".product-descriptions").append(itemDescription);

                if(itemTotalInfo[index].discountApplicable){

                    itemDiscounts+="<tr id='product-discount-"+item.getAttribute("data-id")+"'><td colspan='5' style='padding-left:23px;font-size: 80%;background: aliceblue;'> Discount Offer: <strong>"+itemTotalInfo[index].discountName+"</strong><br>";
                    itemDiscounts+="Item Discount Amount: $<strong>"+itemTotalInfo[index].discountAmount+"</strong></td>";
                    $("#discount-"+item.getAttribute('data-id')).val(Number(itemTotalInfo[index].discountPercentage).toFixed(2));
                    itemDiscounts+="<input type ='hidden' id='price-rule-"+item.getAttribute("data-id")+"' value = '"+itemTotalInfo[index].id+"'>";
                    calculatePrice();

                    //console.log(itemTotalInfo[index].discountAmount);
                }

                itemDiscounts += '</tr>';
                $(".product-descriptions").append(itemDiscounts);

                addItemPriceToRegister(item.getAttribute('data-id'));
            }

            if($('#sale-type').attr("data-selected-type")=="return"){
                convertToReturn();
            }

        }

        function setAllItemToDiscount(){

            var discountAmount = Number($("#allDiscountAmount").val());

            $( ".discount-amount" ).each(function( index ) {
               /* $( this ).val = discountAmount;*/

                   var discount_id = "#"+this.id;
                   $(discount_id).val(discountAmount);
                   var product_id = discount_id.replace(/[^0-9\.-]+/g, "");
                   product_id = product_id.substr(1,product_id.length);
                   addItemPriceToRegister(product_id);

            });

            calculatePrice();
        }


        function setSaleToDiscount(){

            var discountAmount = Number($("#saleDiscountAmount").val()).toFixed(2);;

            if(document.getElementById("product-div-discount") == null) {
                $('.no-items').remove();
                $('.add-payment').show();
                var itemDescription = '<tr class="product-specific-description" style="background: aliceblue;" data-index="0" id="product-div-discount" data-item-type="discount" data-rule-id="0">' +
                        '<td class="col-sm-8 col-md-6">' +
                        '<div class="media">' +
                        '' +
                        '<div class="media-body">' +
                        ' <h6 class="media-heading"><a href="#">Discount</a></h6>' +
                        '';


                itemDescription += '</div>' +
                        '</div></td>' +
                        ' <td class="col-sm-1 col-md-1" style="text-align: center">' +
                        ' <input type="number"  class="form-control" id="product-discount-quantity"  onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" onchange = "addDiscountPriceToRegister()"  value="-1">' +
                        '</td>' +
                        '<td class="col-sm-1 col-md-1 text-center" ><strong data-unit-price = "'+discountAmount+'" id="unit-discount-price">';

                if(discountAmount >= 0 ){
                    itemDescription += "$"+discountAmount;
                }else{
                    itemDescription += "-$"+discountAmount;
                }

                itemDescription += '</strong></td>' +
                        '<td><input disabled  class="form-control discount-amount" type="number"   value="0"></td>' +
                        '<td class="col-sm-1 col-md-1 text-center" ><strong data-total-price="" id ="total-price-discount" class="total-price"></strong></td>' +
                        '<td class="col-sm-1 col-md-1">' +
                        '<button type="button" class="btn btn-danger" onclick = "removeDiscountItem()">' +
                        '<span class="pe-7s-trash"></span> Remove' +
                        '</button></td></tr>' ;

                var itemDiscounts = "";
                $(".product-descriptions").prepend(itemDescription);

                addDiscountPriceToRegister();
            }else{

                if(discountAmount >= 0 ){
                    $("#unit-discount-price").text("$"+discountAmount);
                }else{
                    $("#unit-discount-price").text("-$"+discountAmount);
                }
                $("#unit-discount-price").attr("data-unit-price",discountAmount);

                addDiscountPriceToRegister();
            }


        }

        function removeDiscountItem(){
            $("#product-div-discount").remove();
            if (isEmpty($('.product-descriptions'))) {
                $('.product-descriptions').append('<tr class="no-items"> <td colspan="6"><div class="jumbotron text-center"> <h3>There are no items in the cart [Sales]</h3> </div></td> </tr>');/*
                 $('.no-items').css('display','content');*/
                $('.add-payment').hide();

            }
        }

        function addDiscountPriceToRegister(){

            productUniqueId = "#product-discount-quantity";
            productQuantity = Number($(productUniqueId).val());
            productUnitPriceUniqueId = "#unit-discount-price";
            productUnitPrice = Number($(productUnitPriceUniqueId).attr("data-unit-price"));
            productDiscountAmount =0 ;
            productUnitPrice = productUnitPrice-productDiscountAmount;
            var totalProductPrice = Number(productQuantity * productUnitPrice).toFixed(2);
            $("#total-price-discount").attr("data-total-price",totalProductPrice);
            if(totalProductPrice>=0)
                $("#total-price-discount").text("$" + totalProductPrice);
            else
                $("#total-price-discount").text("-$" + ((-1) * totalProductPrice));

            calculatePrice();

        }

        function selectPayment(e)
        {
            e.preventDefault();

            $("#amount_tendered").removeClass("hidden");
            $("#gift_card_number").addClass("hidden");
            $("#loyalty_card_number").addClass("hidden");
            $('#payment_types').attr("data-value",($(this).attr('data-payment')));
            // start_cc_processing
            $('.select-payment').removeClass('active');
            $(this).addClass('active');
            $("#amount_tendered").focus();
            $("#amount_tendered").attr('placeholder','');

            checkPaymentType();



        }

        function layAwaySale(){

            var total = $("#total").attr("data-total");
            var due = $("#due").attr("data-due");
            if(due<total) addPayment();
            SubmitSales(2);

        }


        function estimateSale(){
            var total = $("#total").attr("data-total");
            var due = $("#due").attr("data-due");
            if(due<total) addPayment();
            SubmitSales(3);
        }

        function  completeSales(){

            if ($("#payment_types").attr("data-value") == "Gift Card"){

                var gift_card_number = $("#gift_card_number").val();
                if(gift_card_number==""){

                    $.notify({
                        icon: '',
                        message: "Gift card number cannot be empty!"

                    },{
                        type: 'danger',
                        timer: 4000
                    });

                }else{

                    var due = $("#due").attr("data-due");
                    $.ajax({
                        url: "{{route('gift_card_use')}}",
                        type: "post",
                        data: {
                            due:due,
                            gift_card_number:gift_card_number
                        },
                        success: function(response){

                            if(response.success){

                                addGiftCardPayment(gift_card_number,response.current_value,response.value_deducted);
                                SubmitSales(1);

                            }else{
                                $.notify({
                                    icon: '',
                                    message: response.message

                                },{
                                    type: 'danger',
                                    timer: 4000
                                });
                            }

                        }
                    })


                }

            }else if ($("#payment_types").attr("data-value") == "Loyalty Card"){

                var loyalty_card_number = $("#loyalty_card_number").val();
                if(loyalty_card_number==""){

                    $.notify({
                        icon: '',
                        message: "Loyalty card number cannot be empty!"

                    },{
                        type: 'danger',
                        timer: 4000
                    });

                }else{

                    var due = $("#due").attr("data-due");
                    $.ajax({
                        url: "{{route('gift_card_use')}}",
                        type: "post",
                        data: {
                            due:due,
                            loyalty_card_number:loyalty_card_number
                        },
                        success: function(response){

                            if(response.success){

                                addGiftCardPayment(loyalty_card_number,response.current_value,response.value_deducted);
                                SubmitSales(1);

                            }else{
                                $.notify({
                                    icon: '',
                                    message: response.message

                                },{
                                    type: 'danger',
                                    timer: 4000
                                });
                            }

                        }
                    })


                }

            }

                else{
                addPayment();
                SubmitSales(1);
            }

        }

        function SubmitSales(status){

            if($(".no-items").length==0) {

                var confirmText = "Are you sure to complete transaction?";
                if(confirm(confirmText)){

                    var customerId = $("#customer").val();
                    var subTotalAmount = $(".subtotal").attr("data-subtotal");
                    var taxAmount = $("#tax").attr("data-tax");
                    var totalAmount = $("#total").attr("data-total");
                    var saleDiscountAmount = $("#saleDiscountAmount").val();
                    var due =  $("#due").attr("data-due");
                    var sale_type = "";

                    if($('#sale-type').attr("data-selected-type")=="return"){
                        sale_type = "{{ \App\Enumaration\SaleTypes::$RETURN  }}";
                    }else{
                        sale_type = "{{ \App\Enumaration\SaleTypes::$SALE  }}";
                    }

                    var totalProfit = 0;
                    var totalItemsSold = 0;

                    paymentInfos = [];

                    $( ".payment-log" ).each(function( index ) {
                        var index = $(this).attr("data-id");
                        var paidAmount = $("#tendered-amount-"+index).attr("data-payment-amount");
                        var paymentType = $("#payment-type-"+index).text();

                        var paymentInfo = {
                            payment_type: paymentType,
                            paid_amount: paidAmount
                        };
                        paymentInfos.push(paymentInfo);
                    });


                    productInfos = [];

                    $( ".product-specific-description" ).each(function( index ) {

                        var itemId = $(this).attr("data-index");
                        var itemType = $(this).attr("data-item-type");
                        var itemRuleId = $(this).attr("data-rule-id");

                        if(itemId==0){

                            var currentQuantity = $("#product-discount-quantity").val();
                            var currentCostPrice =  $("#unit-discount-price").attr("data-unit-price");
                            var currentUnitPrice = $("#unit-discount-price").attr("data-unit-price");
                            var currentDiscountPercentage = 0;
                            var currentTotal = $("#total-price-discount").attr("data-total-price");


                            var productInfo = {
                                item_id:itemId,
                                quantity: currentQuantity,
                                item_type: itemType,
                                cost_price: currentCostPrice,
                                unit_price: currentUnitPrice,
                                item_discount_percentage: currentDiscountPercentage,
                                total_price: currentTotal,
                                price_rule_id: itemRuleId

                            };
                            productInfos.push(productInfo);


                        }else {

                            var currentQuantity = $("#product-" + itemId).val();
                            var currentCostPrice = $("#cost-price-" + itemId).val();
                            var currentUnitPrice = $("#unit-price-" + itemId).attr("data-unit-price");
                            var currentDiscountPercentage = $("#discount-" + itemId).val();
                            var currentTotal = $("#total-price-" + itemId).attr("data-total-price");
                            percentage = (currentDiscountPercentage / 100);
                            var discountAmount = (currentUnitPrice * currentQuantity) - currentTotal;
                            var salesDiscountAmount = $("#saleDiscountAmount").val();

                            if (salesDiscountAmount > 0)
                            {
                              var preSubtotal = Number(subTotalAmount) + Number(salesDiscountAmount);
                              var itemPortionOfSaleDiscount = ((currentTotal/preSubtotal) *  salesDiscountAmount);
                              discountAmount += itemPortionOfSaleDiscount;
                            }
                            var itemProfit = ((currentUnitPrice * currentQuantity) - discountAmount) - (currentCostPrice*currentQuantity);

                            totalProfit += itemProfit;
                            totalItemsSold+=currentQuantity;

                            var productInfo = {
                                item_id:itemId,
                                quantity: currentQuantity,
                                cost_price: currentCostPrice,
                                unit_price: currentUnitPrice,
                                item_type: itemType,
                                item_discount_percentage: currentDiscountPercentage,
                                total_price: currentTotal,
                                discount_amount: discountAmount,
                                price_rule_id: itemRuleId,
                                sale_discount_amount: salesDiscountAmount,
                                item_profit: itemProfit,
                                tax_rate: "{{ $tax_rate }}",
                                tax_amount: currentTotal*taxRate
                            };
                            productInfos.push(productInfo);

                        }
                    });

                    console.log(productInfos);


                    var saleInfo = {
                        subtotal: subTotalAmount,
                        tax: taxAmount,
                        total: totalAmount,
                        discount:saleDiscountAmount,
                        customer_id: customerId,
                        due: due,
                        status: status,
                        profit: totalProfit,
                        items_sold: totalItemsSold,
                        sale_type:sale_type
                    };

                    $.ajax({
                        url: "{{route('new_sale')}}",
                        type: "post",
                        data: {
                            sale_info: saleInfo,
                            product_infos: productInfos,
                            payment_infos: paymentInfos
                        },
                        success: function(response){
                            var sale_id = response;

                            var url = '{{ route("sale_receipt", ":sale_id") }}';
                            url = url.replace(':sale_id', sale_id);
                            window.location.href=url;

                        }
                    })
                }

            }

        }

        function isEmpty( el ){
            return !$.trim(el.html())
        }

        function addPayment(){
            var paymentType = $("#payment_types").attr("data-value");
            var tenderedAmount = $("#amount_tendered").val();
            $(".payment-history").append("<div class='card payment-log' data-id='"+paymentAdded+"' id='payment-"+paymentAdded+"'  style='margin: 10px'><span class='pe-7s-close-circle' onclick='deletePayment("+paymentAdded+")' style='float:left'></span> <p id='payment-type-"+paymentAdded+"' style='float:left'>"+paymentType+"</p><p id='tendered-amount-"+paymentAdded+"' style='float:right' data-payment-amount='"+tenderedAmount+"'>$"+tenderedAmount+"</p><br></div>")
            paymentAdded++;
            calculateDue();
        }

        function addGiftCardPayment(gift_card_number, balance, paidAmount){

            var paymentType = $("#payment_types").attr("data-value");
            var tenderedAmount = paidAmount;
            $(".payment-history").append("<div class='card payment-log' data-id='"+paymentAdded+"' id='payment-"+paymentAdded+"'  " +
                    "style='margin: 10px'><span class='pe-7s-close-circle' onclick='deletePayment("+paymentAdded+")'" +
                    " style='float:left'>" +
                    "</span> <p id='payment-type-"+paymentAdded+"' style='float:left'>"
                    +paymentType+":"+ gift_card_number +" (Balance: $"+ balance +") </p><p id='tendered-amount-"+paymentAdded+"' style='float:right' data-payment-amount='"
                    +tenderedAmount+"'>$"+tenderedAmount+"</p><br></div>");
            paymentAdded++;
            calculateDue();
        }

        function deletePayment(id){
            $("#payment-"+id).remove();
            calculatePrice();
        }

        function lookUpReceipt(){
            var sale_id = $("#receipt-id").val();
            var url = '{{ route("sale_receipt", ":sale_id") }}';
            url = url.replace(':sale_id', sale_id);
            window.location.href=url;
        }

        function calculateDue(){


            var total = Number($("#total").attr("data-total")).toFixed(2);
            var due = Number($("#due").attr("data-due")).toFixed(2);


            var addPaymentBtn = $("#add_payment_button");
            var completeSaleBtn = $("#finish_sale_alternate_button");

            var totalPaidAmount = 0;
            $( ".payment-log" ).each(function( index ) {
                var index = $(this).attr("data-id");
                var paymentId = "#tendered-amount-"+index;
                var paidAmountText = $(paymentId).attr("data-payment-amount");
                var paidAmount = Number(paidAmountText).toFixed(2);
                var paidAmount = Number(paidAmount);

                totalPaidAmount = +totalPaidAmount + paidAmount;

            });

            totalPaidAmount = Number(totalPaidAmount.toFixed(2));
            due = Number(total-totalPaidAmount).toFixed(2);
            if(due>=0)
            {
                /*$("#amount_tendered").val(due);*/
                $("#due").text("$"+due);
                $("#due").attr("data-due",due);
            } else{
               /* $("#amount_tendered").val(due);*/
                $("#due").text("-$"+ (-1)*due);
                $("#due").attr("data-due",due);
            }

            var tenderedAmount = Number(Number($("#amount_tendered").val()).toFixed(2));

            if((tenderedAmount+totalPaidAmount-total)>=0){

                addPaymentBtn.addClass('hidden');
                completeSaleBtn.removeClass('hidden');

            }else{

                completeSaleBtn.addClass('hidden');
                addPaymentBtn.removeClass('hidden');

            }


        }


        function checkPaymentType()
        {

            if ($("#payment_types").attr("data-value") == "Gift Card")
            {
                /*$("#amount_tendered").val('');
                $("#amount_tendered").attr('placeholder',"Swipe\/Type gift card #");*/
                $("#amount_tendered").addClass("hidden");
                $("#gift_card_number").removeClass("hidden");
                $("#gift_card_number").attr('placeholder',"Swipe\/Type gift card #");
                $("#gift_card_number").focus();

            }else if ($("#payment_types").attr("data-value") == "Loyalty Card")
            {
                /*$("#amount_tendered").val('');
                $("#amount_tendered").attr('placeholder',"Swipe\/Type gift card #");*/
                $("#amount_tendered").addClass("hidden");
                $("#loyalty_card_number").removeClass("hidden");
                $("#loyalty_card_number").attr('placeholder',"Swipe\/Type loyalty card #");
                $("#loyalty_card_number").focus();

            }
        }

        function selectCounter(){

           @if(\Illuminate\Support\Facades\Cookie::get('counter_id')==null)
                $("#choose_counter_modal").modal();
                $.ajax({
                    url: "{{route('counter_list_ajax')}}",
                    type:"get",
                    dataType: "json",
                    success: function(response){
                        $(".choose-counter-home").html("");
                        counters = response.counters;
                        counters.forEach(function(counter){
                            var url = '{{ route("counter_set", ":counter_id") }}';
                            url = url.replace(':counter_id', counter.id);
                            $(".choose-counter-home").append('<li><a class="set_employee_current_counter_after_login" href="'+url+'">'+counter.name+'</a></li>');
                        });
                    },
                    error: function () {

                    }
                })
           @endif
        }


        function changeCounter(){

            $("#choose_counter_modal").modal();
            $.ajax({
                url: "{{route('counter_list_ajax')}}",
                type:"get",
                dataType: "json",
                success: function(response){
                    $(".choose-counter-home").html("");
                    counters = response.counters;
                    counters.forEach(function(counter){
                       var url = '{{ route("counter_set", ":counter_id") }}';
                        url = url.replace(':counter_id', counter.id);
                        $(".choose-counter-home").append('<li><a class="set_employee_current_counter_after_login" href="'+url+'">'+counter.name+'</a></li>');
                    });
                },
                error: function () {

                }
            })
        }

    </script>


@stop