@extends('backend.layout')

@section('title')
    <title>Admins &rsaquo; Calendar Notes</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-calendar"></i> {{ trans('site.calendar-notes') }}</h3>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
        <a class="btn btn-success margin-top" href="{{ route('admin.calendar-note.create') }}">{{ trans('site.add-note') }}</a>
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ trans('site.id') }}</th>
                    <th>{{ trans_choice('site.courses', 1) }}</th>
                    <th>{{ trans_choice('site.notes', 1) }}</th>
                    <th>{{ trans('site.date') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($calendar as $note)
                    <tr>
                        <td><a href="{{ route('admin.calendar-note.edit', $note->id) }}">{{ $note->id }}</a></td>
                        <td>
                            <a href="{{ route('admin.course.show', $note->course->id) }}">
                                {{ $note->course->title }}
                            </a>
                        </td>
                        <td>{{ $note->note }}</td>
                        <td>{{ $note->date }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop