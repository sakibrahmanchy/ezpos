@extends('layouts.master')

@section('pageTitle','Add Cash To Register')

@section('breadcrumbs')
    {{--{!! Breadcrumbs::render('new_sale') !!}--}}
    <span><label class="label label-primary pull-right counter-name"><b>{{ \Illuminate\Support\Facades\Cookie::get('counter_name') }}</b></label></span>
    <br><br>
    <a href="javascript:void(0)"  onclick="changeCounter()" class="pull-right">Change Location</a>
    <br>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-piluku">
                <div class="panel-heading">
                    Use the form below to add cash to register			</div>
                <div class="panel-body">
                    <h3>You have already added ${{number_format($added_amount , 2)}} to the register</h3><form action="{{route('add_cash_to_register')}}" id="register_add_subtract_form" class="form-horizontal" method="post" accept-charset="utf-8" novalidate="novalidate">

                        <div class="form-group">
                            <label for="amount" class="col-sm-3 col-md-3 col-lg-2 control-label wide">Additional amount to add to register:</label>				    <div class="col-sm-9 col-md-9 col-lg-10">
                                <input type="text" name="amount" value="" size="8" class="form-control" id="amount">
                                <span class="text-danger">{{ $errors->first('amount') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="note" class="col-sm-3 col-md-3 col-lg-2 control-label wide">Comments:</label>				    <div class="col-sm-9 col-md-9 col-lg-10">
                                <textarea name="note" cols="30" rows="4" id="note" class="form-control text-area"></textarea>
                            </div>
                        </div>


                        <input type="submit" name="submitf" value="Save" id="submitf" class="submit_button btn btn-primary">
                    </form>				<div class="from-group text-right">
                        <a href="{{ route('pop_open_cash_drawer') }}" onclick="{{ route('pop_open_cash_drawer') }}" class="" target="_blank"><i class="ion-android-open"></i> Pop Open Cash Drawer</a>				</div>


                </div>
            </div>
        </div>
    </div>
@endsection
@section('additionalJS')
    <script>
        function calculate_total()
        {
            var total = 0;

            $(".denomination").each(function( index )
            {
                if ($(this).val())
                {
                    total+= $(this).data('value') * $(this).val();
                }
            });

            $("#opening_balance").val(parseFloat(Math.round(total * 100) / 100).toFixed(2));
        }

        $(".denomination").change(calculate_total);
        $(".denomination").keyup(calculate_total);

    </script>
@stop
