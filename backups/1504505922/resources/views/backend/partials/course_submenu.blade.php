<div class="col-sm-12 col-md-2 sub-menu">
<ul>

<li @if($section == 'overview') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}"><i class="fa fa-desktop"></i>&nbsp;&nbsp;Course Overview</a></li>

<li @if($section == 'lessons') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=lessons"><i class="fa fa-folder-open"></i>&nbsp;&nbsp;Lessons</a></li>

<li @if($section == 'manuscripts') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=manuscripts"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;Manuscripts</a></li>

<li @if($section == 'assignments') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=assignments"><i class="fa fa-list-alt"></i>&nbsp;&nbsp;Assignments</a></li>

<li @if($section == 'webinars') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=webinars"><i class="fa fa-play-circle-o"></i>&nbsp;&nbsp;Webinars</a></li>

<li @if($section == 'packages') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=packages"><i class="fa fa-list"></i>&nbsp;&nbsp;Packages</a></li>

<li @if($section == 'learners') class="active" @endif><a href="{{route('admin.course.show', $course->id)}}?section=learners"><i class="fa fa-users"></i>&nbsp;&nbsp;Course Learners</a></li>

</ul>
</div>