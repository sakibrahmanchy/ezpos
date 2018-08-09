<div class="table-responsive data">
    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only" id="invoiceTable">
        <thead>
        <tr>
            <th></th>
            <th align="left" class="header">Sale Id</th>
            <th align="left" class="header">Date</th>
            <th>Sale Amount</th>
            <th align="right" class="header">Amount Paid</th>
            <th align="right" class="header">Amount Due</th>
            <!--<th allign="right" class="header">Due</th>-->
        </tr>
        </thead>
        @php $due = 0; @endphp
        <tbody id="data-table">
        @foreach($dueList as $aTransaction)
            @php $due += ( $aTransaction->due ); @endphp
            <tr data-id="{{ $aTransaction->transaction_id }}">
                <td></td>
                {{--<td> <input class="checkboxes" type="checkbox" name="vehicle" value="{{ $aTransaction->transaction_id }}"></td>--}}
                <td><a href="{{ route('sale_receipt',['sale_id'=>$aTransaction->sale_id]) }}">{{ $aTransaction->sale_id }}</a></td>
                <td>{{$aTransaction->created_at}}</td>
                <td><strong style="font-size: 18px;">${{ $aTransaction->total_amount }}</strong></td>
                <td><strong style="font-size: 18px;">${{number_format($aTransaction->paid_amount, 2)  }}</strong></td>
                <td><strong style="font-size: 18px;">${{ $aTransaction->due }}</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>


    <div class="due hidden">
        <div class="row">
            <div class="col-xs-12" style="background: #fffcc0; padding-right: 100px; margin-bottom:20px">
                <span><strong  class="pull-right" style="font-size: 18px;margin-left:80px">${{  number_format($due, 2) }}</strong></span>
                <span><strong class="pull-right" style="font-size: 18px;">Total Due</strong></span>
            </div>
        </div>
   </div>



