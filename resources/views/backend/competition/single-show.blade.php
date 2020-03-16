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
                            <a href="/{{ $applicant->manuscript }}">
                                {{ \App\Http\AdminHelpers::extractFileName($applicant->manuscript) }}
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

