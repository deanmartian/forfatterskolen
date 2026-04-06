{{-- Sidebar Navigation --}}
<aside class="ed-sidebar" id="edSidebar">
    <div class="ed-sidebar__logo">
        <img src="{{ asset('images/favicon.png') }}" alt="Forfatterskolen" style="width:28px;height:28px;margin-right:8px;vertical-align:middle;">
        <div style="display:inline-block;vertical-align:middle;">
            <div class="ed-sidebar__logo-title">Forfatterskolen</div>
            <div class="ed-sidebar__logo-sub">Redaktørportal</div>
        </div>
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
                            @case('available-manuscripts')
                                <i class="fa fa-book"></i>
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
                        @elseif($page['request_name'] === 'available-manuscripts')
                            Ledige manus
                            @php $availableCount = \App\ShopManuscriptsTaken::where('available_for_editors', 1)->whereNull('feedback_user_id')->count(); @endphp
                            @if($availableCount > 0)
                                <span class="ed-nav-item__badge">{{ $availableCount }}</span>
                            @endif
                        @elseif($page['request_name'] === 'editors-calendar')
                            {{ trans('site.learner.nav.calendar') }}
                        @else
                            {{ trans('site.admin-menu.'.$page['request_name']) }}
                        @endif
                    </span>
                </a>
            </li>
        @endforeach

        {{-- Kurs-tilgang for redaktører --}}
        <li>
            <a href="{{ config('app.url') }}/account/editor-courses" class="ed-nav-item" target="_blank">
                <span class="ed-nav-item__icon"><i class="fa fa-graduation-cap"></i></span>
                <span class="ed-nav-item__label">Mine kurs</span>
            </a>
        </li>
    </ul>

    <div id="pl-sidebar-table" style="padding:0 12px;margin-bottom:12px;display:none;">
        <div style="font-size:11px;font-weight:700;color:#999;letter-spacing:0.5px;margin-bottom:6px;">⚽ PREMIER LEAGUE</div>
        <div id="pl-sidebar-rows" style="font-size:11px;line-height:1.8;"></div>
    </div>
    <script>
    (function(){
        fetch('https://site.api.espn.com/apis/v2/sports/soccer/eng.1/standings')
        .then(r=>r.json()).then(data=>{
            var entries=data.children[0].standings.entries;
            entries.sort((a,b)=>{
                var ar=a.stats.find(s=>s.name==='rank'),br=b.stats.find(s=>s.name==='rank');
                return (ar?ar.value:99)-(br?br.value:99);
            });
            var html='';
            var top=Math.min(6,entries.length);
            for(var i=0;i<top;i++){
                var e=entries[i],name=e.team.abbreviation,pts=e.stats.find(s=>s.name==='points');
                var bold=(name==='ARS')?'font-weight:700;color:#EF0107;':'color:#ccc;';
                html+='<div style="display:flex;justify-content:space-between;'+bold+'">'
                    +'<span>'+(i+1)+'. '+name+'</span>'
                    +'<span>'+(pts?pts.value:'')+'p</span></div>';
            }
            document.getElementById('pl-sidebar-rows').innerHTML=html;
            document.getElementById('pl-sidebar-table').style.display='block';
        }).catch(function(){});
    })();
    </script>

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
