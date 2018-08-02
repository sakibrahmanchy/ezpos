<html>
<style>
    li{
        list-style-type: none;
    }

    table {

        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }

    td, th {
        /* border: 1px solid #dddddd;*/
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>


<div>
    <ul style="margin-bottom:2px;float:left;">

        <li >{{ $settings['company_name'] }}</li>
		@if($settings['address_line_1']!=""||$settings['address_line_1']!=null)
        <li><?php echo $settings['address_line_1'] ?></li>
		@endif
		@if($settings['address_line_2']!=""||$settings['address_line_2']!=null)
        <li><?php echo $settings['address_line_2'] ?></li>
		@endif
		@if($settings['email_address']!=""||$settings['email_address']!=null)
        <li><?php echo $settings['email_address'] ?></li>
		@endif
		@if($settings['phone']!=""||$settings['phone']!=null)
        <li><?php echo $settings['phone'] ?></li>
		@endif
    </ul>
    <ul  style="margin-bottom:2px;float:right;">
        <li>
            Invoices Receipt						 <br>
            <strong>{{date('m/d/Y h:i:s a', time()) }}</strong>
        </li>
        <li><span>Invoice ID:</span>{{ $settings['company_name'] }} {{$invoice->id}}</li>
        <li><span>Employee:</span>{{\Illuminate\Support\Facades\Auth::user()->name }}</li>
        @if(isset($invoice->customer->id))
            <li><span>Customer:</span>{{$invoice->customer->first_name}} {{$invoice->customer->last_name}}</li>
			@if($invoice->Customer->loyalty_card_number && strlen($invoice->Customer->loyalty_card_number)>0)
				<li>
				@php
					$loyalityCarNumber = $invoice->Customer->loyalty_card_number;
					$loyalityCarNumberMasked = str_repeat('X', strlen($loyalityCarNumber) - 4) . substr($loyalityCarNumber, -4);
					echo $loyalityCarNumberMasked;
				@endphp
				</li>
			@endif
        @endif
    </ul>
</div>


<!-- invoice items-->

<table  style="clear: both" >
    <thead>
    <tr>
        <th align="left" class="header">Sale Id</th>
        <th align="left" class="header">Date</th>
        <th align="left" class="header">Description</th>
        {{--<th>Sale Amount</th>--}}
        {{--<th align="right" class="header">Amount Paid</th>--}}
        <th align="right" class="header">Amount Due</th>

    </tr>
    </thead>

    <tbody>
        @php $due = 0; @endphp
        @foreach($invoice->transactions as $aTransaction)
            @php $due += (  $aTransaction->sale_amount -  $aTransaction->paid_amount  ); @endphp
            <tr>
                <td>##{{$aTransaction->sale_id}}</td>
                <td>{{$aTransaction->created_at}}</td>
                <td>
                    Due for sale  <a href="{{ route('sale_receipt',['sale_id'=>$aTransaction->sale_id]) }}">{{ $aTransaction->sale_id }}</a>
                </td>
                {{--<td><strong style="font-size: 18px;">${{ $aTransaction->sale_amount }}</strong></td>--}}
                {{--<td><strong style="font-size: 18px;">${{ $aTransaction->paid_amount }}</strong></td>--}}
                <td><strong style="font-size: 18px;">${{ $aTransaction->sale_amount - $aTransaction->paid_amount }}</strong></td>
            </tr>
        @endforeach
        <tr class="warning">
            <td colspan="5" ><strong class="pull-right" style="font-size: 18px;">Total Due</strong></td>
            <td><strong  style="font-size: 18px;">${{  number_format($due, 2) }}</strong></td>
        </tr>
    </tbody>
</table>

</div>

@if(!is_null($invoice->comment))
<div class="row">
    <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2 col-xs-6"><br>
        <div class="invoice-footer-heading"><strong>Comments</strong></div>
    </div>
    <div class="col-md-offset-8 col-sm-offset-8 col-xs-offset-2 col-md-2 col-sm-2"><br>
        <div class="invoice-footer-heading">{{ $invoice->comment }}</div><br>
    </div><br><br>
</div>
@endif

<div >
    <div>
        <div>
            Change return policy			            </div>
        <div>
            <?php echo DNS1D::getBarcodeHTML($invoice->id , "C39",1,50);	?>					</div>
        <p >{{ $settings['company_name'] }} {{ $invoice->id }}</p>
        <div >
        </div>
    </div>

    <div>
        <div >


        </div>
    </div>
</div>
</div>
<!--container-->
</div>
</div>
</html>