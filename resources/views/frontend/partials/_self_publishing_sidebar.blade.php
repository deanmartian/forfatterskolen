<div id="sidebar">
    <a class="navbar-brand w-100" href="#">
        <img src="https://app.indiemoon.no/images/front-end/logo.png" alt="Logo">
    </a>

    <!-- Sidebar content goes here -->
    <ul class="nav nav-sidebar">
        <li class="@if(Request::is('account/dashboard')) active @endif">
            <a href=" {{ route('learner.dashboard') }} ">
                <i class="fa fa-home"></i> {{ trans('site.author-portal-menu.dashboard') }}
            </a>
        </li>

        <li class=" @if(Request::is('account/time-register')) active @endif">
            <a href=" {{ route('learner.time-register') }} ">
                <i class="fa fa-clock"></i> {{ trans('site.author-portal-menu.time-register') }}
            </a>
        </li>

        <li class="@if(Request::is('account/book-sale')) active @endif">
            @php $hasBookSale = FrontendHelpers::checkIfLearnerHasBookSale()->count() > 0; @endphp
        
            <a 
                href="{{ $hasBookSale ? route('learner.book-sale') . '?year=' . FrontendHelpers::getLearnerSaleYear() 
                    : 'javascript:void(0)' }}" 
                style="{{ $hasBookSale ? '' : 'pointer-events: none; opacity: 0.6; cursor: not-allowed;' }}"
            >
                <i class="fa fa-bar-chart"></i> {{ trans('site.author-portal-menu.sales') }}
            </a>
        </li>

        <li class="@if(Request::is('account/project')) active @endif">
            <a href=" {{ route('learner.project') }} ">
                <i class="fa fa-file"></i> {{ trans('site.author-portal.project') }}
            </a>
        </li>

        <li class="@if(Request::is('account/marketing')) active @endif">
            <a href=" {{ route('learner.marketing') }} ">
                <i class="fa fa-file"></i> {{ trans('site.author-portal.marketing') }}
            </a>
        </li>

        <li class="@if(Request::is('account/progress-plan')) active @endif">
            <a href=" {{ route('learner.progress-plan') }} ">
                <i class="fa fa-file"></i> Fremdriftsplan 
            </a>
        </li>

        <li>
            <a href="#">
                {{ trans('site.author-portal-menu.editor-services') }}
            </a>
            <ul>
                <li class="@if(Request::is('account/self-publishing/list')) active @endif">
                    <a href="{{ route('learner.self-publishing.list') }}">
                        {{ trans('site.author-portal-menu.editor') }}
                    </a>
                </li>
                <li class="@if(Request::is('account/self-publishing/copy-editing')) active @endif">
                    <a href="{{ route('learner.self-publishing.copy-editing') }}">
                        {{ trans('site.author-portal-menu.copy-editing') }}
                    </a>
                </li>
                <li class="@if(Request::is('account/self-publishing/correction')) active @endif">
                    <a href="{{ route('learner.self-publishing.correction') }}">
                        {{ trans('site.author-portal-menu.correction') }}
                    </a>
                </li>
                <li class="@if(Request::is('account/self-publishing/cover')) active @endif">
                    <a href="{{ route('learner.self-publishing.cover') }}">
                        {{ trans('site.homepage.illustration-cover-design') }}
                    </a>
                </li>
                <li class="@if(Request::is('account/self-publishing/page-format')) active @endif">
                    <a href="{{ route('learner.self-publishing.page-format') }}">
                        {{ trans('site.author-portal.page-format') }}
                    </a>
                </li>
                <li>
                    <a href="#">
                        Lydbok
                    </a>
                </li>
                <li>
                    <a href="#">
                        Ebok
                    </a>
                </li>
            </ul>
        </li>
    </ul>

    <a href="{{ route('learner.change-portal', 'learner') }}" class="btn portal-btn">
        {{ trans('site.author-portal-menu.learner-portal') }}
    </a>

    <a href="{{ route('auth.logout-get') }}" style="display: block">
        <form method="POST" action="{{route('auth.logout')}}" class="form-logout">
            {{csrf_field()}}
            <button type="submit" class="btn logout-btn">
                <i class="fa fa-sign-out-alt"></i> {{ trans('site.logout') }}
            </button>
        </form>
    </a>
</div>