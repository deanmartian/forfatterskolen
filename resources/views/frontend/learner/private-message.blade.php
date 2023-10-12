{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
    <title>Mesages &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Private beskjeder @stop

@section('styles')
    <style>
        .fa-use-comment:before {
            content: "\f075";
        }
    </style>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                @include('frontend.partials.learner-search-new')
            </div> <!-- end row -->

            <div class="row mt-5">
                <div class="col-sm-12">
                    <div class="card global-card">
                        <div class="card-body py-0">
                            <table class="table table-global">
                                <thead>
                                    <tr>
                                        <th>Beskjeder</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($messages as $message)
                                        <tr>
                                            <td>
                                                {!! $message->message !!}
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div> <!-- end card-body -->
                    </div> <!-- end card -->

                    <div class="float-right">
                        {{ $messages->render() }}
                    </div>

                </div> <!-- end col-sm-12 -->
            </div> <!-- end row -->

        </div>
    </div>
@stop