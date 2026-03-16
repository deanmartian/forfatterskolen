{{-- Sidebar Navigation --}}
<aside class="ed-sidebar" id="edSidebar">
    <div class="ed-sidebar__logo">
        <div class="ed-sidebar__logo-title">Forfatterskolen</div>
        <div class="ed-sidebar__logo-sub">Redaktørportal</div>
    </div>

    <ul class="ed-sidebar__nav">
        @foreach (\App\Http\AdminHelpers::editorPageList() as $page)
            <li>
                <a href="{{ route($page['route']) }}"
                   class="ed-nav-item {{ Route::currentRouteName() === strtolower($page['route']) ? 'active' : '' }}">
                    <span class="ed-nav-item__icon">
                        @switch($page['request_name'])
                            @case('pending-assignments')
                                <i class="fa fa-tasks"></i>
                                @break
                            @case('upcoming-assignment')
                                <i class="fa fa-clock-o"></i>
                                @break
                            @case('assignment-archive')
                                <i class="fa fa-archive"></i>
                                @break
                            @case('editor-settings')
                                <i class="fa fa-cog"></i>
                                @break
                            @case('assigned-webinar')
                                <i class="fa fa-video-camera"></i>
                                @break
                            @case('editors-note')
                                <i class="fa fa-file-text-o"></i>
                                @break
                            @case('editors-coaching-time')
                                <i class="fa fa-users"></i>
                                @break
                            @case('editors-coaching-sessions')
                                <i class="fa fa-video-camera"></i>
                                @break
                            @case('editors-messages')
                                <i class="fa fa-envelope"></i>
                                @break
                            @case('editors-calendar')
                                <i class="fa fa-calendar"></i>
                                @break
                            @default
                                <i class="fa fa-circle-o"></i>
                        @endswitch
                    </span>
                    <span class="ed-nav-item__label">
                        @if($page['request_name'] === 'upcoming-assignment')
                            {{ trans('site.upcoming-assignment') }}
                        @elseif($page['request_name'] === 'editors-coaching-time')
                            Coaching timer
                        @elseif($page['request_name'] === 'editors-coaching-sessions')
                            Veiledningssamtaler
                        @elseif($page['request_name'] === 'editors-messages')
                            Meldinger
                        @elseif($page['request_name'] === 'editors-calendar')
                            {{ trans('site.learner.nav.calendar') }}
                        @else
                            {{ trans('site.admin-menu.'.$page['request_name']) }}
                        @endif
                    </span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="ed-sidebar__user">
        <div class="ed-sidebar__avatar">
            {{ strtoupper(substr(Auth::user()->fullName, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->fullName)[1] ?? '', 0, 1)) }}
        </div>
        <div style="flex:1; min-width:0;">
            <div class="ed-sidebar__user-name">{{ Auth::user()->fullName }}</div>
            <div class="ed-sidebar__user-role">Redaktør</div>
            <div class="ed-sidebar__user-actions" style="margin-top:6px; display:flex; gap:6px;">
                <button class="ed-btn ed-btn--ghost ed-btn--sm" data-toggle="modal" data-target="#changePasswordModal">
                    <i class="fa fa-key"></i> {{ trans('site.change-password') }}
                </button>
                <form method="POST" action="{{ route('auth.logout') }}" style="display:inline;">
                    {{ csrf_field() }}
                    <button type="submit" class="ed-btn ed-btn--ghost ed-btn--sm">
                        <i class="fa fa-sign-out"></i> {{ trans('site.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
