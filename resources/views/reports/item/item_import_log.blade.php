@extends('layouts.master')

@section('pageTitle','Item Import Details Report')

@section('breadcrumbs')
    {!! Breadcrumbs::render('report_item_import_log') !!}
@stop

@section('content')
    <?php $dateTypes = new \App\Enumaration\DateTypes(); ?>

    <style>
        .large_label{
            font-size: 12px;
        }
    </style>

    <div class="box box-primary nav-tabs-custom" style="padding:20px">

        <div class="se-pre-con text-center hide">
            <img height="30%" width="30%"  src = "{{ asset('img/loader.gif') }}" >
        </div>

        <div class="data">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-reports tablesorter stacktable small-only">
                    <thead>
                    <tr>
                        <th align="right" class="header">Date</th>
                        <th align="left" class="header">User</th>
                        <th align="right" class="header">Imported File</th>
                        <th align="right" class="header">Log File</th>
                        <th align="right" class="header">Import Type</th>
                        <th align="right" class="header">Status</th>
                    </tr>
                    </thead>
                    <tbody id="data-table">
                    @foreach($logs as $aLog)
                        <tr>
                            <td>{{ $aLog->created_at }}</td>
                            <td>{{ $aLog->user->name }}</td>
                            <td><a href="{{ $aLog->uploaded_file_path }}">Download Imported File</a></td>
                            <td><a href="{{ $aLog->downloaded_file_path }}">Download Log File</a></td>
                            <td>
                                @if($aLog->type==\App\Enumaration\ImportType::$NOT_INSERTED||$aLog->type==\App\Enumaration\ImportType::$ITEM)
                                    Item
                                @elseif($aLog->type==\App\Enumaration\ImportType::$SUPPLIER)
                                    Supplier
                                @endif
                            </td>
                            <td>
                                @if($aLog->percentage==100)
                                    <label class="label label-success large_label">100% imported</label>
                                @elseif(@$aLog->percentage>0&&$aLog->percentage<100)
                                    <label class="label label-warning large_label">Partial({{ round($aLog->percentage,2) }}%) Imported</label>
                                @else
                                    <label class="label label-danger large_label">Import failed</label>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection



