<div id="topbar">
    <div class="topbar-left">
        @if (Route::currentRouteName() === 'learner.dashboard')
            <div class="auto-renew-wrapper">
                <label>
                    Automatisk registrert for mentormøter
                </label>
                <input type="checkbox" data-bs-toggle="toggle" data-on="{{ trans('site.front.yes') }}"
                        class="webinar-auto-register-toggle" data-off="{{ trans('site.front.no') }}"
                        data-size="mini"
                @if(Auth::user()->userAutoRegisterToCourseWebinar) {{ 'checked' }} @endif>
            </div>
        @endif

        @if (Route::currentRouteName() === 'learner.invoice')
            <a href="#" data-bs-toggle="modal" data-bs-target="#redeemModal" class="redeem-gift-link">
                <img src="{{ asset('images-new/icon/gift.png') }}" alt="Gaveikon">
            </a>
        @endif
    </div>
    <div class="topbar-right">
        <button type="button" id="sidebarCollapse" class="btn" style="
            align-items: center; justify-content: center;
            width: 44px; height: 44px; border-radius: 12px; border: 2px solid rgba(255,255,255,0.3);
            background: #862736; padding: 0; cursor: pointer;
            box-shadow: 0 4px 12px rgba(134, 39, 54, 0.35);
        ">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round">
                <line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/>
            </svg>
        </button>
    </div>
</div>