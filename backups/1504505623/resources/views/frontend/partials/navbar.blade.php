<div class="top-navbar">
  Velkommen til Forfatterskolen
  <div class="pull-right">
    <a href="https://www.facebook.com/bliforfatter/" target="_blank"><i class="fa fa-facebook"></i></a>
    <a href="https://twitter.com/Forfatterrektor" target="_blank"><i class="fa fa-twitter"></i></a>
    <a href="https://www.instagram.com/forfatterskolen_norge/" target="_blank"><i class="fa fa-instagram"></i></a>
    @if( Auth::guest() )
    <a href="{{route('auth.login.show')}}" class="top-navbar-btn">Min Side</a>
    @endif
  </div>
</div>

<nav class="navbar navbar-default">
  <div class="navbar-brand-container">
    <a class="navbar-brand" href="{{url('')}}"><img src="{{asset('images/logo.png')}}"></a>
  </div>
  <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span> 
        </button>
      </div>
      <div class="collapse navbar-collapse text-center" id="myNavbar">
        <ul class="nav navbar-nav">
          <li @if(Route::currentRouteName() == 'front.course.index') class="active" @endif><a href="{{route('front.course.index')}}">Våre Kurs</a></li>
          <li @if(Route::currentRouteName() == 'front.shop-manuscript.index') class="active" @endif><a href="{{ route('front.shop-manuscript.index') }}">Manusutvikling</a></li>
          <li><a href="http://forfatterreiser.no/" target="_blank">Forfatterreiser</a></li>
          <li><a href="http://www.forfatterdrom.no/" target="_blank">Forlag</a></li>
          <li><a href="http://forfatterblogg.no/" target="_blank">Forfatterblogg</a></li>
          <li @if(Route::currentRouteName() == 'front.workshop.index') class="active" @endif><a href="{{ route('front.workshop.index') }}">Workshop</a></li>
          <li @if( Route::currentRouteName() == 'front.faq' ) class="active" @endif><a href="{{ route('front.faq') }}">FAQ</a></li>
          <li @if( Route::currentRouteName() == 'front.contact-us' ) class="active" @endif><a href="{{ route('front.contact-us') }}">Kontakt Oss</a></li>
          @if(! Auth::guest() )
          <li><a href="#"><i class="fa fa-bell-o"></i></a></li>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              Hei {{Auth::user()->first_name}}
              <i class="fa fa-angle-down"></i>&nbsp;<span class="nav-user-thumb" style="background-image: url('{{Auth::user()->profile_image}}')"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="{{route('learner.course')}}">Mine Kurs</a></li>
              <li><a href="{{route('learner.shop-manuscript')}}">Manuskripter</a></li>
              <li><a href="{{route('learner.workshop')}}">Workshop</a></li>
              <li><a href="{{route('learner.webinar')}}">Webinars</a></li>
              <li><a href="{{route('learner.calendar')}}">Kalender</a></li>
              <li><a href="{{route('learner.profile')}}">Profil</a></li>
              <li><a href="{{route('learner.invoice')}}">Fakturaer</a> </li>
              <li>
                <form method="POST" action="{{route('auth.logout')}}" class="form-logout">
                  {{csrf_field()}}
                  <button type="submit" class="btn btn-block">Logg av</button>
                </form>
              </li>
            </ul>
          </li>
          @endif
        </ul>
      </div>
  </div>
</nav>