<div class="top-navbar">
  Velkommen til Forfatterskolen
  <div class="pull-right">
    <a href="https://no.pinterest.com/forfatterskolen_norge/" target="_blank"><i class="fa fa-pinterest"></i></a>
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
    <a class="navbar-brand" href="{{url('')}}"><img src="{{asset('images/logo.png')}}" alt="Forfatterskolen-logo"></a>
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
          {{--<li @if(Route::currentRouteName() == 'front.other-services-page') class="active" @endif><a href="{{ route('front.other-services-page') }}">Andre Tjenester</a></li>--}}
          <li @if(Route::currentRouteName() == 'front.publishing') class="active" @endif><a href="{{ route('front.publishing') }}">Utgitte Elever</a></li>
          {{--<li><a href="http://forfatterreiser.no/">Forfatterreiser</a></li>--}}
          {{--<li><a href="http://www.forfatterdrom.no/">Forlag</a></li>--}}
          <li><a href="{{ route('front.blog') }}">Blogg</a></li>
          <li @if(Route::currentRouteName() == 'front.workshop.index') class="active" @endif><a href="{{ route('front.workshop.index') }}">Workshop</a></li>
          <li @if( Route::currentRouteName() == 'front.faq' ) class="active" @endif><a href="{{ route('front.faq') }}">FAQ</a></li>
          <li @if( Route::currentRouteName() == 'front.contact-us' ) class="active" @endif><a href="{{ route('front.contact-us') }}">Kontakt Oss</a></li>
          @if(! Auth::guest() )
          <li>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-bell-o"></i>
              @if(Auth::user()->notifications()->where('is_read',0)->get()->count())
                <span class="badge badge-danger notif-badge">{{ Auth::user()->notifications()->where('is_read',0)->get()->count() }}</span>
              @endif
            </a>
            <ul class="dropdown-menu notification-list">
              @forelse(Auth::user()->notifications()->where('is_read',0)->get() as $notification)
                <li id="notif-{{ $notification->id }}" @if(!$notification->is_read)class="unread"@endif>
                  <i class="remove-notif" onclick="layoutMethod.removeNotification({{ $notification->id }})">x</i>
                    <?php
                      $phrase         = $notification->message;
                      $replace_string = array("{book_title}", "{chapter_title}");

                      $book           = \App\PilotReaderBook::find($notification->book_id);
                      $book_title     = $book ? $book->title : '';
                      $chapter        = \App\PilotReaderBookChapter::find($notification->chapter_id);
                      $chapter_title  = $chapter ? ($chapter->title ?:
                          \App\Http\FrontendHelpers::getChapterTitle($book, $notification->chapter_id))
                          : '';

                      // check if the notification is for private groups
                      if($notification->is_group) {
                          $group = \App\PrivateGroup::find($notification->book_id);
                          $book_title     = $group ? $group->name : '';
                          $discussion = \App\PrivateGroupDiscussion::find($notification->chapter_id);
                          $chapter_title = $discussion ? $discussion->subject : '';
                      }

                      $string_value         = array($book_title, $chapter_title);
                      $notification_message = str_replace($replace_string, $string_value, $phrase);
                    ?>
                    {!! $notification_message !!}
                </li>
              @empty
                <li class="text-center">
                  <b>No unread notification</b>
                </li>
              @endforelse
              <li>
                <a href="{{ route('learner.notifications') }}#">
                  See All
                </a>
              </li>
            </ul>
          </li>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              Hei {{Auth::user()->first_name}}
              <i class="fa fa-angle-down"></i>&nbsp;<span class="nav-user-thumb" style="background-image: url('{{Auth::user()->profile_image}}')"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="{{route('learner.course')}}">Mine Kurs</a></li>
              <li><a href="{{route('learner.shop-manuscript')}}">Manuskripter</a></li>
              <li><a href="{{route('learner.workshop')}}">Workshops</a></li>
              <li><a href="{{route('learner.webinar')}}">Webinars</a></li>
              <li><a href="{{route('learner.assignment')}}">Oppgaver</a></li>
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