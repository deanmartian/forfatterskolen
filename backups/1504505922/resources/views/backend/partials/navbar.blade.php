<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#fatterNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span> 
      </button>
      <a class="navbar-brand" href="{{url('')}}"><img src="{{asset('images/logo.png')}}" /></a>
    </div>
    <div class="collapse navbar-collapse" id="fatterNavbar">
  
      <!-- <button class="btn btn-success navbar-btn btn-sm" data-placement="bottom" data-trigger="focus" data-toggle="popover" data-content='{!! AdminHelpers::newButtonMenu() !!}'><i class="fa fa-plus"></i> New</button> -->

      <ul class="nav navbar-nav navbar-right">
        <li @if(Request::is('/')) class="active" @endif><a href="{{route('backend.dashboard')}}">Dashboard</a></li>
        <li @if(Request::is('course')) class="active" @endif><a href="{{route('admin.course.index')}}">Courses</a></li>
        <li @if(Request::is('free-course')) class="active" @endif><a href="{{route('admin.free-course.index')}}">Free Courses</a></li>
        <li @if(Request::is('workshop')) class="active" @endif><a href="{{route('admin.workshop.index')}}">Workshops</a></li>
        <li @if(Request::is('learner')) class="active" @endif><a href="{{route('admin.learner.index')}}">Learners</a></li>
        <li @if(Request::is('assignment')) class="active" @endif><a href="{{route('admin.assignment.index')}}">Assignments</a></li>
        <li @if(Request::is('manuscript')) class="active" @endif><a href="{{route('admin.manuscript.index')}}">Manuscripts</a></li>
        <li @if(Request::is('invoices')) class="active" @endif><a href="{{route('admin.invoice.index')}}">Invoices</a></li>
        <li @if(Request::is('shop-manuscript')) class="active" @endif><a href="{{route('admin.shop-manuscript.index')}}">Shop Manuscripts</a></li>
        <li><a href="#"><i class="fa fa-bell-o"></i></a></li>
        <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
          {{Auth::user()->fullName}}
          <i class="fa fa-angle-down"></i>&nbsp;<span class="nav-user-thumb"></span>
        </a>
        <ul class="dropdown-menu">
          <li>
            <form method="POST" action="{{route('auth.logout')}}" class="form-logout">
              {{csrf_field()}}
              <button type="submit" class="btn btn-block">Logout</button>
            </form>
          </li>
        </ul>
        </li>
      </li>
      </ul>
    </div>
  </div>
</nav>