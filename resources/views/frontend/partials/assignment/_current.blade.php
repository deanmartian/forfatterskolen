<div class="row">
    <div class="col-lg-8">
        @foreach($assignments as $assignment)
            @if (is_null($assignment->parent) || $assignment->parent === 'users'
                || ($assignment->linkedAssignment 
                && !$assignment->linkedAssignment->manuscripts()
                ->where('user_id', Auth::user()->id)->first()))

                @php
                    $manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)
                        ->first();
                    $extension = $manuscript ? explode('.', basename($manuscript->filename)) : '';
                    $submission_date_formatted = $assignment->submission_date;
                    if (!\App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A', 
                    $assignment->submission_date)) {
                        $coursesTaken = Auth::user()->coursesTaken()->get()->toArray();
                        $allowed_packages = $assignment->allowed_package ?
                            json_decode($assignment->allowed_package) : [];

                        $courseStarted = '';
                        foreach ($coursesTaken as $course) {
                            if (in_array($course['package_id'], $allowed_packages)) {
                                $courseStarted =  $course['started_at'];
                            }
                        }
                        // original for submission date format is the one on else, 
                        // should be outside of the condition
                        if ($assignment['course_taken_end_date']) {
                            $courseStarted = $assignment['course_taken_end_date'];
                            $submission_date_formatted = \Carbon\Carbon::parse($courseStarted)
                            ->addDays(0);
                        } else {
                            $submission_date_formatted = \Carbon\Carbon::parse($courseStarted)
                            ->addDays($assignment->submission_date);
                        }
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
                    </div> <!-- end col-md-5 -->
                    <div class="col-md-7 col-sm-12">
                        <div class="row">
                            <div class="col-md-9">
                                <h2>
                                    {{ $assignment->title }}
                                </h2>
                            </div>
                            @if ($assignment->check_max_words)
                                <div class="col-md-3">
                                    <div class="max-word-container">
                                        {{ trans('site.max-words') }} {{ $assignment->max_words }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <p class="description-container">
                            {{ $assignment->description }}
                        </p>

                        @if( $manuscript )
                        <div class="manuscript-container">
                            <div class="col-md-7">
                                Manus:
                                @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                    <a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">
                                        {{ basename($manuscript->filename) }}
                                    </a>
                                @elseif( end($extension) == 'docx' || end($extension) == 'doc' )
                                    <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">
                                        {{ basename($manuscript->filename) }}
                                    </a>
                                @endif
                            </div>
                            @if (!$manuscript->locked)
                                <div class="col-md-5">
                                    <div class="button-container">
                                        <button type="button" class="btn btn-sm btn-info editManuscriptBtn"
                                            data-toggle="modal" data-target="#editManuscriptModal"
                                            data-action="{{ route('learner.assignment.replace_manuscript', $manuscript->id) }}">
                                        <i class="fa fa-pencil-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger deleteManuscriptBtn"
                                                data-toggle="modal" data-target="#deleteManuscriptModal"
                                                data-action="{{ route('learner.assignment.delete_manuscript', $manuscript->id) }}">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                        <a href="{{ end($extension) == 'pdf' || end($extension) == 'odt' 
                                        ? '/js/ViewerJS/#../..' . $manuscript->filename 
                                        : 'https://view.officeapps.live.com/op/embed.aspx?src=' . url('') . $manuscript->filename}}"
                                         class="btn">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @endif

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
                                    @if (!$manuscript && (is_null($assignment->parent) || $assignment->parent === 'users' ||
                                        ($assignment->linkedAssignment && !$assignment->linkedAssignment->manuscripts()
                                        ->where('user_id', Auth::user()->id)->first())))
                                        @if($assignment->for_editor)
                                            <button class="btn red-outline-btn submitEditorManuscriptBtn" 
                                            data-toggle="modal"
                                            data-target="#submitEditorManuscriptModal"
                                            data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
                                            data-show-group-question="{{ $assignment->show_join_group_question }}"
                                            data-send-letter-to-editor="{{ $assignment->send_letter_to_editor }}"
                                            @if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($submission_date_formatted))
                                            && $assignment->parent !== 'users') disabled @endif>
                                                {{ trans('site.learner.upload-script') }}
                                            </button>
                                        @else
                                            <button class="btn red-outline-btn submitManuscriptBtn" 
                                            data-toggle="modal"
                                            data-target="#submitManuscriptModal"
                                            data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
                                            data-show-group-question="{{ $assignment->show_join_group_question }}"
                                            data-send-letter-to-editor="{{ $assignment->send_letter_to_editor }}"
                                            @if(\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($submission_date_formatted))
                                            && $assignment->parent !== 'users') disabled @endif>
                                                {{ trans('site.learner.upload-script') }}
                                            </button>
                                        @endif
                                    @else
                                        @if($assignment->parent === 'users')
                                            <label class="badge badge-info w-100"
                                                style="font-size: 100%">
                                                {{ trans('site.started') }}
                                            </label>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div> <!-- endcol-md-7 -->
                </div> <!-- end assignment-container -->
            @endif
        @endforeach
    </div> <!-- end col-lg-8 -->
    <div class="col-lg-4">
        <div class="upcoming-assignment-container">
            <h2>
                Kommende Oppgaver
            </h2>

            @foreach($upcomingAssignments as $assignment)
                <div class="upcoming-assignment-list">
                    <h3>
                        {{ $assignment->title }}
                    </h3>
                    <h4>
                        {{ trans('site.available-date') }}: 
                        {{ \App\Http\FrontendHelpers::formatDate($assignment->available_date) }}
                    </h4>
                    <span class="font-barlow-regular">{{ trans('site.deadline') }}:</span>
                    <span>{{ \App\Http\FrontendHelpers::formatDateTimeNor($assignment->submission_date) }}</span>

                    @if($assignment->course)
                        <p>
                            {{ trans('site.front.course-text') }}: 
                            {{ $assignment->course->title }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div> <!-- end col-md-4 -->
</div>