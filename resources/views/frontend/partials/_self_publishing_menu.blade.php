<ul class="list-group">
    <li class="list-group-item @if(Request::is('account/dashboard')) active @endif">
        <a href=" {{ route('learner.dashboard') }} ">
            <i class="fa fa-home"></i> Kontrollpanel
        </a>
    </li>

    <li class="list-group-item @if(Request::is('account/time-register')) active @endif">
        <a href=" {{ route('learner.time-register') }} ">
            <i class="fa fa-clock"></i> Time Register
        </a>
    </li>

    <li class="list-group-item @if(Request::is('account/book-sale')) active @endif">
        <a href=" {{ route('learner.book-sale') }} ">
            <i class="fa fa-bar-chart"></i> Sales
        </a>
    </li>

    <li class="list-group-item">
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