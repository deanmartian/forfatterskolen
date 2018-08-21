<nav class="navbar book-menu">
    <div class="inner">
        @foreach(\App\Http\FrontendHelpers::pilotReaderDirectoryNav() as $nav)
            <a href="{{ route($nav['route_name']) }}" class="item link @if(Route::getCurrentRoute()->getName() === $nav['route_name'])current @endif"> {{ $nav['label'] }}</a>
        @endforeach
    </div>
</nav>