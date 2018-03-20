@extends('layouts.master')

@section('pageTitle','Customer Profile')

@section('breadcrumbs')
    {!! Breadcrumbs::render('customer_list') !!}
@stop

@section('content')
    {{--<div class="filter-box">--}}
    {{--<div class="row">--}}
    {{--<div class="input-group">--}}
    {{--<button style="margin-left:10px" class="btn btn-primary" id="deleteButton">Add Balance</button>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="box box-primary" style="padding:20px">--}}
    {{--<div class = "row">--}}
    {{--<div class="col-md-12 table-responsive">--}}
    {{--<h3>Customer Name: {{ $customer->first_name }} {{ $customer->last_name }}</h3>--}}
    {{--<h3>Customer Email: {{ $customer->email }}</h3>--}}
    {{--<h4 class="text text-danger">Due: {{ $customer->transactionSum->get(0)->total }}</h4>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    <section class="content">

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
                                    <b>Due</b> <a class="pull-right"><label class="label label-warning" style="font-size:16px;">${{ $customer->transactionSum->get(0)->totalDue }}</label> </a>
                                    <br>
                                    <a href="" class="pull-right" style="margin-top: 5px">Add Balance</a>
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
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#activity" data-toggle="tab">Activity</a></li>
                        <li><a href="#statistics" data-toggle="tab">Balance</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h3>{{ $saleTotal->count() }}</h3>

                                        <p>Total Orders</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-bag"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3><sup style="font-size: 20px">$</sup>{{ $customer->transactionSum->get(0)->totalPaid }}</h3>

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
                                        <h3><sup style="font-size: 20px">$</sup>{{ $customer->transactionSum->get(0)->totalDue }}</h3>

                                        <p>Total Due</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-person-add"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-xs-6">
                                <!-- small box -->
                                <div class="small-box bg-red">
                                    <div class="inner">
                                        <h3>65</h3>

                                        <p>Total Paid</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-pie-graph"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- ./col -->
                        </div>
                        <div class="active tab-pane" id="activity">
                            <!-- Post -->
                            <div class="post">
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
                                            <table class="table no-margin">
                                                <thead>
                                                <tr>
                                                    <th>Sale ID</th>
                                                    <th>Number of Items Sold</th>
                                                    <th>Sale Amount</th>
                                                    <th>Amount Due</th>
                                                    <th>Payment Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($sales as $aSale)
                                                    <tr>
                                                        <td><a href="{{ route('sale_receipt',['sale_id'=>$aSale->id]) }}">{{ $aSale->id }}</a></td>
                                                        <td>{{ $aSale->items_sold }}</td>
                                                        <td>${{ $aSale->total_amount }}</td>
                                                        <td>{{ $aSale->due }}</td>
                                                        <td>
                                                            @if( $aSale->due>0 )
                                                                <label class="label label-danger">Unpaid</label>
                                                            @else
                                                                <label class="label label-success">Paid</label>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- /.table-responsive -->
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer clearfix">

                                        <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Transactions</a>
                                    </div>
                                    <!-- /.box-footer -->
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="statistics">
                            <!-- The timeline -->
                          
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

    </section>


@endsection

<div class="modal modal-default  fade" id="deleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Delete</h4>
            </div>
            <div class="modal-body">
                <p>You are requesting for a delete operation, which cannot be reverted. Are you sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancel</button>
                <button id="confirmDelete" type="button" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>



@section('additionalJS')
    <script>


    </script>
@stop