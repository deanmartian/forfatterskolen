@extends('frontend.layout')

@section('title')
    <title>Notifications &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content white-background">
            <div class="col-sm-12">
                <h3 class="no-margin-top">
                    Notifications
                </h3>

                <div class="col-sm-6 col-sm-offset-3">
                    @foreach(Auth::user()->notifications as $notification)
                        <?php
                        $phrase         = $notification->message;
                        $replace_string = array("{book_title}", "{chapter_title}");

                        $book           = \App\PilotReaderBook::find($notification->book_id);
                        $book_title     = $book ? $book->title : '';
                        $chapter        = \App\PilotReaderBookChapter::find($notification->chapter_id);
                        $chapter_title  = $chapter ? $chapter->title : '';

                        $string_value         = array($book_title, $chapter_title);
                        $notification_message = str_replace($replace_string, $string_value, $phrase);
                        ?>

                        <div class="global-card with-border" id="all-notif-{{ $notification->id }}">
                            <div class="card-body">
                                <i class="pull-right hand text-red text-bold" onclick="layoutMethod.removeNotification({{ $notification->id }})">x</i>
                                {!! $notification_message !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

@stop