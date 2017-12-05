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
        <li><?php echo substr($settings['address'],20) ?></li>
        <li>{{ $settings['phone']}}</li>

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
            @if($sale->customer->first_name!=null)
            <li><span>Customer:</span>{{$sale->customer->first_name}}</li>
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
                <td > {{ $anItem->item_name }} </td>
                <td> ${{ $anItem->selling_price }} </td>
                <td> {{ $anItem->pivot->quantity }} </td>
                <td> ${{ $anItem->pivot->total_price }} </td>
                <td> {{ $anItem->pivot->item_discount_percentage }}% </td>

            </tr>
            @if(!is_null($anItem->PriceRule))
                @foreach($anItem->PriceRule as $aPriceRule)
                    @if ($aPriceRule->active)

                        @if($aPriceRule->unlimited||$aPriceRule->num_times_to_apply>0)

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
                    @endif
                @endforeach
            @endif
        @endforeach
        @foreach($sale->itemkits as $anItem)
            <tr>
                <td> {{ $anItem->item_kit_name }} </td>
                <td> ${{ $anItem->selling_price }} </td>
                <td> {{ $anItem->pivot->quantity }} </td>
                <td> ${{ $anItem->pivot->total_price }} </td>
                <td> {{ $anItem->pivot->item_discount_percentage }}% </td>

            </tr>
            @if(!is_null($anItem->PriceRule))
                @foreach($anItem->PriceRule as $aPriceRule)
                    @if ($aPriceRule->active)

                        @if($aPriceRule->unlimited||$aPriceRule->num_times_to_apply>0)

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
            <th>Change Due</th>
            <td>  @if($sale->due>=0)
                    ${{$sale->due}}
                @else
                    -${{ (-1) * $sale->due }}
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