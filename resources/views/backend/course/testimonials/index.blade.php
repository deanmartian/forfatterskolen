@extends('backend.layout')

@section('title')
    <title>Course Testimonials &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> All Testimonials</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a class="btn btn-success margin-top" href="{{route('admin.course-testimonial.create')}}">Add Testimonial</a>
        <a class="btn btn-success margin-top" href="{{route('admin.course-video-testimonial.create')}}">Add Video Testimonial</a>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Testimonial</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($testimonials as $testimonial)
                        <tr>
                            <td>{{ $testimonial->id }}</td>
                            <td>
                                @if ($testimonial->is_video)
                                    <a href="{{ route('admin.course-video-testimonial.edit', $testimonial->id) }}">
                                        {{ $testimonial->name }}
                                    </a>
                                @else
                                    <a href="{{ route('admin.course-testimonial.edit', $testimonial->id) }}">
                                        {{ $testimonial->name }}
                                    </a>
                                @endif
                            </td>
                            <td><a href="{{ route('admin.course.show', $testimonial->course->id) }}">{{ $testimonial->course->title }}</a></td>
                            <td>{{ $testimonial->testimony }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop