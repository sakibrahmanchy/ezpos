@extends('layouts.master')

@section('pageTitle','Close Cash Register')

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
                    Before you can get out of the sales register, you must enter a closeout amount.			</div>
                <div class="panel-body">


                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-striped text-center opening_bal">
                                <tbody><tr>
                                    <th>Denomination</th>
                                    <th>Count</th>
                                </tr>
                                @php $den_count = 0; @endphp
                                @foreach($denominations as $aDenomination)
                                    <tr>
                                        <td>{{$aDenomination->denomination_name}}</td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" name="denom.{{$aDenomination->id}}" value="" id="denomination_name[]" data-value="{{$aDenomination->denomination_value}}" class="form-control denomination">
                                            </div>
                                        </td>
                                    </tr>
                                    @php $den_count++; @endphp
                                @endforeach


                                </tbody></table>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <ul class="text-error" id="error_message_box"></ul>

                        <h3 class="text-right"><a href="https://demo.phppointofsale.com/index.php/reports/register_log_details/1" target="_blank">Details</a></h3>


                        <ul class="list-group close-amount">
                            <li class="list-group-item">Open Amount:  <span class="pull-right">${{ $openingBalance }}</span></li>
                            <li class="list-group-item">Cash Sales:  <span class="pull-right">${{ $sales }}</span></li>
                            <li class="list-group-item">Cash additions [<a href="">Edit</a>]:  <span class="pull-right">${{ $additions }} </span></li>
                            <li class="list-group-item">Cash subtractions [<a href="">Edit</a>]:  <span class="pull-right">${{$subtractions  }} </span></li>

                            <li class="list-group-item active">You should have  in the register. <span class="pull-right total-amount">{{ $openingBalance + $sales + $additions+ $subtractions }}</span></li>
                        </ul>



                        <div class="col-md-12">

                            <form action="{{ route('close_cash_register') }}" id="closing_amount_form" class="form-horizontal" method="post" accept-charset="utf-8" novalidate="novalidate">


                                <div class="form-group controll-croups1">
                                    <label for="closing_amount" class="control-label">Closing amount:</label>											<input type="text" name="closing_amount" value="{{ $openingBalance + $sales + $additions- $subtractions }}" id="closing_amount" class="form-control valid">
                                </div>
                                <div class="form-group controll-croups1">
                                    <label for="notes" class="control-label">Notes:</label>											<textarea name="notes" cols="40" rows="10" id="notes" class="form-control text-area"></textarea>
                                </div>

                                <div class="from-group text-right">
                                    <a href="https://demo.phppointofsale.com/index.php/sales/open_drawer" onclick="window.open('https://demo.phppointofsale.com/index.php/sales/open_drawer', '_blank', 'width=800,height=600,scrollbars=yes,menubar=no,status=yes,resizable=yes,screenx=0,screeny=0'); return false;" class="" target="_blank"><i class="ion-android-open"></i> Pop Open Cash Drawer</a>											</div>

                                <br>

                                <div class="form-group form-actions1">
                                    <input type="submit" id="close_submit" class="btn btn-primary" value="Submit">
                                </div>

                                <div style="text-align: center;">
                                    <h3>OR</h3>
                                    <input type="button" id="logout_without_closing" class="btn btn-danger" value="Logout without closing register (Next employee will close)">
                                </div>
                            </form></div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
@section('additionalJS')
    <script>
        $(".denomination").change(calculate_total);
        $(".denomination").keyup(calculate_total);
        $("#closing_amount").focus();

        $("#closing_amount").keypress(function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                check_amount();
            }
        });

        $('#close_submit').click(function(){
            check_amount();
        });

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

            $("#closing_amount").val(parseFloat(Math.round(total * 100) / 100).toFixed(2));
        }


    </script>
@stop
