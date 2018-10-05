<div class="col-sm-12 col-md-2 sub-menu">
<ul>

<li @if($section == 'overview') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}"><i class="fa fa-desktop"></i>&nbsp;&nbsp;{{ trans('site.course-overview') }}</a></li>

<li @if($section == 'lessons') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=lessons"><i class="fa fa-folder-open"></i>&nbsp;&nbsp;{{ trans_choice('site.lessons', 2) }}</a></li>

<li @if($section == 'manuscripts') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=manuscripts"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;{{ trans_choice('site.manuscripts', 2) }}</a></li>

<li @if($section == 'assignments') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=assignments"><i class="fa fa-list-alt"></i>&nbsp;&nbsp;{{ trans_choice('site.assignments', 2) }}</a></li>

<li @if($section == 'webinars') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=webinars"><i class="fa fa-play-circle-o"></i>&nbsp;&nbsp;{{ trans('site.webinars') }}</a></li>

<li @if($section == 'packages') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=packages"><i class="fa fa-list"></i>&nbsp;&nbsp;{{ trans('site.packages') }}</a></li>

<li @if($section == 'learners') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=learners"><i class="fa fa-users"></i>&nbsp;&nbsp;{{ trans('site.course-learners') }}</a></li>

</ul>
</div>