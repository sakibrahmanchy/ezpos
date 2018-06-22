
@extends('layouts.master')

@section('pageTitle','Sales Receipt')

@section('breadcrumbs')
    {!! Breadcrumbs::render('sale_receipt',$sale->id) !!}
    <span><label class="label label-primary pull-right counter-name"><b>{{ \Illuminate\Support\Facades\Cookie::get('counter_name') }}</b></label></span>
    <br><br>
    <a href="javascript:void(0)"  onclick="changeCounter()" class="pull-right">Change Location</a>
    <br>
@stop

@section('content')
        <form action="{{route("print_sale", ["sale_id"=>$sale->id])}}" id="printSaleRecieptForm" method="GET" style="display: none;">
            <input type="hidden" name="sale_id" id="sale_id" value="{{$sale->id}}">
            <input type="hidden" name="print_type" id="print_type" value="1">
            <input type="hidden" name="counter_id" id="counter_id" value="{{ \Illuminate\Support\Facades\Cookie::get('counter_id') }}">
        </form>

        <div class="box box-primary" id="receipt_wrapper_inner">
            <div class="panel panel-piluku">
                <div class="panel-body panel-pad">

                    <div class="row">
                        <div class="panel-body panel-pad pull-right">
                            @if(!$sale->refund_status)
                                <a  href="{{route('sale_edit',['sale_id'=>$sale->id])}}" class="btn btn-primary">Edit Sale</a>
                            @endif
                            <a  href="javascript:void(0)" onclick="selectPrinterCounter()" class="btn btn-primary">Print In Specific Printer</a>
                            <a  href="{{route('print_sale',['sale_id'=>$sale->id, "print_type"=>1])}}" class="btn btn-primary">Print</a>
                            <a  href="{{route('print_sale',['sale_id'=>$sale->id, "print_type"=>2])}}" class="btn btn-primary">Print Pickup</a>
                            <!--<a  href="javascript: void(0);" id="nomralPrintButton" class="btn btn-primary">Print</a>
                            <a  href="javascript: void(0);" id="pickupPrintButton" class="btn btn-primary">Print Pickup</a>-->
                            <a  href="{{route('pop_open_cash_drawer')}}" class="btn btn-primary">Pop Open Cash Drawer</a>
                            <a  href="{{route('new_sale')}}" class="btn btn-primary">New Sale</a>
                            <a  href="{{route('download_sale_receipt',['sale_id'=>$sale->id])}}" class="btn btn-primary">Download as PDF</a>
                            <a  href="{{route('mail_sale_receipt',['sale_id'=>$sale->id])}}" class="btn btn-primary">Email to customer</a>
                        </div>
                    </div><br>

                    <div class="row">
                        <!-- from address-->
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

                        <!--  sales-->
                        <div class="col-md-4 col-sm-4 col-xs-12 text-right" style="float:right" >
                            <ul class="list-unstyled invoice-detail" style="margin-bottom:2px;">
                                <li class="big-screen-title">
                                    Sales Receipt						 <br>
                                    <strong>{{date('m/d/Y h:i:s a', time()) }}</strong>
                                </li>
                                <li><span>Sale ID: </span>{{$settings['company_name']}} No. {{$sale->id}}</li>
                                <li><span>Counter Name: </span><b>{{ $sale->counter->name }}</b></li>
                                <li><span>Cashier: </span>{{\Illuminate\Support\Facades\Auth::user()->name }}</li>
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
                        <!-- to address-->
                        <div class="col-md-4 col-sm-4 col-xs-12">

                        </div>

                        <!-- delivery address-->
                        <div class="col-md-12 col-sm-12 col-xs-12">


                        </div>

                    </div>

                    <br>
                    <!-- invoice items-->
                    <center>@if($sale->refund_status) <label style="font-size: 20px" class="label label-danger">DELETED/VOID</label> @endif</center><br><br>
                    <table  class="table table-hover table-responsive" >
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
                                    <span>{{ $anItem->item_name }}</span><br>@<span>{{ $anItem->pivot->unit_price }}</span>{{ $anItem->item_size==null ? "" : "/".$anItem->item_size }}
                                 @else
                                    {{ $anItem->item_name }}
                                 @endif
                                </td>
                                <td> ${{ $anItem->pivot->unit_price }} </td>
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
                    <hr>
                    <div class="invoice-footer gift_receipt_element">

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
                            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6">
                                <div class="invoice-footer-heading">{{$sale->due>=0?'Due': 'Change Due'}}</div>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-4">
                                <div class="invoice-footer-value invoice-total">
                                    @if($sale->due>=0)
                                         ${{  number_format($sale->due, 2) }}
                                    @else
                                        -${{  number_format((-1) * $sale->due, 2) }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6"><br>
                                <div class="invoice-footer-heading"><strong>Payments</strong></div>
                            </div>
                        </div>

                        @foreach($sale->paymentlogs as $aPayment)
                        <div class="row">
                            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6">
                                <div class="invoice-footer-heading">{{$aPayment->payment_type}} Tendered:</div>
                            </div>
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
                            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6"><br>
                                <div class="invoice-footer-heading"><strong>Comments</strong></div>
                            </div>
                            <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2"><br>
                                <div class="invoice-footer-heading">{{ $sale->comment }}</div><br>
                            </div><br><br>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="text-center">
                                </div>
                            </div>
                        </div>
                    </div>
                    <center>CUSTOMER COPY</center>
                    <!-- invoice footer-->
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

                        <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3">
                            <div id="signature">


                            </div>
                        </div>
                    </div>
                    <center>THANK YOU!</center>
                </div>
                <!--container-->
            </div>
        </div>
        <br><br>

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

        <div class="modal fade" id="choose_printer_counter_modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="chooseCounter">Choose Counter</h4>
                    </div>
                    <div class="modal-body">
                        <ul class="list-inline choose-counter-home">
                            @foreach( $counter_list as $aCounter )
                                <li><a class="set_employee_current_counter_after_login" data-counter-id="{{$aCounter->id}}" href="{{  route('print_sale',['sale_id'=>$sale->id, "print_type"=>1, "counter_id" =>$aCounter->id ]) }}">{{$aCounter->name}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

@endsection



@section('additionalJS')
<script>
    /***************************************Counter Change*****************/
	@if(\Illuminate\Support\Facades\Cookie::get('counter_id')==null)
		selectCounter();
	@endif

    function selectPrinterCounter() {
        $("#choose_printer_counter_modal").modal();
    }

    function selectCounter(){
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
        });
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

	$(document).ready(function(){
		$("#nomralPrintButton").click(function(){
			$("#print_type").val('1');
			$("#choose_counter_modal").modal('hide');
			$("#choose_printer_counter_modal").modal();
		});

        $("#pickupPrintButton").click(function(){
            $("#print_type").val('2');
			$("#choose_counter_modal").modal('hide');
			$("#choose_printer_counter_modal").modal();
        });

        $(".set_printing_counter").click(function(){
           var counterId = $(this).attr("data-counter-id");
           $("#counter_id").val(counterId);
           $("#printSaleRecieptForm").submit();
        })
	});

    /******************************Counter Change ****************************/
</script>

@stop