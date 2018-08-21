@extends('backend.layout')

@section('title')
    <title>Edit {{ $webinar['topic'] }}</title>
@stop

@section('styles')
    <style>
        .no-bullet {
            list-style-type: none;
        }

        .no-bullet li {
            padding: 2px 15px;
        }

        #panelist-list {
            width: 60%
        }
        #panelist-list a {
            color: #f00;
        }

        .tab-content {
            padding: 20px;
        }

        .table thead {
            background-color: #fff;
        }
    </style>
@stop

@section('content')
    <div class="container padding-top">
        <div class="row">
            @include('backend.zoom.webinars.partials.form')
        </div>
    </div>
@stop
