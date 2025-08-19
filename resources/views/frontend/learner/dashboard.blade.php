{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')
    
    <div class="learner-container learner-dashboard-wrapper">
        <div class="container">
            <h1 class="page-title">
                Dashboard
            </h1>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card global-card">
                        <div class="card-header">
                            <h2>
                                {{ trans('site.learner.my-course') }}
                                <a href="{{ route('learner.course') }}" class="float-right view-all">
                                    {{ trans('site.learner.see-all') }}
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </h2>
                        </div>
                        <div class="card-body">
                            @foreach ($coursesTaken as $courseTaken)
                                <div class="course-item">
                                    <div class="col-md-7">
                                        <img data-src="https://www.forfatterskolen.no/{{$courseTaken->package->course->course_image}}" 
                                            alt="{{ $courseTaken->package->course->title }}">
                                    </div>
                                    <div class="col-md-5">
                                        <h3>
                                            {{$courseTaken->package->course->title}}
                                        </h3>
                                        <p>
                                            {!! \Illuminate\Support\Str::limit(
                                                strip_tags($courseTaken->package->course->description), 200
                                                ) !!}
                                        </p>

                                        @if (!Auth::user()->isDisabled)
                                            @if( $courseTaken->is_active )
                                                @if($courseTaken->hasStarted)
                                                    @if($courseTaken->hasEnded)
                                                        <button class="btn light-red-outline-btn" data-toggle="modal"
                                                                data-target="#renewAllModal">
                                                            {{ trans('site.learner.renew-subscription') }}
                                                        </button>
                                                    @else
                                                        <a class="btn light-red-outline-btn"
                                                            href="{{route('learner.course.show', ['id' => $courseTaken->id])}}">
                                                            {{ trans('site.learner.continue-this-course') }}
                                                        </a>
                                                    @endif
                                                @else
                                                    <form method="POST" action="{{route('learner.course.take')}}">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="courseTakenId" value="{{$courseTaken->id}}">
                                                        <button type="submit" class="btn light-red-outline-btn">
                                                            {{ trans('site.learner.start-course') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <a class="btn btn-warning disabled">
                                                    {{ trans('site.learner.course-on-hold') }}
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            
                        </div>
                    </div> <!-- end course card -->

                    <div class="card global-card mt-5">
                        <div class="card-header">
                            <h2>
                                {{ trans('site.learner.my-invoice') }}
                                <a href="{{ route('learner.invoice') }}" class="float-right view-all">
                                    {{ trans('site.learner.see-all') }}
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </h2>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-global">
                                <thead>
                                    <tr>
                                        <th class="font-weight-bold">
                                            {{ trans('site.learner.invoice-number') }}
                                        </th>
                                        <th class="font-weight-bold">
                                            {{ trans('site.learner.deadline') }}
                                        </th>
                                        <th class="font-weight-bold">
                                            {{ trans('site.learner.remainders') }}
                                        </th>
                                        <th class="font-weight-bold">
                                            {{ trans('site.learner.status') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(Auth::user()->invoices()->limit(5)->get() as $invoice)
                                        <?php
                                            $transactions_sum = $invoice->transactions->sum('amount');
                                            // remove if the above code is uncomment
                                            $balance = $invoice->fiken_balance;
                                            $status = $invoice->fiken_is_paid === 1 ? strtoupper(trans('site.learner.paid'))
                                                    : ($invoice->fiken_is_paid === 2 ? strtoupper('sendt til inkasso')
                                                    : strtoupper(trans('site.learner.unpaid')));
                                        ?>
                                        <tr>
                                            <td>
                                                {{$invoice->invoice_number}}
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
                                                @if($invoice->fiken_is_paid === 1)
                                                    <span class="label label-green">
                                                        {{ strtoupper(trans('site.learner.paid')) }}
                                                    </span>
                                                @elseif($invoice->fiken_is_paid === 2)
                                                    <span class="label label-warning text-uppercase label-rounded">
                                                        {{ strtoupper('sendt til inkasso')  }}
                                                    </span>
                                                @elseif($invoice->fiken_is_paid === 3)
                                                    <span class="label label-violet text-uppercase">
                                                        {{ strtoupper('Kreditert')  }}
                                                    </span>
                                                @else
                                                    <span class="label label-danger label-rounded">UBETALT</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end invoice card -->

                    <div class="global-card assignment-card mt-4">
                        <div class="card-header">
                            <h2>
                                {{ trans('site.learner.assignment') }}
                                <a href="{{ route('learner.assignment') }}" class="float-right view-all">
                                    {{ trans('site.learner.see-all') }}
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </h2>
                        </div>
                        <div class="card-body py-0">
                            <table class="table table-global">
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        <?php
                                        $manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first();
                                        ?>
                                        <tr>
                                            <td>{{ $assignment->title }}</td>
                                            <td width="200" class="text-center">
                                                @if( $manuscript )
                                                    @if (!$manuscript->locked)
                                                        <div>
                                                            <button type="button" class="btn btn-info editManuscriptBtn"
                                                                    data-toggle="modal" data-target="#editManuscriptModal"
                                                                    data-action="{{ route('learner.assignment.replace_manuscript',
                                                                    $manuscript->id) }}">
                                                                <i class="fa fa-pen"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger deleteManuscriptBtn"
                                                                    data-toggle="modal" data-target="#deleteManuscriptModal"
                                                                    data-action="{{ route('learner.assignment.delete_manuscript',
                                                                    $manuscript->id) }}">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                @else
                                                    @if($assignment->for_editor)
                                                        <button class="btn red-outline-btn submitEditorManuscriptBtn" 
                                                        data-toggle="modal"
                                                                data-target="#submitEditorManuscriptModal"
                                                                data-action="{{ route('learner.assignment.add_manuscript', 
                                                                $assignment->id) }}">
                                                            {{ trans('site.learner.upload-script') }}
                                                        </button>
                                                    @else
                                                        <button class="btn red-outline-btn submitManuscriptBtn" data-toggle="modal"
                                                                data-target="#submitManuscriptModal"
                                                                data-action="{{ route('learner.assignment.add_manuscript',
                                                                 $assignment->id) }}">
                                                            {{ trans('site.learner.upload-script') }}
                                                        </button>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end assignment card -->
                </div>
                <div class="col-lg-4 right-container">
                    <div class="global-card">
                        <div class="card-header">
                            <h2>
                                Logg inn til:
                            </h2>
                        </div>
                        <div class="card-body pb-0">
                            <a href="https://www.facebook.com/groups/272814062884940" class="btn site-btn-global w-100">
                                <b>Forum</b>
                                <i class="fa fa-key"></i>
                            </a>
                        </div>
                    </div> <!-- end login card -->

                    <div class="card global-card mt-4">
                        <div class="card-header">
                            <h2>
                                {{ trans('site.learner.calendar') }}
                                <a href="{{ route('learner.calendar') }}" class="float-right view-all">
                                    {{ trans('site.learner.see-all') }}
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </h2>
                        </div>
                        <div class="card-body pb-0">
                            <?php
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
                                            <div class="col-md-4 text-center d-flex">
                                                <div class="align-self-center w-100 date-container">
                                                    <span>
                                                        {{ ucfirst(\App\Http\FrontendHelpers::convertMonthLanguage(
                                                            $parseStart->format('n')
                                                            )) }}
                                                        </span>
                                                    <h1>{{ $parseStart->format('d') }}</h1>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <?php $calendarCounter = 1;?>
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
                    </div> <!-- end global-card calendar card -->

                    @if ($freeCourses->count())
                    <div class="card global-card mt-4">
                        <div class="card-header">
                            <h2>
                                {{ trans('site.learner.free-course-available') }}
                            </h2>
                        </div>
                        <div class="card-body">
                            @foreach($freeCourses as $free)
                                <div class="row free-course-row">
                                    <div class="col-md-5">
                                        <img data-src="https://www.forfatterskolen.no/{{$free->course_image}}" 
                                            alt="{{ $free->title }}">
                                    </div>
                                    <div class="col-md-7">
                                        <b>
                                            {{ $free->title }}
                                        </b>

                                        <?php
                                            $course_packages = $free->packages->pluck('id')->toArray();
                                            $courseTaken = App\CoursesTaken::where('user_id', Auth::user()->id)
                                        ->whereIn('package_id', $course_packages)->first();
                                        ?>
                                        @if (!$courseTaken)
                                            <form action="{{ route('front.course.getFreeCourse', $free->id) }}" method="POST"
                                                  onsubmit="disableSubmit(this)" class="form-inline">
                                                {{ csrf_field() }}
                                                <button class="btn btn-theme" type="submit">
                                                    {{ trans('site.learner.get-free-course') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div> <!-- end global-card -->
                    @endif
                </div> <!-- end col-md-4 -->
            </div> <!-- end row -->

            <div class="divider-center-text">
                {{ strtoupper(trans('site.learner.my-webinar')) }}
            </div>

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
                    ->groupBy('webinars.id')
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
                    ->groupBy('webinars.id')
                    ->get();
            ?>
            <div class="row webinar-wrapper">
                @foreach($webinarsRepriser as $webinar)
                    @php
                        $start_date = Carbon\Carbon::parse($webinar->start_date);
                        $now = Carbon\Carbon::now();

                        // Ensure days are rounded (Carbon 3 returns float)
                        $diff = (int) round($now->diffInDays($start_date, false));
                        $diffWithHours = (int) round($now->diffInHours($start_date, false));
                    @endphp

                    @if( $diffWithHours >= 0 )
                        @php
                            $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);
                            $coursesTakenEndDate = \Carbon\Carbon::parse($webinar->start_date)->subDays(1);
                            if ($coursesTaken) {
                                $coursesTakenEndDate = $coursesTaken->end_date ?: 
                                    \Carbon\Carbon::parse($coursesTaken->started_at)->addYear(1)->format('Y-m-d');
                            }
                        @endphp
                        <div class="col-lg-3 col-md-6 webinar-column">
                            <div class="global-card card-global">
                                <?php
                                    $img_web_link = '#';
                                    if (\App\Http\FrontendHelpers::checkIfWebinarRegistrant($webinar->id, Auth::user()->id)) {
                                    $img_web_link = \App\Http\FrontendHelpers::getWebinarJoinURL($webinar->id, Auth::user()->id);
                                    } else {
                                    $img_web_link = \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTakenEndDate))
                                    ? 'javascript:void(0)' :route('learner.webinar.register',
                                    [\App\Http\FrontendHelpers::extractWebinarKeyFromLink($webinar->link), $webinar->id]);
                                    }
                                ?>
                                <div class="card-header">
                                    @if($webinar->link)
                                        <a href="{{ $img_web_link }}">
                                            <img src="https://www.forfatterskolen.no/{{ $webinar->image }}" alt="">
                                        </a>
                                    @else
                                        <img src="https://www.forfatterskolen.no/{{ $webinar->image }}" alt="">
                                    @endif
                                </div>
                                <div class="card-body">
                                    <p>
                                        <i class="fa fa-calendar-alt"></i>
                                        {{ str_replace(['_date_', '_time_'],
                                        [\Carbon\Carbon::parse($webinar->start_date)->format('d.m.Y'),
                                        \Carbon\Carbon::parse($webinar->start_date)->format('H:i')],
                                        trans('site.front.our-course.show.start-date')) }}
                                    </p>

                                    <h3 class="text-center">
                                        {{ $webinar->title }}
                                    </h3>

                                    <p class="text-gray my-4 text-center">
                                        {{ $webinar->description }}
                                    </p>
                                </div> <!-- end card-body -->

                                <div class="button-container">
                                </div>

                                <div>
                                    @if (!Auth::user()->isDisabled)
                                        @if( \App\Http\FrontendHelpers::isWebinarAvailable($webinar) )
                                            <a class="btn w-100" href="{{ $webinar->link }}" target="_blank">
                                                {{ trans('site.learner.join-webinar') }}
                                            </a>
                                        @else

                                            @if ($webinar->id == 24 || $webinar->id == 25 || $webinar->id == 31)
                                                <a class="btn w-100" 
                                                href="{{ $coursesTaken && $coursesTaken->hasEnded
                                                            ? 'javascript:void(0)' : $webinar->link }}" target="_blank">
                                                    {{ trans('site.learner.replay') }}
                                                </a>
                                            @else
                                                @if($webinar->set_as_replay)
                                                    <a class="btn w-100" 
                                                    href="{{ $webinar->link }}" target="_blank">
                                                        {{ trans('site.learner.replay') }}
                                                    </a>
                                                @else
                                                    @if (\App\Http\FrontendHelpers::checkIfWebinarRegistrant($webinar->id, 
                                                    Auth::user()->id))
                                                        <a class="btn w-100"
                                                        href="{{ \App\Http\FrontendHelpers::getWebinarJoinURL($webinar->id, 
                                                        Auth::user()->id) }}">
                                                            {{ trans('site.learner.signed') }}
                                                        </a>
                                                    @else
                                                        {{-- check if have webinar link --}}
                                                        @if($webinar->link)
                                                            <a class="btn w-100 webinarRegister"
                                                            href="{{ \Carbon\Carbon::parse($webinar->start_date)
                                                            ->gt(\Carbon\Carbon::parse($coursesTakenEndDate))
                                                                ? 'javascript:void(0)' :route('learner.webinar.register',
                                                                [\App\Http\FrontendHelpers::extractWebinarKeyFromLink($webinar->link), 
                                                                $webinar->id]) }}">
                                                                {{ trans('site.learner.register') }}
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
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                @foreach($webinars as $webinar)
                    @php
                        $start_date = Carbon\Carbon::parse($webinar->start_date);
                        $now = Carbon\Carbon::now();

                        // Ensure days are rounded (Carbon 3 returns float)
                        $diff = (int) round($now->diffInDays($start_date, false));
                        $diffWithHours = (int) round($now->diffInHours($start_date, false));
                    @endphp

                    @if( $diffWithHours >= 0 )
                        @php
                            $coursesTaken = \App\CoursesTaken::find($webinar->courses_taken_id);
                            $coursesTakenEndDate = \Carbon\Carbon::parse($webinar->start_date)->subDays(1);
                            if ($coursesTaken) {
                                $coursesTakenEndDate = $coursesTaken->end_date ?: 
                                    \Carbon\Carbon::parse($coursesTaken->started_at)->addYear(1)->format('Y-m-d');
                            }
                        @endphp
                        <div class="col-lg-3 col-md-6 webinar-column">
                            <div class="global-card card-global">
                                <?php
                                    $img_web_link = '#';
                                    if (\App\Http\FrontendHelpers::checkIfWebinarRegistrant($webinar->id, Auth::user()->id)) {
                                    $img_web_link = \App\Http\FrontendHelpers::getWebinarJoinURL($webinar->id, Auth::user()->id);
                                    } else {
                                    $img_web_link = \Carbon\Carbon::parse($webinar->start_date)->gt(\Carbon\Carbon::parse($coursesTakenEndDate))
                                    ? 'javascript:void(0)' :route('learner.webinar.register',
                                    [\App\Http\FrontendHelpers::extractWebinarKeyFromLink($webinar->link), $webinar->id]);
                                    }
                                ?>
                                <div class="card-header">
                                    @if($webinar->link)
                                        <a href="{{ $img_web_link }}">
                                            <img src="https://www.forfatterskolen.no/{{ $webinar->image }}" alt="">
                                        </a>
                                    @else
                                        <img src="https://www.forfatterskolen.no/{{ $webinar->image }}" alt="">
                                    @endif
                                </div>
                                <div class="card-body">
                                    <p>
                                        <i class="fa fa-calendar-alt"></i>
                                        {{ str_replace(['_date_', '_time_'],
                                        [\Carbon\Carbon::parse($webinar->start_date)->format('d.m.Y'),
                                        \Carbon\Carbon::parse($webinar->start_date)->format('H:i')],
                                        trans('site.front.our-course.show.start-date')) }}
                                    </p>

                                    <h3 class="text-center">
                                        {{ $webinar->title }}
                                    </h3>

                                    <p class="text-gray my-4 text-center">
                                        {{ $webinar->description }}
                                    </p>
                                </div> <!-- end card-body -->

                                <div class="button-container">
                                </div>

                                <div>
                                    @if (!Auth::user()->isDisabled)
                                        @if( \App\Http\FrontendHelpers::isWebinarAvailable($webinar) )
                                            <a class="btn w-100" href="{{ $webinar->link }}" target="_blank">
                                                {{ trans('site.learner.join-webinar') }}
                                            </a>
                                        @else

                                            @if ($webinar->id == 24 || $webinar->id == 25 || $webinar->id == 31)
                                                <a class="btn w-100" 
                                                href="{{ $coursesTaken && $coursesTaken->hasEnded
                                                            ? 'javascript:void(0)' : $webinar->link }}" target="_blank">
                                                    {{ trans('site.learner.replay') }}
                                                </a>
                                            @else
                                                @if($webinar->set_as_replay)
                                                    <a class="btn w-100" 
                                                    href="{{ $webinar->link }}" target="_blank">
                                                        {{ trans('site.learner.replay') }}
                                                    </a>
                                                @else
                                                    @if (\App\Http\FrontendHelpers::checkIfWebinarRegistrant($webinar->id, 
                                                    Auth::user()->id))
                                                        <a class="btn w-100"
                                                        href="{{ \App\Http\FrontendHelpers::getWebinarJoinURL($webinar->id, 
                                                        Auth::user()->id) }}">
                                                            {{ trans('site.learner.signed') }}
                                                        </a>
                                                    @else
                                                        {{-- check if have webinar link --}}
                                                        @if($webinar->link)
                                                            <a class="btn w-100 webinarRegister"
                                                            href="{{ \Carbon\Carbon::parse($webinar->start_date)
                                                            ->gt(\Carbon\Carbon::parse($coursesTakenEndDate))
                                                                ? 'javascript:void(0)' :route('learner.webinar.register',
                                                                [\App\Http\FrontendHelpers::extractWebinarKeyFromLink($webinar->link), 
                                                                $webinar->id]) }}">
                                                                {{ trans('site.learner.register') }}
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
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div> <!-- end learner-dashboard-wrapper -->

    <div id="renewAllModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        {{ trans('site.learner.renew-all.title') }}
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.renew-all-courses') }}" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <p>
                            {{ trans('site.learner.renew-all.description') }}
                        </p>
                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">
                                {{ trans('site.front.yes') }}
                            </button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                {{ trans('site.front.no') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="submitEditorManuscriptModal" class="global-modal modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        {{ trans('site.learner.upload-script') }}
                    </h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data"
                          onsubmit="disableSubmit(this);">
                        {{ csrf_field() }}
                        <div class="form-group mb-2">
                            <label class="mb-0">* {{ trans('site.learner.manuscript.doc-format-text') }}</label>
                            <input type="file" class="form-control margin-top" required name="filename"
                                   accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        </div>

                        <div class="form-group mb-2">
                            <label class="mb-0">
                                {{ trans('site.front.genre') }}
                            </label>
                            <select class="form-control" name="type" required>
                                <option value="" disabled="disabled" selected>
                                    {{ trans('site.front.select-genre') }}
                                </option>
                                @foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
                                    <option value="{{ $type->id }}"> {{ $type->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            {{ trans('site.learner.manuscript.where-in-manuscript') }} <br>
                            @foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
                                <input type="radio" name="manu_type" value="{{ $manu['id'] }}" required>
                                <label class="mb-0">{{ $manu['option'] }}</label> <br>
                            @endforeach
                        </div>
                        <button type="submit" class="btn red-global-btn pull-right margin-top">
                            {{ trans('site.learner.upload') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="submitManuscriptModal" class="global-modal modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        {{ trans('site.learner.upload-script') }}
                    </h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this);">
                        {{ csrf_field() }}
                        <div class="form-group mb-2">
                            <label class="mb-0">*
                            {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}</label>
                            <input type="file" class="form-control margin-top" required name="filename" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                        </div>

                        <div class="form-group mb-2">
                            <label class="mb-0">
                                {{ trans('site.front.genre') }}
                            </label>
                            <select class="form-control" name="type" required>
                                <option value="" disabled="disabled" selected>
                                    {{ trans('site.front.select-genre') }}
                                </option>
                                @foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
                                    <option value="{{ $type->id }}"> {{ $type->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            {{ trans('site.learner.manuscript.where-in-manuscript') }} <br>
                            @foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
                                <input type="radio" name="manu_type" value="{{ $manu['id'] }}" required>
                                <label class="mb-0">{{ $manu['option'] }}</label> <br>
                            @endforeach
                        </div>
                        <button type="submit" class="btn red-global-btn pull-right margin-top">
                            {{ trans('site.learner.upload') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editManuscriptModal" class="global-modal modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        {{ trans('site.learner.manuscript.replace-manuscript') }}
                    </h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>
                                {{ trans('site.learner.manuscript-text') }}
                            </label>
                            <input type="file" class="form-control" required name="filename" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                            * {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
                        </div>

                        <button type="submit" class="btn red-global-btn pull-right margin-top">
                            {{ trans('site.front.submit') }}
                        </button>
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
                    <h3 class="modal-title">
                        {{ trans('site.learner.delete-manuscript.title') }}
                    </h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {{ trans('site.learner.delete-manuscript.question') }}
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-danger pull-right margin-top">
                            {{ trans('site.learner.delete') }}
                        </button>
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
                    {{ strtr(trans('site.learner.error-max-word-text'),
                    ['_word_count_' => Session::get('editorMaxWord')]) }}
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
                    {{ trans('site.learner.submit-success-text') }}
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->need_pass_update)
        <button class="passUpdateBtn hidden" data-toggle="modal" data-target="#passUpdateModal"></button>
        <div class="modal fade" role="dialog" id="passUpdateModal" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">
                            {{ trans('site.learner.update-password.title') }}
                        </h3>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p class="font-weight-bold">
                            {{ trans('site.learner.update-password.enter-new-password') }}
                        </p>

                        <form action="{{route('learner.password.update')}}" method="POST" onsubmit="disableSubmitOrigText(this)">
                            {{csrf_field()}}

                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa lock-icon"></i></span>
                                </div>
                                <input type="password" name="password" placeholder="{{ trans('site.front.form.password') }}"
                                       class="form-control no-border-left w-auto" required>
                            </div>
                            @if ($errors->has('password'))
                                <div class="alert alert-danger no-bottom-margin">
                                    {{ $errors->first('password') }}
                                </div>
                            @endif

                            <button type="submit" class="btn site-btn-global pull-right">
                                {{ trans('site.learner.update-password.update') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (Session::has('passUpdated'))
        <button class="passUpdatedBtn hidden" data-toggle="modal" data-target="#passUpdatedModal"></button>
        <div id="passUpdatedModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
                        <p>
                            {{ trans('site.learner.update-password.success-text') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>

        @if (Auth::user()->need_pass_update)
            //$("#passUpdateModal").modal('show');
            $(".passUpdateBtn").trigger('click');
        @endif

        @if (Session::has('passUpdated'))
            //$('#passUpdatedModal').modal('show');
            $(".passUpdatedBtn").trigger('click');
        @endif

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

        $(".webinar-auto-register-toggle").change(function(){
            let is_checked = $(this).prop('checked');
            let check_val = is_checked ? 1 : 0;
            $.ajax({
                type:'POST',
                url:'/account/webinar-auto-register-update',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { 'auto_renew' : check_val },
                success: function(data){
                }
            });
        });
    </script>
@stop
