<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>{{ $settings['company_name'] }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/AdminLTE.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skins/skin-blue.min.css') }}">


    <!-- Google Font -->
    <link rel="stylesheet" href="{{ asset('css/fonts/google-fonts-1.css') }}">
    <link rel="stylesheet" href="{{ asset('css/metro-all.min.css') }}">
    <style>
        .keypad .keys.bottom-right {
            top: 100%;
            left: 100%;
            -webkit-transform: translateX(-100%);
            transform: translateX(-100%);
            margin-top: -1px;
            width: 220px !important;
        }
    </style>
</head>

<body class="skin-blue layout-top-nav hold-transition login-page">
<header class="main-header">
    <nav class="navbar navbar-static-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="{{ url('/') }}" class="navbar-brand">{{ $settings['company_name'] }}</a>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</header>

<div class="login-box">
    <div class="login-logo">
        Log In
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form action="{{ route('pin_log_in_post') }}" method="post">
            {{ csrf_field() }}


            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">

                @if ($errors->has('pin'))
                    <span class="help-block">
						{{ $errors->first('pin') }}
					</span>
                @endif

                <input type="password" data-role="keypad" data-key-size="60" data-open="true" data-length="4" data-position="bottom-right" data-cls-keys="bg-cyan fg-white" data-cls-backspace="bg-darkOrange fg-white" data-cls-clear="bg-darkRed fg-white" placeholder="Enter pin" name="pin">
            </div>

            <div class="row">
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/metro.min.js') }}"></script>
</body>
</html>