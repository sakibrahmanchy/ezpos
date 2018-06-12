<html>
<style>
    li{
        list-style-type: none;
    }

    table {

        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }

    td, th {
        /* border: 1px solid #dddddd;*/
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
<div>
    <ul style="margin-bottom:2px;float:left;">

        <li >{{ $settings['company_name'] }}</li>
		@if($settings['address_line_1']!=""||$settings['address_line_1']!=null)
        <li><?php echo $settings['address_line_1'] ?></li>
		@endif
		@if($settings['address_line_2']!=""||$settings['address_line_2']!=null)
        <li><?php echo $settings['address_line_2'] ?></li>
		@endif
		@if($settings['email_address']!=""||$settings['email_address']!=null)
        <li><?php echo $settings['email_address'] ?></li>
		@endif
		@if($settings['phone']!=""||$settings['phone']!=null)
        <li><?php echo $settings['phone'] ?></li>
		@endif
    </ul>
    <ul  style="margin-bottom:2px;float:right;">
        <li>
            Sales Receipt						 <br>
            <strong>{{date('m/d/Y h:i:s a', time()) }}</strong>
        </li>
        <li><span>Sale ID:</span>{{ $settings['company_name'] }} {{$sale->id}}</li>
        <li><span>Counter Name:</span><b>{{ $sale->counter->name }}</b></li>
        <li><span>Employee:</span>{{\Illuminate\Support\Facades\Auth::user()->name }}</li>
        @if(isset($sale->customer->id))
            <li><span>Customer:</span>{{$sale->customer->first_name}} {{$sale->customer->last_name}}</li>
			@if($sale->Customer->loyalty_card_number && strlen($sale->Customer->loyalty_card_number)>0)
				<li>
				@php
					$loyalityCarNumber = $sale->Customer->loyalty_card_number;
					$loyalityCarNumberMasked = str_repeat('X', strlen($loyalityCarNumber) - 4) . substr($loyalityCarNumber, -4);
					echo $loyalityCarNumberMasked;
				@endphp
				</li>
			@endif
        @endif
    </ul>
</div>


<!-- invoice items-->

<table  style="clear: both" >
    <thead>
    <tr>
        <th >Item Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Discount Percentage</th>

    </tr>
    </thead>

    <tbody>
    @foreach($sale->items as $anItem)
        <tr>
            <td >
                @if($anItem->pivot->is_price_taken_from_barcode)
                    <span>{{ $anItem->item_name }}</span><br>@<span>{{ $anItem->pivot->unit_price }}</span>{{ $anItem->item_size==null ? "" : "/".$anItem->item_size }}
                @else
                    {{ $anItem->item_name }}
                @endif
            </td>
            <td>
                {{ $anItem->pivot->unit_price }}
            </td>
            <td> {{ $anItem->pivot->quantity }} </td>
            <td> ${{ $anItem->pivot->total_price }} </td>
            <td> {{ $anItem->pivot->item_discount_percentage }}% </td>

        </tr>
        @if(!is_null($anItem->PriceRule))
            @foreach($anItem->PriceRule as $aPriceRule)
                @if ($aPriceRule->active)

                        @if($aPriceRule->type==1)

                            @if($aPriceRule->percent_off>0)

                                @php
                                    $current_date = new \DateTime('today');
                                    $rule_start_date = new \DateTime($aPriceRule->start_date);
                                    $rule_expire_date = new \DateTime($aPriceRule->end_date);
                                @endphp

                                @if(($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) )
                                    <tr><td colspan="5" style="padding-left:23px;font-size: 80%;background: aliceblue;"> Discount Offer: <strong>{{$aPriceRule->name}}</strong><br>Item Discount Amount: $<strong>{{$anItem->pivot->discount_amount}}</strong></td></tr>
                                @endif

                            @elseif($aPriceRule->fixed_of>0)

                                @php
                                    $current_date = new \DateTime('today');
                                    $rule_start_date = new \DateTime($aPriceRule->start_date);
                                    $rule_expire_date = new \DateTime($aPriceRule->end_date);
                                @endphp

                                @if(($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) )
                                    <tr><td colspan="5" style="padding-left:23px;font-size: 80%;background: aliceblue;"> Discount Offer: <strong>{{$aPriceRule->name}}</strong><br>Item Discount Amount: $<strong>{{$anItem->pivot->discount_amount}}</strong></td></tr>
                                @endif

                            @endif
                        @endif
                    @endif
            @endforeach
        @endif
    @endforeach
    @foreach($sale->itemkits as $anItem)
        <tr>
            <td>
                @if($anItem->pivot->is_price_taken_from_barcode)
                    <span>{{ $anItem->item_name }}</span><br>@<span>{{ $anItem->pivot->unit_price }}</span>{{ $anItem->item_size==null ? "" : "/".$anItem->item_size }}
                @else
                    {{ $anItem->item_name }}
                @endif
            </td>
            <td>
                {{ $anItem->pivot->unit_price }}
            </td>
            <td> {{ $anItem->pivot->quantity }} </td>
            <td> ${{ $anItem->pivot->total_price }} </td>
            <td> {{ $anItem->pivot->item_discount_percentage }}% </td>

        </tr>
        @if(!is_null($anItem->PriceRule))
            @foreach($anItem->PriceRule as $aPriceRule)
                @if ($aPriceRule->active)

                        @if($aPriceRule->type==1)

                            @if($aPriceRule->percent_off>0)

                                @php
                                    $current_date = new \DateTime('today');
                                    $rule_start_date = new \DateTime($aPriceRule->start_date);
                                    $rule_expire_date = new \DateTime($aPriceRule->end_date);
                                @endphp

                                @if(($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) )
                                    <tr><td colspan="5" style="padding-left:23px;font-size: 80%;background: aliceblue;"> Discount Offer: <strong>{{$aPriceRule->name}}</strong><br>Item Discount Amount: $<strong>{{$anItem->pivot->discount_amount}}</strong></td></tr>
                                @endif

                            @elseif($aPriceRule->fixed_of>0)

                                @php
                                    $current_date = new \DateTime('today');
                                    $rule_start_date = new \DateTime($aPriceRule->start_date);
                                    $rule_expire_date = new \DateTime($aPriceRule->end_date);
                                @endphp

                                @if(($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) )
                                    <tr><td colspan="5" style="padding-left:23px;font-size: 80%;background: aliceblue;"> Discount Offer: <strong>{{$aPriceRule->name}}</strong><br>Item Discount Amount: $<strong>{{$anItem->pivot->discount_amount}}</strong></td></tr>
                                @endif

                            @endif
                        @endif
                    @endif
            @endforeach
        @endif
    @endforeach
    </tbody>
</table>

<div style="padding-left: 500px;">
    <table style="width:100%;">
        <tbody>
        <tr>
            <th>Sub total</th>
            <td >

                @if($sale->sub_total_amount>=0)
                    ${{ number_format($sale->sub_total_amount, 2)}}
                @else
                    -${{ number_format((-1) * $sale->sub_total_amount, 2) }}
                @endif

            </td>
        </tr>
        @if( $settings['tax_rate'] >0 )
        <tr>
            <th>Tax</th>
            <td>
                @if($sale->tax_amount>=0)
                    ${{ number_format($sale->tax_amount, 2)}}
                @else
                    ${{ number_format((-1) * $sale->tax_amount, 2) }}
                @endif
            </td>
        </tr>
        @endif
        <tr>
            <th>Total</th>
            <td>

                @if($sale->total_amount>=0)
                    ${{ number_format($sale->total_amount, 2)}}
                @else
                    -${{ number_format((-1) * $sale->total_amount, 2) }}
                @endif

            </td>
        </tr>
        <tr>
            <th>{{$sale->due>=0?'Due': 'Change Due'}}</th>
            <td>  @if($sale->due>=0)
                    ${{ number_format($sale->due, 2) }}
                @else
                    -${{ number_format((-1) * $sale->due, 2) }}
                @endif
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div style="padding-left: 500px;">
    @if(!empty($sale->paymentlogs))
        <table style="width:100%">
            <thead>
            <tr>
                <th>Payment Type</th>
                <th>Paid Amount</th>-
            </tr>
            </thead>
            <tbody>
            @foreach($sale->paymentlogs as $aPayment)
                <tr>
                    <th>{{$aPayment->payment_type}}</th>
                    <td>
                        @if($aPayment->paid_amount>0)
                            ${{$aPayment->paid_amount}}
                        @else
                            -${{number_format((-1) * $aPayment->paid_amount, 2)}}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="row">
    <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6"><br>
        <div class="invoice-footer-heading"><strong>Comments</strong></div>
    </div>
    <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2"><br>
        <div class="invoice-footer-heading">{{ $sale->comment }}</div><br>
    </div><br><br>
</div>

<div >
    <div>
        <div>
            Change return policy			            </div>
        <div >
            <?php echo DNS1D::getBarcodeHTML($sale->id , "C39");	?>					</div>
        <p style="padding-left: 28px">{{ $settings['company_name'] }} {{ $sale->id }}</p>
        <div >
        </div>
    </div>

    <div>
        <div >


        </div>
    </div>
</div>
</div>
<!--container-->
</div>
</div>
</html>