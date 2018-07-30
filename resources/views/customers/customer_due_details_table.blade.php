<div class="table-responsive data">
    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
        <thead>
        <tr>
            <th>Select</th>
            <th align="left" class="header">Sale Id</th>
            <th align="left" class="header">Date</th>
            <th align="left" class="header">Description</th>
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
            <tr>
                <td> <input class="checkboxes" type="checkbox" name="vehicle" value="{{ $aTransaction->transaction_id }}"></td>
                <td>##{{$aTransaction->sale_id}}</td>
                <td>{{$aTransaction->created_at}}</td>
                <td>
                    Due for sale  <a href="{{ route('sale_receipt',['sale_id'=>$aTransaction->sale_id]) }}">{{ $aTransaction->sale_id }}</a>
                </td>
                <td><strong style="font-size: 18px;">${{ $aTransaction->total_amount }}</strong></td>
                <td><strong style="font-size: 18px;">${{ $aTransaction->paid_amount }}</strong></td>
                <td><strong style="font-size: 18px;">${{ $aTransaction->due }}</strong></td>
            </tr>
        @endforeach
        <tr class="warning">
            <td colspan="6" ><strong class="pull-right" style="font-size: 18px;">Total Due</strong></td>
            <td><strong  style="font-size: 18px;">${{  number_format($due, 2) }}</strong></td>
        </tr>
        </tbody>
    </table>
</div>