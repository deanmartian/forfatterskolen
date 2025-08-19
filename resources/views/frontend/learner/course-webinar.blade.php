{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
    <title>Mine Webinar &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container learner-webinar-page learner-course-webinar-page">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="global-card webinar-list-card">
                        <div class="row">
                            <div class="col-lg-3">
                                <h1 class="page-title">
                                    {{ trans('site.learner.my-webinar') }}
                                </h1>
                            </div> <!-- end col-md-3 -->
            
                            <div class="col-lg-8 col-lg-offset-1">
                                <div class="row">
                                    <div class="col-sm-6 second-search">
                                        <div class="form-group mb-0">
                                            <form class="webinar-search-container" method="get"
                                             action="{{ route('learner.course-webinar') }}">
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
                                            <form class="webinar-search-container" method="get"
                                             action="{{ route('learner.course-webinar') }}">
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

                        @if (!$isReplay)
                            @foreach ( $webinars->chunk(4) as $webinarChunk)
                                <div class="row webinar-row">
                                    @foreach ($webinarChunk as $webinar)
                                        @php
                                            $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);
                                            $start_date = Carbon\Carbon::parse($webinar->start_date)->addHour();
                                            $now = Carbon\Carbon::now();
                                        @endphp
                                        <div class="col-lg-3 col-md-6">
                                            <div class="card-global webinar-card">
                                                <div class="card-header webinar-thumb">
                                                    <?php
                                                        $img_web_link = '#';
                                                        $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);
                                                        if (\App\Http\FrontendHelpers::checkIfWebinarRegistrant(
                                                            $webinar->id, Auth::user()->id)) 
                                                        {
                                                            $img_web_link = \App\Http\FrontendHelpers::getWebinarJoinURL(
                                                                $webinar->id, Auth::user()->id);
                                                        } else {
                                                            $img_web_link = \Carbon\Carbon::parse($webinar->start_date)
                                                            ->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                                ? 'javascript:void(0)' 
                                                                : url('/account/webinar/register/' 
                                                                    . $webinar->link . '/' . $webinar->id);
                                                        }
                                                    ?>
                                                    <a href="{{ !Auth::user()->isDisabled ? ($webinar->set_as_replay 
                                                    ? $webinar->link : ($webinar->link ? $img_web_link : '#')) : '#' }}">
                                                        <div data-bg="https://www.forfatterskolen.no/{{ $webinar->image }}">
                                                            <i class="play-button"></i>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="card-body pb-3">
                                                    @if (!$webinar->set_as_replay )
                                                        <p class="date">
                                                            <i class="fa fa-calendar-check"></i>
                                                            {{ str_replace(['_date_', '_time_'],
                                                            [\Carbon\Carbon::parse($webinar->start_date)->format('d.m.Y'),
                                                            \Carbon\Carbon::parse($webinar->start_date)->format('H:i')],
                                                            trans('site.front.our-course.show.start-date')) }}
                                                        </p>
                                                    @endif

                                                    <h3 class="text-center">
                                                        {{ $webinar->title }}
                                                    </h3>

                                                    <p class="text-gray text-center mt-4 description">
                                                        {{ $webinar->description }}
                                                    </p>

                                                    <div class="hidden-container"></div>
                                                    <div class="button-container">
                                                        @if (!Auth::user()->isDisabled)
                                                        
                                                            @if ($webinar->set_as_replay)
                                                                @if( \App\Http\FrontendHelpers::isWebinarAvailable($webinar) )
                                                                    <a class="btn light-red-btn w-100" 
                                                                    href="{{ $webinar->link }}" target="_blank">
                                                                        {{ trans('site.learner.join-webinar') }}
                                                                    </a>
                                                                @else
                                                                    @if ($webinar->id == 24 || $webinar->id == 25 || $webinar->id == 31)
                                                                        <a class="btn site-btn-global w-100 rounded-0" 
                                                                        href="{{ $coursesTaken && $coursesTaken->hasEnded
                                                                                ? 'javascript:void(0)' : $webinar->link }}" target="_blank">
                                                                            {{ trans('site.learner.replay') }}
                                                                        </a>
                                                                    @else
                                                                        @if($webinar->set_as_replay)
                                                                            <a class="btn light-red-btn w-100" href="{{ $webinar->link }}" 
                                                                                target="_blank">
                                                                                {{ trans('site.learner.replay') }}
                                                                            </a>
                                                                        @else
                                                                            <a class="btn light-red-btn w-100"
                                                                            href="{{ \Carbon\Carbon::parse($webinar->start_date)
                                                                            ->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                                                ? 'javascript:void(0)' :$webinar->link }}" target="_blank">
                                                                                {{ trans('site.learner.register') }}
                                                                            </a>
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                            @else {{-- not set as replay --}}
                                                                @if( \App\Http\FrontendHelpers::isWebinarAvailablePlusHour($webinar) )
                                                                    <a class="btn light-red-btn w-100" href="{{ $webinar->link }}" target="_blank">
                                                                        {{ trans('site.learner.join-webinar') }}
                                                                        <i class="img-icon icon-right-arrow"></i>
                                                                    </a>
                                                                @else
                                                                    @if ($webinar->id == 24 || $webinar->id == 25 || $webinar->id == 31)
                                                                        <a class="btn light-red-btn w-100" href="{{ $coursesTaken && $coursesTaken->hasEnded
                                                                            ? 'javascript:void(0)' : $webinar->link }}" target="_blank">
                                                                            {{ trans('site.learner.replay') }}
                                                                        </a>
                                                                    @else
                                                                        @if (\App\Http\FrontendHelpers::checkIfWebinarRegistrant(
                                                                            $webinar->id, Auth::user()->id))
                                                                            <a class="btn light-red-btn w-100"
                                                                            href="{{ \App\Http\FrontendHelpers::getWebinarJoinURL(
                                                                                $webinar->id, Auth::user()->id) }}">
                                                                                @if ($now->diffInMinutes($start_date, false) <= 90)
                                                                                    Se Webinar
                                                                                @else
                                                                                    {{ trans('site.learner.signed') }}
                                                                                @endif
                                                                            </a>
                                                                        @else
                                                                            {{-- check if have webinar link --}}
                                                                            @if($webinar->link)
                                                                                <a class="btn light-red-btn w-100 webinarRegister"
                                                                                href="{{ \Carbon\Carbon::parse($webinar->start_date)
                                                                                ->gt(\Carbon\Carbon::parse(
                                                                                    $coursesTaken->end_date_with_value))
                                                                                ? 'javascript:void(0)' :route('learner.webinar.register',
                                                                                [$webinar->link, $webinar->id]) }}">
                                                                                    {{ trans('site.learner.register') }}
                                                                                </a>
                                                                            @else
                                                                                <a href="javascript:void(0)"
                                                                                class="btn light-red-btn w-100 disabled" disabled>
                                                                                    Påmelding kommer
                                                                                </a>
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                            @endif

                                                            <a href="{{ \Carbon\Carbon::parse($webinar->start_date)
                                                                ->gt(\Carbon\Carbon::parse($coursesTaken->end_date_with_value))
                                                                    ? 'javascript:void(0)' 
                                                                    : route('learner.course.show', 
                                                                    ['id' => $webinar->courses_taken_id]) }}"
                                                                    class="btn light-red-outline-btn mt-3">
                                                                    {{ trans('site.front.course-text') }}:
                                                                    {{ $webinar->course_title }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            @foreach ( $lessonContents->chunk(4) as $lessonContentChunk)
                                <div class="row webinar-row">
                                    @foreach ($lessonContentChunk as $lesson)
                                        <div class="col-lg-3 col-md-6">
                                            <div class="card-global webinar-card">
                                                <div class="card-header webinar-thumb">
                                                    {!! ($lesson->lesson_content) !!}
                                                </div>
                                                <div class="card-body pb-3">
                                                    <h3 class="text-center">
                                                        {{ $lesson->title }}
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @endif

                        <div class="text-center">
                            @if (!$isReplay)
                                {{ $webinars->appends(request()->except('page'))->links('pagination.custom-pagination') }}
                            @else
                                {{ $lessonContents->appends(request()->except('page'))->links('pagination.custom-pagination') }}
                            @endif
                        </div>
                    </div>
                </div>
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

