
    <div class="table-responsive col-md-12">
        <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
            <thead>

            <th>Description</th>
            <th>Data</th>

            </thead>
            <tbody id="data-table-sales">
            <tr><td><h3><b>All Transactions</b></h3></td><td> -- </td></tr>
            <tr >
                <td>Total (Without Tax)</td>
                <td id="total_without_tax">
                    @if($info['subtotal']>=0)
                        ${{ $info['subtotal'] }}
                    @else
                        ${{ (-1) * $info['subtotal'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Total (With Tax)</td>
                <td id="total_with_tax">
                    @if($info['total']>=0)
                        ${{ $info['total'] }}
                    @else
                        ${{ (-1) * $info['total'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Profit</td>
                <td id="profit">
                    @if($info['profit']>=0)
                        ${{ $info['profit'] }}
                    @else
                        ${{ (-1) * $info['profit'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Number of transactions</td>
                <td id="no_transactions">
                    {{ $info['no_transactions'] }}
                </td>
            </tr>
            <tr>
                <td>Average Ticket Size</td>
                <td id="ticket_size">
                    @php
                        if($info["no_transactions"]!=0) $avTicketSize =  number_format($info["total"]/$info["no_transactions"],2);
                        else $avTicketSize = 0;
                    @endphp
                    @if($avTicketSize>=0)
                        ${{ $avTicketSize}}
                    @else
                        ${{ (-1) * $avTicketSize }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Number of Items Sold</td>
                <td id="items_sold">
                    {{ $info['items_sold'] }}
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Tax</td>
                <td id="tax">
                    @if($info['tax']>=0)
                        ${{ $info['tax']}}
                    @else
                        ${{ (-1) * $info['tax'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Number of Items Sold</td>
                <td id="items_sold">
                    {{ $info['items_sold'] }}
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>

            <!-- Overall Payments -->
            @foreach($sales as $aSale)
                <tr>
                    <td>{{ $aSale->payment_type}}</td>
                    <td>
                        @if($aSale->paid_amount>=0)
                            ${{$aSale->paid_amount}}
                        @else
                            -${{ (-1) * $aSale->paid_amount }}
                        @endif
                    </td>
                </tr>
            @endforeach

            <!-- Sale Report -->

            <tr><td><h3><b>Sales</b></h3></td><td> -- </td></tr>
            <tr >
                <td>Total (Without Tax)</td>
                <td id="total_without_tax">
                    @if($infoSales['subtotal']>=0)
                        ${{ $infoSales['subtotal'] }}
                    @else
                        ${{ (-1) * $infoSales['subtotal'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Total (With Tax)</td>
                <td id="total_with_tax">
                    @if($infoSales['total']>=0)
                        ${{ $infoSales['total'] }}
                    @else
                        ${{ (-1) * $infoSales['total'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Profit</td>
                <td id="profit">
                    @if($infoSales['profit']>=0)
                        ${{ $infoSales['profit'] }}
                    @else
                        ${{ (-1) * $infoSales['profit'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Number of transactions</td>
                <td id="no_transactions">
                    {{ $infoSales['no_transactions'] }}
                </td>
            </tr>
            <tr>
                <td>Average Ticket Size</td>
                <td id="ticket_size">
                    @php
                    if($infoSales["no_transactions"]!=0) $avTicketSize =  number_format($infoSales["total"]/$infoSales["no_transactions"],2);
                    else $avTicketSize = 0;
                    @endphp
                    @if($avTicketSize>=0)
                        ${{ $avTicketSize}}
                    @else
                        ${{ (-1) * $avTicketSize }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Number of Items Sold</td>
                <td id="items_sold">
                    {{ $infoSales['items_sold'] }}
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Tax</td>
                <td id="tax">
                    @if($infoSales['tax']>=0)
                        ${{ $infoSales['tax']}}
                    @else
                        ${{ (-1) * $infoSales['tax'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Number of Items Sold</td>
                <td id="items_sold">
                    {{ $infoSales['items_sold'] }}
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>

            <!-- Sale Payments -->
            @foreach($salePayments as $aSale)
                    <tr>
                        <td>{{ $aSale->payment_type}}</td>
                        <td>
                            @if($aSale->paid_amount>=0)
                                ${{$aSale->paid_amount}}
                            @else
                                -${{ (-1) * $aSale->paid_amount }}
                            @endif
                        </td>
                    </tr>
            @endforeach

                    <!-- Return Report -->

                    <tr><td><h3><b>Returns</b></h3></td><td> -- </td></tr>
                    <tr >
                        <td>Total (Without Tax)</td>
                        <td id="total_without_tax">
                            @if($infoReturns['subtotal']>=0)
                                ${{ $infoReturns['subtotal'] }}
                            @else
                                ${{ (-1) * $infoReturns['subtotal'] }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Total (With Tax)</td>
                        <td id="total_with_tax">
                            @if($infoReturns['total']>=0)
                                ${{ $infoReturns['total'] }}
                            @else
                                ${{ (-1) * $infoReturns['total'] }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Profit</td>
                        <td id="profit">
                            @if($infoReturns['profit']>=0)
                                ${{ $infoReturns['profit'] }}
                            @else
                                ${{ (-1) * $infoReturns['profit'] }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Number of transactions</td>
                        <td id="no_transactions">
                            {{ $infoReturns['no_transactions'] }}
                        </td>
                    </tr>
                    <tr>
                        <td>Average Ticket Size</td>
                        <td id="ticket_size">
                            @php
                            if($infoReturns["no_transactions"]!=0) $avTicketSize =  number_format($infoReturns["total"]/$infoReturns["no_transactions"],2);
                            else $avTicketSize = 0;
                            @endphp
                            @if($avTicketSize>=0)
                                ${{ $avTicketSize}}
                            @else
                                ${{ (-1) * $avTicketSize }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Number of Items Sold</td>
                        <td id="items_sold">
                            {{ $infoReturns['items_sold'] }}
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td id="tax">
                            @if($infoReturns['tax']>=0)
                                ${{ $infoReturns['tax']}}
                            @else
                                ${{ (-1) * $infoReturns['tax'] }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Number of Items Sold</td>
                        <td id="items_sold">
                            {{ $infoReturns['items_sold'] }}
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>

                    <!-- Return Payments -->
                    @foreach($returnPayments as $aSale)
                        <tr>
                            <td>{{ $aSale->payment_type}}</td>
                            <td>
                                @if($aSale->paid_amount>=0)
                                    ${{$aSale->paid_amount}}
                                @else
                                    -${{ (-1) * $aSale->paid_amount }}
                                @endif
                            </td>
                        </tr>
                    @endforeach

                  <!-- Discounts -->
                        <tr><td><h3><b>Discounts</b></h3></td><td> -- </td></tr>
                        <tr>
                            <td>Total</td>
                            <td>
                                @if($discount>=0)
                                    ${{$discount}}
                                @else
                                    -${{ (-1) * $discount }}
                                @endif
                            </td>
                        </tr>
                            <td>Number Discounts</td>
                            <td id="items_discounts">
                                {{ $numberOfDiscounts }}
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>

                    <!-- Inventory -->
                    <tr><td><h3><b>Inventory</b></h3></td><td> --
                        <tr>
                            <td>Total Items in Inventory</td>
                            <td id="items_discounts">
                                {{ $inventoryItems }}
                            </td>
                        </tr>
                        <td>Number Discounts</td>
                        <td>
                            @if($totalInventoryValues>=0)
                                ${{$totalInventoryValues}}
                            @else
                                -${{ (-1) * $totalInventoryValues }}
                            @endif
                        </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>

            </tbody>
        </table>
    </div>
