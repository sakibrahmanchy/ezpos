
<style>
    thead {
        table-layout: fixed;
        word-wrap: break-word;
    }

</style>

@extends('layouts.master')

@section('pageTitle','Suspended Sale List')

@section('content')
    <div class="row">
        {{--<div class="col-md-9 col-sm-10 col-xs-10">
            <form action="https://demo.phppointofsale.com/index.php/suspended_sale/search" id="search_form" autocomplete="off" method="post" accept-charset="utf-8">
                <div class="search no-left-border">
                    <ul class="list-inline">
                        <li>
                            <span role="status" aria-live="polite" class="hidden">No search results.</span><input type="text" class="form-control ui-autocomplete-input" name="search" id="search" value="" placeholder="Search Suspended Sales" autocomplete="off">
                        </li>
                        <li>
                            <button type="submit" class="btn btn-primary"><span class="ion-ios-search-strong"></span><span class="hidden-xs hidden-sm"> Search</span></button>
                        </li>
                        <li>
                            <div class="clear-block hidden">
                                <a class="clear" href="https://demo.phppointofsale.com/index.php/suspended_sale/clear_state">
                                    <i class="ion ion-close-circled"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </form>

        </div>--}}
        <div style="float:right;margin-right:20px">
            <div class="buttons-list">
                <div class="pull-right-btn">
                   {{-- <a href="{{route('new_suspended_sale')}}" class="btn btn-primary hidden-sm hidden-xs" title="New Suspended Sale"><span class="">New Suspended Sale</span></a>					<div class="piluku-dropdown btn-group">

                        <button type="button" class="btn btn-more dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="hidden-xs ion-android-more-horizontal"> </span>
                            <span class="pe-7s-more" aria-hidden="true"></span>
                        </button>
                        <ul class="dropdown-menu  dropdown-menu-right" role="menu">

                            <li class="visible-sm visible-xs">
                                <a href="{{route("new_suspended_sale")}}" class="" title="New Suspended Sale"><span class="ion-plus-round"> Add New Suspended Sale</span></a>							</li>


                            <li>
                            </li>
                            <li>
                                <a href="https://demo.phppointofsale.com/index.php/suspended_sales/excel_export" class="hidden-xs import" title="Excel Export"><span class="ion-ios-upload-outline"> Excel Export</span></a>							</li>

                        </ul>--}}
                    </div>
                </div>
            </div>
        </div>


    </div>
    <style>
        td{
            white-space: nowrap;
        }
    </style>
    <div class="table-responsive">

        <table  class="table table-hover " >
            <thead>
            <tr>

                <th>Suspended Sale Id</th>
                <th>Date</th>
                <th>Type</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Amount Paid</th>
                <th>Amount Due</th>
                <th>Unsuspend</th>
                <th>Sales Receipt</th>
            </tr>
            </thead>
            <tbody>
            @foreach($suspended_sales as $suspended_sale)
                <tr>

                    <td>EZPOS {{$suspended_sale->id}} </td>
                    <td>{{$suspended_sale->created_at}}</td>
                    <td>
                        @if($suspended_sale->sale_status==\App\Enumaration\SaleStatus::$LAYAWAY)
                            Lay Away
                        @else
                            Estimate
                        @endif
                    </td>
                    <td></td>
                    <td>
                        @foreach($suspended_sale->item_names as $anItemName)
                            {{$anItemName}},
                         @endforeach
                    </td>

                    <td>{{$suspended_sale->total_amount}}</td>
                    <td>{{  $suspended_sale->total_amount-$suspended_sale->due }}</td>
                    <td>{{$suspended_sale->due}}</td>
                    <td><a class="btn btn-default" href="{{ route("sale_edit",["sale_id"=>$suspended_sale->id]) }}">Unsuspend</a></td>
                    <td><a href="{{route('sale_receipt',["sale_id"=>$suspended_sale->id])}}" class="btn btn-default">Receipt</a></td>

                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
    </div>
@endsection


<script>


</script>