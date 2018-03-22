@extends('layouts.master')

@section('pageTitle','Customer Profile')

@section('breadcrumbs')
    {!! Breadcrumbs::render('customer_list') !!}
@stop

@section('content')

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
                                            <h3> {{ $saleTotal->count() }}</h3>

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
                        <div class="box box-info">
                            <div class="box-header with-border" style="background: #00c0ef; color:white;">
                                <h3 class="box-title" >Latest Sales</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <!-- /.box-header -->


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
                                            <th allign="right" class="header">Due</th>
                                        </tr>
                                        </thead>
                                        @php $due = 0; @endphp
                                        <tbody id="data-table">
                                        @foreach($transactionHistory[0]->transactions as $aTransaction)
                                            @php $due += ( $aTransaction->sale_amount - $aTransaction->amount_paid ); @endphp
                                            <tr>
                                                <td>##{{$aTransaction->id}}</td>
                                                <td>{{$aTransaction->created_at}}</td>
                                                <td>
                                                    @if(is_null($aTransaction->sale_id))
                                                        Due Amount Paid.
                                                    @else
                                                        Paid for: <a href="{{ route('sale_receipt',["sale_id"=>$aTransaction->sale_id]) }}"> Sale {{ $aTransaction->sale_id }}</a>
                                                    @endif
                                                </td>
                                                <td><strong style="font-size: 18px;">${{ $aTransaction->sale_amount }}</strong></td>
                                                <td><strong style="font-size: 18px;">${{ $aTransaction->amount_paid }}</strong></td>
                                                <td><strong style="font-size: 18px;">${{  $due }}</strong></td>
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




                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

@endsection



@section('additionalJS')
    <script>
        $(document).ready(function(){
            countAnimate();
        });


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