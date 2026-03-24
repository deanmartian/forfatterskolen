{{-- Admin Sidebar Navigation --}}
<aside class="ed-sidebar" id="edSidebar">
    <div class="ed-sidebar__logo">
        <div class="ed-sidebar__logo-title">Forfatterskolen</div>
        <div class="ed-sidebar__logo-sub">Adminportal</div>
    </div>

    <ul class="ed-sidebar__nav">
        <li>
            <a href="{{ route('backend.dashboard') }}"
               class="ed-nav-item {{ Request::is('/') ? 'active' : '' }}">
                <span class="ed-nav-item__icon"><i class="fa fa-th-large"></i></span>
                <span class="ed-nav-item__label">{{ trans('site.admin-menu.dashboard') }}</span>
            </a>
        </li>

        @if(\Auth::user()->role == 1)
            @php $pageList = \App\Http\AdminHelpers::pageList(); @endphp
            @foreach ($pageList as $page)
                @php
                    $hasAccess = true;
                    if (\Auth::user()->pageAccess->count()) {
                        $hasAccess = in_array($page['id'], \Auth::user()->pageAccess->pluck('page_id')->toArray());
                    }
                    if ($page['id'] === 12) {
                        $hasAccess = \Auth::user()->head_editor || \Auth::user()->with_head_editor_access;
                    }
                    if (!$hasAccess) continue;

                    $single = ['support', 'faq', 'admin', 'community', 'emails', 'crm'];
                    $request_name = in_array($page['request_name'], $single)
                        ? $page['request_name']
                        : ($page['request_name'] == 'publishing' ? 'support' : $page['request_name'].'s');
                @endphp
                <li>
                    <a href="{{ route($page['route']) }}"
                       class="ed-nav-item {{ Request::is(strtolower($page['request_name']).'*') ? 'active' : '' }}{{ '' }}">
                        <span class="ed-nav-item__icon">
                            @switch($page['request_name'])
                                @case('course')
                                    <i class="fa fa-graduation-cap"></i>
                                    @break
                                @case('free-course')
                                    <i class="fa fa-play-circle"></i>
                                    @break
                                @case('workshop')
                                    <i class="fa fa-users"></i>
                                    @break
                                @case('learner')
                                    <i class="fa fa-user"></i>
                                    @break
                                @case('assignment')
                                    <i class="fa fa-tasks"></i>
                                    @break
                                @case('project')
                                    <i class="fa fa-folder-open"></i>
                                    @break
                                @case('publishing')
                                    <i class="fa fa-book"></i>
                                    @break
                                @case('free-manuscript')
                                    <i class="fa fa-pencil"></i>
                                    @break
                                @case('other-service')
                                    <i class="fa fa-briefcase"></i>
                                    @break
                                @case('yearly_calendar')
                                    <i class="fa fa-calendar-o"></i>
                                    @break
                                @case('shop-manuscript')
                                    <i class="fa fa-edit"></i>
                                    @break
                                @case('faq')
                                    <i class="fa fa-question-circle"></i>
                                    @break
                                @case('admin')
                                    <i class="fa fa-shield"></i>
                                    @break
                                @case('head-editor')
                                    <i class="fa fa-star"></i>
                                    @break
                                @case('community')
                                    <i class="fa fa-comments"></i>
                                    @break
                                @case('emails')
                                    <i class="fa fa-envelope"></i>
                                    @break
                                @case('crm')
                                    <i class="fa fa-address-book"></i>
                                    @break
                                @case('anthology')
                                    <i class="fa fa-snowflake-o"></i>
                                    @break
                                @default
                                    <i class="fa fa-circle-o"></i>
                            @endswitch
                        </span>
                        <span class="ed-nav-item__label">{{ trans('site.admin-menu.'.$request_name) }}</span>
                    </a>
                </li>
            @endforeach
        @endif
        <li>
            <a href="{{ route('admin.assignment-review.index') }}"
               class="ed-nav-item {{ str_starts_with(Route::currentRouteName() ?? '', 'admin.assignment-review') ? 'active' : '' }}">
                <span class="ed-nav-item__icon"><i class="fa fa-magic"></i></span>
                <span class="ed-nav-item__label">
                    AI-tilbakemeldinger
                    @php
                        $pendingReviewCount = \App\AssignmentSubmission::whereIn('status', ['pending', 'ai_generated'])->count();
                    @endphp
                    @if($pendingReviewCount > 0)
                        <span class="badge" style="background: #e74c3c; color: #fff; border-radius: 10px; font-size: 10px; padding: 2px 6px; margin-left: 4px;">{{ $pendingReviewCount }}</span>
                    @endif
                </span>
            </a>
        </li>
        <li>
        <li>
            <a href="{{ route('admin.inbox.index') }}"
               class="ed-nav-item {{ str_starts_with(Route::currentRouteName() ?? '', 'admin.inbox') ? 'active' : '' }}">
                <span class="ed-nav-item__icon"><i class="fa fa-inbox"></i></span>
                <span class="ed-nav-item__label">
                    Inbox
                    @php
                        try { $openInboxCount = \App\Models\Inbox\InboxConversation::whereIn('status', ['open', 'pending'])->count(); } catch(\Exception $e) { $openInboxCount = 0; }
                    @endphp
                    @if($openInboxCount > 0)
                        <span class="badge" style="background: #e74c3c; color: #fff; border-radius: 10px; font-size: 10px; padding: 2px 6px; margin-left: 4px;">{{ $openInboxCount }}</span>
                    @endif
                </span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.ai.index') }}"
               class="ed-nav-item {{ str_starts_with(Route::currentRouteName() ?? '', 'admin.ai') ? 'active' : '' }}">
                <span class="ed-nav-item__icon"><i class="fa fa-robot"></i></span>
                <span class="ed-nav-item__label">AI-hjelper</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.ads.dashboard') }}"
               class="ed-nav-item {{ str_starts_with(Route::currentRouteName() ?? '', 'admin.ads') ? 'active' : '' }}">
                <span class="ed-nav-item__icon"><i class="fa fa-bullhorn"></i></span>
                <span class="ed-nav-item__label">
                    Ad OS
                    @php
                        try { $pendingAdApprovals = \App\Models\AdOs\AdApprovalRequest::where('status', 'pending')->count(); } catch(\Exception $e) { $pendingAdApprovals = 0; }
                    @endphp
                    @if($pendingAdApprovals > 0)
                        <span class="badge" style="background: #e74c3c; color: #fff; border-radius: 10px; font-size: 10px; padding: 2px 6px; margin-left: 4px;">{{ $pendingAdApprovals }}</span>
                    @endif
                </span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.messages.index') }}"
               class="ed-nav-item {{ str_starts_with(Route::currentRouteName() ?? '', 'admin.messages') ? 'active' : '' }}">
                <span class="ed-nav-item__icon"><i class="fa fa-envelope-o"></i></span>
                <span class="ed-nav-item__label">
                    Meldinger
                    @php
                        $unreadAdminMsgCount = \App\ConversationParticipant::where('user_id', Auth::id())
                            ->where(function($q) {
                                $q->whereNull('last_read_at')
                                  ->orWhereRaw('last_read_at < (SELECT MAX(created_at) FROM conversation_messages WHERE conversation_messages.conversation_id = conversation_participants.conversation_id)');
                            })->count();
                    @endphp
                    @if($unreadAdminMsgCount > 0)
                        <span class="badge" style="background: #e74c3c; color: #fff; border-radius: 10px; font-size: 10px; padding: 2px 6px; margin-left: 4px;">{{ $unreadAdminMsgCount }}</span>
                    @endif
                </span>
            </a>
        </li>
    </ul>

    <div class="ed-sidebar__user">
        <div class="ed-sidebar__avatar">
            {{ strtoupper(substr(Auth::user()->fullName, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->fullName)[1] ?? '', 0, 1)) }}
        </div>
        <div style="flex:1; min-width:0;">
            <div class="ed-sidebar__user-name">{{ Auth::user()->fullName }}</div>
            <div class="ed-sidebar__user-role">Admin</div>
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
