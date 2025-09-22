<?php
$urlList = array('pulse', 'board');
?>

<nav class="navbar navbar-default {{ in_array(Request::segment(1), $urlList) ? 'navbar-fixed-top' : '' }}">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#fatterNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span> 
      </button>
    </div>
    <div class="collapse navbar-collapse" id="fatterNavbar">
  
      <!-- <button class="btn btn-success navbar-btn btn-sm" data-placement="bottom" data-trigger="focus" data-toggle="popover" data-content='{!! AdminHelpers::newButtonMenu() !!}'><i class="fa fa-plus"></i> New</button> -->
      
      <ul class="nav navbar-nav navbar-right">
        <!-- <li @if(Request::is('/')) class="active" @endif><a href="{{route('backend.dashboard')}}">{{ trans('site.admin-menu.dashboard') }}</a></li> -->
        @foreach (\App\Http\AdminHelpers::editorPageList() as $page)
        <li @if(Route::currentRouteName() === strtolower($page['route'])) class="active" @endif>
          <a href="{{ route($page['route']) }}">
            @if($page['request_name'] === 'upcoming-assignment')
                {{ trans('site.'.$page['request_name']) }}
            @elseif($page['request_name'] === 'editors-coaching-time')
                {{ trans('site.coaching-timer-text') }}
            @else
                {{ trans('site.admin-menu.'.$page['request_name']) }}
            @endif
        </a>
        </li>
        @endforeach
        <li>
          <a href="#"><i class="fa fa-bell-o"></i></a>
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            {{Auth::user()->fullName}}
            <i class="fa fa-angle-down"></i>&nbsp;<span class="nav-user-thumb"></span>
          </a>
          <ul class="dropdown-menu">
            <li class="form-logout">
                <button type="submit" class="btn btn-block" data-toggle="modal" data-target="#changePasswordModal">{{ trans('site.change-password') }}</button>
            </li>
            <li>
              <form method="POST" action="{{route('auth.logout')}}" class="form-logout">
                {{csrf_field()}}
                <button type="submit" class="btn btn-block">{{ trans('site.logout') }}</button>
              </form>
            </li>
            {{--<li>
              <a href="{{ route('admin.pulse.index') }}" class="dapulse-link">Dapulse</a>
            </li>--}}
          </ul>
        </li>

      </ul>

    </div>
  </div>
</nav>