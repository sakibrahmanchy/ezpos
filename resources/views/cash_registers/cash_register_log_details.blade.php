@extends('layouts.master')

@section('pageTitle','Register Log Details')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="row">

                <div class="text-center">
                    <a class="btn btn-primary text-white hidden-print" id="print_button" href="{{ route('print_register_log_summary',["register_id" => $register->id]) }}"> Print Summary</a>
                    <a class="btn btn-primary text-white hidden-print" id="print_button" href="{{ route('print_register_log_details',["register_id" => $register->id]) }}"> Print Details</a>
                </div>
                <br>
                @php $difference = $register->opening_balance - ($register->closing_balance + $sales + $additions + $subtractions) @endphp
                <div class="col-md-12">

                    <div class="row" id="register_log_details">
                        <div class="col-lg-4 col-md-12">
                            <ul class="list-group">
                                <li class="list-group-item">Register log ID: <strong class="pull-right">{{ $register->id }}</strong></li>
                                <li class="list-group-item">Open Employee: <strong class="pull-right">{{ $opened_by }}</strong></li>
                                <li class="list-group-item">Close Employee: <strong class="pull-right">{{ $closed_by }}</strong></li>
                                <li class="list-group-item">Shift Start: <strong class="pull-right">{{ $register->opening_time }}</strong></li>
                                <li class="list-group-item">Shift End: <strong class="pull-right">{{ $register->closing_time }}</strong></li>
                                <li class="list-group-item">Open Amount: <strong class="pull-right">${{ number_format( $register->opening_balance, 2) }}</strong></li>
                                <li class="list-group-item">Close Amount: <strong class="pull-right">${{ number_format( $register->closing_balance, 2) }}</strong></li>
                                <li class="list-group-item">Cash Sales: <strong class="pull-right">${{ number_format($sales, 2) }}</strong></li>
                                <li class="list-group-item">Check Sales: <strong class="pull-right">${{ number_format($paymentInfo["checkTotal"], 2) }}</strong></li>
                                <li class="list-group-item">Credit Card Sales: <strong class="pull-right">${{ number_format($paymentInfo["creditCardTotal"], 2) }}</strong></li>
                                <li class="list-group-item">Debit Card Sales: <strong class="pull-right">${{ number_format($paymentInfo["debitCardTotal"], 2) }}</strong></li>
                                <li class="list-group-item">Gift Card Sales: <strong class="pull-right">${{ number_format($paymentInfo["giftCardTotal"], 2) }}</strong></li>
                                <li class="list-group-item">Loyalty Card Sales: <strong class="pull-right">${{ number_format($paymentInfo["loyalityTotal"], 2) }}</strong></li>
                                <li class="list-group-item">Change Due: <strong class="pull-right">${{ number_format($changedDue, 2) }}</strong></li>
                                <li class="list-group-item">Cash additions: <strong class="pull-right">${{ number_format($additions, 2) }}</strong></li>
                                <li class="list-group-item">Cash subtractions: <strong class="pull-right">${{ number_format($subtractions, 2) }}</strong></li>
                                <li class="list-group-item">Difference: <strong class="pull-right">{{ $difference >= 0 ? "$".number_format($difference,2) : "-$".number_format((-1) * $difference,2) }}</strong></li>
                                <li class="list-group-item">Notes: <strong class="pull-right"></strong></li>
                            </ul>
                        </div>

                        <div class="col-lg-8  col-md-12">
                            <div class="panel panel-piluku">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        Cash Additions and Subtractions						</h3>
                                </div>
                                <div class="panel-body nopadding table_holder  table-responsive">
                                    <table class="table  table-hover table-reports table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Employee</th>
                                            <th>Amount</th>
                                            <th>Notes</th>
                                            <th>Type</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($transactions as $aTransaction )
                                            <tr>
                                                <td>{{ $aTransaction->created_at }}</td>
                                                <td>{{ $closed_by }}</td>
                                                <td>{{ number_format($aTransaction->amount,2) }}</td>
                                                <td>{{ $aTransaction->comments }}</td>
                                                <td>
                                                    @if($aTransaction->transaction_type==\App\Enumaration\CashRegisterTransactionType::$ADD_BALANCE)
                                                        Cash added
                                                    @elseif($aTransaction->transaction_type==\App\Enumaration\CashRegisterTransactionType::$SUBTRACT_BALANCE)
                                                        Cash subtracted
                                                    @else
                                                        Cash from Sale
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Col-md-6 -->

                    </div>
                    <!-- row -->

                </div>
            </div>	</div>
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
