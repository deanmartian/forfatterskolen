<nav class="navbar book-menu">
    <div class="inner">
        <?php
            $readerProfile = 'learner.pilot-reader.account.reader-profile';
        ?>
        @foreach(\App\Http\FrontendHelpers::pilotReaderProfileNav() as $nav)
            <?php
                $userPreference         = \App\UserPreference::where('user_id', Auth::user()->id)->first();
                $isReaderProfile        = 0;
                if ($userPreference && $userPreference->role !== 2) {
                    $isReaderProfile = 1;
                }
            ?>
            @if ($nav['route_name'] == $readerProfile)
                @if ($isReaderProfile)
                    <a href="{{ route($nav['route_name']) }}" class="item link reader-profile-link @if(Route::getCurrentRoute()->getName() === $nav['route_name'])current @endif">
                        {{ $nav['label'] }}
                    </a>
                @endif
            @else
                <a href="{{ route($nav['route_name']) }}" class="item link @if(Route::getCurrentRoute()->getName() === $nav['route_name'])current @endif">
                    {{ $nav['label'] }}
                </a>
            @endif
        @endforeach
    </div>
</nav>