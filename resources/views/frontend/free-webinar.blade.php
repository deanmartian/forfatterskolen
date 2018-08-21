@extends('frontend.layout')

@section('title')
    <title>Free Webinar &rsaquo; {{ $freeWebinar->title }}</title>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1 text-center">
                <div class="subscribe-success">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h1>{{ $freeWebinar->title }}</h1>
                            <p>
                                {!! nl2br($freeWebinar->description) !!}
                            </p>
                            <form action="{{ route('front.free-webinar', $freeWebinar->id) }}" method="POST"
                            class="form-inline">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                                </div>

                                <div class="form-group">
                                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                                </div>

                                <button type="submit" class="btn btn-primary">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
