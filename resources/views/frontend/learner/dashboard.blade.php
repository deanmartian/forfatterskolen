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
                                <a href="{{ route('learner.course') }}" class="float-right view-all">Se alle</a>
                            </h1>
                        </div>
                        <div class="card-body">
                            @foreach( Auth::user()->coursesTaken()->limit(3)->get() as $courseTaken )
                                <div class="col-md-12 course-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 image-container">
                                            <img src="{{$courseTaken->package->course->course_image}}" alt="">
                                        </div>
                                        <div class="col-md-6 course-details">
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
                                <a href="{{ route('learner.calendar') }}" class="float-right view-all">Ser mer</a>
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

                                $filteredUniqueStart = array_filter($uniqueStart); // filter empty
                                sort($filteredUniqueStart); // sort the result
                                $counter = 1;
                            ?>
                            @foreach($filteredUniqueStart as $k => $start)
                                <?php
                                    $parseStart = \Carbon\Carbon::parse($start);
                                ?>
                                @if ($counter <= 2)
                                    <div class="col-md-12 calendar-item">
                                        <div class="row">
                                            <div class="col-md-4 date-container text-center d-flex">
                                                <div class="align-self-center w-100">
                                                    <span>{{ ucfirst(\App\Http\FrontendHelpers::convertMonthLanguage($parseStart->format('n'))) }}</span>
                                                    <h1>{{ $parseStart->format('d') }}</h1>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <?php $calendarCounter = 1;?>
                                                {{--@foreach($dashboardCalendar as $ck =>$calendar)
                                                    @if ($calendarCounter <= 2)
                                                        <p>
                                                            {{ $calendar['title'] }}
                                                        </p>
                                                    @endif--}}
                                                    <?php /*$calendarCounter++;*/?>
                                                {{--@endforeach--}}

                                                    @foreach($dashboardCalendar as $calendar)
                                                        @if ($calendar['start'] == $start && $calendarCounter <= 2)
                                                            <p>
                                                                {{ $calendar['title'] }}
                                                            </p>
                                                            <?php $calendarCounter++;?>
                                                        @endif
                                                    @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <?php $counter++?>
                            @endforeach
                        </div>
                    </div> <!-- end global-card -->

                    @if(\App\Course::free()->count())
                        <div class="card global-card mt-3">
                            <div class="card-header">
                                <h1>
                                    Gratis kurs tilgjengelig
                                </h1>
                            </div>
                            <div class="card-body">
                                @foreach(\App\Course::free() as $free)
                                    <div class="col-md-12 mb-3">
                                        <div class="col-md-7">
                                            <b>
                                                {{ $free->title }}
                                            </b>
                                        </div>
                                        <div class="col-md-5">
                                            <?php
                                                $course_packages = $free->packages->pluck('id')->toArray();
                                                $courseTaken = App\CoursesTaken::where('user_id', Auth::user()->id)
                                            ->whereIn('package_id', $course_packages)->first();
                                            ?>
                                            @if (!$courseTaken)
                                                <form action="{{ route('front.course.getFreeCourse', $free->id) }}" method="POST"
                                                      onsubmit="disableSubmit(this)" class="form-inline">
                                                    {{ csrf_field() }}
                                                    <button class="btn btn-theme" type="submit">Få gratis kurset</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div> <!-- end global-card -->
                    @endif <!-- end if has free course -->
                </div> <!-- end dashboard-calendar -->
            </div> <!-- end row -->

            <div class="row dashboard-webinar-container">
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
                <div class="divider-center-text">MINE WEBINAR</div>

                @foreach($webinarsRepriser as $webinar)
                    <?php
                        $start_date = Carbon\Carbon::parse($webinar->start_date);
                        $now = Carbon\Carbon::now();
                        $diff = $now->diffIndays($start_date, false);
                        $diffWithHours = $now->diffInHours($start_date, false);
                    ?>
                    @if( $diffWithHours >= 0 )
                        <?php
                            $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);
                            $coursesTakenEndDate = $coursesTaken->end_date ?: \Carbon\Carbon::parse($coursesTaken->started_at)->addYear(1)->format('Y-m-d');
                        ?>
                        <div class="col-md-3 mb-4">
                            <a href="{{ \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTakenEndDate))
                                                    ? 'javascript:void(0)' :$webinar->link }}">
                                <div class="image-container" style="background-image: url({{ $webinar->image }})">
                                </div>
                            </a>
                            <div class="details-container">
                                <h3>
                                    {{ $webinar->title }}
                                </h3>
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
                        <?php
                            $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);
                            $coursesTakenEndDate = $coursesTaken->end_date ?: \Carbon\Carbon::parse($coursesTaken->started_at)->addYear(1)->format('Y-m-d');
                        ?>
                        <div class="col-md-3 mb-4">
                            <a href="{{ \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTakenEndDate))
                                                ? 'javascript:void(0)' :$webinar->link }}">
                                <div class="image-container" style="background-image: url({{ $webinar->image }})">
                                </div>
                            </a>
                            <div class="details-container">
                                <h3>
                                    {{ $webinar->title }}
                                </h3>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div> <!-- end dashboard-webinar-container-->

            <div class="row dashboard-last-container">
                <div class="col-md-6 no-left-padding">
                    <div class="card global-card">
                        <div class="card-header">
                            <h1>
                                Oppgaver
                                <a href="{{ route('learner.assignment') }}" class="float-right view-all">Se alle</a>
                            </h1>
                        </div>
                        <div class="card-body py-0">
                            <table class="table table-global">
                                <tbody>
                                    @foreach(\App\Http\Controllers\Frontend\LearnerController::dashboardAssignment() as $assignment)
                                        <?php
                                        $manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first();
                                        ?>
                                        <tr>
                                            <td>{{ $assignment->title }}</td>
                                            <td width="200" class="text-center">
                                                @if( $manuscript )
                                                    @if (!$manuscript->locked)
                                                        <div>
                                                            <button type="button" class="btn btn-info editManuscriptBtn" data-toggle="modal" data-target="#editManuscriptModal" data-action="{{ route('learner.assignment.replace_manuscript', $manuscript->id) }}"><i class="fa fa-pencil"></i></button>
                                                            <button type="button" class="btn btn-danger deleteManuscriptBtn" data-toggle="modal" data-target="#deleteManuscriptModal" data-action="{{ route('learner.assignment.delete_manuscript', $manuscript->id) }}"><i class="fa fa-trash"></i></button>
                                                        </div>
                                                    @endif
                                                @else
                                                    @if($assignment->for_editor)
                                                        <button class="btn site-btn-global submitEditorManuscriptBtn" data-toggle="modal"
                                                                data-target="#submitEditorManuscriptModal"
                                                                data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
                                                                {{--@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($assignment->submission_date))) disabled @endif--}}>
                                                            Last opp manus
                                                        </button>
                                                    @else
                                                        <button class="btn site-btn-global submitManuscriptBtn" data-toggle="modal"
                                                                data-target="#submitManuscriptModal"
                                                                data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
                                                                {{--@if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($assignment->submission_date))) disabled @endif--}}>
                                                            Last opp manus
                                                        </button>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end assignment section -->

                <div class="col-md-6 no-right-padding">
                    <div class="card global-card">
                        <div class="card-header">
                            <h1>
                                Mine Fakturaer
                                <a href="{{ route('learner.invoice') }}" class="float-right view-all">Se alle</a>
                            </h1>
                        </div>
                        <div class="card-body py-0">
                            <table class="table table-global">
                                <thead>
                                    <tr>
                                        <th>Fakturanummer</th>
                                        <th>Frist</th>
                                        <th>Restbeløp</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(Auth::user()->invoices()->limit(5)->get() as $invoice)
                                        <?php
                                            $transactions_sum = $invoice->transactions->sum('amount');
                                            // remove if the above code is uncomment
                                            $balance = $invoice->fiken_balance;
                                            $status = $invoice->fiken_is_paid ? "BETALT" : "UBETALT";
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="{{route('learner.invoice.show', $invoice->id)}}">{{$invoice->invoice_number}}</a>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($invoice->fiken_dueDate)->format('d.m.Y') }}
                                            </td>
                                            <td>
                                                @if($invoice->fiken_is_paid)
                                                    {{\App\Http\FrontendHelpers::currencyFormat(0)}}
                                                @else
                                                    {{\App\Http\FrontendHelpers::currencyFormat($balance - $transactions_sum)}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($invoice->fiken_is_paid)
                                                    <span class="label label-success">{{$status}}</span>
                                                @else
                                                    <span class="label label-danger">{{$status}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- end invoice section-->
            </div> <!-- end dashboard-last-container-->
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

    <div id="submitEditorManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Last opp manus</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data"
                          onsubmit="disableSubmit(this);">
                        {{ csrf_field() }}
                        <div class="form-group mb-2">
                            <label class="mb-0">* Godkjente fil formater er DOC, DOCX.</label>
                            <input type="file" class="form-control margin-top" required name="filename" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        </div>

                        <div class="form-group mb-2">
                            <label class="mb-0">
                                Sjanger
                            </label>
                            <select class="form-control" name="type" required>
                                <option value="" disabled="disabled" selected>Select Type</option>
                                @foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
                                    <option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            Hvor i manuset <br>
                            @foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
                                <input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label class="mb-0">{{ $manu['option'] }}</label> <br>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">Upload</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="submitManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Last opp manus</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this);">
                        {{ csrf_field() }}
                        <div class="form-group mb-2">
                            <label class="mb-0">* Godkjente fil formater er DOC, DOCX, PDF og ODT.</label>
                            <input type="file" class="form-control margin-top" required name="filename" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                        </div>

                        <div class="form-group mb-2">
                            <label class="mb-0">
                                Sjanger
                            </label>
                            <select class="form-control" name="type" required>
                                <option value="" disabled="disabled" selected>Select Type</option>
                                @foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
                                    <option value="{{ $type['id'] }}"> {{ $type['option'] }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            Hvor i manuset <br>
                            @foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
                                <input type="radio" name="manu_type" value="{{ $manu['id'] }}" required> <label class="mb-0">{{ $manu['option'] }}</label> <br>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">Upload</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Replace manuscript</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Manuscript</label>
                            <input type="file" class="form-control" required name="filename" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                            * Godkjente fil formater er DOC, DOCX, PDF og ODT.
                        </div>

                        <button type="submit" class="btn btn-primary pull-right margin-top">Submit</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Delete manuscript</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    Are you sure to delete this manuscript?
                    Warning: This cannot be undone.
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="errorMaxword" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
                    Antall ord er for mange, maks {{ Session::get('editorMaxWord') }} ord. Rediger teksten og send inn på nytt.
                </div>
            </div>
        </div>
    </div>

    <div id="submitSuccessModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
                    Din oppgave har blitt levert!
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>

        @if (Session::has('success'))
            $('#submitSuccessModal').modal('show');
        @endif

        @if (Session::has('errorMaxWord'))
            $('#errorMaxword').modal('show');
        @endif

        $(".renewAllBtn").click(function(){
            let form = $('#renewAllModal').find('form');
            let action = $(this).data('action');
            form.attr('action', action)
        });

        $('.submitEditorManuscriptBtn').click(function(){
            let form = $('#submitEditorManuscriptModal').find('form');
            let action = $(this).data('action');
            form.attr('action', action);
        });

        $('.submitManuscriptBtn').click(function(){
            let form = $('#submitManuscriptModal').find('form');
            let action = $(this).data('action');
            form.attr('action', action);
        });

        $('.editManuscriptBtn').click(function(){
            let form = $('#editManuscriptModal').find('form');
            let action = $(this).data('action');
            form.attr('action', action);
        });

        $('.deleteManuscriptBtn').click(function(){
            let form = $('#deleteManuscriptModal').find('form');
            let action = $(this).data('action');
            form.attr('action', action)
        });
    </script>
@stop
