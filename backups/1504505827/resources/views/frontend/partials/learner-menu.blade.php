<div class="col-sm-12 col-md-2 sub-menu">
<ul>
<li @if(Request::is('account/course*')) class="active" @endif>
<a href="{{route('learner.course')}}"><i class="fa fa-graduation-cap"></i>&nbsp;&nbsp;Mine kurs</a>
</li>
<li @if(Request::is('account/shop-manuscript*')) class="active" @endif>
<a href="{{route('learner.shop-manuscript')}}"><i class="fa fa-file-text"></i>&nbsp;&nbsp;Manuskripter</a>
</li>
<li @if(Request::is('account/workshop*')) class="active" @endif>
<a href="{{route('learner.workshop')}}"><i class="fa fa-briefcase"></i>&nbsp;&nbsp;Workshops</a>
</li>
<li @if(Request::is('account/webinar*')) class="active" @endif>
<a href="{{route('learner.webinar')}}"><i class="fa fa-play-circle-o"></i>&nbsp;&nbsp;Webinars</a>
</li>
<li @if(Request::is('account/calendar')) class="active" @endif>
<a href="{{route('learner.calendar')}}"><i class="fa fa-calendar"></i>&nbsp;&nbsp;Kalender</a>
</li>
<li @if(Request::is('account/profile')) class="active" @endif>
<a href="{{route('learner.profile')}}"><i class="fa fa-user-o"></i>&nbsp;&nbsp;Profil</a>
</li>
<li @if(Request::is('account/invoice')) class="active" @endif>
<a href="{{route('learner.invoice')}}"><i class="fa fa-list-alt"></i>&nbsp;&nbsp;Fakturaer</a>
</li>
<li @if(Request::is('account/assignment*')) class="active" @endif>
<a href="{{route('learner.assignment')}}"><i class="fa fa-address-book-o"></i>&nbsp;&nbsp;Assignments</a>
</li>
</ul>
</div>