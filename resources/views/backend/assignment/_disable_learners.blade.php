<table class="table dt-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Is Disabled</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($courseLearners as $courseLearner)
            <tr>
                <td>
                    {{ $courseLearner->user->full_name }}
                </td>
                <td>
                    <input type="checkbox" data-toggle="toggle" data-on="{{ trans('site.front.yes') }}"
                            class="disable-learner-toggle" data-off="{{ trans('site.front.no') }}"
                            data-id="{{ $courseLearner->user_id }}" data-size="small"
                            @if (in_array($courseLearner->user_id, $disabledLearners))
                                checked
                            @endif>
                </td>
                <td>
                    <button class="btn btn-primary btn-sm personalAssignmentBtn assignment-learner-{{ $courseLearner->user_id }} 
                        {{ in_array($courseLearner->user_id, $disabledLearners) ? '' : 'd-none'  }}"
                        data-toggle="modal" data-target="#personalAssignmentModal" type="button"
                        data-action="testing-{{ $courseLearner->id }}" onclick="personalAssignment({{ $courseLearner->user_id }})">
                        Assign as Personal Assignment
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>