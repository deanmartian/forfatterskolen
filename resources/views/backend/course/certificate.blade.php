@extends('backend.layout')

@section('title')
    <title>Certificate &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

    @include('backend.course.partials.toolbar')

    <div class="course-container">
	
        @include('backend.partials.course_submenu')
    
        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table table-side-bordered table-white">
                        <thead>
                            <tr>
                                <th>Package</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($course->packages as $package)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.package.certificate', [
                                            'course_id' => $package->course_id, 
                                            'package_id' => $package->id
                                        ]) }}">
                                            {{ $package->variation }}
                                        </a>
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
@stop