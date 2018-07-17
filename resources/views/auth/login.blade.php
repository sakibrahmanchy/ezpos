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
    <link rel="stylesheet" href="{{ asset('css/iCheck/square/blue.css') }}">

    <!-- Google Font -->
    <link rel="stylesheet" href="{{ asset('fonts/google-fonts.css') }}">

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
        @if ( session()->has('error'))
            <center><label class="text text-danger">{{  session()->get('error') }}</label></center>
        @endif
        <form action="{{ url('/login') }}" method="post">
            {{ csrf_field() }}

            <div class="form-group has-feedback{{ $errors->has('identity') ? ' has-error' : '' }}">
                <input type="text" class="form-control" placeholder="Email or Username" value="{{ old('identity') }}" name="identity">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

                @if ($errors->has('identity'))
                    <span class="help-block">
                            {{ $errors->first('identity') }}
                        </span>
                @endif
            </div>

            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                <input type="password" class="form-control" placeholder="Password" name="password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                @if ($errors->has('password'))
                    <span class="help-block">
                            {{ $errors->first('password') }}
                        </span>
                @endif
            </div>

            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        {{--<label>--}}
                            {{--<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : ''}}> Remember Me--}}
                        {{--</label>--}}
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>

                    <a href="{{ route('pin_log_in') }}" role="button" class="btn btn-primary btn-block btn-flat">Enter PIN</a>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <div class="social-auth-links text-center">
            <p>- OR -</p>
        </div>
        <!-- /.social-auth-links -->

        <a href="{{ url('/password/reset') }}">I forgot my password</a><br>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/iCheck/icheck.min.js') }}"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>