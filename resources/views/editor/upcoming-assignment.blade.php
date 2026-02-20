@extends('editor.layout')

@section('title')
<title>{{ trans('site.upcoming-assignment') }} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('page-title', trans('site.upcoming-assignment'))

@section('content')

<div class="ed-section">
    <div class="ed-section__header">
        <h3 class="ed-section__title">
            {{ trans('site.upcoming-assignment') }}
            <span class="ed-section__count">{{ $upcomingAssignments->count() }}</span>
        </h3>
    </div>
    <div class="ed-section__body">
        <table class="ed-table dt-table">
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
                            <span style="font-weight:600; color:var(--ink);">{{ $assignment->title }}</span>
                        </td>
                        <td>{{ $assignment->course->title ?? '' }}</td>
                        <td class="mono">{{ number_format($assignment->word_count ?? 0) }}</td>
                        <td class="mono">{{ $assignment->user->id ?? '' }}</td>
                        <td>{{ $assignment->user->fullName ?? '' }}</td>
                        <td>
                            <span style="display:flex; align-items:center; gap:5px;">
                                <i class="fa fa-calendar"></i>
                                {{ FrontendHelpers::formatDate($assignment->submission_date) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop
