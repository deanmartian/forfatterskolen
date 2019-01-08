@extends('frontend.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen</title>
@stop


@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">

                <div class="col-md-8 dashboard-course no-left-padding">
                    <div class="card global-card">
                        <div class="card-header">
                            <h1>
                                Mine Kurs
                                <a href="{{ route('learner.course') }}" class="float-right view-all">View all</a>
                            </h1>
                        </div>
                        <div class="card-body">
                            @foreach( Auth::user()->coursesTaken as $courseTaken )
                                <div class="col-md-12 course-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 image-container">
                                            <img src="{{$courseTaken->package->course->course_image}}" alt="">
                                        </div>
                                        <div class="col-md-6">
                                            <h3 class="font-weight-normal">
                                                {{$courseTaken->package->course->title}}
                                            </h3>
                                            <p>
                                                {{str_limit(strip_tags($courseTaken->package->course->description), 200)}}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            @if( $courseTaken->is_active )
                                                @if($courseTaken->hasStarted)
                                                    @if($courseTaken->hasEnded)
                                                        <button class="btn site-btn-global" data-toggle="modal" data-target="#renewAllModal">Forny abonnement</button>
                                                    @else
                                                        <a class="btn site-btn-global" href="{{route('learner.course.show', ['id' => $courseTaken->id])}}">Fortsett med dette kurset</a>
                                                    @endif
                                                @else
                                                    <form method="POST" action="{{route('learner.course.take')}}">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="courseTakenId" value="{{$courseTaken->id}}">
                                                        <button type="submit" class="btn site-btn-global">Start dette kurset</button>
                                                    </form>
                                                @endif
                                            @else
                                                <a class="btn btn-warning disabled">Kurs på vent</a>
                                            @endif
                                        </div>
                                    </div> <!-- row -->
                                </div> <!-- end course-item -->
                            @endforeach
                        </div>
                    </div> <!-- end global-card -->
                </div> <!-- end dashboard-course -->

                <div class="col-md-4 dashboard-calendar no-right-padding">
                    <div class="card global-card">
                        <div class="card-header">
                            <h1>
                                Kalender
                                <a href="{{ route('learner.calendar') }}" class="float-right view-all">See more</a>
                            </h1>
                        </div>
                        <div class="card-body">
                            <?php
                                $dashboardCalendar = \App\Http\Controllers\Frontend\LearnerController::dashboardCalendar();
                                // get the unique start
                                $uniqueStart = array_unique(array_map(function ($i) {
                                    if (\Carbon\Carbon::parse($i['start'])->gte(\Carbon\Carbon::today())) {
                                        return $i['start'];
                                    }
                                }, $dashboardCalendar));
                                $counter = 1;
                            ?>
                            @foreach($uniqueStart as $k => $start)
                                @if ($counter <= 2)
                                    <div class="col-md-12 calendar-item">
                                        <div class="row">
                                            <div class="col-md-4 date-container text-center d-flex">
                                                <div class="align-self-center w-100">
                                                    <span>NOVEMBER</span>
                                                    <h1>29</h1>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <?php $calendarCounter = 1;?>
                                                @foreach($dashboardCalendar as $ck =>$calendar)
                                                    @if ($calendarCounter <= 2)
                                                        <p>
                                                            {{ $calendar['title'] }}
                                                        </p>
                                                    @endif
                                                    <?php $calendarCounter++?>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <?php $counter++?>
                            @endforeach
                        </div>
                    </div> <!-- end global-card -->
                </div> <!-- end dashboard-calendar -->
            </div> <!-- end row -->

            <div class="row">
                <div class="divider-center-text w-100 text-center">MINE WEBINAR</div>
            </div>
        </div> <!-- end container -->
    </div>

    <div id="renewAllModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Forny alle kursene for ett år</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.renew-all-courses') }}" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <p>Vil du fornye alle kursene dine for ett år ekstra for kroner 1490,?</p>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">ja</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Nei</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(".renewAllBtn").click(function(){
            let form = $('#renewAllModal').find('form');
            let action = $(this).data('action');
            form.attr('action', action)
        });
    </script>
@stop
