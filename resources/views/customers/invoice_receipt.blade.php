
@extends('layouts.master')

@section('pageTitle','Invoice Receipt')

{{--@section('breadcrumbs')--}}
    {{--{!! Breadcrumbs::render('sale_receipt',$invoice->id) !!}--}}
    {{--<span><label class="label label-primary pull-right counter-name"><b>{{ \Illuminate\Support\Facades\Cookie::get('counter_name') }}</b></label></span>--}}
    {{--<br><br>--}}
    {{--<a href="javascript:void(0)"  onclick="changeCounter()" class="pull-right">Change Location</a>--}}
    {{--<br>--}}
{{--@stop--}}

@php

    $idList  = array();
    foreach ($invoice->transactions as $aTransaction) {
        array_push($idList, $aTransaction->id);
    }

@endphp

@section('content')
		<style>
			*{
				font-weight: bold!important;
			}
		</style>
        <form action="{{route("print_sale", ["sale_id"=>$invoice->id])}}" id="printSaleRecieptForm" method="GET" style="display: none;">
            <input type="hidden" name="sale_id" id="sale_id" value="{{$invoice->id}}">
            <input type="hidden" name="print_type" id="print_type" value="1">
            <input type="hidden" name="counter_id" id="counter_id" value="{{ \Illuminate\Support\Facades\Cookie::get('counter_id') }}">
        </form>

        <div class="box box-primary" id="receipt_wrapper_inner">
            <div class="panel panel-piluku">
                <div class="panel-body panel-pad">

                    <div class="row">
                        <div class="panel-body panel-pad pull-right">
                            {{--@if(!$sale->refund_status)--}}
                                {{--<a  href="{{route('sale_edit',['sale_id'=>$invoice->id])}}" class="btn btn-primary">Edit Sale</a>--}}
                            {{--@endif--}}
                            {{--<a  href="javascript:void(0)" onclick="selectPrinterCounter()" class="btn btn-primary">Print In Specific Printer</a>--}}
                            {{--<a  href="javascript:void(0)" onclick="PrintElem()"  class="btn btn-primary">Print</a>--}}
                            <a  href="{{route('customer_invoice_pdf',['invoice_id'=>$invoice->id])}}" class="btn btn-primary">Download as PDF</a>
                            <a  href="{{route('customer_invoice_email',['invoice_id'=>$invoice->id])}}" class="btn btn-primary">Email to customer</a>
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
                        <div class="col-md-4 col-sm-4 col-xs-12 text-right" style="float:right" id="report_invoice">
                            <ul class="list-unstyled invoice-detail" style="margin-bottom:2px;">
                                <li class="big-screen-title">
                                    Invoice Receipt						 <br>
                                    <strong>{{date('m/d/Y h:i:s a', time()) }}</strong>
                                </li>
                                <li><span>Invoice ID: </span>Invoice No. {{$invoice->id}}</li>
                                {{--<li><span>Counter Name: </span><b>{{ $sale->counter->name }}</b></li>--}}
                                <li><span>Cashier: </span>{{\Illuminate\Support\Facades\Auth::user()->name }}</li>
                                @if(isset($invoice->customer->id))
                                    <li><span>Customer:</span> {{$invoice->customer->first_name}} {{$invoice->customer->last_name}}</li>
									@if($invoice->Customer->loyalty_card_number && strlen($invoice->Customer->loyalty_card_number)>0)
										<li>
										@php
											$loyalityCarNumber = $invoice->Customer->loyalty_card_number;
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
                    {{--<center>@if($sale->refund_status) <label style="font-size: 20px" class="label label-danger">DELETED/VOID</label> @endif</center><br><br>--}}

                    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                        <thead>
                        <tr>
                            <th align="left" class="header">Sale Id</th>
                            <th align="left" class="header">Date</th>
                            <th>Sale Amount</th>
                            <th align="right" class="header">Amount Paid</th>
                            <th align="right" class="header">Amount Due</th>
                            <!--<th allign="right" class="header">Due</th>-->
                        </tr>
                        </thead>
                        @php $due = 0; @endphp
                        <tbody id="data-table">
                        @foreach($invoice->transactions as $aTransaction)
                            @php $due += (  $aTransaction->sale_amount -  $aTransaction->paid_amount  ); @endphp
                            <tr>
                                <td><a href="{{ route('sale_receipt',['sale_id'=>$aTransaction->sale_id]) }}">{{ $aTransaction->sale_id }}</a></td>
                                <td>{{$aTransaction->created_at}}</td>
                                <td><strong style="font-size: 18px;">${{ number_format($aTransaction->sale_amount,2) }}</strong></td>
                                <td><strong style="font-size: 18px;">${{ number_format($aTransaction->paid_amount, 2) }}</strong></td>
                                <td><strong style="font-size: 18px;">${{ number_format($aTransaction->sale_amount - $aTransaction->paid_amount,2) }}</strong></td>
                            </tr>
                        @endforeach
                        <tr class="warning">
                            <td colspan="4" ><strong class="pull-right" style="font-size: 18px;margin-right: 115px">Total Due</strong></td>
                            <td><strong  style="font-size: 18px;">${{  number_format($due, 2) }}</strong></td>
                        </tr>
                        {{--<tr class="success">--}}
                            {{--<td colspan="5"><strong  class="pull-right" style="font-size: 18px;">Customer Advance Payment</strong></td>--}}
                            {{--<td><strong  style="font-size: 18px;">${{  number_format($advance, 2) }}</strong></td>--}}
                        {{--</tr>--}}
                        {{--<tr class="danger">--}}
                            {{--<td colspan="5"><strong  class="pull-right" style="font-size: 18px;">Current Due</strong></td>--}}
                            {{--<td><strong  style="font-size: 18px;">${{  number_format($due - $advance,2) }}</strong></td>--}}
                        {{--</tr>--}}
                        </tbody>
                    </table>

                    {{--<hr>--}}
                    {{--<div class="invoice-footer gift_receipt_element">--}}

                        {{--<div class="row">--}}
                            {{--<div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">--}}
                                {{--<div class="invoice-footer-heading">Sub Total</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-2 col-sm-2 col-xs-4">--}}
                                {{--<div class="invoice-footer-value">--}}
                                    {{--<strong>--}}
                                    {{--<span style="white-space:nowrap;"></span>--}}
                                         {{--@if($sale->sub_total_amount>=0)--}}
                                            {{--${{ number_format($sale->sub_total_amount, 2)}}--}}
                                         {{--@else--}}
                                            {{---${{ number_format((-1) * $sale->sub_total_amount, 2) }}--}}
                                         {{--@endif--}}
                                    {{--</strong>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--@if( $settings['tax_rate'] >0 )--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">--}}
                                    {{--<div class="invoice-footer-heading">Tax({{ $settings['tax_rate']  }}%)</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-2 col-sm-2 col-xs-4">--}}
                                {{--<div class="invoice-footer-value">--}}
                                    {{--<strong>--}}
                                        {{--<span style="white-space:nowrap;"></span>--}}
                                        {{--@if($sale->tax_amount>=0)--}}
                                            {{--${{ number_format($sale->tax_amount, 2)}}--}}
                                        {{--@else--}}
                                            {{--${{ number_format((-1) * $sale->tax_amount, 2) }}--}}
                                        {{--@endif--}}
                                    {{--</strong>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--@endif--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-offset-4 col-sm-offset-4 col-md-6 col-sm-6 col-xs-8">--}}
                                {{--<div class="invoice-footer-heading">Total</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-2 col-sm-2 col-xs-4">--}}
                                {{--<div class="invoice-footer-value invoice-total">--}}

                                    {{--<strong>--}}
                                    {{--<span style="white-space:nowrap;"></span>--}}
                                        {{--@if($sale->total_amount>=0)--}}
                                            {{--${{ number_format($sale->total_amount, 2)}}--}}
                                        {{--@else--}}
                                            {{---${{ number_format((-1) * $sale->total_amount, 2) }}--}}
                                        {{--@endif--}}
                                    {{--</strong>--}}

                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}


                        {{--<div class="row">--}}
                            {{--<div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6">--}}
                                {{--<div class="invoice-footer-heading">{{$sale->due>=0?'Due': 'Change Due'}}</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-2 col-sm-2 col-xs-4">--}}
                                {{--<div class="invoice-footer-value invoice-total">--}}
                                    {{--@if($sale->due>=0)--}}
                                         {{--${{  number_format($sale->due, 2) }}--}}
                                    {{--@else--}}
                                        {{---${{  number_format((-1) * $sale->due, 2) }}--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="row">--}}
                            {{--<div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6"><br>--}}
                                {{--<div class="invoice-footer-heading"><strong>Payments</strong></div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--@foreach($sale->paymentlogs as $aPayment)--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6">--}}
                                {{--<div class="invoice-footer-heading">{{array_search($aPayment->payment_type, \App\Enumaration\PaymentTypes::$TypeList) }} Tendered:</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-2 col-sm-2 col-xs-4">--}}
                                {{--<div class="invoice-footer-value invoice-total">--}}
                                    {{--@if($aPayment->paid_amount>0)--}}
                                        {{--${{$aPayment->paid_amount}}--}}
                                    {{--@else--}}
                                        {{---${{number_format((-1) * $aPayment->paid_amount, 2)}}--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}


                        {{--@endforeach--}}
                        {{--@if(!is_null($sale->comment))--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6"><br>--}}
                                {{--<div class="invoice-footer-heading"><strong>Comments</strong></div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2"><br>--}}
                                {{--<div class="invoice-footer-heading">{{ $sale->comment }}</div><br>--}}
                            {{--</div><br><br>--}}
                        {{--</div>--}}
                        {{--@endif--}}

                        {{--<div class="row">--}}
                            {{--<div class="col-md-12 col-sm-12 col-xs-12">--}}
                                {{--<div class="text-center">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<center>CUSTOMER COPY</center>--}}
                    {{--<!-- invoice footer-->--}}
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            {{--<div class="invoice-policy">--}}
                                {{--Change return policy			            </div>--}}
                            {{--<div id="receipt_type_label" style="display: none;" class="receipt_type_label invoice-policy">--}}
                                {{--Merchant Copy						</div>--}}


                                {{--<p >{{ $settings['company_name'] }} Invoice {{ $invoice->id }}</p>--}}

                            <div id="announcement" class="invoice-policy">
                                @if($due>0)
                                    <a href="javascript:void(0)" id="clearPayment" onclick="clearPayments()" class=" btn btn-sm btn-success btn-flat pull-right">Mark as paid</a>
                                @else
                                    <a href="javascript:void(0)" id="clearPayment" onclick= class=" btn btn-sm btn-warning btn-flat pull-right">Undo Payment</a>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3">
                            <div id="signature">


                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="choose_payment_modal" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="chooseCounter">Mark as paid</h4>
                                </div>
                                <div class="modal-body">
                                    <p  class="label label-info" style="font-size: 20px" id="total_due"></p><br><br>
                                    <div>
                                        <p>&nbsp;&nbsp;&nbsp;<b>Choose a payment type</b></p>
                                        <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Cash">
                                            Cash				</a>
                                        <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Check">
                                            Check				</a>
                                        <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Debit Card">
                                            Debit Card				</a>
                                        <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Credit Card">
                                            Credit Card				</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <center>
                        @if($due>0)
                            <label class="" style="font-size: 30px; color: white; background: #f39c12; padding: 10px;"> UNPAID</label>
                        @else
                            <label class="" style="font-size: 30px; color: white; background: #00a65a; padding: 10px;"> PAID</label>
                        @endif
                    </center>
                </div>
                <!--container-->
            </div>
        </div>
        <br><br>


        <div class="modal fade" id="choose_payment_modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="chooseCounter">Mark as paid</h4>
                    </div>
                    <div class="modal-body">
                        <p  class="label label-info" style="font-size: 20px" id="total_due"></p><br><br>
                        <div>
                            <p>&nbsp;&nbsp;&nbsp;<b>Choose a payment type</b></p>
                            <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Cash">
                                Cash				</a>
                            <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Check">
                                Check				</a>
                            <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Debit Card">
                                Debit Card				</a>
                            <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Credit Card">
                                Credit Card				</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--<div class="modal fade" id="choose_counter_modal" role="dialog">--}}
            {{--<div class="modal-dialog" role="document">--}}
                {{--<div class="modal-content">--}}
                    {{--<div class="modal-header">--}}
                        {{--<h4 class="modal-title" id="chooseCounter">Choose Counter</h4>--}}
                    {{--</div>--}}
                    {{--<div class="modal-body">--}}
                        {{--<ul class="list-inline choose-counter-home">--}}

                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

        {{--<div class="modal fade" id="choose_printer_counter_modal" role="dialog">--}}
            {{--<div class="modal-dialog" role="document">--}}
                {{--<div class="modal-content">--}}
                    {{--<div class="modal-header">--}}
                        {{--<h4 class="modal-title" id="chooseCounter">Choose Counter</h4>--}}
                    {{--</div>--}}
                    {{--<div class="modal-body">--}}
                        {{--<ul class="list-inline choose-counter-home">--}}
                            {{--@foreach( $counter_list as $aCounter )--}}
                                {{--<li><a class="set_employee_current_counter_after_login" data-counter-id="{{$aCounter->id}}" href="{{  route('print_sale',['sale_id'=>$invoice->id, "print_type"=>1, "counter_id" =>$aCounter->id ]) }}">{{$aCounter->name}}</a></li>--}}
                            {{--@endforeach--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        @php
            $customer_profile_url = route('customer_invoice',["customer_id"=>$invoice->id])
        @endphp
        <div class="hidden">
            <form  id="selectedTransactionsSubmitForm" action="{{ route('clear_customer_invoice',["invoice_id"=>$invoice->id]) }}" method="post">
                {{ csrf_field() }}
                <input name = "pre_intended_url" value="{{ $customer_profile_url }}">
                <input name = "customer_id" id="customer_id">
                <input name = "payment_type" id="payment_type">
            </form>
        </div>



@endsection



@section('additionalJS')
<script>

    var selected = <?php echo json_encode($idList) ?>;

    $(document).ready(function ( ){
        $('.select-payment').on('click',selectPayment);
    });

    function PrintElem()
    {
        var mywindow = window.open('', 'PRINT');
        mywindow.document.write(document.getElementById("receipt_wrapper_inner").innerHTML);
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/
        mywindow.print();
        mywindow.close();

        return true;
    }

    function clearPayments() {
            console.log(selected);
            getTotalDueForSelefctedSales(selected);
    }

    function getTotalDueForSelefctedSales(selectedIds) {
        $.ajax({
            url: "{{route('customer_due_selected_total')}}",
            type: "post",
            data: {
                transaction_list: selectedIds,
            },
            success: function(response){
                $("#total_due").html('Total amount to pay: $'+response);
                $("#choose_payment_modal").modal();

            }
        });
    }



    function selectPayment(e)
    {
        e.preventDefault();

        $('#payment_types').attr("data-value",($(this).attr('data-payment')));
        $('.select-payment').removeClass('active');
        $(this).addClass('active');

        let payment_type = $(this).attr('data-payment');


        selected.forEach(function(id) {
            $("#selectedTransactionsSubmitForm").append('<input name = "transaction_list[]" value="'+id+'">')
        });

        $("#customer_id").val(customer_id);
        $("#payment_type").val(payment_type);
        // console.log($("#selectedTransactionsSubmitForm"));
        $("#selectedTransactionsSubmitForm").submit();
    }

</script>

@stop