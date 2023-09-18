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
                                Bokprosjekt
                            </h1>

                            <button class="btn btn-primary projectBtn pull-right" data-toggle="modal" data-target="#projectModal">
                                Add Bokprosjekt
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Project Number</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
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
                                                <span class="badge badge-primary">Active</span>
                                            @elseif ($project->status === 'lead')
                                                <span class="badge badge-warning">Lead</span>
                                            @elseif($project->status === 'finished')
                                                <span class="badge badge-success">Finished</span>
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

            <iframe
src="https://www.chatbase.co/chatbot-iframe/s7nqoF2-3_v5RucONplQE"
width="100%"
height="700"
frameborder="0"
></iframe>
        </div>
    </div>

    <div id="projectModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Add Project
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.save-project') }}" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" class="form-control" name="start_date">
                        </div>

                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" class="form-control" name="end_date">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
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
@stop