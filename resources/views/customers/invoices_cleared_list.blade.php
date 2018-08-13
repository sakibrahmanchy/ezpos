@extends('layouts.master')

@section('pageTitle','Cleared Invoices')

@section('content')
    <style>
        .rightgap {
            margin-right: 5px;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="table-responsive data">
                    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                        <thead>
                        <tr>
                            <th align="left" class="header">Invoice Id</th>
                            <th align="left" class="header">Date Created</th>
                            <th align="left" class="header">Last date of payment</th>
                            <th align="right" class="header">Total amount charged</th>
                            <th align="right" class="header">Show receipt</th>
                            <!--<th allign="right" class="header">Due</th>-->
                        </tr>
                        </thead>
                        @php $due = 0; @endphp
                        <tbody id="data-table">
                        @foreach($invoices as $anInvoice)
                            <tr>
                                <td> ##{{ $anInvoice->id }}</td>
                                <td>{{$anInvoice->created_at}}</td>
                                <td>{{$anInvoice->last_date_of_payment}}</td>
                                <td><strong style="font-size: 18px;">${{ $anInvoice->total_amount_of_charge }}</strong></td>
                                <td><strong style="font-size: 18px;"><a href="{{ route('customer_invoice',["invoice_id"=>$anInvoice->id]) }}"><button class="btn btn-info">Show receipt</button></a></strong></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <div class="modal fade" id="choose_payment_modal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="chooseCounter">Choose a payment type</h4>
                </div>
                <div class="modal-body">
                    <div style="padding:20px">

                        <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Cash">
                            Cash				</a>
                        <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Check">
                            Check				</a>
                        <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Debit Card">
                            Debit Card				</a>
                        <a tabindex="-1" href="#" class="btn btn-pay select-payment" data-payment="Credit Card">
                            Credit Card				</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('additionalJS')
    <script>

    </script>
@stop