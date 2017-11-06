

@extends('layouts.master')

@section('pageTitle','Upload Files')

@section('content')

    <div class="image_upload_div" >
        <form class= "dropzone dz-clickable" id="uploadForm" action = '{{ route('insert_file') }}' method = "post" enctype="multipart/form-data" >
            <input type="hidden" name="_token" value="{{ csrf_token() }}">


        </form>

        <div class  ="alert alert-info text-center">Drag and drop or click in the upload area to select image. Press <button class="btn btn-primary" id ="add">Upload Now</button> when done.</div>
    </div>
@endsection

