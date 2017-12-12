
@extends('layouts.master')

@section('pageTitle','Reports')

@section('breadcrumbs')
    {!! Breadcrumbs::render('report_dashboard') !!}
@stop

@section('content')
    <div class="box box-primary" style="padding:20px">
        <div class="row report-listing">
            <div class="col-md-6  ">
                <div class="panel">
                    <div class="panel-body">
                        <div class="list-group parent-list">

                            <a href="#" class="list-group-item" id="categories"><i class="icon ti-layout-grid3"></i>	Categories</a>


                            <a href="#" class="list-group-item" id="closeout"><i class="icon ti-close"></i>	Closeout</a>

                            <a href="#" class="list-group-item" id="custom-report">
                                <i class="icon ti-search"></i>	Custom Report						</a>



                            <a href="#" class="list-group-item" id="customers"><i class="icon ti-user"></i>	Customers</a>


                            {{--<a href="#" class="list-group-item" id="deleted-sales"><i class="icon ti-trash"></i>	Deleted Sales</a>--}}

                            <a href="#" class="list-group-item" id="discounts"><i class="icon ti-wand"></i>	Discounts</a>

                            <a href="#" class="list-group-item" id="employees"><i class="icon ti-id-badge"></i>	Employees</a>

                           {{-- <a href="#" class="list-group-item" id="expenses"><i class="icon ti-money"></i>	Expenses</a>
    --}}
                            {{--<a href="#" class="list-group-item" id="giftcards"><i class="icon ti-credit-card"></i>	Giftcards</a>--}}


                            <a href="#" class="list-group-item" id="inventory"><i class="icon ti-bar-chart"></i>	Inventory Reports</a>


                            <a href="#" class="list-group-item" id="item-kits"><i class="icon ti-harddrives"></i>	Item Kits</a>



                            <a href="#" class="list-group-item" id="items"><i class="icon ti-harddrive"></i>	Items</a>

                            <a href="#" class="list-group-item" id="manufacturers"><i class="icon ti-layout-grid3"></i>	Manufacturers</a>



                            <a href="#" class="list-group-item" id="payments"><i class="icon ti-money"></i>	Payments</a>

                            <a href="#" class="list-group-item" id="profit-and-loss"><i class="icon ti-shopping-cart-full"></i>	Profit and Loss</a>

                            {{--<a href="#" class="list-group-item" id="receivings"><i class="icon ti-cloud-down"></i>	Receiving</a>--}}

                            {{--<a href="#" class="list-group-item" id="register-log"><i class="icon ti-search"></i>	Register Logs</a>--}}

                            <a href="#" class="list-group-item" id="sales"><i class="icon ti-shopping-cart"></i>	Sales</a>

                            {{--<a href="#" class="list-group-item" id="store-accounts"><i class="icon ti-credit-card"></i>	Store Accounts</a>--}}

                            <a href="#" class="list-group-item" id="suppliers"><i class="icon ti-download"></i>	Suppliers</a>

                            <a href="#" class="list-group-item" id="suspended_sales"><i class="icon ti-download"></i>	Suspended Sales</a>

                            {{--<a href="#" class="list-group-item" id="tags"><i class="icon ti-layout-grid3"></i>	Tags</a>

                            <a href="#" class="list-group-item" id="taxes"><i class="icon ti-agenda"></i>	Taxes</a>--}}


                            {{--<a href="#" class="list-group-item" id="tiers"><i class="icon ti-stats-up"></i>	Tiers</a>

                            <a href="#" class="list-group-item" id="timeclock"><i class="icon ti-bell"></i>	Time clock</a>--}}



                        </div>
                    </div>
                </div> <!-- /panel -->
            </div>
            <div class="col-md-6" id="report_selection">
                <div class="panel">
                    <div class="panel-body child-list">
                        <h3 id="right_heading" class="page-header text-info"><i class="icon ti-angle-double-left"></i>Make a selection</h3>
                        <div class="list-group custom-report hidden">
                            <a href="{{ route('search_sale') }}" class="list-group-item ">
                                <i class="icon ti-search report-icon"></i>  Detailed Search Report					</a>
                        </div>
                        <div class="list-group customers hidden">
                            <a class="list-group-item" href="{{ route('report_customer_graphical') }}"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a class="list-group-item" href="{{ route('report_customer_summary') }}"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a class="list-group-item" href="{{ route('report_customer_detail') }}"><i class="icon ti-calendar"></i> Detailed Reports</a>
                        </div>

                        <div class="list-group commissions hidden">
                            <a class="list-group-item" href="https://demo.phppointofsale.com/index.php/reports/graphical_summary_commissions"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>

                            <a class="list-group-item" href="https://demo.phppointofsale.com/index.php/reports/summary_commissions"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a class="list-group-item" href="https://demo.phppointofsale.com/index.php/reports/detailed_commissions"><i class="icon ti-calendar"></i> Detailed Reports</a>
                        </div>

                        <div class="list-group employees hidden">
                            <a class="list-group-item" href="{{ route('report_employee_graphical') }}"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a class="list-group-item" href="{{ route('report_employee_summary') }}"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a class="list-group-item" href="{{ route('report_employee_detail') }}"><i class="icon ti-calendar"></i> Detailed Reports</a>
                        </div>

                        <div class="list-group sales hidden">
                            <a class="list-group-item" href="{{ route('report_sale_graphical') }}"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a class="list-group-item" href="{{ route('report_sale_summary') }}"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a class="list-group-item" href="{{ route('report_sale_detail') }}"><i class="icon ti-calendar"></i> Detailed Reports</a>
                            <a class="list-group-item" href="{{ route('report_sale_summary_hourly') }}"><i class="icon ti-receipt"></i> Summary Sales by Time Reports</a>
                            <a class="list-group-item" href="{{ route('report_sale_graphical_hourly') }}"><i class="icon ti-bar-chart-alt"></i> Graphical Summary Sales by Time Reports</a>
                        </div>
                        <div class="list-group deleted-sales hidden">
                            <a href="https://demo.phppointofsale.com/index.php/reports/deleted_sales" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>
                        </div>
                        <div class="list-group register-log hidden">
                            <a href="https://demo.phppointofsale.com/index.php/reports/detailed_register_log" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>
                        </div>
                        <div class="list-group categories hidden">
                            <a href="{{ route('report_category_graphical') }}" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a href="{{ route('report_category_summary') }}" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                        </div>
                        <div class="list-group discounts hidden">
                            <a href="{{  route('report_discount_graphical') }}" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a href="{{  route('report_discount_summary') }}" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                        </div>
                        <div class="list-group items hidden">
                            <a href="{{ route('report_item_graphical') }}" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a href="{{ route('report_item_summary') }}" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                            {{--<a class="list-group-item" href="https://demo.phppointofsale.com/index.php/reports/summary_items_variance"><i class="icon ti-receipt"></i> Price Variance Report</a>--}}
                        </div>

                        <div class="list-group manufacturers hidden">
                            <a href="{{ route('report_manufacturer_graphical') }}" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a href="{{ route('report_manufacturer_summary') }}" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                        </div>


                        <div class="list-group item-kits hidden">
                            <a href="{{ route('report_itemkit_graphical') }}" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a href="{{  route('report_itemkit_summary') }}" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                            {{--<a class="list-group-item" href="https://demo.phppointofsale.com/index.php/reports/summary_item_kits_variance"><i class="icon ti-receipt"></i> Price Variance Report</a>--}}

                        </div>
                        <div class="list-group payments hidden">
                            <a href="{{ route('report_payment_graphical') }}" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a href="{{ route('report_payment_summary') }}" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a href="{{ route('report_payment_detail') }}" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>
                        </div>
                        <div class="list-group suppliers hidden">
                            <a href="{{ route('report_supplier_graphical') }}" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a href="{{ route('report_supplier_summary') }}" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a href="{{ route('report_supplier_detail') }}" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>
                            {{--<a href="https://demo.phppointofsale.com/index.php/reports/graphical_summary_suppliers_receivings" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical Receivings Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/summary_suppliers_receivings" class="list-group-item"><i class="icon ti-receipt"></i> Summary Receivings Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/specific_supplier_receivings" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Receivings Reports</a>--}}

                        </div>

                        <div class="list-group suspended_sales hidden">
                            <a href="{{ route('report_suspended_detail') }}" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>
                        </div>

                        <div class="list-group taxes hidden">
                            <a href="https://demo.phppointofsale.com/index.php/reports/graphical_summary_taxes" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/summary_taxes" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                        </div>

                        <div class="list-group timeclock hidden">
                            <a href="https://demo.phppointofsale.com/index.php/reports/summary_timeclock" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/detailed_timeclock" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>
                        </div>

                        <div class="list-group tiers hidden">
                            <a href="https://demo.phppointofsale.com/index.php/reports/summary_tiers" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                        </div>

                        <div class="list-group receivings hidden">
                            <a href="https://demo.phppointofsale.com/index.php/reports/detailed_receivings" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/detailed_suspended_receivings" class="list-group-item"><i class="icon ti-calendar"></i> Suspended Receivings</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/deleted_recevings" class="list-group-item"><i class="icon ti-receipt"></i> Deleted Receivings Report</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/summary_taxes_receivings" class="list-group-item"><i class="icon ti-receipt"></i> Summary taxes report</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/graphical_summary_taxes_receivings" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical summary taxes report</a>
                            <br>
                            <h4 class="text-info">Payments</h4>

                            <a href="https://demo.phppointofsale.com/index.php/reports/receivings_graphical_summary_payments" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/receivings_summary_payments" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/receivings_detailed_payments" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>

                        </div>
                        <div class="list-group inventory hidden">
                            <a href="{{ route('report_inventory_low') }}" class="list-group-item"><i class="icon ti-stats-down"></i> Low Inventory</a>
                            <a href="{{ route('report_inventory_summary') }}" class="list-group-item"><i class="icon ti-receipt"></i> Inventory Summary</a>
                            <a href="{{ route('report_inventory_detail') }}" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>
                        </div>
                        <div class="list-group giftcards hidden">
                            <a href="https://demo.phppointofsale.com/index.php/reports/summary_giftcards" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/detailed_giftcards" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/giftcard_audit" class="list-group-item"><i class="icon ti-calendar"></i> Audit Report</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/summary_giftcard_sales" class="list-group-item"><i class="icon ti-receipt"></i> Gift Card Sales Reports</a>

                        </div>
                        <div class="list-group store-accounts hidden">


                            <h4 class="text-info">Customers</h4>
                            <a href="https://demo.phppointofsale.com/index.php/reports/store_account_statements" class="list-group-item"><i class="icon ti-calendar"></i> Store Account Statements</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/summary_store_accounts" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/specific_customer_store_account" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/store_account_activity" class="list-group-item"><i class="icon ti-receipt"></i> Activity reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/store_account_outstanding" class="list-group-item"><i class="icon ti-stats-down"></i> Outstanding Sales</a>
                            <br>
                            <h4 class="text-info">Suppliers</h4>
                            <a href="https://demo.phppointofsale.com/index.php/reports/supplier_store_account_statements" class="list-group-item"><i class="icon ti-calendar"></i> Store Account Statements</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/supplier_summary_store_accounts" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/supplier_specific_store_account" class="list-group-item"><i class="icon ti-calendar"></i> Detailed Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/supplier_store_account_activity" class="list-group-item"><i class="icon ti-receipt"></i> Activity reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/supplier_store_account_outstanding" class="list-group-item"><i class="icon ti-stats-down"></i> Outstanding Receivings</a>
                        </div>
                        <div class="list-group profit-and-loss hidden">
                            <a class="list-group-item" href="{{ route('report_profit_loss_summary') }}"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a class="list-group-item" href="{{ route('report_profit_loss_detail') }}"><i class="icon ti-calendar"></i> Detailed Reports</a>
                        </div>
                        <div class="list-group expenses hidden">
                            <a class="list-group-item" href="https://demo.phppointofsale.com/index.php/reports/summary_expenses"><i class="icon ti-receipt"></i> Summary Reports</a>
                            <a class="list-group-item" href="https://demo.phppointofsale.com/index.php/reports/detailed_expenses"><i class="icon ti-calendar"></i> Detailed Reports</a>
                        </div>

                        <div class="list-group closeout hidden">
                            <a href="{{ route('report_close_out_summary') }}" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                        </div>

                        <div class="list-group tags hidden">
                            <a href="https://demo.phppointofsale.com/index.php/reports/graphical_summary_tags" class="list-group-item"><i class="icon ti-bar-chart-alt"></i> Graphical Reports</a>
                            <a href="https://demo.phppointofsale.com/index.php/reports/summary_tags" class="list-group-item"><i class="icon ti-receipt"></i> Summary Reports</a>
                        </div>


                    </div>
                </div> <!-- /panel -->
            </div>
        </div>
    </div>
@endsection



@section('additionalJS')

    <script>
        $('.parent-list a').click(function(e){
            e.preventDefault();
            $('.parent-list a').removeClass('active');
            $(this).addClass('active');
            var currentClass='.child-list .'+ $(this).attr("id");
            $('.child-list .page-header').html($(this).html());
            $('.child-list .list-group').addClass('hidden');
            $(currentClass).removeClass('hidden');
            $('#right_heading').addClass('active');
            $('html, body').animate({
                scrollTop: $("#report_selection").offset().top
            }, 500);
        });

    </script>



@stop