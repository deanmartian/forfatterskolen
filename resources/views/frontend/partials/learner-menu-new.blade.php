<div class="row learner-menu navbar-expand-md">
    <ul class="navbar-nav nav-fill">
        <li class="nav-item @if(!Request::is('account/course-webinar') && Request::is('account/course*')) active @endif">
            <a class="nav-link" href="{{route('learner.course')}}">
                <i class="sprite-menu student-cap d-block"></i>
                Mine Kurs
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/shop-manuscript*')) active @endif">
            <a class="nav-link" href="{{route('learner.shop-manuscript')}}">
                <i class="sprite-menu file d-block"></i>
                Manuskripter
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/workshop*')) active @endif">
            <a class="nav-link" href="{{route('learner.workshop')}}">
                <i class="sprite-menu briefcase d-block"></i>
                Skriveverksted
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/webinar*')) active @endif">
            <a class="nav-link" href="{{route('learner.webinar')}}">
                <i class="sprite-menu play-button d-block"></i>
                Mandagswebinarer
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/course-webinar*')) active @endif">
            <a class="nav-link" href="{{route('learner.course-webinar')}}">
                <i class="sprite-menu play-button d-block"></i>
                Kurswebinarer
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/assignment*')) active @endif">
            <a class="nav-link" href="{{route('learner.assignment')}}">
                <i class="sprite-menu agenda d-block"></i>
                Oppgaver
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/calendar')) active @endif">
            <a class="nav-link" href="{{route('learner.calendar')}}">
                <i class="sprite-menu calendar d-block"></i>
                Kalender
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/invoice*')) active @endif">
            <a class="nav-link" href="{{route('learner.invoice')}}">
                <i class="sprite-menu list-on-window d-block"></i>
                Fakturaer
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/upgrade*')) active @endif">
            <a class="nav-link" href="{{route('learner.upgrade')}}">
                <i class="sprite-menu internet d-block"></i>
                Kurspakker - Oppgradering
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/competition')) active @endif">
            <a class="nav-link" href="{{route('learner.competition')}}">
                <i class="sprite-menu star d-block"></i>
                Konkurranser
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/profile')) active @endif">
            <a class="nav-link" href="{{route('learner.profile')}}">
                <i class="sprite-menu user d-block"></i>
                Profil \ Kursbevis
            </a>
        </li>
    </ul>
</div>