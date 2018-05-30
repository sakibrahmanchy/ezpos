<div class="table-responsive">
    <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
        <thead>
        <tr>
            <th align="left" class="header">Register Log Id</th>
            <th align="left" class="header">Counter</th>
            <th align="left" class="header">Open Employee</th>
            <th align="left" class="header">Close Employee</th>
            <th align="left" class="header">Shift Start</th>
            <th align="right" class="header">Shift End</th>
            <th align="right" class="header">Open amount</th>
            <th align="right" class="header">Close Amount</th>
            <th align="right" class="header">Cash Sales</th>
            <th align="right" class="header">Cash Additions</th>
            <th align="right" class="header">Cash Subtractions</th>
            <th align="right" class="header">Difference</th>

        </tr>
        </thead>
        <tbody id="data-table">
        @foreach($cash_registers as $aCashRegister)
            @php
                if(isset($aCashRegister->saleSum[0]))
                    $saleSum = $aCashRegister->saleSum[0]->aggregate;
                else
                    $saleSum = 0;
                if(isset($aCashRegister->additionSum[0]))
                    $additionSum = $aCashRegister->additionSum[0]->aggregate;
                else
                    $additionSum = 0;
                if(isset($aCashRegister->subtractionSum[0]))
                    $subtractionSum = $aCashRegister->subtractionSum[0]->aggregate;
                else
                    $subtractionSum = 0;

                $difference = ($aCashRegister->closing_balance) - ($aCashRegister->opening_balance + $saleSum + $additionSum - $subtractionSum);
            @endphp
            <tr>
                <td><a href="{{route('cash_register_log_details',["register_id"=>$aCashRegister->id])}}">{{ $aCashRegister->id }}</a></td>
                <td>{{ $aCashRegister->Counter->name }}</td>
                <td>{{ $aCashRegister->OpenedByUser->name }}</td>
                <td>{{ $aCashRegister->ClosedByUser->name }}</td>
                <td>{{ $aCashRegister->opening_time }}</td>
                <td>{{ $aCashRegister->closing_time }}</td>
                <td>${{ $aCashRegister->opening_balance }}</td>
                <td>${{ $aCashRegister->closing_balance }}</td>
                <td>${{ $saleSum }}</td>
                <td>${{ $additionSum }}</td>
                <td>${{ $subtractionSum }}</td>
                <td>@if($difference>=0)
                        ${{ $difference }}
                    @else
                        -${{ (-1) * round($difference,2) }}
                    @endif</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>