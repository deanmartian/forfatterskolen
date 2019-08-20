@extends('backend.layout')

@section('title')
    <title>Learner Notes &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar" style="position: relative;">
        <h3><i class="fa fa-users"></i> Learner Notes</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="350">{{ trans_choice('site.learners', 1) }}</th>
                        <th>{{ trans_choice('site.notes', 2) }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($userNotes as $userNote)
                        <tr>
                            <td>
                                <a href="{{ route('admin.learner.show', $userNote->id) }}">{{ $userNote->full_name }}</a>
                            </td>
                            <td>{{ $userNote->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{$userNotes->render()}}
        </div>
    </div>
@stop