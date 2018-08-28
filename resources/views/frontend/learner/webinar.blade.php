@extends('frontend.layout')

@section('styles')
    <style>
        .course-meta {
            min-height: 245px;
        }

        .course-meta .btn {
            position: absolute;
            bottom: 25px;
            right: 27px;
        }
    </style>
@stop

@section('title')
    <title>Mine Webinar &rsaquo; Forfatterskolen</title>
@stop


@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12">
                <div>
                    <div class="col-sm-2">
                        <h3 class="no-margin-top">Mine Webinar</h3>
                    </div>

                    <div class="col-sm-10">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <form class="" method="POST" action="{{ route('learner.webinar') }}">
                                    {{ csrf_field() }}
                                    <div class="input-group-global">
                                        <input type="text" name="search_upcoming" class="form-control" placeholder="Søk etter webinar (kommende)" aria-label="Enter here..." aria-describedby="basic-addon2"
                                               value="{{ Request::get('search_upcoming') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-success border-color-grey" type="submit"><i class="fa fa-search"></i> Search</button>
                                            <a class="btn btn-outline-info border-color-grey" type="reset"
                                               href="{{ route('learner.webinar') }}"><i class="fa fa-redo"></i> Reset</a>
                                        </div>
                                    </div>
                                </form> <!-- end searchBoxForm -->
                            </div> <!-- end #simpleSearchbox -->
                        </div>

                        {{--<div class="col-sm-6">
                            <div class="form-group">
                                <form class="" method="POST" action="{{ route('learner.webinar') }}">
                                    {{ csrf_field() }}
                                    <div class="input-group-global">
                                        <input type="text" name="search_replay" class="form-control" placeholder="Søk etter webinar (reprise)" aria-label="Enter here..." aria-describedby="basic-addon2"
                                               value="{{ Request::get('search_replay') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-success border-color-grey" type="submit"><i class="fa fa-search"></i> Search</button>
                                            <a class="btn btn-outline-info border-color-grey" type="reset"
                                               href="{{ route('learner.webinar') }}"><i class="fa fa-redo"></i> Reset</a>
                                        </div>
                                    </div>
                                </form> <!-- end searchBoxForm -->
                            </div> <!-- end #simpleSearchbox -->
                        </div>--}}
                    </div>
                </div>

                @if (!$isPost)
                    <div class="row">
                    <?php
                        // separate the id's and display the Repriser first
                    $webinarsRepriser = DB::table('courses_taken')
                        ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
                        ->join('courses', 'packages.course_id', '=', 'courses.id')
                        ->join('webinars', 'courses.id', '=', 'webinars.course_id')
                        ->select('webinars.*','courses_taken.id as courses_taken_id','courses.title as course_title')
                        ->where('user_id',Auth::user()->id)
                        ->where('courses.id',17) // just added this line to show all webinar pakke webinars
                        ->where(function($query){
                            $query->whereIn('webinars.id',[24, 25, 31]);
                            $query->orWhere('set_as_replay',1);
                        })
                        //->whereIn('webinars.id',[24, 25, 31]) // remove this to return the original
                        ->orderBy('courses.type', 'ASC')
                        ->orderBy('webinars.start_date', 'ASC')
                        ->get();

                    $webinars = DB::table('courses_taken')
                        ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
                        ->join('courses', 'packages.course_id', '=', 'courses.id')
                        ->join('webinars', 'courses.id', '=', 'webinars.course_id')
                        ->select('webinars.*','courses_taken.id as courses_taken_id','courses.title as course_title')
                        ->where('user_id',Auth::user()->id)
                        ->where('courses.id',17) // just added this line to show all webinar pakke webinars
                        ->whereNotIn('webinars.id',[24, 25, 31])
                        ->where('set_as_replay',0)
                        ->orderBy('courses.type', 'ASC')
                        ->orderBy('webinars.start_date', 'ASC')
                        ->get();
                    ?>

                        @foreach($webinarsRepriser as $webinar)
                            <?php
                            $start_date = Carbon\Carbon::parse($webinar->start_date);
                            $now = Carbon\Carbon::now();
                            $diff = $now->diffIndays($start_date, false);
                            $diffWithHours = $now->diffInHours($start_date, false);
                            ?>
                            @if( $diffWithHours >= 0 )
                                <div class="col-sm-12 col-md-4">
                                    <div class="webinar-thumb">
                                        <i class="fa fa-play-circle-o"></i>
                                        <a href="{{ $webinar->link }}">
                                            <div style="background-image: url({{ $webinar->image }})"></div>
                                        </a>
                                    </div>
                                    <div class="dashboard-courses" style="padding-top: 40px">
                                        <?php $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);?>

                                        <div class="course-meta">
                                            <div style="margin-bottom: 3px;"><strong style="font-size: 16px;">{{ $webinar->title }}</strong></div>
                                            <div style="margin-bottom: 3px;">Kurs: <a href="{{ \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date))
                                                        ? 'javascript:void(0)' : route('learner.course.show', ['id' => $webinar->courses_taken_id]) }}">{{ $webinar->course_title }}</a></div>
                                            <p class="margin-bottom">
                                                {{ $webinar->description }}
                                            </p>

                                            <div class="text-right margin-top">
                                                @if( \App\Http\FrontendHelpers::isWebinarAvailable($webinar) )
                                                    <a class="btn btn-success" href="{{ $webinar->link }}" target="_blank">Bli med på webinar</a>
                                                @else

                                                    @if ($webinar->id == 24 || $webinar->id == 25 || $webinar->id == 31)
                                                        <a class="btn btn-warning" href="{{ $coursesTaken && $coursesTaken->hasEnded
                                                        ? 'javascript:void(0)' : $webinar->link }}" target="_blank">Repriser</a>
                                                    @else
                                                        @if($webinar->set_as_replay)
                                                            <a class="btn btn-warning" href="{{ $webinar->link }}" target="_blank">Repriser</a>
                                                        @else
                                                            <a class="btn btn-warning" href="{{ \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date))
                                                        ? 'javascript:void(0)' :$webinar->link }}" target="_blank">Registrer Deg</a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                    @foreach($webinars as $webinar)
                            <?php
                            $start_date = Carbon\Carbon::parse($webinar->start_date);
                            $now = Carbon\Carbon::now();
                            $diff = $now->diffIndays($start_date, false);
                            $diffWithHours = $now->diffInHours($start_date, false);
                            ?>
                            @if( $diffWithHours >= 0 )
                                <div class="col-sm-12 col-md-4">
                                    <div class="webinar-thumb">
                                        <i class="fa fa-play-circle-o"></i>
                                        <a href="{{ $webinar->link }}">
                                            <div style="background-image: url({{ $webinar->image }})"></div>
                                        </a>
                                    </div>
                                    <div class="dashboard-courses" style="padding-top: 40px">
                                        <?php $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);?>

                                        <div class="course-meta">
                                            <div style="margin-bottom: 3px;"><strong style="font-size: 16px;">{{ $webinar->title }}</strong></div>
                                            <div style="margin-bottom: 3px;">Kurs: <a href="{{ \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date))
                                                        ? 'javascript:void(0)' : route('learner.course.show', ['id' => $webinar->courses_taken_id]) }}">{{ $webinar->course_title }}</a></div>
                                            <div style="margin-bottom: 7px;"><i class="fa fa-calendar"></i>
                                                Starter
                                                {{ \Carbon\Carbon::parse($webinar->start_date)->format('d.m.Y') }}
                                                Klokken
                                                {{ \Carbon\Carbon::parse($webinar->start_date)->format('H:i') }}
                                                </div>
                                            <p class="margin-bottom">
                                                {{ $webinar->description }}
                                            </p>

                                            <div class="text-right margin-top">
                                                @if( \App\Http\FrontendHelpers::isWebinarAvailable($webinar) )
                                                    <a class="btn btn-success" href="{{ $webinar->link }}" target="_blank">Bli med på webinar</a>
                                                @else

                                                    @if ($webinar->id == 24 || $webinar->id == 25 || $webinar->id == 31)
                                                        <a class="btn btn-warning" href="{{ $coursesTaken && $coursesTaken->hasEnded
                                                        ? 'javascript:void(0)' : $webinar->link }}" target="_blank">Repriser</a>
                                                    @else
                                                            <a class="btn btn-warning" href="{{ \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date))
                                                        ? 'javascript:void(0)' :$webinar->link }}" target="_blank">Registrer Deg</a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                    @endforeach
                </div>
                @else
                    <div class="row">
                        @foreach($searchResult as $result)
                            <div class="col-sm-12 col-md-4">
                                <div class="webinar-thumb">
                                    <i class="fa fa-play-circle-o"></i>
                                    <a href="{{ $result->link }}">
                                        <div style="background-image: url({{ $result->image }})"></div>
                                    </a>
                                </div>
                                <div class="dashboard-courses" style="padding-top: 40px">
                                    <?php $coursesTaken = \App\CoursesTaken::find($result->courses_taken_id);?>

                                    <div class="course-meta">
                                        <div style="margin-bottom: 3px;"><strong style="font-size: 16px;">{{ $result->title }}</strong></div>
                                        <div style="margin-bottom: 3px;">Kurs: <a href="{{ \Carbon\Carbon::parse($result->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date))
                                                        ? 'javascript:void(0)' : route('learner.course.show', ['id' => $result->courses_taken_id]) }}">{{ $result->course_title }}</a></div>
                                        <div style="margin-bottom: 7px;"><i class="fa fa-calendar"></i>
                                            Starter
                                            {{ \Carbon\Carbon::parse($result->start_date)->format('d.m.Y') }}
                                            Klokken
                                            {{ \Carbon\Carbon::parse($result->start_date)->format('H:i') }}
                                        </div>
                                        <p class="margin-bottom">
                                            {{ $result->description }}
                                        </p>

                                        <div class="text-right margin-top">
                                            @if( \App\Http\FrontendHelpers::isWebinarAvailable($result) )
                                                <a class="btn btn-success" href="{{ $result->link }}" target="_blank">Bli med på webinar</a>
                                            @else

                                                @if ($result->id == 24 || $result->id == 25 || $result->id == 31)
                                                    <a class="btn btn-warning" href="{{ $coursesTaken && $coursesTaken->hasEnded
                                                        ? 'javascript:void(0)' : $result->link }}" target="_blank">Repriser</a>
                                                @else
                                                    <a class="btn btn-warning" href="{{ \Carbon\Carbon::parse($result->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date))
                                                        ? 'javascript:void(0)' :$result->link }}" target="_blank">Registrer Deg</a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
        <div class="clearfix"></div>
    </div>

@stop

