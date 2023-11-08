<div class="col-sm-12 col-md-2 sub-menu">
<ul>

<li @if($section == 'overview') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}"><i class="fa fa-desktop"></i>&nbsp;&nbsp;{{ trans('site.course-overview') }}</a></li>

<li @if($section == 'lessons') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=lessons"><i class="fa fa-folder-open"></i>&nbsp;&nbsp;{{ trans_choice('site.lessons', 2) }}</a></li>

<li @if($section == 'manuscripts') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=manuscripts"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;{{ trans_choice('site.manuscripts', 2) }}</a></li>

<li @if($section == 'assignments') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=assignments"><i class="fa fa-list-alt"></i>&nbsp;&nbsp;{{ trans_choice('site.assignments', 2) }}</a></li>

<li @if($section == 'webinars') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=webinars"><i class="fa fa-play-circle-o"></i>&nbsp;&nbsp;{{ trans('site.webinars') }}</a></li>

<li @if($section == 'packages') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=packages"><i class="fa fa-list"></i>&nbsp;&nbsp;{{ trans_choice('site.packages', 2) }}</a></li>

<li @if($section == 'learners') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=learners"><i class="fa fa-users"></i>&nbsp;&nbsp;{{ trans('site.course-learners') }}</a></li>

@if ($course->id !== 17)
    <li @if($section == 'email-out') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=email-out"><i class="fa fa-envelope"></i>&nbsp;&nbsp;{{ trans('site.email-out') }}</a></li>
@endif

@if ($course->pay_later_with_application)
<li @if($section == 'applications') class="active" @endif>
    <a href="{{route('admin.course.show', $course->id)}}?section=applications">
        <i class="fa fa-file"></i>&nbsp;&nbsp;Applications
    </a>
</li>
@endif

@if ($course->rewardPackages->count())
    <li @if($section == 'reward-coupons') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=reward-coupons"><i class="fa fa-star"></i>&nbsp;&nbsp;Reward Coupons</a></li>
@endif

    <li @if($section == 'surveys') class="active" @endif>
        <a href="{{route('admin.course.show', $course->id)}}?section=surveys">
            <i class="fa fa-question"></i>&nbsp;&nbsp;{{ trans('site.surveys') }}
        </a>
    </li>

    <li @if($section == 'certificate') class="active" @endif>
        <a href="{{route('admin.course.show', $course->id)}}?section=certificate">
            <i class="fa fa-certificate"></i>&nbsp;&nbsp;Certificate
        </a>
    </li>
</ul>
</div>