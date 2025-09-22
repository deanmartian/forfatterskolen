<section class="waiting-for-feedback-wrapper">
    <div class="col-lg-8">
        @foreach($waitingForResponse as $assignment)
        @php
            $manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first();
            $extension = $manuscript ? explode('.', basename($manuscript->filename)) : '';
            $submission_date_formatted = $assignment->submission_date;
            if (!\App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)) {
                $coursesTaken = Auth::user()->coursesTaken()->get()->toArray();
                $allowed_packages = $assignment->allowed_package ?
                    json_decode($assignment->allowed_package) : [];

                $courseStarted = '';
                foreach ($coursesTaken as $course) {
                    if (in_array($course['package_id'], $allowed_packages)) {
                        $courseStarted =  $course['started_at'];
                    }
                }

                $submission_date_formatted = \Carbon\Carbon::parse($courseStarted)
                    ->addDays((int) $assignment->submission_date);
            }
        @endphp
            <div class="assignment-container">
                <div class="col-md-5 col-sm-12">
                    <div class="date-container">
                        <p>{{ trans('site.learner.deadline') }}</p>
                        <div class="row">
                            <div class="col-md-3">
                                <i class="fa fa-calendar-check"></i>
                            </div>
                            <div class="col-md-9 pl-0">
                                <h3>
                                    {{ \App\Http\FrontendHelpers::formatDateTimeNor(
                                        $submission_date_formatted) }}
                                </h3>
                            </div>
                        </div>
                    </div> <!-- end date-container -->
                </div> <!-- end col-md col-sm -->
                <div class="col-md-7 col-sm-12">
                    <h2>
                        <img src="{{ asset('images-new/icon/assignment-file.png') }}" alt="Oppgavefil ikon">{{ $assignment->title }}
                    </h2>

                    <p class="description-container">
                        {{ $assignment->description }}
                    </p>

                    <div class="bottom-container">
                        <div class="row">
                            <div class="col-md-6">
                                @if($assignment->course)
                                    <p>
                                        {{ trans('site.front.course-text') }}: 
                                        {{ $assignment->course->title }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                    <a href="/js/ViewerJS/#../..{{ $manuscript->filename }}" class="btn red-outline-btn">
                                        {{ basename($manuscript->filename) }} <i class="far fa-eye"></i>
                                    </a>
                                @elseif( end($extension) == 'docx' || end($extension) == 'doc' )
                                    <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}"
                                        class="btn red-outline-btn">
                                        {{ basename($manuscript->filename) }} <i class="far fa-eye"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div> <!-- end col-md-7 col-sm-12 -->
            </div> <!-- end assignment-container -->
        @endforeach
    </div>
    <div class="col-lg-4">
        <div class="finish-assignment-wrapper">
            <h2>
                {{ trans('site.finished') }}
            </h2>

            @foreach($expiredAssignments as $assignment)
                @php
                    $manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first();
                    $extension = $manuscript ? explode('.', basename($manuscript->filename)) : '';
                @endphp
                @if ($manuscript)
                    @php
                        /* $assignmentFeedback = \App\AssignmentFeedbackNoGroup::where('assignment_manuscript_id', $manuscript->id)
                        ->first();
                        $assignmentGroups = AssignmentGroup::where('assignment_id', $assignment->id)->pluck('id')->toArray();
                        $userAssignmentGroupLearner = AssignmentGroupLearner::where('user_id', Auth::user()->id)
                            ->whereIn('assignment_group_id', $assignmentGroups)->first(); */
                    @endphp
                    <div class="timeline-wrapper">
                        <h3>
                            {{ $assignment->title }}
                        </h3>
                        <div class="timeline">
                            <p>
                                {{ $assignment->description }}
                            </p>

                            @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                <a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">
                                    {{ basename($manuscript->filename) }}
                                </a>
                            @elseif( end($extension) == 'docx' || end($extension) == 'doc' )
                                <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">
                                    {{ basename($manuscript->filename) }}
                                </a>
                            @endif
                            {{-- @if ($assignmentFeedback)
                                <div class="feedback-container">
                                    <p class="mb-0">
                                        Tilbakemelding gitt: {{ FrontendHelpers::formatDate($assignmentFeedback->created_at) }}
                                    </p>
                                    <a href="#" class="btn btn-sm btn-primary">
                                        {{ trans('site.learner.download-feedback') }}
                                    </a>
                                </div>
                            @endif --}}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>