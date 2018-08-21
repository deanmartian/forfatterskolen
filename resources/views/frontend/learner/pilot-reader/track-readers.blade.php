@extends('frontend.layout')

@section('title')
    <title>{{ $book->title }} Track Readers &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Track Readers @stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content book-track-readers global white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">

                    @include('frontend.learner.pilot-reader.partials.nav')

                    <header class="page-main">
                        <h1>@yield('heading')</h1>
                        <hr>
                    </header>

                    <?php
                        $chapter_id_list = $book->chapters->pluck('id');
                        $chaptersWithReaders = \App\PilotReaderBookReadingChapter::whereIn('chapter_id', $chapter_id_list)
                            ->groupBy('chapter_id')
                            ->orderBy('chapter_id', 'ASC')
                            ->get();
                    ?>


                    <h2>Tracking by Chapter</h2>
                    <div class="row" id="track-readers-view">
                        <div class="chapter">
                            @foreach($chaptersWithReaders as $chaptersWithReader)
                                <?php

                                    $chapter = \App\PilotReaderBookChapter::find($chaptersWithReader->chapter_id);
                                    $chapterCount = 0;
                                    foreach ($book->chapters as $k=>$ch) {
                                        if ($chaptersWithReader->chapter_id == $ch->id) {
                                            $chapterCount = $k+1;
                                        }
                                    }
                                ?>
                                <div class="chapter-title">
                                    <div class="item chapter">
                                        <a href="{{ route('learner.book-author-book-view-chapter',
                                        ['book_id' => $book->id, 'chapter_id' => $chapter->id]) }}">
                                            @if ($chapter->type == 1)
                                                {{ $chapter->title ? $chapter->title : \App\Http\FrontendHelpers::getChapterTitle($book, $chapter->id) }}
                                            @else
                                                {{ $chapter->title ? $chapter->title : \App\Http\FrontendHelpers::getQuestionnaireTitle($book, $chapter->id) }}
                                            @endif
                                        </a>
                                    </div> <!-- end item chapter -->
                                    <div class="item readers">Readers</div>
                                </div> <!-- end chapter-title -->

                                <div class="version">
                                    <div class="version-title">
                                        <span class="version-meta">
                                           {{ $chapter->created_at }} - {{ $chapter->word_count }} words
                                        </span>
                                    </div> <!-- end version title -->

                                    <div class="readers">
                                        <ul class="compact">
                                            @foreach($chapter->readers as $reader)
                                                <li class="reader">
                                                    <span class="user">
                                                        {{ $reader->user->full_name }}
                                                    </span>
                                                    <span class="comments">
                                                        <?php
                                                            $chapterFeedback = \App\PilotReaderChapterFeedback::where('chapter_id', $chapter->id)
                                                            ->where('user_id', $reader->user->id)
                                                            ->first();

                                                            $commentCount = count($chapterFeedback['readerMessages']);
                                                        ?>
                                                        {{ $commentCount }} comment{{ $commentCount > 1 ? 's' : '' }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div> <!-- end version -->
                            @endforeach
                        </div> <!-- end chapter -->
                    </div> <!-- end track-readers-view -->

                    <h2>Tracking by Reader</h2>
                    <div class="row" id="track-by-reader">
                        <table class="action-table">
                            <thead>
                            <tr>
                                <th>Reader</th>
                                <th>Started At</th>
                                <th>Last Seen</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($book->readers()->withTrashed()->get() as $reader)
                                <tr>
                                    <td class="reader">
                                        <div class="name">
                                            {{ $reader->user->full_name }}
                                        </div>
                                        <div class="email hint">
                                            <a href="mailto:{{ $reader->user->email }}">{{ $reader->user->email }}</a>
                                        </div>
                                    </td>
                                    <td>{{ $reader->started_at }}</td>
                                    <td>{{ $reader->last_seen }}</td>
                                    <td>
                                        @if($reader->deleted_at)
                                            revoked
                                        @else
                                            @if($reader->started_at)
                                                started
                                            @else
                                                not yet started
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div> <!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div><!-- col-sm-12 col-md-10 sub-right-content book-track-readers global -->

        <div class="clearfix"></div>
    </div>
@stop