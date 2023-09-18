<table class="table dt-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Is Disabled</th>
            <th>Action</th>
            <th>Personal Assignment</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($courseLearners as $courseLearner)
            @php
                $personalAssignment = $assignment->getLinkedPersonalAssignment($courseLearner->user_id)
            @endphp
            <tr>
                <td>
                    {{ $courseLearner->user->full_name }}
                </td>
                <td>
                    @if (!$personalAssignment)
                        <input type="checkbox" data-toggle="toggle" data-on="{{ trans('site.front.yes') }}"
                                class="disable-learner-toggle" data-off="{{ trans('site.front.no') }}"
                                data-id="{{ $courseLearner->user_id }}" data-size="small"
                                @if (in_array($courseLearner->user_id, $disabledLearners))
                                    checked
                                @endif>
                    @else
                        {{ trans('site.front.yes') }}
                    @endif
                </td>
                <td>
                    @if (!$personalAssignment)
                        <button class="btn btn-primary btn-sm personalAssignmentBtn assignment-learner-{{ $courseLearner->user_id }} 
                            {{ in_array($courseLearner->user_id, $disabledLearners) ? '' : 'd-none'  }}"
                            data-toggle="modal" data-target="#personalAssignmentModal" type="button"
                            data-action="testing-{{ $courseLearner->id }}" onclick="personalAssignment({{ $courseLearner->user_id }})">
                            Assign as Personal Assignment
                        </button>
                    @endif
                </td>
                <td>
                    @if ($personalAssignment)
                        <a href="{{ route('admin.learner.assignment',
                        [$personalAssignment->parent_id, $personalAssignment->id]) }}">
                            {{ $personalAssignment->title }}
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>