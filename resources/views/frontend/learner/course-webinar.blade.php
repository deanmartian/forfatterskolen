{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
    <title>Mine Webinar &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container learner-webinar-page">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h1 class="font-barlow-regular">
                        {{ trans('site.learner.my-webinar') }}
                    </h1>
                </div> <!-- end col-md-3 -->

                <div class="col-md-8 col-sm-offset-1">
                    <div class="row">
                        <div class="col-sm-6 first-search">
                            <div class="form-group mb-0">
                                <form class="webinar-search-container" method="get" action="{{ route('learner.course-webinar') }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search_upcoming"
                                               value="{{ Request::input('search_upcoming') }}"
                                               placeholder="{{ trans('site.learner.search-webinar-upcoming') }}"
                                               aria-label="Enter here...">
                                        <span class="input-group-btn">
                                            <button class="btn" type="submit"><i class="fa fa-search"></i></button>
                                            <a class="btn" type="reset" href="{{ route('learner.course-webinar') }}">
                                                <i class="fa fa-redo"></i>
                                            </a>
                                        </span>
                                    </div> <!-- end input-group -->
                                </form> <!-- end searchBoxForm -->
                            </div> <!-- end #simpleSearchbox -->
                        </div> <!-- end col-sm-6 -->

                        <div class="col-sm-6 second-search">
                            <div class="form-group mb-0">
                                <form class="webinar-search-container" method="get" action="{{ route('learner.course-webinar') }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search_replay"
                                               value="{{ Request::input('search_replay') }}"
                                               placeholder="{{ trans('site.learner.search-webinar-replay') }}"
                                               aria-label="Enter here...">
                                        <span class="input-group-btn">
                                            <button class="btn" type="submit"><i class="fa fa-search"></i></button>
                                            <a class="btn" type="reset" href="{{ route('learner.course-webinar') }}">
                                                <i class="fa fa-redo"></i>
                                            </a>
                                        </span>
                                    </div> <!-- end input-group -->
                                </form> <!-- end searchBoxForm -->
                            </div> <!-- end #simpleSearchbox -->
                        </div> <!-- end col-sm-6 -->
                    </div> <!-- end row -->
                </div> <!-- end col-sm-10 -->

            </div> <!-- end first row -->

            <div class="row">
                @if (!$isPost)
                    <?php
                    // separate the id's and display the Repriser first
                    $webinarsRepriser = DB::table('courses_taken')
                        ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
                        ->join('courses', 'packages.course_id', '=', 'courses.id')
                        ->join('webinars', 'courses.id', '=', 'webinars.course_id')
                        ->select('webinars.*','courses_taken.id as courses_taken_id','courses.title as course_title', 'courses_taken.deleted_at')
                        ->where('user_id',Auth::user()->id)
                        ->where('courses.id','!=',17) // just added this line to show all other courses webinar except webinar pakke
                        ->where(function($query){
                            $query->whereIn('webinars.id',[24, 25, 31]);
                            $query->orWhere('set_as_replay',1);
                        })
                        //->whereIn('webinars.id',[24, 25, 31]) // remove this to return the original
                        ->whereNull('courses_taken.deleted_at')
                        ->orderBy('courses.type', 'ASC')
                        ->orderBy('webinars.start_date', 'ASC')
                        ->get();

                    $webinars = DB::table('courses_taken')
                        ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
                        ->join('courses', 'packages.course_id', '=', 'courses.id')
                        ->join('webinars', 'courses.id', '=', 'webinars.course_id')
                        ->select('webinars.*','courses_taken.id as courses_taken_id','courses.title as course_title', 'courses_taken.deleted_at')
                        ->where('user_id',Auth::user()->id)
                        ->where('courses.id','!=',17) // just added this line to show all other courses webinar except webinar pakke
                        ->whereNotIn('webinars.id',[24, 25, 31])
                        ->where('set_as_replay',0)
                        ->whereNull('courses_taken.deleted_at')
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
                            <div class="col-sm-12 col-md-6 col-lg-4 mt-5">
                                <div class="card card-global border-0">
                                    <div class="card-header webinar-thumb">
                                        <a href="{{ $webinar->link }}">
                                            <div data-bg="https://www.forfatterskolen.no/{{ $webinar->image }}">
                                                <i class="play-button"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <?php $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);?>
                                        <div class="webinar-header">
                                            <h4>
                                                <i class="book"></i> {{ trans('site.front.course-text') }}:
                                                <a href="{{ \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                        ? 'javascript:void(0)' : route('learner.course.show', ['id' => $webinar->courses_taken_id]) }}">{{ $webinar->course_title }}</a>
                                            </h4>
                                        </div>

                                        <h2>{{ $webinar->title }}</h2>

                                        <p class="note-color my-4">
                                            {{ $webinar->description }}
                                        </p>
                                    </div> <!-- end card-body -->
                                    <div class="card-footer border-0 p-0">
                                        @if( \App\Http\FrontendHelpers::isWebinarAvailable($webinar) )
                                            <a class="btn site-btn-global w-100 rounded-0" href="{{ $webinar->link }}" target="_blank">
                                                {{ trans('site.learner.join-webinar') }}
                                                <i class="img-icon icon-right-arrow"></i>
                                            </a>
                                        @else
                                            @if ($webinar->id == 24 || $webinar->id == 25 || $webinar->id == 31)
                                                <a class="btn site-btn-global w-100 rounded-0" href="{{ $coursesTaken && $coursesTaken->hasEnded
                                                        ? 'javascript:void(0)' : $webinar->link }}" target="_blank">
                                                    {{ trans('site.learner.replay') }}
                                                    <i class="img-icon icon-right-arrow"></i>
                                                </a>
                                            @else
                                                @if($webinar->set_as_replay)
                                                    <a class="btn site-btn-global w-100 rounded-0" href="{{ $webinar->link }}" target="_blank">
                                                        {{ trans('site.learner.replay') }}
                                                        <i class="img-icon icon-right-arrow"></i>
                                                    </a>
                                                @else
                                                    <a class="btn site-btn-global w-100 rounded-0"
                                                       href="{{ \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                        ? 'javascript:void(0)' :$webinar->link }}" target="_blank">
                                                        {{ trans('site.learner.register') }}
                                                        <i class="img-icon icon-right-arrow"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </div> <!-- end card -->
                            </div>
                        @endif
                    @endforeach <!-- end $webinarsRepriser -->

                    @foreach($webinars as $webinar)
                        <?php
                        $start_date = Carbon\Carbon::parse($webinar->start_date)->addHour();
                        $now = Carbon\Carbon::now();
                        $diff = $now->diffIndays($start_date, false);
                        $diffWithHours = $now->diffInHours($start_date, false);
                        ?>
                        @if( $diffWithHours >= 0 )
                            <div class="col-sm-12 col-md-6 col-lg-4 mt-5">
                                <div class="card card-global border-0">
                                    <div class="card-header webinar-thumb">
                                        <?php
                                            $img_web_link = '#';
                                            $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);
                                            if (\App\Http\FrontendHelpers::checkIfWebinarRegistrant($webinar->id, Auth::user()->id)) {
                                                $img_web_link = \App\Http\FrontendHelpers::getWebinarJoinURL($webinar->id, Auth::user()->id);
                                            } else {
                                                $img_web_link = \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                    ? 'javascript:void(0)' :url('/account/webinar/register/' . $webinar->link . '/' . $webinar->id);
                                                /*route('learner.webinar.register',
                                                        [$webinar->link, $webinar->id])*/
                                            }
                                        ?>
                                        @if($webinar->link)
                                            <a href="{{ $img_web_link }}">
                                                <div style="background-image: url({{ $webinar->image }})">
                                                    <i class="play-button"></i>
                                                </div>
                                            </a>
                                        @else
                                            <div style="background-image: url({{ $webinar->image }})">
                                                <i class="play-button"></i>
                                            </div>
                                        @endif

                                    </div>
                                    <div class="card-body">
                                        <?php $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);
                                        ?>
                                        <div class="webinar-header">
                                            <h4>
                                                <i class="book"></i> {{ trans('site.front.course-text') }}:
                                                <a href="{{ \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                    ? 'javascript:void(0)' : route('learner.course.show', ['id' => $webinar->courses_taken_id]) }}">{{ $webinar->course_title }}</a>
                                            </h4>

                                            <h4>
                                                <i class="calendar"></i>
                                                {{ str_replace(['_date_', '_time_'],
                                                [\Carbon\Carbon::parse($webinar->start_date)->format('d.m.Y'),
                                                \Carbon\Carbon::parse($webinar->start_date)->format('H:i')],
                                                trans('site.front.our-course.show.start-date')) }}
                                            </h4>
                                        </div>

                                        <h2>{{ $webinar->title }}</h2>

                                        <p class="note-color my-4">
                                            {{ $webinar->description }}
                                        </p>
                                    </div> <!-- end card-body -->
                                    <div class="card-footer border-0 p-0">
                                        @if( \App\Http\FrontendHelpers::isWebinarAvailablePlusHour($webinar) )
                                            <a class="btn site-btn-global w-100 rounded-0" href="{{ $webinar->link }}" target="_blank">
                                                {{ trans('site.learner.join-webinar') }}
                                                <i class="img-icon icon-right-arrow"></i>
                                            </a>
                                        @else
                                            @if ($webinar->id == 24 || $webinar->id == 25 || $webinar->id == 31)
                                                <a class="btn site-btn-global w-100 rounded-0" href="{{ $coursesTaken && $coursesTaken->hasEnded
                                                    ? 'javascript:void(0)' : $webinar->link }}" target="_blank">
                                                    {{ trans('site.learner.replay') }}
                                                    <i class="img-icon icon-right-arrow"></i>
                                                </a>
                                            @else
                                                {{--@if($webinar->set_as_replay)
                                                    <a class="btn site-btn-global w-100 rounded-0" href="{{ $webinar->link }}" target="_blank">
                                                        Repriser
                                                        <i class="img-icon icon-right-arrow"></i>
                                                    </a>
                                                @else
                                                    <a class="btn site-btn-global w-100 rounded-0" href="{{ \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                    ? 'javascript:void(0)' :$webinar->link }}" target="_blank">
                                                        Registrer Deg
                                                        <i class="img-icon icon-right-arrow"></i>
                                                    </a>
                                                @endif--}}
                                                @if (\App\Http\FrontendHelpers::checkIfWebinarRegistrant($webinar->id, Auth::user()->id))
                                                    <a class="btn site-btn-global w-100 rounded-0"
                                                       href="{{ \App\Http\FrontendHelpers::getWebinarJoinURL($webinar->id, Auth::user()->id) }}">
                                                        @if ($now->diffInMinutes($start_date, false) <= 90)
                                                            Se Webinar
                                                        @else
                                                            {{ trans('site.learner.signed') }}
                                                        @endif
                                                    </a>
                                                @else
                                                    {{-- check if have webinar link --}}
                                                    @if($webinar->link)
                                                        <a class="btn site-btn-global w-100 rounded-0 webinarRegister"
                                                           href="{{ \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                        ? 'javascript:void(0)' :route('learner.webinar.register',
                                                        [$webinar->link, $webinar->id]) }}">
                                                            {{ trans('site.learner.register') }}
                                                            <i class="img-icon icon-right-arrow"></i>
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0)"
                                                           class="btn w-100 rounded-0 btn-success disabled" disabled>
                                                            Påmelding kommer
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </div> <!-- end card -->
                            </div>
                        @endif
                    @endforeach
                @else
                    @foreach($searchResult->chunk(3) as $k => $result_chunk)
                        @foreach($result_chunk as $result)
                            @if (!$isReplay)
                                <div class="col-sm-12 col-md-6 col-lg-4 mt-5">
                                    <div class="card card-global border-0">
                                        <div class="card-header webinar-thumb">
                                            <?php
                                                $img_web_link = '#';
                                                $coursesTaken = \App\CoursesTaken::find($result->courses_taken_id);
                                                if (\App\Http\FrontendHelpers::checkIfWebinarRegistrant($result->id, Auth::user()->id)) {
                                                    $img_web_link = \App\Http\FrontendHelpers::getWebinarJoinURL($result->id, Auth::user()->id);
                                                } else {
                                                    $img_web_link = \Carbon\Carbon::parse($result->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                        ? 'javascript:void(0)' :route('learner.webinar.register',
                                                            [$result->link, $result->id]);
                                                }
                                            ?>
                                            <a href="{{ $img_web_link }}">
                                                <div style="background-image: url({{ $result->image }})">
                                                    <i class="play-button"></i>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="card-body">
                                            <?php $coursesTaken = \App\CoursesTaken::find($result->courses_taken_id);?>
                                            <div class="webinar-header">
                                                <h4>
                                                    <i class="book"></i> {{ trans('site.front.course-text') }}:
                                                    <a href="{{ \Carbon\Carbon::parse($result->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                        ? 'javascript:void(0)' : route('learner.course.show', ['id' => $result->courses_taken_id]) }}">{{ $result->course_title }}</a>
                                                </h4>

                                                <h4>
                                                    <i class="calendar"></i>
                                                    {{ str_replace(['_date_', '_time_'],
                                                [\Carbon\Carbon::parse($result->start_date)->format('d.m.Y'),
                                                \Carbon\Carbon::parse($result->start_date)->format('H:i')],
                                                trans('site.front.our-course.show.start-date')) }}
                                                </h4>
                                            </div>

                                            <h2>{{ $result->title }}</h2>

                                            <p class="note-color my-4">
                                                {{ $result->description }}
                                            </p>
                                        </div> <!-- end card-body -->

                                        <div class="card-footer border-0 p-0">
                                            @if( \App\Http\FrontendHelpers::isWebinarAvailable($result) )
                                                <a class="btn site-btn-global w-100 rounded-0" href="{{ $result->link }}" target="_blank">
                                                    {{ trans('site.learner.join-webinar') }}
                                                    <i class="img-icon icon-right-arrow"></i>
                                                </a>
                                            @else
                                                @if ($result->id == 24 || $result->id == 25 || $result->id == 31)
                                                    <a class="btn site-btn-global w-100 rounded-0" href="{{ $coursesTaken && $coursesTaken->hasEnded
                                                            ? 'javascript:void(0)' : $result->link }}" target="_blank">
                                                        {{ trans('site.learner.replay') }}
                                                        <i class="img-icon icon-right-arrow"></i>
                                                    </a>
                                                @else
                                                    {{--<a class="btn site-btn-global w-100 rounded-0" href="{{ \Carbon\Carbon::parse($result->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                            ? 'javascript:void(0)' :$result->link }}" target="_blank">
                                                        Registrer Deg
                                                        <i class="img-icon icon-right-arrow"></i>
                                                    </a>--}}
                                                    @if (\App\Http\FrontendHelpers::checkIfWebinarRegistrant($result->id, Auth::user()->id))
                                                        <a class="btn site-btn-global w-100 rounded-0"
                                                           href="{{ \App\Http\FrontendHelpers::getWebinarJoinURL($result->id, Auth::user()->id) }}">
                                                            {{ trans('site.learner.signed') }}
                                                        </a>
                                                    @else
                                                        <a class="btn site-btn-global w-100 rounded-0 webinarRegister"
                                                           href="{{ \Carbon\Carbon::parse($result->start_date)->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                    ? 'javascript:void(0)' :route('learner.webinar.register',
                                                    [$result->link, $result->id]) }}">
                                                            {{ trans('site.learner.register') }}
                                                            <i class="img-icon icon-right-arrow"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </div> <!-- end card-footer -->
                                    </div> <!-- end card -->
                                </div> <!-- end col-sm-12 col-md-4 mt-5 -->
                            @else
                                <div class="col-sm-12 col-md-6 col-lg-4 mt-5">
                                    <div class="card card-global border-0">
                                        <div class="card-header webinar-thumb">
                                            {{--<i class="play-button"></i>--}}
                                            {!! ($result->lesson_content) !!}
                                        </div>
                                        <div class="card-body">
                                            <h2>{{ $result->title }}</h2>
                                        </div>
                                    </div> <!-- end card -->
                                </div> <!-- end col-sm-12 col-md-4 -->
                            @endif
                        @endforeach
                    @endforeach
                @endif
            </div> <!-- end row -->

        </div> <!-- end container -->
    </div>

    <div id="submitSuccessModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
                    <p>
                        {{ trans('site.learner.webinar-register-success') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(".webinarRegister").click(function(){
            let register_btn = $(this);
            register_btn.text('');
            register_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            register_btn.attr('disabled', 'disabled');
        });

        @if (Session::has('success'))
        $('#submitSuccessModal').modal('show');
        @endif
    </script>
@stop

