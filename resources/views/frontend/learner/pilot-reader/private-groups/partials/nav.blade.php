<nav class="navbar book-menu">
    <div class="inner">
        @foreach(\App\Http\FrontendHelpers::privateGroupsNav() as $nav)
            <a href="{{ route($nav['route_name'], $privateGroup->id) }}" class="item link @if(Route::getCurrentRoute()->getName() === $nav['route_name'])current @endif"> {{ $nav['label'] }}</a>
        @endforeach
    </div>
</nav>