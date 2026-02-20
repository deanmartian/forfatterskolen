@extends('editor.layout')

@section('title')
    <title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/editor.css')}}">
    <style>
        .panel {
            overflow-x: auto;
        }
    </style>
@stop

@section('content')
    <div class="col-sm-12 dashboard-left">
        <div class="row">
            <div class="col-sm-12">

                <!-- Upcoming assignments -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><h4>{{ trans('site.upcoming-assignment') }}</h4></div>
                            <div class="panel-body">
                                <div class="table-users table-responsive margin-top">
                                    <table class="table dt-table" id="copyEditingTable">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('site.assignments', 1) }}</th>
                                            <th>{{ trans_choice('site.courses', 1) }}</th>
                                            <th>{{ trans_choice('site.words', 2) }}</th>
                                            <th>{{ trans('site.learner-id') }}</th>
                                            <th>{{ trans_choice('site.learners', 1) }}</th>
                                            <th>
                                                {{ trans('site.submission-date') }}
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($upcomingAssignments as $assignment)
                                            <tr>
                                                <td>
                                                    @if ($assignment->parent === 'users')
                                                        <a href="{{ route('admin.learner.assignment',
												[$assignment->parent_id, $assignment->id]) }}">
                                                            {{ $assignment->title }}
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.assignment.show',
										['course_id' => $assignment->course->id, 'id' => $assignment->id]) }}">
                                                            {{$assignment->title}}
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($assignment->course)
                                                        <a href="{{ route('admin.course.show', $assignment->course->id) }}">
                                                            {{ $assignment->course->title }}
                                                        </a>
                                                    @endif
                                                </td>
                                                <td> {{ $assignment->max_words }} </td>
                                                <td>{{ $assignment->learner->id }}</td>
                                                <td>{{ $assignment->learner->full_name }}</td>
                                                <td>
                                                    <span style="display:none;">{{ strtotime($assignment->submission_date) }}</span>
                                                    {{ $assignment->submission_date }}
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
                <!-- End Upcoming assignments -->
            </div>
        </div>
    </div>
@stop