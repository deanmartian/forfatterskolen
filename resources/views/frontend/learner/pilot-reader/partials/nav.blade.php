<nav class="navbar book-menu">
    <div class="inner">
        @if ($book->user_id == Auth::user()->id || ($reader && $reader->role == 'collaborator'))
            <!-- author navigation -->
            @foreach(\App\Http\FrontendHelpers::pilotReaderNav() as $nav)
                <a href="{{ route($nav['route_name'], $book->id) }}" class="item link @if(Route::getCurrentRoute()->getName() === $nav['route_name'])current @endif"> {{ $nav['label'] }}</a>
            @endforeach
        @else
            <!-- reader navigation -->
                @foreach(\App\Http\FrontendHelpers::pilotReaderReaderNav() as $nav)
                    <?php
                        $link_name = $nav['label'];
                        if ($nav['route_name'] == 'learner.book-author-book-reader-feedback-list') {
                            if ($is_viewer == 1) {
                                $link_name = 'Feedback';
                            }
                        }

                    ?>

                    <a href="{{ route($nav['route_name'], $book->id) }}" class="item link @if(Route::getCurrentRoute()->getName() === $nav['route_name'])current @endif"> {{ $link_name }}</a>
                @endforeach
        @endif
    </div>
</nav>