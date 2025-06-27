@extends('backend.layout')

@section('title')
<title>Surveys &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    @include('backend.course.partials.toolbar')

    <div class="course-container">
        @include('backend.partials.course_submenu')
        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12 col-md-12">
                <a class="btn btn-success margin-top"
                   href="#addSurveyModal" data-toggle="modal">{{ trans('site.add-survey') }}</a>
                <div class="table-responsive" style="padding: 10px">
                    <table class="table dt-table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>{{ trans('site.title') }}</th>
                            <th>{{ trans('site.description') }}</th>
                            <th>{{ trans('site.start-date') }}</th>
                            <th>{{ trans('site.end-date') }}</th>
                            <th width="100"></th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($course->surveys()->orderBy('created_at', 'desc')->get() as $survey)
                                <tr>
                                    <td>{{ $survey->title }}</td>
                                    <td>{{ $survey->description }}</td>
                                    <td>{{ $survey->start_date }}</td>
                                    <td>{{ $survey->end_date }}</td>
                                    <td>
                                        <a href="{{ route('admin.survey.show', $survey->id) }}"
                                           class="btn btn-primary btn-xs"
                                           title="Edit Survey">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @if($survey->answers()->count())
                                            <a href="{{ route('admin.survey.answers', $survey->id) }}"
                                               class="btn btn-info btn-xs" title="View Answers">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.survey.download-answers', $survey->id) }}"
                                               class="btn btn-success btn-xs" title="{{ trans('site.download') }}">
                                                <i class="fa fa-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>

    <div id="addSurveyModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.create-survey') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.survey.store') }}" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                        <div class="form-group">
                            <label>{{ trans('site.title') }}</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.description') }}</label>
                            <textarea name="description" id="" cols="30" rows="10"
                                      class="form-control" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.start-date') }}</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.end-date') }}</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.add') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop