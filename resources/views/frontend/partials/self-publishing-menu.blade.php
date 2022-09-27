<div class="row learner-menu navbar-expand-md">
    <ul class="navbar-nav nav-fill">
        <li class="nav-item @if(Request::is('account/profile')) active @endif">
            <a class="nav-link" href="{{route('learner.profile')}}">
                <i class="sprite-menu user d-block"></i>
                {!! trans('site.learner.nav.profile') !!}
            </a>
        </li>
    </ul>
</div>