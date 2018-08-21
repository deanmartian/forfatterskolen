@extends('frontend.layout')

@section('title')
    <title>{{ $book->title }} Settings &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css">
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
    <style>
        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px !important; }
        .toggle.ios .toggle-handle { border-radius: 20px !important; }

        /* The switch - the box around the slider */
        .switch {
            float: left;
            position: relative;
            width: 56px;
            height: 29px;
            margin-bottom: 0;
        }

        .switch input {
            display: none;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }
        .slider.round {
            border-radius: 34px;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(25px);
            -ms-transform: translateX(25px);
            transform: translateX(25px);
        }
        .slider.round:before {
            border-radius: 50%;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 23px;
            width: 23px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .switch-label {
            margin-top: 2px;
            margin-left: 8px;
            font-weight: 300;
            margin-bottom: 0;
        }
    </style>
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">
                    @include('frontend.learner.pilot-reader.partials.nav')
                    @if ($book->user_id == Auth::user()->id || ($reader && $reader->role == "collaborator"))
                        @include('frontend.learner.pilot-reader.partials.author-book-settings')
                    @else
                        @include('frontend.learner.pilot-reader.partials.reader-book-settings')
                    @endif
                </div><!-- col-xs-offset-2 col-xs-8 margin-top -->

            </div>
        </div>

        <input type="hidden" name="book_id" value="{{ $book->id }}">
        <div class="clearfix"></div>
    </div>
@stop

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.min.js"></script>
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('js/pilot-reader/book-settings.js') }}"></script>
    <script src="{{ asset('js/pilot-reader/author-book-settings.js') }}"></script>
@stop