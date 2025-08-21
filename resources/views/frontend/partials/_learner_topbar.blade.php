<div id="topbar">
    <div class="col-md-6">
        <h3>
            Velkommen til Forfatterskolens portal
        </h3>
    </div>
    <div class="col-md-6 text-right">
        @if (Route::currentRouteName() === 'learner.dashboard')
            <div class="auto-renew-wrapper">
                <label>
                    Automatisk registert for felleswebinarer
                </label>
                <input type="checkbox" data-toggle="toggle" data-on="{{ trans('site.front.yes') }}"
                        class="webinar-auto-register-toggle" data-off="{{ trans('site.front.no') }}"
                        data-size="mini"
                @if(Auth::user()->userAutoRegisterToCourseWebinar) {{ 'checked' }} @endif>
            </div>
        @endif

        @if (Route::currentRouteName() === 'learner.invoice')
            <a href="#" data-toggle="modal" data-target="#redeemModal" class="redeem-gift-link">
                <img src="{{ asset('images-new/icon/gift.png') }}" alt="Gaveikon">
            </a>
        @endif
        <div class="user-image-container d-inline-block">
            <!-- User image and dropdown menu -->
            <a href="{{ route('learner.profile') }}">
                <img src="{{Auth::user()->profile_image}}" alt="User Image" id="user-image">
            </a>
        </div>
        <button type="button" id="sidebarCollapse" class="btn btn-default d-xl-none">
            <span class="glyphicon glyphicon-menu-hamburger"></span>
        </button>
    </div>
</div>