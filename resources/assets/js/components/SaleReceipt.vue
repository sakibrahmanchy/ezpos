<div class="sales-header">
    <div class="col-md-12"
         style="padding: 10px; background: rgb(51, 122, 183); color:white; border-top-left-radius: 5px; border-top-right-radius: 5px">
        {{--<div class="sale-buttons input-group" style = "border-bottom:solid #ddd 1px; padding:10px;max-width: 100%;display: inline-block;">--}}
        <div class="pull-right col-md-12">
            <button v-if="activeTab != 1" type="button" class="pull-right btn btn-default" @click="activeTab=1">Item
                Grid
            </button>

            <button type="button" class="btn btn-default pull-right" @click="activeTab=2">Options <i
                    v-if="activeTab!=2" class="fa fa-chevron-down"></i></button>
            <div class="pull-right padding-left-md" style='padding-right: 10px'>
                <button type="button" class="btn btn-warning" @click="activeTab=2">Cancel Sale</button>
            </div>
            <div class="col-md-2 pull-right">

            </div>
        </div>
        <div style="clear:both">
        </div>
    </div>
        <div>

            <div class="col-xs-6" style="padding-left:0px;">
                <div class="card" style="margin-top: 0px">
                    <div class="panel panel-piluku">
                        <div class="panel-body panel-pad">
                            <div class="row">

                                <div class="col-md-4 col-sm-4 col-xs-12">
                                    <ul class="list-unstyled invoice-address" style="margin-bottom:2px;">
                                        <li class="company-title">{{$settings['company_name']}}, Inc</li>

                                        <li>{{$settings['company_name']}}</li>
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
                                </div>


                                <div class="col-md-4 col-sm-4 col-xs-12 text-right" style="float:right">
                                    <ul class="list-unstyled invoice-detail" style="margin-bottom:2px;">
                                        <li class="big-screen-title">
                                            Sales Receipt <br>
                                            <strong>{{date('m/d/Y h:i:s a', time()) }}</strong>
                                        </li>
                                        <li><span>Sale ID: </span>{{$settings['company_name']}} No. {{$sale->id}}</li>
                                        <li><span>Counter Name: </span><b>{{ $sale->counter->name }}</b></li>
                                        <li><span>Employee:</span> {{ $name  }}</li>
                                        @if(isset($sale->customer->id))
                                        <li>
                                            <span>Customer:</span>{{$sale->customer->first_name}} {{$sale->customer->last_name}}
                                        </li>
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

                            </div>

                            <br>

                            <center>@if($sale->refund_status) <label style="font-size: 20px" class="label label-danger">DELETED/VOID</label> @endif
                            </center>
                            <br><br>
                            <table class="table table-hover table-responsive">
                                <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Discount Percentage</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sale->items as $anItem)
                                <tr>
                                    <td>
                                        @if($anItem->pivot->is_price_taken_from_barcode)
                                        <span>{{ $anItem->item_name }}</span><br>@
                                        <span>{{ $anItem->pivot->unit_price }}</span>{{ $anItem->item_size==null ? "" : "/".$anItem->item_size }}
                                        @else
                                        {{ $anItem->item_name }}
                                        @endif
                                    </td>
                                    <td> ${{ $anItem->pivot->unit_price }} </td>
                                    <td> {{ $anItem->pivot->quantity }} </td>
                                    <td> ${{ $anItem->pivot->total_price }} </td>
                                    <td> {{ $anItem->pivot->item_discount_percentage }}%</td>

                                </tr>

                                @if(!is_null($anItem->PriceRule))
                                @foreach($anItem->PriceRule as $aPriceRule)
                                @if ($aPriceRule->active)

                                @if($aPriceRule->type==1)

                                @if($aPriceRule->percent_off>0)

                                @php
                                $current_date = new DateTime('today');
                                $rule_start_date = new DateTime($aPriceRule->start_date);
                                $rule_expire_date = newDateTime($aPriceRule->end_date);
                                @endphp

                                @if(($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) )
                                <tr>
                                    <td colspan="5"
                                        style="padding-left:23px;font-size: 80%;background: aliceblue;">
                                        Discount Offer:
                                        <strong>{{$aPriceRule->name}}</strong><br>Item
                                        Discount Amount:
                                        $<strong>{{$anItem->pivot->discount_amount}}</strong>
                                    </td>
                                </tr>
                                @endif

                                @elseif($aPriceRule->fixed_of>0)

                                @php
                                $current_date = new DateTime('today');
                                $rule_start_date = new DateTime($aPriceRule->start_date);
                                $rule_expire_date = new DateTime($aPriceRule->end_date);
                                @endphp

                                @if(($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) )
                                <tr>
                                    <td colspan="5"
                                        style="padding-left:23px;font-size: 80%;background: aliceblue;">
                                        Discount Offer:
                                        <strong>{{$aPriceRule->name}}</strong><br>Item
                                        Discount Amount:
                                        $<strong>{{$anItem->pivot->discount_amount}}</strong>
                                    </td>
                                </tr>
                                @endif

                                @endif
                                @endif
                                @endif
                                @endforeach
                                @endif
                                @endforeach
                                </tbody>
                            </table>
                            <hr>
                            <div style="background: aliceblue; padding: 10px;" class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="invoice-policy">
                                                Change return policy			            </div>
                                            <div id="receipt_type_label" style="display: none;" class="receipt_type_label invoice-policy">
                                                Merchant Copy						</div>
                                            <div id="barcode" class="invoice-policy">
                                                <?php echo DNS1D::getBarcodeHTML($sale->id, "C39", 1, 50);    ?>                    </div>
                                            <p >{{ $settings['company_name'] }} {{ $sale->id }}</p>
                                            <div id="announcement" class="invoice-policy">
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div id="signature">
                                                <br><br><br>
                                                CUSTOMER COPY
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="invoice-footer gift_receipt_element col-md-6">

                                        <div class="row">
                                            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
                                                <div class="invoice-footer-heading">Sub Total</div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-4">
                                                <div class="invoice-footer-value">
                                                    <strong>
                                                        <span style="white-space:nowrap;"></span>
                                                        @if($sale->sub_total_amount>=0)
                                                        ${{ number_format($sale->sub_total_amount, 2)}}
                                                        @else
                                                        -${{ number_format((-1) * $sale->sub_total_amount, 2) }}
                                                        @endif
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                        @if( $settings['tax_rate'] >0 )
                                        <div class="row">
                                            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
                                                <div class="invoice-footer-heading">Tax({{ $settings['tax_rate']  }}%)</div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-4">
                                                <div class="invoice-footer-value">
                                                    <strong>
                                                        <span style="white-space:nowrap;"></span>
                                                        @if($sale->tax_amount>=0)
                                                        ${{ number_format($sale->tax_amount, 2)}}
                                                        @else
                                                        ${{ number_format((-1) * $sale->tax_amount, 2) }}
                                                        @endif
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
                                                <div class="invoice-footer-heading">Total</div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-4">
                                                <div class="invoice-footer-value invoice-total">

                                                    <strong>
                                                        <span style="white-space:nowrap;"></span>
                                                        @if($sale->total_amount>=0)
                                                        ${{ number_format($sale->total_amount, 2)}}
                                                        @else
                                                        -${{ number_format((-1) * $sale->total_amount, 2) }}
                                                        @endif
                                                    </strong>

                                                </div>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
                                                <div class="invoice-footer-heading">{{$sale->due>=0?'Due': 'Change Due'}}</div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-4">
                                                <div class="invoice-footer-value invoice-total">
                                                    <strong>
                                                        <span style="white-space:nowrap;"></span>
                                                        @if($sale->due>=0)
                                                        ${{  number_format($sale->due, 2) }}
                                                        @else
                                                        -${{  number_format((-1) * $sale->due, 2) }}
                                                        @endif
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8"><br>
                                                <div class="invoice-footer-heading"><strong>Payments</strong></div>
                                            </div>
                                        </div>

                                        @foreach($sale->paymentlogs as $aPayment)
                                        <div class="row">
                                            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
                                                <div class="invoice-footer-heading">{{array_search($aPayment->payment_type, \App\Enumaration\PaymentTypes::$TypeList) }} Tendered</div>
                                            </div>
                                            <br>
                                            <div class="col-md-2 col-sm-2 col-xs-4">
                                                <div class="invoice-footer-value invoice-total">
                                                    @if($aPayment->paid_amount>0)
                                                    ${{$aPayment->paid_amount}}
                                                    @else
                                                    -${{number_format((-1) * $aPayment->paid_amount, 2)}}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>


                                        @endforeach
                                        @if(!is_null($sale->comment))
                                        <div class="row">
                                            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8"><br>
                                                <div class="invoice-footer-heading"><strong>Comments</strong></div>
                                            </div>
                                            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2"><br>
                                                <div class="invoice-footer-heading">{{ $sale->comment }}</div><br>
                                            </div><br><br>
                                        </div>
                                        @endif



                                        <div class="row">
                                            <div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">
                                                <br>
                                                Thank You!!

                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-6 pull-right" style="padding-right: 0px;">
            <div class="card" style="margin-top:0px;">
                <div class="row">
                    <div v-if="activeTab==2" class="col-md-12" style="height: 720px ">
                        <div class="col-md-4">
                            <div class="options">
                                <div class="vertical-align">
                                    <a style="color:white" href="{{route('suspended_sale_list')}}" class=""
                                       title="Suspended Sales"><i class="ion-ios-list-outline"></i> Print Pickup</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="options">
                                <div class="vertical-align">
                                    <a style="color:white" href="{{route('search_sale')}}" class=""
                                       title="Search Sales"><i class="ion-search"></i> Print receipt</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="options">
                                <div class="vertical-align">
                                    <a style="color:white" href="#look-up-receipt" class="look-up-receipt"
                                       data-toggle="modal">
                                        Send to printer
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="options">
                                <div class="vertical-align">
                                    <a style="color:white" href="{{route('sale_last_receipt')}}" target="_blank"
                                       class="look-up-receipt" title="Lookup Receipt">
                                        Download Receipt
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="options">
                                <div class="vertical-align">
                                    <a style="color:white" href="{{route('pop_open_cash_drawer')}}"
                                       class="look-up-receipt" title="Lookup Receipt">
                                        Email Receipt
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div style="display: block; clear: both;"></div>

            </div>
        </div>
    </div>
</div>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>