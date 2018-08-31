<div>

    <file_explorer @choose-item="ChooseItem" :shown="shown"></file_explorer>
    <div class="sales-header panel panel-primary">
        <div class="panel-heading">
            Panel content
        </div>
        <div class="panel-body">
            <div class="col-sm-5 card ">
                <div class="search section">
                    <div class="input-group col-md-12">
                        <a href="{{route('new_item')}}" target="_blank" class="input-group-addon" id="sizing-addon2"
                           style="background-color:#337ab7;color:white;border:solid #337ab7 1px;border-radius: 3px; font-size: 20px; padding-left: 20px; padding-right: 20px"><strong>+</strong></a>
                        <auto-complete @set-autocomplete-result="setAutoCompleteResult"
                                       :auto-select="auto_select"></auto-complete>

                        <div class="input-group-btn bs-dropdown-to-select-group">
                            <button type="button" class="btn btn-primary dropdown-toggle as-is bs-dropdown-to-select"
                                    data-toggle="dropdown">
                                <span id="bs-drp-sel-label" data-bind="bs-drp-sel-label"><i class="fa fa-shopping-cart"
                                                                                            style="margin-right: 5px"></i>Sale</span>
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu" style="">
                                <li data-value="1"><a @click="convertToSale()" href="#">Sale</a></li>
                                <li data-value="2"><a @click="convertToReturn()" href="#">Return</a></li>
                                {{--
                                <li data-value="3"><a href="#">Store Account Payment</a></li>
                                --}}
                            </ul>
                            {{--
                            <button class="btn btn-success" @click="shown = !shown"><i class="fa fa-th"
                                                                                       style="margin-right: 5px"></i>
                                Show Grid
                            </button>
                            --}}
                        </div>
                    </div>


                    <div class="center">
                        <input type="checkbox" checked id="auto_select" v-model="auto_select">
                        <b>Add automatically to cart when item found.</b>
                    </div>

                </div>

                <br>

                <div class="table-responsive">
                    <div class="product-holder">
                        <table class="table table-hover  table-responsive"
                               style="border-color:#c0c0c0; border-collapse: collapse;">
                            <thead style="background: #f5f5f5; border: solid 1px #c0c0c0;   display: block; ">
                            <tr>
                                <th style="width: 140px">Product</th>
                                <th style="width: 60px">&nbsp;&nbsp;&nbsp;&nbsp;Qty&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th style="width: 70px" class="text-center ">&nbsp;&nbsp;&nbsp;&nbsp;Price&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th style="width: 50px" class="text-center ">Disc%</th>
                                <th style="width: 60px" class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th style="width: 60px"></th>
                            </tr>
                            </thead>
                            <tbody v-if="itemList.length>0" style="height: 300px; overflow:auto;display: block;">
                            <template v-for="(anItem,index) in itemList" class="product-descriptions">
                                <tr class="product-specific-description">
                                    <td style="width: 152px" class="col-sm-8 col-md-6">
                                        <div class="media">
                                            <div class="media-body">
                                                <h6 class="media-heading"><a href="#">@{{itemList[index].item_name}}</a>
                                                </h6>
                                                {{--
                                                <h6 v-if="itemList[index].company_name" class="media-heading">
                                                    by <a href="#">@{{itemList[index].company_name}}</a></h6>
                                                <span>Status: </span>
                                                <span v-if="itemList[index].item_quantity>10"
                                                      class="text-success"><strong>In Stock</strong>
                                                                </span>
                                                <span v-else-if="itemList[index].item_quantity<=0" class="text-success"><strong>Out of Stock</strong>
                                                                                            </span>
                                                <span v-else
                                                      class="text-warning"><strong>Soon will be out of Stock </strong>
                                                                                            </span>
                                                --}}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-sm-1 col-md-1" style="text-align: center; width: 120px">
                                        <input style="width: 50px;text-align:center; border-radius: 4px" min="0"
                                               class="form-control quantity" value="1"
                                               v-model="itemList[index].items_sold">
                                    </td>
                                    <td style="width: 120px" class="col-sm-1 col-md-1 text-center">
                                        <inline-edit v-model="itemList[index].unit_price"
                                                     if-user-permitted="{{UserHasPermission(" edit_sale_cost_price
                                        ")}}" ></inline-edit>
                                    </td>
                                    <td style="width: 110px">
                                        <input style="text-align:center; border-radius: 4px"
                                               class="form-control discount-amount"
                                               v-model="itemList[index].item_discount_percentage">
                                    </td>
                                    <td style="width: 60px" class="col-sm-1 col-md-1 text-center">
                                        <strong class="total-price">
                                            <currency-input currency-symbol="$"
                                                            :value="GetLineTotal(index)"></currency-input>
                                        </strong>
                                    </td>
                                    <td style="width: 6 0px" class="col-sm-1 col-md-1">
                                        <button type="button" class="btn btn-danger"
                                                @click="Remove(itemList[index].item_id)"><span
                                                class="pe-7s-trash"></span></button>
                                    </td>
                                </tr>

                                <tr v-if="itemList[index].discountApplicable">
                                    <td colspan='5' style='padding-left:23px;font-size: 80%;background: aliceblue;'>
                                        Discount Offer:
                                        <strong>@{{itemList[index].discount_name}}</strong><br>
                                        Item Discount Amount: $<strong>@{{itemList[index].discount_amount}}</strong>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                            <tbody v-if="itemList.length<=0" class="no-items">
                            <td style="background-color: #eee" colspan="6">
                                <div class="jumbotron text-center"><h3>There are no items in the cart [Sales]</h3></div>
                            </td>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>

                    <div style="padding:  10px;background: rgb(222, 224, 225);border-radius: 4px;margin: 5px;">
                        <div class="row">
                            <div class="col-md-5">
                                Subtotal:
                                <currency-input currency-symbol="$" :value="GetSubtotal"></currency-input>
                                <br><br>
                                Discount (%): <input id="allDiscountAmount" type="number"
                                                     v-model="allDiscountAmountPercentage"
                                                     style="max-width:45px;"><br><br>
                                Tax({{ $tax_rate }}%):
                                <currency-input currency-symbol="$" :value="GetTax"></currency-input>
                            </div>
                            <div class="col-md-4">
                                <p style="font-size: 18px;">Total:
                                    <currency-input currency-symbol="$" :value="GetTotalSale"></currency-input>
                                    <br><br>
                                <p>
                                <p style="font-size: 20px; color:red">Due:
                                    <currency-input currency-symbol="$" :value="GetDue"></currency-input>
                                </p>
                            </div>
                            <div class="col-md-3">
                                <a href="javascript:void(0)">
                                    <div style="background: rgb(51, 122, 183);font-size: 50px;margin: 5px;color:  white;border-radius: 50%;/* position: relative; *//* width: 100%; *//* height: auto; *//* padding-top: 100%; */">
                                        <center>$</center>
                                    </div>
                                    <center style="color:  rgb(51, 122, 183);">Pay</center>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="">
                </div>

            </div>
            <div class="col-sm-6 pull-right">
                <div class="form-group">
                    <div class="row">
                        {{--
                        <div class="card">--}}

                            {{--
                            <div class="sale-buttons input-group"
                                 style="border-bottom:solid #ddd 1px; padding:10px;max-width: 100%;display: inline-block;">
                                --}}
                                {{--
                                <div class="btn-group input-group-btn">--}}
                                    {{--
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false">--}}
                                        {{--<strong>Options</strong>--}}
                                        {{--</button>
                                    --}}
                                    {{--
                                    <ul class="dropdown-menu sales-dropdown" role="menu">--}}
                                        {{--
                                        <li>--}}
                                            {{--<a href="{{route('suspended_sale_list')}}" class=""
                                                   title="Suspended Sales"><i class="ion-ios-list-outline"></i>
                                                Suspended Sales</a></li>
                                        --}}
                                        {{--
                                        <li>--}}
                                            {{--<a href="{{route('search_sale')}}" class="" title="Search Sales"><i
                                                    class="ion-search"></i> Search Sales</a>--}}
                                            {{--</li>
                                        --}}

                                        {{--
                                        <li>--}}
                                            {{--<a href="#look-up-receipt" class="look-up-receipt"
                                                   data-toggle="modal"><i class="ion-document"></i> Look up Receipt</a>
                                        </li>
                                        --}}

                                        {{--
                                        <li><a href="{{route('sale_last_receipt')}}" target="_blank"
                                               class="look-up-receipt" title="Lookup Receipt"><i
                                                class="ion-document"></i> Show last sale receipt</a></li>
                                        --}}
                                        {{--
                                        <li><a href="{{route('pop_open_cash_drawer')}}" class="look-up-receipt"
                                               title="Lookup Receipt"><i class="ion-document"></i> Pop Open Cash Drawer</a>
                                        </li>
                                        --}}
                                        {{--
                                        <li><a href="{{ route('add_cash_to_register') }}">Add cash to register</a></li>
                                        --}}
                                        {{--
                                        <li><a href="{{ route('subtract_cash_from_register') }}">Remove cash from register</a>
                                        </li>
                                        --}}
                                        {{--
                                        <li><a href="{{ route('customer_balance_add') }}">Add Customer Balance</a></li>
                                        --}}
                                        {{--
                                        <li><a href="{{ route('close_cash_register') }}">Close register</a></li>
                                        --}}
                                        {{--</ul>
                                    --}}
                                    {{--
                                    <form action="" id="cancel_sale_form" autocomplete="off" method="post"
                                          accept-charset="utf-8">--}}

                                        {{--
                                        <div class="btn-group input-group-btn">--}}
                                            {{--
                                            <button type="button" class="btn btn-warning dropdown-toggle"
                                                    data-toggle="dropdown" aria-expanded="false">--}}
                                                {{--<i class="ion-pause"></i>--}}
                                                {{--Suspend
                                                Sale                                </button>
                                            --}}
                                            {{--
                                            <ul class="dropdown-menu sales-dropdown" id="sale-type"
                                                data-selected-type="sale" role="menu">--}}
                                                {{--
                                                <li><a href="#" @click="layAwaySale()" id="layaway_sale_button"><i
                                                        class="ion-pause"></i> Charge Account </a></li>
                                                --}}
                                                {{--
                                                <li><a href="#" @click="estimateSale()" id="estimate_sale_button"><i
                                                        class="ion-help-circled"></i> Estimate</a></li>
                                                --}}

                                                {{--</ul>
                                            --}}
                                            {{--</div>
                                        --}}
                                        {{--<a href="" class="btn btn-danger input-group-addon" id="cancel_sale_button">--}}
                                            {{--<i class="ion-close-circled"></i>--}}
                                            {{--Cancel
                                            Sale                </a>--}}

                                        {{--</form>
                                    --}}
                                    {{--</div>
                                --}}
                                {{--</div>
                            --}}
                            {{--If
                            customer
                            is
                            added
                            to
                            the
                            sale--}}

                            {{--
                            <div class="customer-form">--}}

                                {{--
                                if the customer
                                is
                                not
                                set , show
                                customer
                                adding
                                form--}}
                                {{--
                                <form action="" id="select_customer_form" autocomplete="off" class="form-inline"
                                      method="post" accept-charset="utf-8">--}}
                                    {{--
                                    <div class="input-group contacts" style="padding-top:10px;padding-left:10px">--}}

                                        {{--<a href="{{route('new_customer')}}" target="_blank"
                                               class="input-group-addon" id="sizing-addon2"
                                               style="background-color:#337ab7;color:white;border:solid #337ab7 1px; "><strong>+</strong></a>--}}
                                        {{--
                                        <select2 v-model="customer_id">--}}
                                            {{--
                                            <option value="0" selected>Select Customer for sale</option>
                                            --}}
                                            {{--@foreach($customerList as $aCustomer)--}}
                                            {{--
                                            <option value="{{$aCustomer->id}}">
                                                {{$aCustomer - > first_name}} {{$aCustomer - > last_name}}
                                            </option>
                                            --}}
                                            {{--@endforeach--}}
                                            {{--</select2>
                                        --}}
                                        {{--</div>
                                    --}}
                                    {{--</form>
                                --}}

                                {{--</div>
                            --}}
                            {{--</div>
                        --}}
                        {{--</div>
                    --}}

                    <div class="row">
                        <div class="card">
                            <h4 class="text-center"><strong>Receipt</strong></h4>
                            <hr>
                            <div class="card">
                                <strong>Subtotal</strong> <span style="float: right"><strong data-subtotal="0"
                                                                                             class="subtotal"><currency-input
                                    currency-symbol="$" :value="GetSubtotal"></currency-input></strong></span><br>
                                <strong>+Tax({{ $tax_rate }}%)</strong><span style="float: right"><strong data-tax="0"
                                                                                                          id="tax"><currency-input
                                    currency-symbol="$" :value="GetTax"></currency-input></strong></span><br>
                                <strong>Discount all items by percent</strong><span style="float: right"><strong
                                    id=""><input id="allDiscountAmount" type="number"
                                                 v-model="allDiscountAmountPercentage"
                                                 style="max-width:45px;float: right"></strong></span><br><br>

                                <strong>Discount entire sale</strong><span style="float: right"><strong id=""><input
                                    id="saleFlatDiscountAmount" style="max-width:45px;float: right"
                                    v-model="saleFlatDiscountAmount"></strong></span>
                            </div>

                            <div class="card" style="background-color: #778a9b;color:whitesmoke;font-size:20px;">
                                Total <span style="float: right"><strong data-total="0" id="total"> <currency-input
                                    currency-symbol="$" :value="GetTotalSale"></currency-input></strong></span>
                            </div>
                            <div class="card" style="background-color: #778a9b;color:whitesmoke;font-size:20px;">
                                Due <span style="float: right"><strong data-due="0" id="due"> <currency-input
                                    currency-symbol="$" :value="GetDue"></currency-input></strong></span>
                            </div>
                            <br>
                            <div class="row">
                                {{--<input type="number" id="paid-amount" name="paid-amount"
                                           class="col-md-8 form-control" style="float:left">
                                <button type="button" class="col-md-4 btn btn-success" style="float:right"
                                        onclick="SubmitSales()">
                                    Checkout <span class="pe-7s-cart"></span>
                                </button>
                                <br><br>--}}


                                <div class="add-payment">

                                    <div class="payment-history">
                                        <div v-for="(aPayment, index) in paymentList" class="card payment-log"
                                             style="margin: 10px">
                                            <span class="pe-7s-close-circle" style="float:left"
                                                  @click="RemovePayment(index)"></span>
                                            <p style="float:left">@{{aPayment.payment_type}}</p>
                                            <p style="float:right">
                                                <currency-input currency-symbol="$"
                                                                :value="aPayment.paid_amount"></currency-input>
                                            </p>
                                            <br/>
                                        </div>
                                    </div>
                                    <div style="padding:20px">

                                        <div class="side-heading">Add Payment</div>

                                        <a tabindex="-1" v-for="aPaymentType in paymentTypeList"
                                           href="javascript: void(0);" :class="GetPaymentButtonClass(aPaymentType)"
                                           @click="SetActivePaymentType(aPaymentType)">
                                            @{{aPaymentType}}</a>

                                    </div>

                                    <div v-show="activePaymentType=='Gift Card'" style="padding:20px"
                                         class="input-group">
                                        <label>Gift Card Number</label>
                                        <input class="form-control" type="text" name="gift_card_number"
                                               id="gift_card_number" class="add-input numKeyboard form-control"
                                               v-model="gift_card_number"/>
                                    </div>
                                    <div v-show="activePaymentType=='Loyalty Card'" style="padding:20px"
                                         class="input-group">
                                        <label>Loyalty Card Number</label>
                                        <input class="form-control" type="text" name="loyalty_card_number"
                                               id="loyalty_card_number" class="add-input numKeyboard form-control"
                                               v-model="loyalty_card_number"/>
                                    </div>

                                    <div class="input-group add-payment-form" style="padding:20px">
                                        <input type="number" name="amount_tendered" value="0.00" id="amount_tendered"
                                               class="add-input numKeyboard form-control" v-model="amountTendered">
                                        <span class="input-group-addon"
                                              style="background: #5cb85c; border-color: #4cae4c;color:white; cursor: pointer;"
                                              @click="CompleteSales()">
                                                        Complete Sale
                                            <!--<a href="javascript:void(0)" id="add_payment_button" onclick = "addPayment()" style=" color:white;text-decoration:none;">Add Payment</a>
                                            <a class="javascript:void(0)" id="finish_sale_alternate_button" style=" color:white;text-decoration:none;">Complete Sale</a>-->
                                                    </span>


                                    </div>

                                    <div style="padding:20px">
                                        <div class="side-heading">Comments</div>
                                        <input type="text" name="comment" id="comment" class="form-control"/>
                                    </div>

                                </div>
                            </div>

                            <form id="saleSubmit" method="post" action="{{route('new_sale')}}">

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>