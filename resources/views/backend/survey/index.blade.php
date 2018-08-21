@extends('backend.layout')

@section('title')
    <title>Surveys &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> All Surveys</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a class="btn btn-success margin-top" href="#addSurveyModal" data-toggle="modal">Add Survey</a>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                    @foreach($surveys as $survey)
                        <tr>
                            <td>{{ $survey->id }}</td>
                            <td>
                                <a href="{{route('admin.course.show', $survey->course_id)}}">
                                    {{ $survey->course->title }}
                                </a>
                            </td>
                            <td>{{ $survey->title }}</td>
                            <td>{{ $survey->description }}</td>
                            <td>
                                <a href="{{ route('admin.survey.show', $survey->id) }}" class="fa fa-edit"
                                title="Edit Survey"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="clearfix"></div>

    <div id="addSurveyModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create Survey</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.survey.store') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="" cols="30" rows="10"
                            class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Course</label>
                            <select class="form-control" name="course_id" required>
                                <option value="" disabled="disabled" selected>Select Course</option>
                                @foreach(\App\Course::all() as $course)
                                    <option value="{{ $course->id }}"> {{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">Add</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop