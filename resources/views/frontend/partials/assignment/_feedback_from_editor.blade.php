@php
    $noGroupWithFeedback = \App\AssignmentFeedbackNoGroup::where('learner_id', Auth::user()->id)
                            ->orderBy('created_at', 'desc')
                            ->get();
@endphp
<div class="feedback-from-editor-container">
    <h2>
        {{ trans('site.learner.feedback-from-editor') }}
    </h2>

    <div style="overflow: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>
                        {{ trans_choice('site.assignments', 1) }}
                    </th>
                    <th width="400">
                        Kurs/oppgavetekst
                    </th>
                    <th>Av</th>
                    <th>
                        Original manus
                    </th>
                    <th>
                        {{ trans('site.date-out') }}
                    </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if($noGroupWithFeedback->count() > 0)
                    @foreach( $noGroupWithFeedback as $feedback )
                        @if( $feedback->is_active 
                        && (!$feedback->availability ||  date('Y-m-d') >= $feedback->availability)
                        && $feedback->manuscript->status)
                            @php
                                $files = explode(',',$feedback->filename);
                                $title = $feedback->manuscript->assignment->course 
                                        ? $feedback->manuscript->assignment->course->title
                                        : $feedback->manuscript->assignment->title;
                                $titleLabel = $feedback->manuscript->assignment->course 
                                        ? trans('site.front.course-text')
                                        : trans('site.learner.assignment');

                                $filesDisplay = $feedback->manuscript->assignment->title 
                                    .' <br/> ' . $titleLabel . ': '. $title;
                            @endphp
                            <tr>
                                <td>
                                    {{ $feedback->manuscript->assignment->title }}
                                </td>
                                <td>
                                    {{ $titleLabel . ': '. $title }}
                                </td>
                                <td style="color: #B3B3B3">
                                    @if( $feedback->is_admin ) 
                                        Forfatterskolens redaktør
                                    @endif
                                </td>
                                <td>
                                    {!! $feedback->manuscript->file_link_with_download !!}
                                </td>
                                <td class="td-date">
                                    {{ \App\Http\FrontendHelpers::formatDate($feedback->availability) }}
                                </td>
                                <td>
                                    <a href="{{route('learner.assignment.no-group-feedback.download', $feedback->id)}}"
                                        class="w-100 btn download-feedback">
                                        {{ trans('site.learner.download-feedback') }}
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>