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

    <li class="list-group-item @if(Request::is('account/project')) active @endif">
        <a href=" {{ route('learner.project') }} ">
            <i class="fa fa-file"></i> Bokprosjekt
        </a>
    </li>
</ul>