<div class="row learner-menu navbar-expand-md">
    <ul class="navbar-nav nav-fill">
        <li class="nav-item @if(Request::is('account/time-register')) active @endif">
            <a class="nav-link" href="{{route('learner.time-register')}}">
                <i class="fa fa-clock d-block" style="font-size: 20px;color: #000;"></i>
                Time Register
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/book-sale')) active @endif">
            <a class="nav-link" href="{{route('learner.book-sale')}}">
                <i class="fa fa-bar-chart d-block" style="font-size: 20px;color: #000;"></i>
                Sales
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/project')) active @endif">
            <a class="nav-link" href="{{route('learner.project')}}">
                <i class="fa fa-file d-block" style="font-size: 20px;color: #000;"></i>
                Projects
            </a>
        </li>
        <li class="nav-item @if(Request::is('account/profile')) active @endif">
            <a class="nav-link" href="{{route('learner.profile')}}">
                <i class="sprite-menu user d-block"></i>
                {!! trans('site.learner.nav.profile') !!}
            </a>
        </li>
    </ul>
</div>