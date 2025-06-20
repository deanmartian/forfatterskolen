<div class="group-learner-list-wrapper">
    <div class="title-container">
        <h1>
            {{ $group->title }}
        </h1>
        <span v-if="currentGroup.assignment">
            {{ $group->assignment->title }}
        </span>
    </div>

    <p class="date">
        {{ trans('site.learner.deadline') }}
        {{ $group->submission_date_time_text }}
    </p>

    <div class="row">
        @foreach( $groupLearnerList as $learner )
            @php
                $manuscript = $group->assignment->manuscripts->where('user_id', $learner->user_id)->first();
                $extension = explode('.', basename($manuscript->filename));
                $isCurrentUser = $learner->user->id == Auth::user()->id ? true : false;
            @endphp
            <div class="col-md-6">
                <div class="learner-details-wrapper {{ $isCurrentUser ? 'active' : '' }}">
                    <div class="header">
                        <h2 class="text-center">
                            {{  $isCurrentUser ? trans('site.learner.you-text') 
                                : trans('site.learner.learner-text') . " " . $learner->user_id }}
                        </h2>
                    </div>
                    <div class="body">
                        @if( $manuscript->filename )
                            <div class="file-container">
                                <i class="fa fa-file-alt"></i>
                                <b>{{ basename($manuscript->filename) }}</b>

                                @if ($isCurrentUser)
                                    <a href="{{ $manuscript->file_link_url }}" class="btn blue-outline-btn">
                                        Forhåndsvisning
                                    </a>
                                @else
                                    <a href="{{route('learner.assignment.manuscript.download', $manuscript->id)}}" 
                                        class="btn blue-outline-btn" download>
                                        {{ trans('site.learner.download-text') }}
                                        <i class="fa fa-download"></i>
                                    </a>
                                @endif
                            </div>

                            <p>
                                <b>
                                    {{ $manuscript->assignment_type }}
                                </b>
                                - {{ $manuscript->where_in_script }}
                            </p>
                        @else
                            <em>{{ trans('site.learner.no-manuscript-uploaded-text') }}</em>
                        @endif

                        @if ($isCurrentUser)
                            <button type="button" class="btn red-global-btn disabled">
                                {{ trans('site.learner.script-is-uploaded-text') }}
                            </button>
                        @else
                            @php
                                $feedback = App\AssignmentFeedback::where('assignment_group_learner_id',
                                    $learner->id)->where('user_id', Auth::user()->id)->first();
                            @endphp

                            @if( $feedback )
                                <button type="button" class="btn pink-global-btn disabled">
                                    @if( $feedback->is_active )
                                        {{ trans('site.learner.feeback-provided') }}
                                    @else
                                        {{ trans('site.learner.delivered-text') }}
                                    @endif
                                </button>

                                @if( !$feedback->is_active && !$feedback->locked)
                                    <div class="my-3">
                                        <button type="button" class="btn btn-danger pull-right w-50 
                                        rounded-0 font-16"
                                            data-toggle="modal" data-target="#deleteManuscriptModal"
                                            data-action="{{ route('learner.assignment.group.delete_feedback', $feedback->id) }}"
                                            onclick="deleteFeedbackFromGroup(this)">
                                            <i class="fa fa-trash text-white"></i>
                                        </button>
                                        <button type="button" class="btn btn-info pull-right w-50 rounded-0 font-16"
                                                data-toggle="modal" data-target="#editManuscriptModal"
                                                data-action="{{ route('learner.assignment.group.replace_feedback', $feedback->id) }}"
                                                onclick="editFeedbackFromGroup(this)">
                                            <i class="fa fa-edit text-white"></i>
                                        </button>

                                        <div class="clearfix"></div>
                                    </div>
                                @endif
                            @else
                                <button type="button" class="btn pink-global-btn"
												data-toggle="modal" data-target="#submitFeedbackModal"
												data-name="Learner {{ $learner->user->id }}"
												data-action="{{ route('learner.assignment.group.submit_feedback',
												['group_id' => $group->id, 'id' => $learner->id]) }}"
                                                onclick="submitFeedbackFromGroup(this)">
											{{ trans('site.learner.give-feedback') }}
                                </button>
                            @endif
                        @endif
                    </div> <!-- end .body -->
                </div>
            </div>
        @endforeach
    </div> <!-- end row -->
</div>
    @php
        $groupLearner = $group->learners->where('user_id', Auth::user()->id)->first();
        $feedbacks = App\AssignmentFeedback::where('assignment_group_learner_id', $groupLearner->id)->orderBy('created_at', 'desc')
            ->get();
    @endphp   

    @if( $feedbacks->count() > 0) {{-- && $assignmentManuscript->status --}}
        <div class="group-learner-list-wrapper group-learner-feedback-wrapper mt-5">
            <div class="title-container" style="justify-content: space-between">
                <h1>
                    {{ trans('site.learner.feedback-text') }}
                </h1>
                @if ($group->allow_feedback_download)
                    <a href="{{ route('learner.assignment.group.feedback.download-all', $group->id) }}"
                        class="btn red-global-btn w-25">
                        {{ trans('site.learner.download-all') }}
                    </a>
                @endif
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Fil</th>
                        <th>Av</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach( $feedbacks as $feedback )
                        @if( $feedback->is_active 
                            && (!$feedback->availability ||  date('Y-m-d') >= $feedback->availability)
                            && (($manuscript->editor_id === $feedback->user_id && $manuscript->status) 
                                || $manuscript->editor_id !== $feedback->user_id))
                                <tr>
                                    <td class="font-weight-bold">
                                        @php
											$files = explode(',',$feedback->filename);
											$filesDisplay = '';

											foreach ($files as $file) {
												$extension = explode('.', basename($file));
												if (end($extension) == 'pdf' || end($extension) == 'odt') {
													$filesDisplay .= '<a href="/js/ViewerJS/#../..'.trim($file).'" class="text-red">'
                                                        .basename($file).'</a>, ';
												} else {
													$filesDisplay .= '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').trim($file).'"
                                                    class="text-red">'
                                                            .basename($file).'</a>, ';
												}
											}

											echo trim($filesDisplay, ', ');
										@endphp
                                    </td>
                                    <td class="text-gray">
                                        @if( $feedback->is_admin ) 
                                            {{ trans('site.learner.admin-text') }} 
                                        @else
                                            {{ trans('site.learner.learner-text') . " " .$feedback->user_id }}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('learner.assignment.feedback.download', $feedback->id)}}"
                                            class="btn blue-outline-btn">
                                             {{ trans('site.learner.download-text') }}
                                             <i class="fa fa-download"></i>
                                         </a>
                                    </td>
                                </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif