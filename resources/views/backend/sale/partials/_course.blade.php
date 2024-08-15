<ul class="nav nav-tabs sub-nav margin-top">
    <li @if( $tab != 'archive' ) class="active" @endif>
        <a href="?p={{ $page }}&tab=new">{{ trans('site.new') }}</a>
    </li>
    <li @if( $tab == 'archive' ) class="active" @endif>
        <a href="?p={{ $page }}&tab=archive">{{ trans('site.archive') }}</a>
    </li>
</ul>

@if( $tab != 'archive' )
    <div class="table-users table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>{{ trans_choice('site.packages', 1) }}</th>
                <th>{{ trans_choice('site.learners', 1) }}</th>
                <th>{{ trans('site.date-sold') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($newCourses as $newCourseTaken)
                <tr>
                    <td>
                        <a href="{{ route('admin.course.show',
                        $newCourseTaken->package->course_id) }}?section=packages">
                            {{ $newCourseTaken->package->course->title . ' - ' .
                            $newCourseTaken->package->variation }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('admin.learner.show', $newCourseTaken->user->id) }}">
                            {{ $newCourseTaken->user->full_name }}
                        </a>
                    </td>
                    <td>
                        {{ $newCourseTaken->created_at }}
                    </td>
                    <td>
                        <?php
                        // select the template for course, if not template for course use the general.
                            $emailTemplate = null;
                            $tempData = null;
                            
                            $emailTemplate = null;
                            $tempData = null;
                            
                            if ($newCourseTaken->package->course->type === 'Group') {
                                
                                $tempData = \App\EmailTemplate::where('course_id', $newCourseTaken->package->course->id)->where('course_type', 'GROUP')->first();
                                $emailTemplate = $tempData ? $tempData : $groupCourseEmail;

                            }else{ //Single
                                $tempData = \App\EmailTemplate::where('course_id', $newCourseTaken->package->course->id)->where('course_type', 'SINGLE')->first();
                                $emailTemplate = $tempData ? $tempData : $singleCourseEmail;
                            }

                        ?>
                        <button class="btn btn-success btn-xs sendEmailBtn"
                            data-toggle="modal"
                            data-target="#sendEmailModal"
                            data-email-template="{{ json_encode($emailTemplate) }}"
                            data-action="{{ route('admin.sales.send-email',
                            [$newCourseTaken->id, 'courses-taken-welcome']) }}">
                            {{ trans('site.send-email') }}
                        </button>
                        <button class="btn btn-warning btn-xs moveToArchiveBtn"
                            data-toggle="modal"
                            data-target="#moveToArchiveModal"
                            data-action="{{ route('admin.sales.move-to-archive', [$newCourseTaken->id]) }}">
                            {{ trans('site.move-to-archive') }}
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div> <!-- end new course -->
    <div class="pull-right">{{$newCourses->appends(request()->except('page'))}}</div>
@else
    <div class="table-users table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>{{ trans_choice('site.packages', 1) }}</th>
                <th>{{ trans_choice('site.learners', 1) }}</th>
                <th>{{ trans('site.date-sold') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($archiveCourses as $archiveCourse)
                <tr>
                    <td>
                        <a href="{{ route('admin.course.show',
                        $archiveCourse->package->course_id) }}?section=packages">
                            {{ $archiveCourse->package->course->title . ' - ' .
                            $archiveCourse->package->variation }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('admin.learner.show', $archiveCourse->user->id) }}">
                            {{ $archiveCourse->user->full_name }}
                        </a>
                    </td>
                    <td>
                        {{ $archiveCourse->created_at }}
                    </td>
                    <td>
                        <button class="btn btn-primary btn-xs viewEmailBtn"
                                data-toggle="modal"
                                data-target="#viewEmailModal"
                                data-record="{{ json_encode($archiveCourse) }}"
                                data-type="courses-taken">
                            View Email
                        </button>

                        <button class="btn btn-success btn-xs sendEmailBtn"
                                data-toggle="modal"
                                data-target="#sendEmailModal"
                                data-email-template="{{ json_encode($followUpEmailCourseTaken) }}"
                                data-action="{{ route('admin.sales.send-email',
                        [$archiveCourse->id, 'courses-taken-follow-up']) }}">
                            Send following up email
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div> <!-- end new course -->
    <div class="pull-right">{{$archiveCourses->appends(request()->except('page'))}}</div>
@endif