<div class="col-sm-12 col-md-2 sub-menu">
<ul>
<li @if(Request::is('account/course*')) class="active" @endif>
<a href="{{route('learner.course')}}"><i class="fa fa-graduation-cap"></i>&nbsp;&nbsp;Mine kurs</a>
</li>
<li @if(Request::is('account/shop-manuscript*')) class="active" @endif>
<a href="{{route('learner.shop-manuscript')}}"><i class="fa fa-file-text"></i>&nbsp;&nbsp;Manuskripter</a>
</li>
<li @if(Request::is('account/workshop*')) class="active" @endif>
<a href="{{route('learner.workshop')}}"><i class="fa fa-briefcase"></i>&nbsp;&nbsp;Skriveverksted</a>
</li>
<li @if(Request::is('account/webinar*')) class="active" @endif>
<a href="{{route('learner.webinar')}}"><i class="fa fa-play-circle-o"></i>&nbsp;&nbsp;Mandagswebinarer</a>
</li>
<li @if(Request::is('account/course-webinar*')) class="active" @endif>
    <a href="{{route('learner.course-webinar')}}"><i class="fa fa-play-circle-o"></i>&nbsp;&nbsp;Kurswebinarer</a>
</li>
<li @if(Request::is('account/assignment*')) class="active" @endif>
<a href="{{route('learner.assignment')}}"><i class="fa fa-address-book-o"></i>&nbsp;&nbsp;Oppgaver</a>
</li>
<li @if(Request::is('account/calendar')) class="active" @endif>
<a href="{{route('learner.calendar')}}"><i class="fa fa-calendar"></i>&nbsp;&nbsp;Kalender</a>
</li>
<li @if(Request::is('account/invoice')) class="active" @endif>
<a href="{{route('learner.invoice')}}"><i class="fa fa-list-alt"></i>&nbsp;&nbsp;Fakturaer</a>
</li>
    <li @if(Request::is('account/upgrade')) class="active" @endif>
        <a href="{{route('learner.upgrade')}}"><i class="fa fa-upload"></i>&nbsp;&nbsp;Kurspakker - Oppgradering</a>
    </li>

    {{--@if (Auth::user()->coursesTaken->count())
        <li @if(Request::is('account/publishing')) class="active" @endif>
            <a href="{{route('learner.publishing')}}"><i class="fa fa-newspaper-o"></i>&nbsp;&nbsp;Forlagsliste (kommer)</a>
        </li>
    @endif--}}

    <li @if(Request::is('account/competition')) class="active" @endif>
        <a href="{{route('learner.competition')}}"><i class="fa fa-star"></i>&nbsp;&nbsp;Konkurranser</a>
    </li>

    {{--@if (Auth::user()->coursesTaken->count())
        <li @if(Request::is('account/writing-groups') || Request::is('account/writing-group/*')) class="active" @endif>
            <a href="{{route('learner.writing-groups')}}"><i class="fa fa-edit"></i>&nbsp;&nbsp;Skrivegrupper (kommer)</a>
        </li>
    @endif--}}

<li @if(Request::is('account/profile')) class="active" @endif>
<a href="{{route('learner.profile')}}"><i class="fa fa-user-o"></i>&nbsp;&nbsp;Profil \ Kursbevis</a>
</li>
</ul>
</div>