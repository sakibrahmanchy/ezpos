<table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
    <thead>
    <tr>
        <th align="left" class="header">Transaction Id</th>
        <th align="left" class="header">Date</th>
        <th align="left" class="header">Description</th>
        <th>Sale Amount</th>
        <th align="right" class="header">Amount Paid</th>
        <th allign="right" class="header">Due</th>
    </tr>
    </thead>
    @php $due = 0; @endphp
    <tbody id="data-table">
    @foreach($info["transactionHistory"][0]->allTransactions as $aTransaction)
        @php $due += ( $aTransaction->sale_amount - $aTransaction->paid_amount ); @endphp
        <tr>
            <td>##{{$aTransaction->id}}</td>
            <td>{{$aTransaction->created_at}}</td>
            <td>
                @if(is_null($aTransaction->sale_id))
                    Due Amount Paid.
                @else
                    Paid for: <a href="{{ route('sale_receipt',["sale_id"=>$aTransaction->sale_id]) }}"> Sale {{ $aTransaction->sale_id }}</a>
                @endif
            </td>
            <td><strong style="font-size: 18px;">${{ $aTransaction->sale_amount }}</strong></td>
            <td><strong style="font-size: 18px;">${{ $aTransaction->paid_amount }}</strong></td>
            <td><strong style="font-size: 18px;">${{  $due }}</strong></td>
        </tr>
    @endforeach
    </tbody>
</table>