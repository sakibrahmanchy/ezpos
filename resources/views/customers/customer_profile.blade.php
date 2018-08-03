    @extends('layouts.master')

    @section('pageTitle','Customer Profile')

    @section('breadcrumbs')
        {!! Breadcrumbs::render('customer_list') !!}
    @stop

    @section('content')
            <style>
                .rightgap {
                    margin-right: 5px;
                }
            </style>
            <div class="row">
                <div class="col-md-3">

                    <!-- Profile Image -->
                    <div class="box box-primary">
                        <div class="box-body box-profile">
                            {{--<img class="profile-user-img img-responsive img-circle" src="" alt="User profile picture">--}}
                            @if($customer->image_token!=null)
                                <img src="{{asset('img/customers/userpictures/'.$customer->image_token)}}"  class="profile-user-img img-responsive img-circle" id="image_empty" alt="" />
                            @else
                                <img src="{{asset('img/faces/face-0.jpg')}}" class="profile-user-img img-responsive img-circle"   id="image_empty" alt="" />
                            @endif
                            <h3 class="profile-username text-center">{{ $customer->first_name }} {{ $customer->last_name }}</h3>

                            <p class="text-muted text-center">{{ $customer->company_name }}</p>

                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b>Account No</b> <a class="pull-right">#{{ $customer->account_number }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Loyalty Card:</b> <a class="pull-right">@if($customer->loyalty_card_number) {{ $customer->loyalty_card_number }} @else None @endif</a>
                                </li>
                                @if($customer->loyalty_card_number)
                                    <li class="list-group-item">
                                        <b>Loyalty Card Balance: <a class="pull-right"><label class="label label-default" name="loyalty_card_number">${{ round($customer->balance,2) }}</label></a></b>
                                    </li>
                                @endif
                                <li class="list-group-item">
                                    <b>Email</b> <a class="pull-right">{{ $customer->email }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Phone</b> <a class="pull-right">{{ $customer->phone }}</a>
                                </li>
                                @if(isset($customer->transactionSum))
                                    @php $due =  $customer->transactionSum->get(0); @endphp
                                    @if(isset($due))
                                        <br>
                                        <b>Due</b> <a class="pull-right"><label class="label label-warning" style="font-size:16px;">{{ $customer->transactionSum->get(0)->totalDue }}</label> </a>
                                        <br>
                                        <a href="{{ route('customer_balance_add',["customer_id"=>$customer->id]) }}" class="pull-right" style="margin-top: 5px">Add Balance</a>
                                        <br>
                                    @endif
                                @endif
                            </ul>

                            @if(UserHasPermission("customer_add_update"))
                                <a href="{{route('customer_edit',['customer_id'=>$customer->id])}}" class="btn btn-primary btn-block">Edit Customer</a>
                                <a href="{{route('customer_assign_price_level_get',['customer_id'=>$customer->id])    }}" class="btn btn-primary btn-block">Assign Price Levels</a>
                            @endif
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->

                    <!-- About Me Box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Basic Information</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>

                            <p class="text-muted">@if( $customer->address_1){{ $customer->address_1 }}, @endif
                                @if($customer->city){{ $customer->city }}  - {{ $customer->zip }},@endif @if($customer->country) {{ $customer->country }} @endif</p>

                            <hr>

                            <strong><i class="fa fa-file-text-o margin-r-5"></i> Notes</strong>

                            <p>{{$customer->comments}}</p>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="row" style="margin-left: 5px;margin-right: 10px" >
                        <div class="tab-content">
                            @if(isset($customer))
                                @php $due =  $customer->transactionSum->get(0); @endphp
                                <div class="row">
                                    <div class="col-lg-3 col-xs-6">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                                <h3> {{ $saleTotal }}</h3>

                                                <p>Total Orders</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-bag"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-xs-6">
                                        <!-- small box -->
                                        <div class="small-box bg-green">
                                            <div class="inner">
                                                @if(isset($due))
                                                    <h3 class="count">{{ $customer->transactionSum->get(0)->total_receivable }}</h3>
                                                @else
                                                    <h3 class="count">0</h3>
                                                @endif
                                                <p>Total Receivable</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ./col -->
                                    <div class="col-lg-3 col-xs-6">
                                        <!-- small box -->
                                        <div class="small-box bg-green">
                                            <div class="inner">
                                                @if(isset($due))
                                                    <h3 class="count">{{ $customer->transactionSum->get(0)->totalPaid }}</h3>
                                                @else
                                                    <h3 class="count">0</h3>
                                                @endif
                                                <p>Total Paid</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ./col -->
                                    <div class="col-lg-3 col-xs-6">
                                        <!-- small box -->
                                        <div class="small-box bg-yellow">
                                            <div class="inner">
                                                @if(isset($due))
                                                    <h3 class="count">{{ $customer->transactionSum->get(0)->totalDue }}</h3>
                                                @else
                                                    <h3 class="count">0</h3>
                                                @endif

                                                <p>Total Due</p>
                                            </div>
                                            <div class="icon">
                                                <i class="ion ion-person-add"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="nav-tabs-custom">
                                <div class="nav-tabs-custom">
                                    {{--<h3 class="box-title" >Latest Sales</h3>--}}
                                    <ul class="nav nav-tabs pull-right">
                                        <li ><a href="#latest_transactions" data-toggle="tab" aria-expanded="false">Latest Transactions</a></li>
                                        <li class="active" style="color:white"><a href="#due_details" data-toggle="tab" aria-expanded="true">Due Details</a></li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane" id="latest_transactions">
                                            <div class="box-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                                                        <thead>
                                                        <tr>
                                                            <th align="left" class="header">Transaction Id</th>
                                                            <th align="left" class="header">Date</th>
                                                            <th align="left" class="header">Description</th>
                                                            <th>Sale Amount</th>
                                                            <th align="right" class="header">Amount Paid</th>
                                                            <!--<th allign="right" class="header">Due</th>-->
                                                        </tr>
                                                        </thead>
                                                        @php $due = 0; @endphp
                                                        <tbody id="data-table">
                                                        @foreach($customer->transactions as $aTransaction)
                                                            @php $due += ( $aTransaction->sale_amount - $aTransaction->paid_amount ); @endphp
                                                            <tr>
                                                                <td>##{{$aTransaction->id}}</td>
                                                                <td>{{$aTransaction->created_at}}</td>
                                                                <td>
                                                                    @if(is_null($aTransaction->sale_id))
                                                                        Due/Advance Amount Paid
                                                                    @else
                                                                        Paid for: <a href="{{ route('sale_receipt',["sale_id"=>$aTransaction->sale_id]) }}"> Sale {{ $aTransaction->sale_id }}</a>
                                                                    @endif
                                                                </td>
                                                                <td><strong style="font-size: 18px;">${{ $aTransaction->sale_amount }}</strong></td>
                                                                <td><strong style="font-size: 18px;">${{ $aTransaction->paid_amount }}</strong></td>
                                                            <!--<td><strong style="font-size: 18px;">${{  $due }}</strong></td>-->
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- /.table-responsive -->
                                            </div>
                                            <!-- /.box-body -->
                                            <div class="box-footer clearfix">

                                                <a href="{{ route('report_transaction_details',["customer_id"=>$customer->id]) }}" class="btn btn-sm btn-default btn-flat pull-right">View All Transactions</a>
                                            </div>
                                            <!-- /.box-footer -->
                                        </div>
                                        <div class="tab-pane active" id="due_details">
                                            <div class="box-body">
                                                <div class="row">

                                                    <div class="col-md-12">
                                                        <div class="form-inline">
                                                            <div class="form-group" style="float:right">
                                                                <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                                                    <input id="end_date_formatted" name="end_date_formatted" type="text" class="form-control" value="{{ date('Y-m-d') }}">
                                                                    <div class="input-group-addon">
                                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group" style="float:right">
                                                                <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                                                    <input id="start_date_formatted" name="start_date_formatted" type="text" class="form-control" value="">
                                                                    <div class="input-group-addon">
                                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                                    </div>
                                                                </div>
                                                                To
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="se-pre-con text-center hide">
                                                        <img height="30%" width="30%"  src = "{{ asset('img/loader.gif') }}" >
                                                    </div>
                                                </div>
                                                <br>
                                                <div id="table_view">
                                                    @include('customers.customer_due_details_table')
                                                </div>
                                                <!-- /.table-responsive -->
                                            </div>
                                            <!-- /.box-body -->
                                            <div class="box-footer clearfix">
                                                @if(count($dueList) != 0)
                                                    <a href="javascript:void(0)" id="clearPayment" onclick="clearPayments()" class="hidden btn btn-sm btn-success btn-flat">Mark as paid</a>
                                                    <a href="javascript:void(0)" onclick="generateInvoice()" class="btn btn-sm btn-default btn-flat pull-right">Generate Invoice</a>
                                                    <input type="text" name="hire_date" value="" id="last_date_of_payment" class="datepicker pull-right rightgap">
                                                    <span class="pull-right rightgap"><b>Last date of payment</b></span><br><br>
                                                    <span class="pull-right rightgap"><b class="invoice-error text-danger"></b></span>
                                                @endif
                                                <a href="{{ route('customer_invoices_list',["customer_id" => $customer->id]) }}" class="pull-right btn btn-info" >View Generated Invoices</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- /.box-header -->
                            </div>
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.nav-tabs-custom -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

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
    @endsection



    @section('additionalJS')
        <script>

            let selected = [];
            $(document).ready(function(){
                countAnimate();
                $('.select-payment').on('click',selectPayment);

                $(".checkboxes").click(function(){
                    selected = [];
                    $('input:checked').each(function() {
                        selected.push($(this).val());
                    });
                    if(selected.length > 0 ) {
                        $("#clearPayment").removeClass('hidden');
                    } else {
                        $("#clearPayment").addClass('hidden');
                    }
                })

                $(' #start_date_formatted, #end_date_formatted').change(function() {
                    $('.data').addClass('hide');
                    $('.se-pre-con').removeClass('hide');

                    var start_date_formatted = $("#start_date_formatted").val();
                    var end_date_formatted = $("#end_date_formatted").val();

                    $.ajax({
                        method: "POST",
                        url: "{{ route('customer_due_details_ajax') }}",
                        data: {
                            customer_id:"{{ $customer->id }}",
                            start_date_formatted: start_date_formatted,
                            end_date_formatted: end_date_formatted
                        }
                    }).done(function( data ) {

                        $("#table_view").html("");
                        $("#table_view").html(data.contents);

                        $('.se-pre-con').addClass('hide');
                        $('.data').removeClass('hide');
                    });
                });

            });

            function clearPayments() {
                selected = [];
                $('input:checked').each(function() {
                    selected.push($(this).val());
                });
                $('.invoice-error').text("");
                if(selected.length == 0) {
                    $('.invoice-error').text("Please select one or more due invoices to clear payment");
                } else {
                    getTotalDueForSelefctedSales(selected);

                }
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
                selected = [];
                $('input:checked').each(function() {
                    selected.push($(this).val());
                });

                $.ajax({
                    url: "{{route('clear_due_payments_selected')}}",
                    type: "post",
                    data: {
                        payment_type: payment_type,
                        transaction_list: selected,
                        customer_id: "{{ $customer->id }}"
                    },
                    success: function(response){
                        location.reload();

                    }
                });
            }


            function generateInvoice() {

                if($("#last_date_of_payment").val()=="") {
                    $('.invoice-error').text("Select Last date of payment");
                } else {
                   let dateToday = new Date();
                   let lastDayOfPayment = $("#last_date_of_payment").val();
                   if(new Date(lastDayOfPayment)<=dateToday) {
                       $('.invoice-error').text("Please select a later date from today");
                   } else {
                       $('.invoice-error').text("");

                       selected = [];
                       $('input:checked').each(function() {
                          selected.push($(this).val());
                       });
                       if(selected.length == 0) {
                           $('.invoice-error').text("Please select one or more due invoices to generate");
                       }else {
                           $.ajax({
                               url: "{{route('customer_invoice_generate')}}",
                               type: "post",
                               data: {
                                   last_date_of_payment: lastDayOfPayment,
                                   transaction_list: selected,
                                   customer_id: "{{ $customer->id }}"
                               },
                               success: function(response){
                                    if(response.success) {
                                        var url = '{{ route("customer_invoice", ":invoice_id") }}';
                                        url = url.replace(':invoice_id', response.invoice_id);
                                        window.location.href=url;
                                    }
                               }
                           });
                       }
                   }
                }

            }


            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            function countAnimate(){
                $('.count').each(function () {
                    var value = $(this).text();

                    var unformatted = value.replace(",", "");
                    /*var value = parseInt($(this).text());*/

                    $(this).prop('Counter',0).animate({
                        Counter: unformatted
                    }, {
                        duration: 500,
                        easing: 'swing',
                        step: function (now) {
                            if(now>=0)
                                $(this).html("<sup  style='font-size: 20px'>$</sup>"+numberWithCommas(now.toFixed(2)));
                            else
                                $(this).html("- <sup  style='font-size: 20px'>$</sup>"+numberWithCommas((-1) * now.toFixed(2)));
                        }
                    });
                });
            }
        </script>
    @stop