@extends('layouts.master')

@section('pageTitle','Customer Add Balance')

@section('breadcrumbs')
    {!! Breadcrumbs::render('customer_list') !!}
@stop

@section('content')
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
    <div class="row">
        <div class="modal-body">
            <div class="box box-info" id="add_balance_section">
                <form method="post" action="{{ route('customer_balance_add') }}">
                    <div class="box-header with-border" style="background: #00c0ef; color:white;">
                        <h3 class="box-title" >Add New Balance</h3>
                    </div>
                    <div class="box-body">
                        <label for="customer_id">Customer</label>
                        <select class="form-control" name="customer_id" id="customer_id">
                            <option value="" @if($customer_id==0) selected @endif>Select customer</option>
                            @foreach($customers as $aCustomer)
                                <option @if($aCustomer->id==$customer_id) selected @endif value="{{ $aCustomer->id }}">{{ $aCustomer->first_name }} {{ $aCustomer->last_name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                        <br>
                        <label for="payment_method">Payment Method</label>
                        <select class="form-control" name="payment_method" id="payment_method">
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="Credit Card">Credit Card</option>
                        </select>
                        <span class="text-danger">{{ $errors->first('payment_method') }}</span><br>
                        <label for="amount_to_add">Amount to add</label>
                        <input type="text" class="form-control" name="amount_to_add" placeholder="Amount">
                        <span class="text-danger">{{ $errors->first('amount_to_add') }}</span><br>
                        <input type="submit" class="btn btn-info pull-right" name="submit" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@section('additionalJS')
    <script>
        $(document).ready(function(){
            $("#customer_id").select2();
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