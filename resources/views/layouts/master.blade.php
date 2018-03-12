<?php

$employee = \App\Model\Employee::where("user_id",\Illuminate\Support\Facades\Auth::user()->id)->first();
if($employee!=null)
    $user_image_token = $employee->image_token;
else
    $user_image_token = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>{{ $settings['company_name'] }} admin</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">

    <!-- Jquery Ui -->
    <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet" />

    <!-- Dropzone css -->
    <link href={{ asset('css/dropzone.css')}} rel="stylesheet" />

    <!-- Tokenize Input css -->
    <link href={{ asset('css/token-input.css')}} rel="stylesheet" />
    <link href={{ asset('css/token-input-facebook.css')}} rel="stylesheet" />

    <!--  Light Bootstrap Table core CSS    -->
    <link href={{ asset('css/navbar-fixed-side.css')}} rel="stylesheet"/>


    <!-- Bootstrap Datepicker css -->
    <link href={{ asset('css/bootstrap-datepicker.css')}} rel="stylesheet"/>

    <!--     Fonts and icons     -->

    <link href='{{ asset('fonts/fonts.css') }}' rel='stylesheet' type='text/css'>
    <link href={{ asset('css/pe-icon-7-stroke.css')}} rel="stylesheet" />
    <link href={{ asset('css/tagit.ui-zendesk.css')}} rel="stylesheet" />
    <link href={{ asset('css/jquery.tagit.css')}} rel="stylesheet" />
    <link href="{{ asset('css/bootstrap-switch.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/ezpos.css') }}">
    <!-- Select2 -->
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="{{ asset('DataTables/datatables.min.css') }}"/>

    <link rel="stylesheet" href="{{ asset('css/AdminLTE.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skins/skin-blue.min.css') }}">

    <!-- Google Font -->
    <link rel="stylesheet" href="{{ asset('css/fonts/google-fonts-2.css') }}">




</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="{{--{{ route('home_view') }}--}}" class="logo" >
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>{{ $settings['company_name'][0] }}</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><img style="border-radius: 50px;padding:5px;" src="{{ asset('img/logo.png?'.rand()) }}" height="50px" width="50px"> <b>{{ $settings['company_name'] }}</b> Admin</span>
        </a>

        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <a href="{{--{{ route('home_view') }}--}}" class="navbar-brand">{{ $settings['company_name'] }}
            </a>

            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            @if($user_image_token!=null)
                                <img src="{{ asset('img/employees/userpictures/'.$user_image_token) }}" class="user-image" alt="User Image">
                            @else
                                <img src="{{ asset('img/profile.png') }}" class="user-image" alt="User Image">
                            @endif
                            <span class="hidden-xs">{{ Auth::user()->name }}</span>
                        </a>


                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">

                                @if($user_image_token!=null)
                                    <img src="{{ asset('img/employees/userpictures/'.$user_image_token) }}" class="img-circle" alt="User Image">
                                @else
                                    <img src="{{ asset('img/profile.png') }}" class="img-circle" alt="User Image">
                                @endif
                                <p>
                                    {{ Auth::user()->name }}
                                    <small>Member since {{ gmdate("F d, Y",strtotime(\Illuminate\Support\Facades\Auth::user()->created_at)) }}</small>
                                </p>
                            </li>

                            <!-- Menu Body -->
                            <!-- <li class="user-body">

                            </li> -->

                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{ route('user_profile_edit',["user_id"=>\Illuminate\Support\Facades\Auth::User()->id]) }}" class="btn btn-default btn-flat">Profile</a>
                                </div>

                                <div class="pull-right">
                                    <a href="#" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" class="btn btn-default btn-flat">Sign out</a>
                                    <form id="logout-form" action="{{route('logout')}}" method="POST" style="display: none;">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}"> </form>
                                    <a href="{{ route('change_settings') }}"><span class="hidden-xs"><i class="fa fa-cog fa-spin fa-fw margin-bottom"></i></span></a>

                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    @if($user_image_token!=null)
                        <img src="{{ asset('img/employees/userpictures/'.$user_image_token) }}" class="img-circle" alt="User Image">
                    @else
                        <img src="{{ asset('img/profile.png') }}" class="img-circle" alt="User Image">
                    @endif
                </div>
                <div class="pull-left info">
                    <p>{{ Auth::user()->name }}</p>
                    <p class="text-muted">EZPOS</p>
                </div>
            </div>

            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">
                {{--<li class="header">MAIN NAVIGATION</li>--}}
                <li class="{{ Request::is('home') ? 'active' : '' }}">
                    <a href="{{route('dashboard')}}">
                        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    </a>

                </li>

                @if(UserHasAccessToModule(\App\Enumaration\PermissionCategories::$CUSTOMER))
                <li class="{{ Request::is('customer/*') ? 'active' : '' }}">
                        <a href="{{route('customer_list')}}">
                        <i class="fa fa-users"></i> <span>Customers&nbsp;</span>
                    </a>
                </li>
                @endif

                @if(UserHasAccessToModule(\App\Enumaration\PermissionCategories::$ITEM))
                    <li class="{{ Request::is('item/*') ? 'active' : '' }}">
                        <a href="{{route("item_list")}}">
                            <i class="glyphicon glyphicon-hdd "></i><span> Items</span>
                        </a>
                    </li>
                @endif
                @if(UserHasAccessToModule(\App\Enumaration\PermissionCategories::$ITEM_KIT))
                    <li  class="{{ Request::is('itemkit/*') ? 'active' : '' }}">
                        <a href="{{route('item_kit_list')}}">
                            <i  class="glyphicon glyphicon-align-justify"></i><span>Item Kits&nbsp;</span>
                        </a>
                    </li>
                @endif
                @if(UserHasAccessToModule(\App\Enumaration\PermissionCategories::$PRICE_RULE))
                    <li  class="{{ Request::is('price_rule/*') ? 'active' : '' }}">
                        <a href="{{route('price_rule_list')}}">
                            <i  class="showopacity glyphicon glyphicon-tags"></i><span>Price Rules&nbsp;</span>
                        </a>
                    </li>
                @endif
                @if(UserHasAccessToModule(\App\Enumaration\PermissionCategories::$SUPPLIER))
                    <li  class="{{ Request::is('supplier/*') ? 'active' : '' }}">
                        <a href="{{route('supplier_list')}}">
                            <i class="glyphicon glyphicon-download-alt"></i><span>Suppliers&nbsp;</span>
                        </a>
                    </li>
                @endif
                @if(UserHasAccessToModule(\App\Enumaration\PermissionCategories::$REPORT))
                    <li class="nav-header {{ Request::is('report/*') ? 'active' : '' }}"> <a href="{{ route('report_dashboard') }}">
                            <i class="glyphicon glyphicon-stats"></i><span>Reports&nbsp;</span>
                        </a>
                    </li>
                @endif
                @if(UserHasAccessToModule(\App\Enumaration\PermissionCategories::$SALE))
                    <li  class="{{ Request::is('sale/*') ? 'active' : '' }}">
                        <a href="{{route('new_sale')}}">
                            <i class="glyphicon glyphicon-shopping-cart"></i><span> Sales&nbsp;</span>
                        </a>
                    </li>
                @endif
                @if(UserHasAccessToModule(\App\Enumaration\PermissionCategories::$EMPLOYEE))
                    <li class = {{ Request::is('employee/*') ? 'active' : '' }}>
                        <a href="{{route('employee_list')}}">
                            <i  class="fa fa-id-card"></i><span> Employees&nbsp;</span>
                        </a>
                    </li>
                @endif
                @if(UserHasAccessToModule(\App\Enumaration\PermissionCategories::$GIFT_CARDS))
                    <li class = {{ Request::is('gift_card/*') ? 'active' : '' }}>
                        <a href="{{route('gift_card_list')}}">
                            <i class="glyphicon glyphicon-gift"></i><span> Gift Card&nbsp;</span>
                        </a>
                    </li>
                @endif
                @if(UserHasAccessToModule(\App\Enumaration\PermissionCategories::$COUNTERS))
                    <li class = {{ Request::is('counter/*') ? 'active' : '' }}>
                        <a href="{{route('counter_list')}}">
                            <i class="fa fa-map-marker"></i><span> Counters&nbsp;</span>
                        </a>
                    </li>
                @endif
            </ul>
        </section>
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @yield('pageTitle')
            </h1>

            @yield('breadcrumbs')
        </section>

        <!-- Main content -->
        <section class="content">
                @yield('content')
        </section>
    </div>

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 0.3.0
        </div>

        <strong>Copyright &copy; 2014-{{date('Y')}} <a href="#">Grims Technologies</a>.</strong> All rights reserved.
    </footer>
</div> <!-- ./wrapper -->


<!-- JS Scripts -->

<!--   Core JS Files   -->
<script src="{{ asset('js/jquery.min.js')  }}" type="text/javascript" charset="utf-8"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}" type="text/javascript" charset="utf-8"></script>
<script src="{{ asset("js/tag-it.js")}}" type="text/javascript" charset="utf-8"></script>
<script src={{ asset('js/bootstrap.min.js')}} type="text/javascript"></script>

<!-- Admin LTE -->
<script src="{{ asset('js/adminlte.js') }}"></script>

<!-- Chart JS -->
<script src = "{{asset('js/Chart.min.js')}}" type="text/javascript" charset="UTF-8"></script>


<!-- Data table -->
<script type="text/javascript" src="{{ asset('DataTables/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset("DataTables/mark.min.js") }}"></script>
<script type="text/javascript" src="{{ asset("js/datatables.mark.js") }}"></script>
<!-- Select 2 -->
<script src="{{ asset('js/select2.min.js') }}"></script>

<!-- Bootstrap DatePicker JS -->
<script src={{ asset('js/bootstrap-datepicker.js')}} type="text/javascript"></script>

<!-- Token Input js -->
<script src={{ asset('js/jquery.tokeninput.js')}}></script>

<!--  Checkbox, Radio & Switch Plugins -->
<script src={{ asset('js/bootstrap-checkbox-radio-switch.js')}}></script>

<!--  Dropzone ZS -->
<script src={{ asset('js/dropzone.js')}}></script>

<!--  Charts Plugin -->

<!--  Notifications Plugin    -->
<script src={{ asset('js/bootstrap-notify.js')}}></script>

<!-- Random Color -->
<script src={{ asset('js/randomColor.js')}}></script>



<script type="text/javascript">

    $(document).ready(function(){

        $.extend(true, $.fn.dataTable.defaults, {
            mark: true
        });

        var is_error = '{{ ( session()->has('error')) ? session()->get('error') : 0 }}';

        if(is_error!="0"){

            $.notify({
                icon: 'pe-7s-gift',
                message: is_error

            },{
                type: 'danger',
                timer: 4000
            });

            {{  session()->forget('error') }}
        }

        var is_success = '{{ ( session()->has('success')) ? session()->get('success') : 0 }}';

        if(is_success!="0"){

            $.notify({
                icon: 'pe-7s-gift',
                message: is_success

            },{
                type: 'success',
                timer: 4000
            });
            {{  session()->forget('success') }}
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.datepicker').datepicker({
            orientation: "bottom",
            autoclose: true,
            format: 'yyyy/mm/dd'
        });


    });

</script>
@yield('additionalJS');
</body>
</html>
