{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
    <title>Mine Webinar &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
	<style>
		/* .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
			color: #555;
			cursor: default;
			background-color: #fff;
			border: 1px solid #ddd;
			border-bottom-color: transparent;
		}

		.nav-tabs {
			border-bottom: none;
		}

		.tab-content {
			border-top: 1px solid #dee2e6;
		} */
	</style>
@stop

@section('content')
    <div class="learner-container learner-webinar-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    @php
                        $tabWithLabel = [
                            [
                                'name' => 'replay',
                                'label' => trans('site.replay')
                            ]
                        ];
                        $now = Carbon\Carbon::now();
                    @endphp

                <ul class="nav global-nav-tabs">
                    <li class="nav-item">
                        <a href="?tab=webinars" 
                        class="nav-link {{ !in_array(Request::input('tab'), array_column($tabWithLabel, 'name')) 
                        ? 'active' : '' }}">
                            {{ trans('site.learner.my-webinar') }}
                        </a>
                    </li>

                    @foreach($tabWithLabel as $tab)
                        <li class="nav-item">
                            <a href="?tab={{ $tab['name'] }}" 
                            class="nav-link {{ Request::input('tab') == $tab['name'] ? 'active' : '' }}">
                                {{ $tab['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade in active pt-5">
                        @if( Request::input('tab') == 'replay' )
                            <div class="col-md-12">
                                <div class="global-card replay-card">
                                    <div class="card-body pt-0">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <h1 class="page-title">
                                                    {{ trans('site.replay') }}
                                                </h1>
                                            </div>
                                            <div class="col-sm-6 second-search">
                                                <div class="form-group mb-0">
                                                    <form class="webinar-search-container" method="get" 
                                                    action="{{ route('learner.webinar') }}">
                                                        <div class="input-group">
                                                            <input type="hidden" name="tab" value="replay">
                                                            <input type="text" class="form-control" name="search_replay"
                                                                value="{{ Request::input('search_replay') }}"
                                                                placeholder="{{ trans('site.learner.search-webinar-replay') }}"
                                                                aria-label="Enter here...">
                                                            <span class="input-group-btn">
                                                                <button class="btn" type="submit">
                                                                    <i class="fa fa-search"></i>
                                                                </button>
                                                                <a class="btn" type="reset" 
                                                                href="{{ route('learner.webinar') }}?tab=replay">
                                                                    <i class="fa fa-redo"></i>
                                                                </a>
                                                            </span>
                                                        </div> <!-- end input-group -->
                                                    </form> <!-- end searchBoxForm -->
                                                </div> <!-- end #simpleSearchbox -->
                                            </div> <!-- end col-sm-6 -->
                                        </div>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <td>{{ trans('site.date') }}</td>
                                                    <td>{{ trans('site.title') }}</td>
                                                    <td width="400">{{ trans('site.description') }}</td>
                                                    <td width="150">Link</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($replayWebinars as $replayWebinar)
                                                    <tr>
                                                        <td>{{ $replayWebinar->date ? FrontendHelpers::formatDate($replayWebinar->date) : NULL }}</td>
                                                        <td>{{ $replayWebinar->title }}</td>
                                                        <td>{{ $replayWebinar->description }}</td>
                                                        <td>
                                                            @if (!Auth::user()->isDisabled)
                                                                <a href="#" data-toggle="modal" data-target="#videoModal"
                                                                data-record="{{ json_encode($replayWebinar) }}" 
                                                                class="videoBtn red-outline-btn px-4 py-2">
                                                                    {{ trans('site.view') }}
                                                                    <i class="fa fa-eye"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div class="pull-right">
                                            {{ $replayWebinars->appends(Request::all())->links('pagination.short-pagination') }}
                                        </div>

                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="global-card webinar-list-card">
                                        <div class="card-body">
                                            <div class="row top-row">
                                                <div class="col-md-6">
                                                    <h1 class="page-title">
                                                        {{ trans('site.learner.my-webinar') }}
                                                    </h1>
                                                </div> <!-- end col-sm-2 -->
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-sm-10 col-sm-offset-2 first-search">
                                                            <div class="form-group mb-0">
                                                                <form class="webinar-search-container" method="get" action="{{ route('learner.webinar') }}">
                                                                    <div class="input-group">
                                                                        <input type="hidden" name="tab" value="webinars">
                                                                        <input type="text" class="form-control" name="search_upcoming"
                                                                            value="{{ Request::input('search_upcoming') }}"
                                                                            placeholder="{{ trans('site.learner.search-webinar-upcoming') }}"
                                                                            aria-label="Enter here...">
                                                                        <span class="input-group-btn">
                                                                            <button class="btn" type="submit"><i class="fa fa-search"></i></button>
                                                                            <a class="btn" type="reset" href="{{ route('learner.webinar') }}">
                                                                                <i class="fa fa-redo"></i>
                                                                            </a>
                                                                        </span>
                                                                    </div> <!-- end input-group -->
                                                                </form> <!-- end searchBoxForm -->
                                                            </div> <!-- end #simpleSearchbox -->
                                                        </div> <!-- end col-sm-6 -->
                                                    </div> <!-- end row -->
                                                </div> <!-- end col-sm-10 -->
                                            </div>

                                            @foreach ( $subscriptionWebinars->chunk(4) as $subscriptionChunk)
                                                <div class="row webinar-row">
                                                    @foreach ($subscriptionChunk as $webinar)
                                                    @php
                                                        $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);
                                                        $start_date = Carbon\Carbon::parse($webinar->start_date);
                                                    @endphp
                                                        <div class="col-lg-3 col-md-6">
                                                            <div class="card-global webinar-card">
                                                                <div class="card-header webinar-thumb">
                                                                    <?php
                                                                        $img_web_link = '#';
                                                                        if (\App\Http\FrontendHelpers::checkIfWebinarRegistrant(
                                                                            $webinar->id, Auth::user()->id)
                                                                            ) {
                                                                            $img_web_link = \App\Http\FrontendHelpers::getWebinarJoinURL(
                                                                                $webinar->id, Auth::user()->id);
                                                                        } else {
                                                                            $img_web_link = \Carbon\Carbon::parse($webinar->start_date)
                                                                            ->gt(\Carbon\Carbon::parse($coursesTaken->end_date))
                                                                                ? 'javascript:void(0)' 
                                                                                : ($webinar->link ? route('learner.webinar.register',
                                                                                    [$webinar->link, $webinar->id]) : '#');
                                                                        }
                                                                    ?>
                                                                    @if($webinar->link)
                                                                        <a href="{{ $img_web_link }}">
                                                                            <div data-bg="https://www.forfatterskolen.no/{{ 
                                                                                $webinar->image }}">
                                                                                <i class="play-button"></i>
                                                                            </div>
                                                                        </a>
                                                                    @else
                                                                        <div data-bg="https://www.forfatterskolen.no/{{ 
                                                                        $webinar->image }}">
                                                                            <i class="play-button"></i>
                                                                        </div>
                                                                    @endif
                                                                </div> <!-- end card-header -->
                                                                <div class="card-body">
                                                                    <p class="date">
                                                                        <i class="fa fa-calendar-check"></i>
                                                                        {{ str_replace(['_date_', '_time_'],
                                                                        [\Carbon\Carbon::parse($webinar->start_date)->format('d.m.Y'),
                                                                        \Carbon\Carbon::parse($webinar->start_date)->format('H:i')],
                                                                        trans('site.front.our-course.show.start-date')) }}
                                                                    </p>

                                                                    <h3 class="text-center">{{ $webinar->title }}</h3>

                                                                    <p class="text-gray text-center mt-4">
                                                                        {{ $webinar->description }}
                                                                    </p>
                                                                    <div class="hidden-container"></div>
                                                                    <div class="button-container">
                                                                        @if( \App\Http\FrontendHelpers::isWebinarAvailablePlusHour($webinar) )
                                                                            <a class="btn light-red-btn w-100" 
                                                                            href="{{ $webinar->link }}" target="_blank">
                                                                                {{ trans('site.learner.join-webinar') }}
                                                                            </a>
                                                                        @else
                                                                            @if ($webinar->id == 24 || $webinar->id == 25 || $webinar->id == 31)
                                                                                <a class="btn light-red-btn w-100" 
                                                                                href="{{ $coursesTaken && $coursesTaken->hasEnded
                                                                                    ? 'javascript:void(0)' : $webinar->link }}" 
                                                                                    target="_blank">Repriser
                                                                                </a>
                                                                            @else
                                                                                @if($webinar->set_as_replay)
                                                                                    <a class="btn light-red-btn w-100" 
                                                                                    href="{{ $webinar->link }}" target="_blank">
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
                                                                                            ->gt(\Carbon\Carbon::parse($coursesTaken->end_date))
                                                                                    ? 'javascript:void(0)' :route('learner.webinar.register',
                                                                                    [$webinar->link, $webinar->id]) }}">
                                                                                                {{ trans('site.learner.register') }}
                                                                                            </a>
                                                                                        @else
                                                                                            <a href="javascript:void(0)"
                                                                                            class="btn w-100 rounded-0 btn-success disabled" 
                                                                                            disabled>
                                                                                                Påmelding kommer
                                                                                            </a>
                                                                                        @endif
                                                                                    @endif
                                                                                @endif
                                                                            @endif
                                                                        @endif

                                                                        <a href="{{ \Carbon\Carbon::parse($webinar->start_date)
                                                                        ->gt(\Carbon\Carbon::parse($coursesTaken->end_date))
                                                                            ? 'javascript:void(0)' 
                                                                            : route('learner.course.show', 
                                                                            ['id' => $webinar->courses_taken_id]) }}"
                                                                            class="btn light-red-outline-btn mt-3">
                                                                            {{ trans('site.front.course-text') }}:
                                                                            {{ $webinar->course_title }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div> <!-- end card-global -->
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach

                                            <div class="text-center">
                                                {{ $subscriptionWebinars->appends(request()->except('page'))->links('pagination.custom-pagination') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end row -->
                        @endif
                    </div>
                </div>
                </div>
            </div> <!-- end row -->
        </div>
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

    <div id="videoModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                    </h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <div id="video-container"></div>
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

        $(".videoBtn").click(function(){
            let modal = $("#videoModal");
            let record = $(this).data('record');

            modal.find(".modal-title").text(record.title);
            modal.find('#video-container').html(record.lesson_content);
        });
    </script>
@stop