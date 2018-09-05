@extends('layouts.master')

{{--@section('pageTitle','Sales')--}}

{{--@section('breadcrumbs')--}}
{{--{!! Breadcrumbs::render('sale_receipt') !!}--}}
{{--<span><label class="label label-primary pull-right counter-name"><b>{{ \Illuminate\Support\Facades\Cookie::get('counter_name') }}</b></label></span>--}}
{{--<br><br>--}}
{{--<a href="javascript:void(0)"  onclick="changeCounter()" class="pull-right">Change Location</a>--}}
{{--<br>--}}
{{--@stop--}}

@section('content')

    @php
        if($sale->employee_id==1) {
            $name = "Algrims";
        } else {
            $user = \App\Model\User::where('id',$sale->employee_id)->first();
             $name = $user->name;
        }
    @endphp

    <style>
        *{
            font-size: 12px;
        }

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

        /*.no-items{*/
        /*border: solid 1px #c0c0c0;*/
        /*border-top: 0px;*/
        /*}*/
        .table>thead>tr>td {

            border: solid 1px #c0c0c0;
        }

        .table>thead>tr>th {
            border-bottom:solid rgb(192, 192, 192) 1px;
        }

        .product-specific-description{
            border: solid 1px #ddd;
        }

        .center {
            padding: 0px 0;
            text-align: center;
        }

        .sales-header{
            margin-left: 60px;
            margin-right: 60px;

        }

        .options {
            cursor: pointer;
            height: 110px;
            width: 110px;
            margin: 5px;
            position: relative;
            background-color: rgb(51, 122, 183);
            border-width: 1px;
            border-color:rgb(51, 122, 183);
            border-style: solid;
            padding-left: 5px;
            font-size: 13px;
            text-align: center;
            color:white;
            float:left;
            display: table;
        }

        .btn-circle {
            width: 30px;
            height: 30px;
            text-align: center;
            padding: 6px 0;
            font-size: 12px;
            line-height: 1.428571429;
            border-radius: 15px;
            background: white;
        }

        .btn-circle-lg {
            width: 80px;
            height: 80px;
            text-align: center;
            padding: 6px 0;
            font-size: 12px;
            line-height: 1.428571429;
            border-radius: 40px;
            background: white;
        }

        .btn-circle:hover{
            border: solid 1px #0d6aad;
            color: #0d6aad;
        }

        .btn-circle:active{
            border: solid 1px #0d6aad;
            color: #0d6aad;
        }

        .blue-theme-circle-button{
            border: solid 1px #0d6aad;
            color: #0d6aad;
        }

        .btn-dark{
            background: #0d6aad;
            color: white;
        }

        .blue-font{
            color: #0d6aad;
        }

        .xs-font{
            font-size: 10px
        }

        .sm-font{
            font-size: 12px
        }

        .xxxl-font{
            font-size: 45px;
            color: white;
        }
        ::-webkit-scrollbar {
            width: 10px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

    </style>


    <div id="app" class="row">
        <div class="sales-header">
            <div class="col-md-12"
                 style="padding: 10px; background: rgb(51, 122, 183); color:white; border-top-left-radius: 5px; border-top-right-radius: 5px">
                {{--<div class="sale-buttons input-group" style = "border-bottom:solid #ddd 1px; padding:10px;max-width: 100%;display: inline-block;">--}}
                <div class="pull-right col-md-12">
                    <button  type="button" class="pull-left btn btn-default" @click="activeTab=1">
                        New Sale
                    </button>
                    @if(!$sale->refund_status)
                        @if(UserHasPermission('sale_edit'))
                            <a  href="{{route('sale_edit',['sale_id'=>$sale->id])}}" class="btn btn-default pull-right">Edit Sale</a>
                        @endif
                    @endif
                    <div class="pull-right padding-left-md" style='padding-right: 10px'>
                        <a type="button" class="btn btn-default">Open Drawer</a>
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
                        <div class="panel-body panel-pad">
                            <div class="receipt-holder">
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
                                <div class="row">
                                    <div class="col-md-12">
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
                                    </div>
                                </div>
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
                                                    <?php echo DNS1D::getBarcodeHTML($sale->id , "C39",1,50);	?>					</div>
                                                <p >{{ $settings['company_name'] }} {{ $sale->id }}</p>
                                                <div id="announcement" class="invoice-policy">
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div>
                                            <table class="table" style="border-collapse: initial">
                                                <tr>
                                                    <tbody>
                                                    <td class="td-without-padding">Subtotal</td>
                                                    <td class="td-without-padding">
                                                        @if($sale->sub_total_amount>=0)
                                                            ${{ number_format($sale->sub_total_amount, 2)}}
                                                        @else
                                                            -${{ number_format((-1) * $sale->sub_total_amount, 2) }}
                                                        @endif
                                                    </td>
                                                    </tbody>
                                                    @if( isset($sale->tax) )
                                                        <tbody>
                                                        <td>Tax</td>
                                                        <td>
                                                            @if($sale->tax_amount>=0)
                                                                ${{ number_format($sale->tax_amount, 2)}}
                                                            @else
                                                                ${{ number_format((-1) * $sale->tax_amount, 2) }}
                                                            @endif
                                                        </td>
                                                        </tbody>
                                                    @endif
                                                    <tbody>
                                                    <td >Total</td>
                                                    <td>
                                                        @if($sale->total_amount>=0)
                                                            ${{ number_format($sale->total_amount, 2)}}
                                                        @else
                                                            -${{ number_format((-1) * $sale->total_amount, 2) }}
                                                        @endif
                                                    </td>
                                                    </tbody>
                                                    <tbody>
                                                    <td >Due</td>
                                                    <td>
                                                        @if($sale->due>=0)
                                                            ${{  number_format($sale->due, 2) }}
                                                        @else
                                                            -${{  number_format((-1) * $sale->due, 2) }}
                                                        @endif
                                                    </td>
                                                    </tbody>
                                                    <tbody>
                                                    <td  style="padding:10px">Payment</td>
                                                    <td>

                                                    </td>
                                                    </tbody>
                                                    @foreach($sale->paymentlogs as $aPayment)
                                                        <tbody>
                                                        <td>
                                                            {{array_search($aPayment->payment_type, \App\Enumaration\PaymentTypes::$TypeList) }} Tendered
                                                        </td>
                                                        <td>
                                                            @if($aPayment->paid_amount>0)
                                                                ${{$aPayment->paid_amount}}
                                                            @else
                                                                -${{number_format((-1) * $aPayment->paid_amount, 2)}}
                                                            @endif
                                                        </td>
                                                        </tbody>
                                                    @endforeach
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            </div>

            <div class="col-xs-6 pull-right" style="padding-right: 0px; height: 600px">
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
@endsection


@section('additionalJS')

    <script src="{{asset("js/vue/vue.min.js")}}"></script>
    <script src="{{asset("js/axios/axios.min.js")}}"></script>
    <script src="{{asset("js/lodash/lodash.min.js")}}"></script>


    <script>

        $(document).ready(function(e){
            @if(!\Illuminate\Support\Facades\Cookie::get('counter_id'))
            changeCounter();
            @endif
        });


        new Vue({
            e1: '#app',
           data: function(){
                return {

                }
            },
        });




    </script>
@stop

