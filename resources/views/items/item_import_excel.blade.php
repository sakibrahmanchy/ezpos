@extends('layouts.master')

@section('pageTitle','Import Items')

@section('breadcrumbs')
    {!! Breadcrumbs::render('item_import') !!}
@stop

@section('content')
    <div class="box box-primary" style="padding:20px">
        @include('includes.message-block')
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <div class=" col-md-offset-3 col-md-6">
                    <form action="{{ route('item_import_excel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                        <input type="file" name="import_file" />
                        <br><button class="btn btn-primary">Import File</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


