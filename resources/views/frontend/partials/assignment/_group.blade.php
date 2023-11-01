<div class="group-assignment-wrapper">
    <div class="col-lg-8">
        <div class="loading-wrapper d-none" id="loading-wrapper">
            <i class="fa fa-pulse fa-loading fa-spinner"></i>
        </div>
        <div id="group-details-container"></div>
    </div>
    <div class="col-lg-4">
        <div class="group-list-wrapper">
            <h2>
                {{ trans('site.learner.groups') }}
            </h2>

            
            @foreach( $assignmentGroupLearners as $groupLearner )
                <div class="group-container" id="group-{{ $groupLearner->group->id }}"
                     onclick="showGroupDetails({{ $groupLearner->group->id }})">
                    <h3>
                        {{ $groupLearner->group->title }}
                    </h3>
                    <b>
                        {{ trans('site.front.course-text') }}:
                        {{ $groupLearner->group->assignment->course->title }}
                    </b>
                    <p>
                        {{ trans('site.learner.assignment-single') }}:
                        {{ $groupLearner->group->assignment->title }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</div>