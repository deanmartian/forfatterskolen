@extends('backend.layout')

@section('title')
    <title>Personal Trainer Applicants &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> Personal Trainer Applicants</h3>
        <a href="{{ route('admin.personal-trainer.create') }}" class="btn btn-success pull-right" style="margin-right: 5px">
            Add Applicant
        </a>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <td>{{ trans('site.id') }}</td>
                        <th>{{ trans('site.learner-id') }}</th>
                        <th>{{ trans('site.first-name') }}</th>
                        <th>{{ trans('site.last-name') }}</th>
                        <th>{{ trans_choice('site.emails', 1) }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applicants as $applicant)
                        <tr>
                            <td>
                                {{ $applicant->id }}
                            </td>
                            <td>
                                <a href="{{route('admin.learner.show', $applicant->user_id)}}">
                                    {{$applicant->user_id}}
                                </a>
                            </td>
                            <td>{{$applicant->user->first_name}}</td>
                            <td>{{$applicant->user->last_name}}</td>
                            <td>{{$applicant->user->email}}</td>
                            <td>
                                <a href="{{ route('admin.personal-trainer.show', $applicant->id) }}"
                                   class="btn btn-xs btn-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{$applicants->appends(Request::all())->render()}}
        </div>
    </div>
@stop