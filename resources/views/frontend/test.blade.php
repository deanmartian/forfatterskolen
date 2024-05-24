@extends('frontend.layout')

@section('content')
<div class="container" style="min-height: 500px;">
    <form action="{{ route('dropbox.post-upload') }}" method="POST" style="margin-top: 50px" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" 
        accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.oasis.opendocument.text">
        <button type="submit" class="btn btn-primary" style="margin-top: 10px">Submit</button>
    </form>
</div>
@stop