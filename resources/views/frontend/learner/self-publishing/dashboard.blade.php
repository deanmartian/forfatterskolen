@extends('frontend.learner.self-publishing.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        .fa-home-red:before {
            content: "\f015";
        }

        .fa-home-red {
            color: #e80707 !important;
        }
    </style>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12 dashboard-course no-left-padding">
                    <div class="card global-card">
                        <div class="card-header">
                            <h1>
                                Self Publishing
                            </h1>
                        </div>
                        <div class="card-body" style="padding: 0">
                            <table class="table" style="margin-bottom: 0">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.title') }}</th>
                                    <th>{{ trans('site.description') }}</th>
                                    <th>File</th>
                                    <th>{{ trans('site.expected-finish') }}</th>
                                    <th>Feedback</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($selfPublishingList as $publishing)
                                    <tr>
                                        <td>
                                            {{ $publishing->title }}
                                        </td>
                                        <td>
                                            {{ $publishing->description }}
                                        </td>
                                        <td>
                                            {!! $publishing->file_link !!}
                                        </td>
                                        <td>
                                            {{ $publishing->expected_finish }}
                                        </td>
                                        <td>
                                            @if($publishing->feedback)
                                                @if($publishing->feedback->is_approved)
                                                    <button class="btn btn-primary btn-xs viewFeedbackBtn"
                                                            data-target="#viewFeedbackModal"
                                                            data-toggle="modal"
                                                            data-fields="{{ json_encode($publishing) }}">
                                                        View Feedback
                                                    </button>
                                                @else
                                                    <label class="label label-warning" style="margin-right: 5px;">
                                                        Pending
                                                    </label>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end container -->
    </div>

    <div id="viewFeedbackModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                        <div id="manus-container"></div>
                    </div>

                    <div class="form-group">
                        <label>{{ trans_choice('site.notes', 1) }}</label>
                        <div id="notes-container" style="white-space: pre;max-height: 500px;overflow: auto;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script>
        $(".viewFeedbackBtn").click(function(){
            let modal = $("#viewFeedbackModal");
            let fields = $(this).data('fields');
            modal.find("#manus-container").html(fields.feedback.file_link);
            modal.find("#notes-container").text(fields.feedback.notes);
        });
    </script>
@stop
