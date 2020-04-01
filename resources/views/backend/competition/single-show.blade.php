@extends('backend.layout')

@section('title')
    <title>Show Applicant &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="container padding-top">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{ route('admin.single-competition.index') }}" class="btn btn-default">
                    <i class="fa fa-chevron-left"></i> Back
                </a>

                <button class="btn btn-danger pull-right" data-toggle="modal"
                        data-target="#deleteCompetitionModal">
                    {{ trans('site.delete') }}
                </button>

                <h3><em>{{ $applicant->user->fullname }}</em></h3>

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>{{ trans('site.front.form.full-name') }}</label>
                                <em class="d-block font-16">
                                    {{ $applicant->user->fullname }}
                                </em>
                            </div>
                            <div class="col-sm-6">
                                <label>{{ trans('site.front.form.email-address') }}</label>
                                <em class="d-block font-16">
                                    {{ $applicant->user->email }}
                                </em>
                            </div>
                        </div>

                        <div class="form-group">
                            <a href="{{ url($applicant->manuscript) }}">
                                {{ \App\Http\AdminHelpers::extractFileName($applicant->manuscript) }}
                            </a>

                            @if ($applicant->manuscript)
                                <button class="btn btn-danger btn-xs" data-toggle="modal"
                                        data-target="#deleteManuscriptModal">
                                    <i class="fa fa-trash"></i>
                                </button>
                            @endif

                            <button class="btn btn-primary btn-xs" data-toggle="modal"
                                    data-target="#updateManuscriptModal">
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="updateManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Update Manuscript</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('admin.single-competition.update', $applicant->id)}}"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                            <input type="file" name="manuscript" required>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">
                            {{ trans('site.update') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Manuscript</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('admin.single-competition.delete-manuscript', $applicant->id)}}"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <p>
                            {{ trans('site.delete-manuscript-question') }}
                        </p>

                        <button type="submit" class="btn btn-danger pull-right margin-top">
                            {{ trans('site.delete') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteCompetitionModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Competition Record</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('admin.single-competition.delete', $applicant->id)}}"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <p>
                            {!! trans('site.delete-learner-question') !!}
                        </p>

                        <button type="submit" class="btn btn-danger pull-right margin-top">
                            {{ trans('site.delete') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

