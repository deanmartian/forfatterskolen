<div id="sidebar">
    <a class="navbar-brand w-100" href="#">
        <img src="https://app.indiemoon.no/images/front-end/logo.png" alt="Logo">
    </a>

    <!-- Sidebar content goes here -->
    <ul class="nav nav-sidebar">
        <li class="@if(Request::is('account/dashboard')) active @endif">
            <a href=" {{ route('learner.dashboard') }} ">
                <i class="fa fa-home"></i> Kontrollpanel
            </a>
        </li>

        <li class=" @if(Request::is('account/time-register')) active @endif">
            <a href=" {{ route('learner.time-register') }} ">
                <i class="fa fa-clock"></i> Time Register
            </a>
        </li>

        <li class="@if(Request::is('account/book-sale')) active @endif">
            <a href=" {{ route('learner.book-sale') }} ">
                <i class="fa fa-bar-chart"></i> Sales
            </a>
        </li>

        <li>
            <a href="#">Editor Services</a>
            <ul>
                <li class="@if(Request::is('account/self-publishing/list')) active @endif">
                    <a href="{{ route('learner.self-publishing.list') }}">Redaktør</a>
                </li>
                <li class="@if(Request::is('account/self-publishing/copy-editing')) active @endif">
                    <a href="{{ route('learner.self-publishing.copy-editing') }}">Språkvask</a>
                </li>
                <li class="@if(Request::is('account/self-publishing/correction')) active @endif">
                    <a href="{{ route('learner.self-publishing.correction') }}">Korrektur</a>
                </li>
            </ul>
        </li>
    </ul>

    <a href="{{ route('learner.change-portal', 'learner') }}" class="btn portal-btn">
        Learner Portal
    </a>

    <a href="{{ route('auth.logout-get') }}" style="display: block">
        <form method="POST" action="{{route('auth.logout')}}" class="form-logout">
            {{csrf_field()}}
            <button type="submit" class="btn logout-btn">
                <i class="fa fa-sign-out-alt"></i> Logg av
            </button>
        </form>
    </a>
</div>