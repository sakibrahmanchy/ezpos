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
	<style>
		*{
			font-weight: bold!important;
		}
		 .input-group {
            padding-left:0px
        }
        .card{
            margin-top:0px;
            margin-bottom: 10px;
        }
		.dropdown-menu > li > a
		{
			color: #000!important;
			font-weight: bold;
		}
		.btn
		{
			font-weight: bold;
		}
	</style>
	{{--Sale config--}}
    <?php $tax_rate = $settings['tax_rate'] ; ?>
	{{--Sale config--}}

	<div id="app" class="row">
		<new_sale></new_sale>
	</div>


	<!-- Look up receipt Modal -->
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


	<style>
		.autocomplete-results {
			position: absolute;
			z-index: 1000;
			margin: 0;
			margin-top: 34px;
			padding: 0;

			border: 1px solid #eee;
			list-style: none;
			border-radius: 4px;
			background-color: #fff;
			box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
		}

		.autocomplete-result {
			list-style: none;
			text-align: left;
			padding: 4px 2px;
			cursor: pointer;
		}

		.autocomplete-result.is-active,.autocomplete-result.is-active a{
			background-color: #3c8dbc;
			color: #fff;
		}

		.dropdown-menu > li > a
		{
			color: #000!important;
			font-weight: bold;
		}
		
	</style>

	<script src="{{asset("js/vue/vue.min.js")}}"></script>
	<script src="{{asset("js/axios/axios.min.js")}}"></script>
	<script src="{{asset("js/lodash/lodash.min.js")}}"></script>
	<!-- Vue Components ---->
	@include('vue_components')

	<script>
        /**/
        //Grid component
        /**/
        /********autocomplete starts*******/

        $(document).ready(function(e){
            $("body").addClass("sidebar-collapse");
            @if(!\Illuminate\Support\Facades\Cookie::get('counter_id'))
            changeCounter();
            @endif

        });

		Vue.component('new_sale',
        {
            template:  `<div>
					<file_explorer @choose-item="ChooseItem" :shown="shown"></file_explorer>
					<div class="col-sm-7" >
						<div class = "search section">
							<div class="input-group">
								<a href="{{route('new_item')}}" target="_blank" class="input-group-addon" id="sizing-addon2" style="background-color:#337ab7;color:white;border:solid #337ab7 1px; "><strong>+</strong></a>
								<auto-complete @set-autocomplete-result="setAutoCompleteResult" :auto-select="auto_select"></auto-complete>

								<div class="input-group-btn bs-dropdown-to-select-group">
									<button type="button" class="btn btn-success dropdown-toggle as-is bs-dropdown-to-select" data-toggle="dropdown">
										<span id="bs-drp-sel-label" data-bind="bs-drp-sel-label"><i class="fa fa-shopping-cart" style="margin-right: 5px"></i>Sale</span>
										<span class="caret"></span>
										<span class="sr-only">Toggle Dropdown</span>
									</button>
									<ul class="dropdown-menu" role="menu" style="" >
										<li data-value="1"><a @click="convertToSale()"  href="#">Sale</a></li>
										<li data-value="2"><a @click="convertToReturn()" href="#">Return</a></li>{{--
										<li data-value="3"><a href="#">Store Account Payment</a></li>--}}
									</ul>
									<button class="btn btn-success" @click="shown = !shown" ><i class="fa fa-th" style="margin-right: 5px"></i>  Show Grid</button>
								</div>
							</div>



							<input type="checkbox" checked  id="auto_select" v-model="auto_select"> <b>Add automatically to cart when item found.</b>

						</div>

						<br>

						<div class="card table-responsive" >
							<table class="table table-hover table-responsive">
								<thead>
								<tr>
									<th>Product</th>
									<th>&nbsp;&nbsp;&nbsp;&nbsp;Qty&nbsp;&nbsp;&nbsp;&nbsp;</th>
									<th class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;Price&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
									<th class="text-center">Disc%</th>
									<th class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
									<th>Del</th>
								</tr>
								</thead>
								<tbody  v-for="(anItem,index) in itemList" class = "product-descriptions">

									<tr class="product-specific-description">
										<td class="col-sm-8 col-md-6">
											<div class="media">
												<div class="media-body">
													<h6 class="media-heading"><a href="#">@{{itemList[index].item_name}}</a></h6>
													<h6 v-if="itemList[index].company_name" class="media-heading">
														by <a href="#">@{{itemList[index].company_name}}</a></h6>
													<span>Status: </span>
													<span v-if="itemList[index].item_quantity>10" class="text-success"><strong>In Stock</strong>
														</span>
													<span v-else-if="itemList[index].item_quantity<=0" class="text-success"><strong>Out of Stock</strong>
														</span>
													<span v-else class="text-warning"><strong>Soon will be out of Stock </strong>
														</span>
												</div>
											</div>
										</td>
										<td class="col-sm-1 col-md-1" style="text-align: center">
											<input min="0" class="form-control quantity" value="1" v-model="itemList[index].items_sold">
										</td>
										<td class="col-sm-1 col-md-1 text-center">
											<inline-edit v-model="itemList[index].unit_price" if-user-permitted="{{UserHasPermission("edit_sale_cost_price")}}" ></inline-edit>
										</td>
										<td>
											<input class="form-control discount-amount" v-model="itemList[index].item_discount_percentage">
										</td>
										<td class="col-sm-1 col-md-1 text-center">
											<strong class="total-price">
												<currency-input currency-symbol="$" :value="GetLineTotal(index)"></currency-input>
											</strong>
										</td>
										<td class="col-sm-1 col-md-1">
											<button type="button" class="btn btn-danger" @click="Remove(itemList[index].item_id)"><span class="pe-7s-trash"></span></button>
										</td>
									</tr>

									<tr v-if="itemList[index].discountApplicable" >
										<td  colspan='5' style='padding-left:23px;font-size: 80%;background: aliceblue;'>
											Discount Offer:
											<strong>@{{itemList[index].discount_name}}</strong><br>
											Item Discount Amount: $<strong>@{{itemList[index].discount_amount}}</strong>
										</td>
									</tr>
								</tbody>
								<tbody v-if="itemList.length<=0" class="no-items"> <td colspan="6"><div class="jumbotron text-center"> <h3>There are no items in the cart [Sales]</h3> </div></td> </tbody>
								<tfoot>
								</tfoot>
							</table>
						</div>

					</div>
					<div class ="col-sm-4">
						<div class="form-group">
							<div class="row">
								<div class = "card">

									<div class="sale-buttons input-group" style = "border-bottom:solid #ddd 1px; padding:10px;max-width: 100%;display: inline-block;">
										<div class="btn-group input-group-btn">
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
												<strong>Options</strong>
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
												<li><a href="{{ route('customer_balance_add') }}">Add Customer Balance</a></li>
												<li><a href="{{ route('close_cash_register') }}">Close register</a></li>
											</ul>
											<form action="" id="cancel_sale_form" autocomplete="off" method="post" accept-charset="utf-8">

												<div class="btn-group input-group-btn"  >
													<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
														<i class="ion-pause"></i>
														Suspend Sale								</button>
													<ul class="dropdown-menu sales-dropdown" id = "sale-type" data-selected-type="sale" role="menu">
														<li><a href="#" @click = "layAwaySale()" id="layaway_sale_button"><i class="ion-pause"></i> Charge Account </a></li>
														<li><a href="#" @click = "estimateSale()" id="estimate_sale_button"><i class="ion-help-circled"></i> Estimate</a></li>

													</ul>
												</div>
												<a href="" class="btn btn-danger input-group-addon" id="cancel_sale_button">
													<i class="ion-close-circled"></i>
													Cancel Sale				</a>

											</form>
										</div>
									</div>
									{{-- If customer is added to the sale --}}

									<div class="customer-form">

										{{-- if the customer is not set , show customer adding form --}}
										<form action="" id="select_customer_form" autocomplete="off" class="form-inline" method="post" accept-charset="utf-8">
											<div class="input-group contacts" style="padding-top:10px;padding-left:10px">

												<a href="{{route('new_customer')}}" target="_blank" class="input-group-addon" id="sizing-addon2" style="background-color:#337ab7;color:white;border:solid #337ab7 1px; "><strong>+</strong></a>
												<select2 v-model="customer_id">
													<option value ="0" selected>Select Customer for sale</option>
													@foreach($customerList as $aCustomer)
														<option value = "{{$aCustomer->id}}">{{$aCustomer->first_name}} {{$aCustomer->last_name}}</option>
													@endforeach
												</select2>
											</div>
										</form>

									</div>
								</div></div>

							<div class="row"><div class = "card" >
									<h4 class="text-center"><strong>Receipt</strong></h4>
									<hr>
									<div class="card">
										<strong>Subtotal</strong> <span style="float: right"><strong data-subtotal="0" class="subtotal"><currency-input currency-symbol="$" :value="GetSubtotal"></currency-input></strong></span><br>
										<strong>+Tax({{ $tax_rate }}%)</strong><span style="float: right"><strong data-tax="0" id="tax"><currency-input currency-symbol="$" :value="GetTax"></currency-input></strong></span><br>
										<strong>Discount all items by percent</strong><span style="float: right"><strong id=""><input id ="allDiscountAmount" type ="number" v-model="allDiscountAmountPercentage" style="max-width:45px;float: right"></strong></span><br><br>

										<strong>Discount entire sale</strong><span style="float: right"><strong id=""><input id ="saleFlatDiscountAmount" style="max-width:45px;float: right" v-model="saleFlatDiscountAmount"></strong></span>
									</div>

									<div class = "card" style="background-color: #778a9b;color:whitesmoke;font-size:20px;">
										Total <span style="float: right"><strong data-total="0" id = "total"> <currency-input currency-symbol="$" :value="GetTotalSale"></currency-input></strong></span>
									</div>
									<div class = "card" style="background-color: #778a9b;color:whitesmoke;font-size:20px;">
										Due <span style="float: right"><strong data-due="0" id = "due"> <currency-input currency-symbol="$" :value="GetDue"></currency-input></strong></span>
									</div><br>
									<div class="row">
										{{--<input type="number" id = "paid-amount" name="paid-amount" class="col-md-8 form-control" style="float:left">
										<button type="button" class="col-md-4 btn btn-success" style="float:right" onclick = "SubmitSales()">
											Checkout <span class="pe-7s-cart"></span>
										</button><br><br>--}}


										<div class="add-payment">

											<div class="payment-history">
												<div v-for="(aPayment, index) in paymentList" class="card payment-log" style="margin: 10px">
													<span class="pe-7s-close-circle" style="float:left" @click="RemovePayment(index)"></span>
													<p style="float:left">@{{aPayment.payment_type}}</p>
													<p style="float:right"><currency-input currency-symbol="$" :value="aPayment.paid_amount"></currency-input></p>
													<br />
												</div>
											</div>
											<div style="padding:20px">

												<div class="side-heading">Add Payment</div>

												<a tabindex="-1" v-for="aPaymentType in paymentTypeList" href="javascript: void(0);" :class="GetPaymentButtonClass(aPaymentType)" @click="SetActivePaymentType(aPaymentType)" >
													@{{aPaymentType}}</a>

											</div>

											<div v-show="activePaymentType=='Gift Card'" style="padding:20px" class="input-group">
												<label>Gift Card Number</label>
												<input class="form-control" type="text" name="gift_card_number"  id="gift_card_number" class="add-input numKeyboard form-control" v-model="gift_card_number" />
											</div>
											<div v-show="activePaymentType=='Loyalty Card'" style="padding:20px" class="input-group">
												<label>Loyalty Card Number</label>
												<input class="form-control" type="text" name="loyalty_card_number"  id="loyalty_card_number" class="add-input numKeyboard form-control" v-model="loyalty_card_number"/>
											</div>

											<div class="input-group add-payment-form" style="padding:20px">
												<input type="number" name="amount_tendered" value="0.00" id="amount_tendered" class="add-input numKeyboard form-control" v-model="amountTendered">
												<span class="input-group-addon" style="background: #5cb85c; border-color: #4cae4c;color:white; cursor: pointer;" @click = "CompleteSales()">
													Complete Sale
													<!--<a href="javascript:void(0)" id="add_payment_button" onclick = "addPayment()" style=" color:white;text-decoration:none;">Add Payment</a>
													<a class="javascript:void(0)" id="finish_sale_alternate_button" style=" color:white;text-decoration:none;">Complete Sale</a>-->
												</span>


											</div>

											<div style="padding:20px">
												<div class="side-heading">Comments</div>
												<input type="text" name="comment" id="comment" class="form-control" />
											</div>

										</div>
									</div>

									<form id = "saleSubmit" method = "post" action = "{{route('new_sale')}}">

										<input type="hidden" name="_token" value="{{ csrf_token() }}">
									</form>
								</div></div>

						</div>
					</div>
			</div>`,
			data: function(){
				return {
					itemList: [],
					auto_select: true,
					shown: false,
					customer_id: 0,
					options: [],
					tax:  {{$tax_rate}},
					negativeInventory: {{$settings['negative_inventory']}},
					allDiscountAmountPercentage: 0,
					saleFlatDiscountAmount: 0,
					activePaymentType: "Cash",
					paymentList: [],
					paymentTypeList: ['Cash', 'Check','Debit Card', 'Credit Card', 'Gift Card', 'Loyalty Card'],
					amountTendered: 0.0,
					gift_card_number: "",
					loyalty_card_number: "",
					sale_type: 1,
					flatDiscountApplied: false
				}
			},
			methods:
                {
                    setAutoCompleteResult: function(selectedItem)
                    {
                        if( selectedItem.quantity<=0 && !this.negativeInventory )
                        {
                            alert("Sorry, product is out of stock.");
                            return;
                        }
						
						
                        var found = false;
                        for(var index=0;index<this.itemList.length; index++)
                        {
                            if( this.itemList[index].item_id==selectedItem.item_id)
                            {
								if( !selectedItem.useScanPrice 
									|| ( selectedItem.useScanPrice && selectedItem.new_price==this.itemList[index].unit_price ) )
								{
									found = true;
									this.itemList[index].items_sold++;
								}
                            }
                        }
                        if(found)
                            return;

                        var that = this

                        this.GetItemPrice(selectedItem.item_id)
                            .then(function (response) {
                                let sale_type =  $("#sale-type").attr("data-selected-type");
                                let items_sold = ( sale_type == "sale") ? 1 : -1;

                                var itemDetails = {
                                    item_id : selectedItem.item_id,
                                    item_name : selectedItem.item_name,
                                    company_name : selectedItem.company_name,
                                    items_sold : selectedItem.items_sold,
                                    unit_price : response.data.price,
                                    cost_price: selectedItem.cost_price,
                                    items_sold : items_sold,
                                    price_rule_id: selectedItem.price_rule_id

                                };

                                if(selectedItem.useScanPrice)
                                   itemDetails.unit_price = selectedItem.new_price;

                                if(selectedItem.discountApplicable)
                                {
                                    itemDetails.discountApplicable = true;
                                    if(this.allDiscountAmountPercentage === 0||this.allDiscountAmountPercentage === undefined){
                                        itemDetails.item_discount_percentage = selectedItem.discountPercentage;
                                        itemDetails.discount_amount = selectedItem.discountAmount.toFixed(2);
                                        itemDetails.discount_name = selectedItem.discountName;
                                    }
                                    else
                                        itemDetails.item_discount_percentage = this.allDiscountAmountPercentage;

                                }
                                else
                                {
                                    itemDetails.discountApplicable = false;
                                    itemDetails.item_discount_percentage = 0;
                                }
                                that.itemList.push(itemDetails);
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    },
                    GetItemPrice: function(itemId){
                        return axios.post("{{route('item_price')}}",
                            {
                                item_id: itemId,
                                customer_id: this.customer_id
                            })
                    },
                    Remove: function(itemId){
                        for(var index=0;index<this.itemList.length;index++)
                        {
                            if(this.itemList[index].item_id == itemId)
                            {
                                if(itemId==0)
                                    this.saleFlatDiscountAmount = 0;
                                this.itemList.splice(index, 1);
                            }
                        }
                    },
                    GetLineTotal: function(index)
                    {

                        if(this.itemList[index].item_id==0)
                            return this.itemList[index].items_sold * this.itemList[index].unit_price ;
                        return this.itemList[index].items_sold * this.itemList[index].unit_price * (100 -  this.itemList[index].item_discount_percentage)/100;
                    },
                    SetActivePaymentType: function(activePaymentType)
                    {
                        this.activePaymentType = activePaymentType;
                    },
                    GetPaymentButtonClass(payment_type)
                    {
                        return {
                            btn: true,
                            'btn-pay': true,
                            'select-payment': true,
                            active: payment_type==this.activePaymentType
                        }
                    },
                    layAwaySale: function () {
                        let total = this.GetTotalSale;
                        let due = this.GetDue;
                        //if(due>0) this.CompleteSales();
						if(!this.customer_id){
							alert("Please choose a customer id");
							return;
						}
						//for charged account there will be no payment
						//this.paymentList.splice(0,this.paymentList.length);
                        this.SubmitSales(2);
                    },
                    estimateSale: function () {
                        let total = this.GetTotalSale;
                        let due = this.GetDue;
                        if(due>0) this.CompleteSales();
                        this.SubmitSales(3);
                    },
                    CompleteSales: function(){
						if( this.amountTendered>0 )
						{
							if( this.activePaymentType == 'Cash' || this.activePaymentType == 'Check' || this.activePaymentType == 'Debit Card' || this.activePaymentType == 'Credit Card' )
							{

								var aPaymentItem = {
									paid_amount: this.amountTendered,
									payment_type: this.activePaymentType
								}
								this.paymentList.push(aPaymentItem);
                                this.SubmitSales(1);

							}else if(this.activePaymentType=='Gift Card') {
								this.ValidateGiftCard();
							}
							else if(this.activePaymentType=="Loyalty Card"){
								this.ValidateLoyalty();
							}
						} else {
                            this.SubmitSales(1);
						}
                    },
                    ChooseItem: function(product) {

                        var found = false;
                        for(var index=0;index<this.itemList.length; index++)
                        {
                            if(this.itemList[index].item_id==product.item_id)
                            {
                                found = true;
                                this.itemList[index].items_sold++;
                            }
                        }

                        if(found)
                            return;

                        this.itemList.push(product);

                    },
                    SubmitSales: function (status) {
                        let customerId = this.customer_id;
                        let subTotalAmount = this.GetSubtotal;
                        let taxAmount = this.GetTax;
                        let totalAmount = this.GetTotalSale;
                        let saleDiscountAmount = this.saleFlatDiscountAmount;
                        let due =  this.GetDue;
                        let sale_type = "";
                        let comment = $("#comment").val();

                        if(this.itemList.length>0) {
                            if(status==1)
                                confirmText = "Are you sure to complete transaction?";
                            else
                                confirmText = "Are you sure to suspend sale?";

                            if(due>0&&customerId!=0)
                                confirmText = "Are you sure to leave due for this customer?";

                            if( confirm(confirmText)) {

                                if ($('#sale-type').attr("data-selected-type") == "return") {
                                    sale_type = "{{ \App\Enumaration\SaleTypes::$RETURN  }}";
                                } else {
                                    sale_type = "{{ \App\Enumaration\SaleTypes::$SALE  }}";
                                }

                                var totalProfit = 0;
                                var totalItemsSold = 0;
                                console.log(this.paymentList.length);
                                paymentInfos = [];
                                $.map(this.paymentList, function(value, index) {
                                    var paymentInfo = {
                                        payment_type: value.payment_type,
                                        paid_amount: value.paid_amount
                                    };
                                    paymentInfos.push(paymentInfo);
                                });

                                productInfos = [];
                                that = this;
                                let totalDiscount = 0;
                                $.map(this.itemList, function(item, index) {

                                    let itemType = "item";

                                    if(item.item_id==0) {

                                        var productInfo = {
                                            item_id:item.item_id,
                                            quantity: item.items_sold,
                                            item_type: itemType,
                                            cost_price: item.cost_price,
                                            unit_price: item.unit_price,
                                            item_discount_percentage: 0,
                                            total_price: item.unit_price * item.items_sold,
                                            price_rule_id: 0
                                        };

                                        productInfos.push(productInfo);
                                    }
                                    else{

                                        var currentQuantity = item.items_sold;
                                        var currentCostPrice = item.cost_price;
                                        var currentUnitPrice = item.unit_price;
                                        var currentDiscountPercentage = item.item_discount_percentage;
                                        var currentTotal = that.GetLineTotal(index);
                                        var isPriceScannedFromBarcode = item.is_price_taken_from_barcode;
                                        var scanStatus = (isPriceScannedFromBarcode == 'true' ? 1 : 0);

                                        percentage = (currentDiscountPercentage / 100);
                                        var discountAmount = (currentUnitPrice * currentQuantity) - currentTotal;
                                        var salesDiscountAmount = that.saleFlatDiscountAmount;

                                        if (salesDiscountAmount > 0)
                                        {
                                            q
                                        }
                                        var itemProfit = ((currentUnitPrice * currentQuantity) - discountAmount) - (currentCostPrice*currentQuantity);

                                        totalProfit += itemProfit;
                                        totalItemsSold+=currentQuantity;
                                        totalDiscount += discountAmount;

                                        var productInfo = {
                                            item_id:item.item_id,
                                            quantity: currentQuantity,
                                            cost_price: currentCostPrice,
                                            unit_price: currentUnitPrice,
                                            item_type: itemType,
                                            item_discount_percentage: currentDiscountPercentage,
                                            total_price: currentTotal,
                                            discount_amount: discountAmount,
                                            price_rule_id: item.price_rule_id,
                                            sale_discount_amount: salesDiscountAmount,
                                            item_profit: itemProfit,
                                            tax_rate: "{{ $tax_rate }}",
                                            tax_amount: taxAmount,
                                            is_price_taken_from_barcode: scanStatus
                                        };
                                        productInfos.push(productInfo);
                                    }
                                });

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
                                    sale_type:sale_type,
                                    comment: comment,
                                    total_sales_discount: totalDiscount
                                };

                                axios.post("{{route('new_sale')}}",
                                    {
                                        sale_info: saleInfo,
                                        product_infos: productInfos,
                                        payment_infos: paymentInfos
                                    }).then(function (response) {

                                    var sale_id = response.data;

                                    switch (status){
                                        case 1:
                                        case 2:
											var url = '{{ route("sale_receipt", ":sale_id") }}';
                                            url = url.replace(':sale_id', sale_id);
                                            window.location.href=url;
                                            break;
                                        case 3:
                                            var url = '{{ route("new_sale") }}';
                                            window.location.href=url;
                                            break;

                                    }

                                })
                                    .catch(function (error) {
                                        console.log(error);
                                    });

                            }
                        }
                    },
                    ValidateGiftCard: function()
                    {
                        var that = this;
                        axios.post("{{route('gift_card_use')}}",
                            {
                                due: that.amountTendered,
                                gift_card_number: that.gift_card_number
                            }).then(function (response) {
                            if(response.data.success){
                                var aPaymentItem = {
                                    paid_amount: response.data.value_deducted,
                                    payment_type: 'Gift Card'
                                }
                                that.paymentList.push(aPaymentItem);
                                this.SubmitSales(1);
                            }else{
                                $.notify({
                                    icon: '',
                                    message: response.data.message

                                },{
                                    type: 'danger',
                                    timer: 4000
                                });
                            }
                        })
                            .catch(function (error) {
                                console.log(error);
                            });
                    },
                    ValidateLoyalty: function()
                    {
                        var that = this;
                        axios.post("{{route('loyalty_card_use')}}",
                            {
                                due: that.amountTendered,
                                loyalty_card_number: that.loyalty_card_number
                            }).then(function (response) {
                            if(response.data.success){
                                that.customer_id = response.data.customer_id;
                                var aPaymentItem = {
                                    paid_amount: response.data.balance_deducted,
                                    payment_type: 'Loyalty Card: '+that.loyalty_card_number+" ( Balance: "+response.data.current_balance+")"
                                }
                                that.paymentList.push(aPaymentItem);
                                this.SubmitSales(1);
                            }else{
                                $.notify({
                                    icon: '',
                                    message: response.data.message
                                },{
                                    type: 'danger',
                                    timer: 4000
                                });
                            }
                        })
						.catch(function (error) {
							console.log(error);
						});
                    },
                    RemovePayment(index)
                    {
                        this.paymentList.splice(index, 1);
                    },
                    convertToSale: function() {
                        $('#bs-drp-sel-label').text("Sale");
                        $("#sale-type").attr("data-selected-type", "sale");
                        this.itemList.forEach(function(item){
                            if(item.items_sold<0)
                                item.items_sold = (-1) * item.items_sold;
                        });

                    },
                    convertToReturn: function() {
                        $('#bs-drp-sel-label').text("Return");
                        $("#sale-type").attr("data-selected-type", "return");
                        this.itemList.forEach(function(item){
                            if(item.items_sold>0)
                                item.items_sold = (-1) * item.items_sold;
                        });
                    }
                },
            watch:{
                customer_id: function (newVal, oldValue) {
                    if(newVal==oldValue || newVal=="" || this.itemList.length<=0)
                        return;
                    var that = this;
                    this.itemList.forEach(function(anItem) {
                        that.GetItemPrice(anItem.item_id)
                            .then(function (response) {
                                anItem.unit_price =response.data.price;
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    });
                },
                allDiscountAmountPercentage: function (newVal, oldValue){
                    if(newVal==oldValue || newVal==0)
                        return;
                    this.itemList.forEach(function(anItem){
                        anItem.item_discount_percentage = newVal;
                    })

                },
                saleFlatDiscountAmount: function (newVal, oldValue){
                    if(newVal==oldValue || newVal==0)
                        return;

                    let obj = this.itemList.find(function (obj) { return obj.item_id === 0; });
                    if(obj==undefined) {
                        let itemDetails = {
                            item_id : 0,
                            item_name : "Discount",
                            company_name : "",
                            items_sold : 1,
                            unit_price : (-1) * newVal,
                            cost_price: (-1) * newVal,
                            items_sold : 1,
                        };
                        this.itemList.push(itemDetails);
                    }else{
                        obj.unit_price = (-1) * newVal;
                        obj.cost_price = (-1) * newVal;
                    }
                }

            },
            computed:{
                GetSubtotal()
                {
                    var subtotal = 0;
                    for(var index=0;index<this.itemList.length;index++)
                    {
                        subtotal += this.GetLineTotal(index);
                    }
                    return subtotal;
                },
                GetTax()
                {
                    return  this.tax * this.GetSubtotal / 100;
                },
                GetTotalSale()
                {
                    return this.GetSubtotal + this.GetTax ;
                },
                GetDue()
                {
                    var totalTendered = 0;
                    for(var index=0;index<this.paymentList.length; index++)
                    {
                        totalTendered += Number(this.paymentList[index].paid_amount);
                    }
                    var due = this.GetTotalSale - totalTendered;
                    due = due.toFixed(2);
                    if(due>0)
                        this.amountTendered = due;
                    else
                        this.amountTendered = 0;
                    return due;
                }
            },
            created: function(){
            },
            mounted() {
                document.getElementById("item-names").focus();
            }
		});

        var app = new Vue({
            el: '#app'
        });

        function lookUpReceipt(){
            var sale_id = $("#receipt-id").val();
            var url = '{{ route("sale_receipt", ":sale_id") }}';
            url = url.replace(':sale_id', sale_id);
            window.location.href=url;
        }


	</script>
@stop