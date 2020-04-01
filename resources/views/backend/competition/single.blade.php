@extends('backend.layout')

@section('title')
    <title>Single Competition &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-star"></i> Competition</h3>
        <button class="btn btn-success btn-sm" style="margin-left: 10px" data-toggle="modal"
                data-target="#addCompetitionModal">
            Add Learner to Competition
        </button>
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
                            <a href="{{ route('admin.single-competition.show', $applicant->id) }}"
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

    <div id="addCompetitionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add to Competition</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('admin.single-competition.store')}}"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans_choice('site.learners', 1) }}</label>
                            <select name="learner" class="form-control select2" required>
                                <option value="" selected disabled>- Search Learner -</option>
                                @foreach($learners as $learner)
                                    <option value="{{$learner->id}}">{{ $learner->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Manuscript</label>
                            <input type="file" name="manuscript">
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">Add</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop