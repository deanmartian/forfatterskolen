<nav class="navbar book-menu">
    <div class="inner">
        <?php
            $memberNav = ['learner.private-groups.show', 'learner.private-groups.discussion',
                'learner.private-groups.books', 'learner.private-groups.preferences'];
        ?>
        @foreach(\App\Http\FrontendHelpers::privateGroupsNav() as $nav)
            @if (!$manager)
                @if (in_array($nav['route_name'], $memberNav))
                    <a href="{{ route($nav['route_name'], $privateGroup->id) }}" class="item link @if(Route::getCurrentRoute()->getName() === $nav['route_name'])current @endif"> {{ $nav['label'] }}</a>
                @endif
            @else
                <a href="{{ route($nav['route_name'], $privateGroup->id) }}" class="item link @if(Route::getCurrentRoute()->getName() === $nav['route_name'])current @endif"> {{ $nav['label'] }}</a>
            @endif
        @endforeach
    </div>
</nav>