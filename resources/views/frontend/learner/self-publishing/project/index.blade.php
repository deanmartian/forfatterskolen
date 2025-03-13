@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Time Register &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <style>
        .fa-file-red:before {
            content: "\f15b";
        }

        .fa-file-red {
            color: #862736 !important;
            font-size: 20px;
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
                            <h1 class="d-inline-block">
                                {{ trans('site.author-portal.book-project') }}
                            </h1>

                            <button class="btn btn-primary projectBtn pull-right" data-toggle="modal" data-target="#projectModal">
                                {{ trans('site.author-portal.add-book-project') }}
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.project-number') }}</th>
                                    <th>{{ trans('site.author-portal.project-name') }}</th>
                                    <th>{{ trans('site.description') }}</th>
                                    <th>{{ trans('site.status') }}</th>
                                    <th>{{ trans('site.author-portal.standard-project') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($projects as $project)
                                    <tr>
                                        <td>
                                            {{ $project->identifier }}
                                        </td>
                                        <td>
                                            <a href="{{ route('learner.project.show', $project->id) }}">
                                                {{ $project->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $project->description }}
                                        </td>
                                        <td>
                                            {{ $project->start_date}}
                                            @if($project->end_date)
                                                - {{ $project->end_date }}
                                            @endif

                                            <br>

                                            @if($project->status === 'active')
                                                <span class="badge badge-primary">
                                                    {{ trans('site.author-portal.active') }}
                                                </span>
                                            @elseif ($project->status === 'lead')
                                                <span class="badge badge-warning">Lead</span>
                                            @elseif($project->status === 'finished')
                                                <span class="badge badge-success">
                                                    {{ trans('site.finish') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($project->is_standard)
                                                <span class="badge badge-primary">
                                                    {{ trans('site.author-portal.current') }}
                                                </span>
                                            @else
                                                <button class="btn btn-primary btn-xs standardProjectBtn" data-toggle="modal" 
                                                data-action="{{ route('learner.project.set-standard', $project->id) }}"
                                                data-target="#standardProjectModal">
                                                    {{ trans('site.author-portal.set-standard') }}
                                                </button>
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

            {{-- <iframe
src="https://www.chatbase.co/chatbot-iframe/s7nqoF2-3_v5RucONplQE"
width="100%"
height="700"
frameborder="0"
></iframe> --}}
        </div>
    </div>

    <div id="projectModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        {{ trans('site.author-portal.add-book-project') }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.save-project') }}" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans('site.author-portal.project-name') }}</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.description') }}</label>
                            <textarea name="description" cols="30" rows="10" class="form-control"></textarea>
                        </div>

                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div id="standardProjectModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        {{ trans('site.author-portal.standard-project') }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <p>
                            Are you sure you want to set this project as <em>standard</em>?
                        </p>

                        <div class="text-right">
                            <button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
    $(".standardProjectBtn").click(function() {
        const action = $(this).data('action');

        $("#standardProjectModal").find("form").attr("action", action)
    });
</script>
@stop