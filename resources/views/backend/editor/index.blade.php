@extends('backend.layout')

@section('title')
    <title>Admins &rsaquo; Editors</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-users"></i> Editors</h3>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
        <a class="btn btn-success margin-top" href="{{ route('admin.editor.create') }}">Add Editor</a>
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th width="20%">Name</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                @foreach($editors as $editor)
                    <tr>
                        <td><a href="{{ route('admin.editor.edit', $editor->id) }}">{{ $editor->name }}</a></td>
                        <td>{{ $editor->description }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop