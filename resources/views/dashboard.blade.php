@extends('layouts.master')

@section('pageTitle','Dashboard')

@section('breadcrumbs')
    {!! Breadcrumbs::render('dashboard') !!}
@stop

@section('content')

        <div class="text-center">

            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <a href="{{ route('new_sale') }}">
                        <div class="dashboard-stats">
                            <div class="left">
                                <h3 class="flatBluec">{{  $info["total_sales"] }}</h3>
                                <h4>Total Sales</h4>
                            </div>
                            <div class="right flatBlue">
                                <i class="glyphicon glyphicon-shopping-cart"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <a href="{{ route('customer_list')}}">
                        <div class="dashboard-stats" id="totalCustomers">
                            <div class="left">
                                <h3 class="flatGreenc">{{  $info["total_customers"] }}</h3>
                                <h4>Total Customers</h4>
                            </div>
                            <div class="right flatGreen">
                                <i class="glyphicon glyphicon-user"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <a href="{{ route('item_list')}}">
                        <div class="dashboard-stats">
                            <div class="left">
                                <h3 class="flatRedc">{{  $info["total_items"] }}</h3>
                                <h4>Total Items</h4>
                            </div>
                            <div class="right flatRed">
                                <i class="glyphicon glyphicon-hdd"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <a href="{{ route('item_kit_list')}}">
                        <div class="dashboard-stats">
                            <div class="left">
                                <h3 class="flatOrangec">{{  $info["total_item_kits"] }}</h3>
                                <h4>Total Item Kits</h4>
                            </div>
                            <div class="right flatOrange">
                                <i class="glyphicon glyphicon-align-justify"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <h2 class="text-center" style="color:darkblue">Welcome to EZ Point Of Sale, choose a common task below to get started!</h2>
        <div class="row quick-actions">

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="list-group">
                    <a id="listBtn" class="list-group-item" href="{{ route('new_sale') }}"> <i class="glyphicon glyphicon-shopping-cart" style="margin-right: 10px"></i> Start a New Sale</a>
                </div>
            </div>


            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="list-group">
                    <a id="listBtn" class="list-group-item" href="{{ route('report_close_out_summary') }}"> <i class="glyphicon glyphicon-time"  style="margin-right: 10px"></i> Today's closeout report</a>
                </div>
            </div>



            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="list-group">
                    <a id="listBtn" class="list-group-item" href="{{ route('report_sale_detail') }}"> <i class="glyphicon glyphicon-stats"  style="margin-right: 10px"></i> Today's detailed sales report</a>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="list-group">
                    <a id="listBtn" class="list-group-item" href="{{ route('report_item_summary') }}"> <i class="glyphicon glyphicon-list-alt" style="margin-right: 10px"></i> Today's summary items report</a>
                </div>
            </div>


        </div>


        <div class="row ">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body nav-tabs-custom">

                        <div class="panel-heading">
                            <h4 class="text-center">Sales Information</h4>
                        </div>
                        <br><br>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs pull-right ui-sortable-handle" role="tablist">
                            <li role="presentation" class="active"><a href="#month" data-type="monthly" data-toggle="tab" aria-controls="month" role="tab">Month</a></li>
                            <li role="presentation"><a href="#week" data-type="weekly" data-toggle="tab" aria-controls="week" role="tab">Week</a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content ">
                            <div role="tabpanel" class="tab-pane active" id="month">
                                <div class="chart">
                                    <canvas id="chart-monthly" width="1187" height="296" style="width: 1187px; height: 296px;"></canvas>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="week">
                                <div class="chart">
                                    <canvas id="chart-weekly" width="1187" height="296" style="width: 1187px; height: 296px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>





@endsection

@section('additionalJS')

    <script>
        usedIndex = [];
        var myChart;
        $(document).ready(function(e){



            var dataset = JSON.parse('<?php echo json_encode($valueMonthly) ?>')
            backgroundColor = [];
            dataset.forEach(function(element){
                backgroundColor.push(getRandomColor());
            });

            var monthlyChart = document.getElementById("chart-monthly");
            myChart = new Chart(monthlyChart, {
                type: 'line',
                data: {
                    labels:  JSON.parse('<?php echo json_encode($labelMonthly) ?>'),
                    datasets: [{
                        label:"Monthly",
                        data:  dataset,
                        backgroundColor:backgroundColor,

                        borderWidth: 1
                    }]
                },
                options: {

                }
            });

            var dataset = JSON.parse('<?php echo json_encode($valueWeekly) ?>')

            var weeklyChart = document.getElementById("chart-weekly");
            myChart = new Chart(weeklyChart, {
                type: 'line',
                data: {
                    labels:  JSON.parse('<?php echo json_encode($labelWeekly) ?>'),
                    datasets: [{
                        label:"Weekly",
                        data:  dataset,
                        backgroundColor:backgroundColor,

                        borderWidth: 1
                    }]
                },
                options: {

                }
            });

        });


        function getRandomColor() {
            var color = randomColor({
                luminosity: 'light',
                hue: 'blue'
            });
            return color;
        }


    </script>



@stop