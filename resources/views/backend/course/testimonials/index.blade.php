@extends('backend.layout')

@section('title')
    <title>Course Testimonials &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> {{ trans('site.all-testimonials') }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a class="btn btn-success margin-top" href="{{route('admin.course-testimonial.create')}}">{{ trans('site.add-testimonial') }}</a>
        <a class="btn btn-success margin-top" href="{{route('admin.course-video-testimonial.create')}}">{{ trans('site.add-video-testimonial') }}</a>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ trans('site.id') }}</th>
                    <th>{{ trans('site.name') }}</th>
                    <th>{{ trans_choice('site.courses',1) }}</th>
                    <th>{{ trans_choice('site.testimonials',1) }}</th>
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

            <div class="pull-right">
                {{ $testimonials->render() }}
            </div>
        </div>
    </div>
@stop