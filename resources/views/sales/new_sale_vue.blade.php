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
        .input-group {
            padding-left:0px
        }
        .card{
            margin-top:0px;
            margin-bottom: 10px;
        }
    </style>
    {{--Sale config--}}
    <?php $tax_rate = $settings['tax_rate'] ; ?>
    {{--Sale config--}}

    <div id="app" class="row">
        <div class="col-sm-7" >
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
                <input type="checkbox" checked  id = "auto_select"> <b>Add automatically to cart when item found.</b>

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
						<tr v-for="anItem in itemList" class="product-specific-description">
							<td class="col-sm-8 col-md-6">
								<div class="media">
									<div class="media-body"> 
										<h6 class="media-heading"><a href="#">@{{anItem.item_name}}</a></h6>
										<h6 v-if="anItem.company_name" class="media-heading">
										by <a href="#">@{{anItem.company_name}}</a></h6>
										<span>Status: </span>
										<span v-if="anItem.item_quantity>10" class="text-success"><strong>In Stock</strong>
										</span>
										<span v-else-if="anItem.item_quantity<=0" class="text-success"><strong>Out of Stock</strong>
										</span>
										<span v-else class="text-success"><strong>Soon will be out of Stock</strong>
										</span>
									</div>
								</div>
							</td> 
							<td class="col-sm-1 col-md-1" style="text-align: center"> 
								<input type="number" min="0" class="form-control quantity" value="1" v-model="anItem.bought_quantity">
							</td>
							<td class="col-sm-1 col-md-1 text-center">
								<a class="unit-price editable editable-click" href="javascript:void(0)" style="display: inline;" v-model="anItem.price">anItem.price</a>
							</td>
							<td>
								<input class="form-control discount-amount" type="number" v-model="anItem.discount">
							</td>
							<td class="col-sm-1 col-md-1 text-center">
								<strong class="total-price">${{anItem.bought_quantity*(anItem.price-anItem.discount)}}</strong>
							</td>
							<td class="col-sm-1 col-md-1">
								<button type="button" class="btn btn-danger" ><span class="pe-7s-trash"></span> Remove</button>
							</td>
						</tr>
                    </tbody>
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
                                    <li><a href="{{ route('customer_balance_add') }}">Add Customer Balance</a></li>
                                    <li><a href="{{ route('close_cash_register') }}">Close register</a></li>
                                </ul>
                                <form action="" id="cancel_sale_form" autocomplete="off" method="post" accept-charset="utf-8">

                                    <div class="btn-group input-group-btn"  >
                                        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <i class="ion-pause"></i>
                                            Suspend Sale								</button>
                                        <ul class="dropdown-menu sales-dropdown" id = "sale-type" data-selected-type="sale" role="menu">
                                            <li><a href="#" onclick = "layAwaySale()" id="layaway_sale_button"><i class="ion-pause"></i> Charge Account </a></li>
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
                    </div></div>

                <div class="row"><div class = "card" >
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
    </div>

    <div id="edit_item_price_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Price</h4>
                </div>
                <div class="modal-body">
                    <input type = "text" class="form-control" name = "edit_item_price" id = "edit_item_price" placeholder="New Price"><br>
                    <input type ="hidden" name="edit_item_id" id="edit_item_id" />
                    <button onclick ="setNewItemPrice()" type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
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
	<script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script>
        var app = new Vue({
			el: '#app',
			data: {
				itemList: []
			}
		})
		
    </script>
@stop