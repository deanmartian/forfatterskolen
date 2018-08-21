@extends('backend.layout')

@section('title')
    <title>Admins &rsaquo; Calendar Notes</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-calendar"></i> Calendar Notes</h3>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
        <a class="btn btn-success margin-top" href="{{ route('admin.calendar-note.create') }}">Add Note</a>
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Course</th>
                    <th>Note</th>
                    <th>Date</th>
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