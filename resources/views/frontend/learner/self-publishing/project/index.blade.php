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
                            <h1>
                                Projects
                            </h1>
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

                                            @if($project->is_finished)
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
        </div>
    </div>
@stop