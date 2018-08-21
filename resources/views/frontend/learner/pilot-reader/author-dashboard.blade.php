@extends('frontend.layout')

@section('title')
    <title>Book Author &rsaquo; Forfatterskolen</title>
@stop

@section('heading') My Books @stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">
                    @if ($invitations->count() || $groupInvitations->count())
                        <div class="row pending-invitations">
                            <h5 class="font-16">Pending Invitations</h5>

                            <ul class="compact invitations">
                                @foreach($invitations as $invitation)
                                    <li>
                                        <span class="label">
                                            {{ $invitation->book->author->full_name }} has invited you to read
                                            {{ $invitation->book->title }}
                                        </span>

                                        <a href="{{ route('learner.book-invitation-action',
                                        ['_token' => $invitation->_token, 'action' => 1]) }}"
                                           class="action color success">Accept</a>
                                        <a href="{{ route('learner.book-invitation-action',
                                        ['_token' => $invitation->_token, 'action' => 2]) }}"
                                           class="action color danger">Decline</a>
                                    </li>
                                @endforeach

                                @foreach($groupInvitations as $groupInvitation)
                                        <li>
                                            <span class="label">
                                                <?php
                                                    $manager = $groupInvitation->group->members()->where(['role' => 'manager'])->first();
                                                ?>
                                                {{ $manager->user->full_name }} has invited you to join
                                                {{ $groupInvitation->group->name }}
                                            </span>

                                            <a href="{{ route('learner.private-groups.invitation.action',
                                        ['status' => 1, 'token' => $groupInvitation->token]) }}"
                                               class="action color success">Accept</a>
                                            <a href="{{ route('learner.private-groups.invitation.action',
                                        ['status' => 2, 'token' => $groupInvitation->token]) }}"
                                               class="action color danger">Decline</a>
                                        </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <?php
                      $userPreference = \App\UserPreference::where('user_id', Auth::user()->id)->first();
                    ?>
                    @if (!$userPreference || $userPreference->role !== 1)
                        <div class="row">
                            <div class="col-sm-4">
                                <h2 class="no-margin-top group-label">@yield('heading')</h2>
                            </div>
                        </div>

                        <div class="col-sm-12 margin-top">
                            <div class="col-sm-12 col-md-6 no-left-padding">
                                @if (Auth::user()->books->count())
                                    <ul class="book-list">
                                        @foreach(Auth::user()->books as $book)
                                            <li>
                                                <a href="{{ route('learner.book-author-book-show', $book->id) }}">{{ $book->title }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>Ready to create your first book?</p>
                                @endif
                                <a href="{{ route('learner.book-author-create') }}" class="beta-button">
                                    <i class="fa fa-plus"></i>
                                    <span>Create</span>
                                </a>
                            </div>

                            <div class="col-sm-12 col-md-6 no-left-padding">
                                <div class="callout sans tiny">
                                    Looking to connect with new readers? Take a <a href="{{ route('learner.reader-directory.index') }}">look</a> at the Reader Directory.
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="clearfix"></div>

                    <div class="row margin-top">
                        <div class="col-sm-4">
                            <h2 class="no-margin-top group-label">Books I'm Reading</h2>
                        </div>
                    </div>

                    <div class="col-sm-12 margin-top">
                        <ul class="book-list">
                        @forelse ($readingBooks as $readingBook)
                            <li>
                                <a href="{{ route('learner.book-author-book-show', $readingBook->book->id) }}">
                                    {{ $readingBook->book->title }}
                                </a>
                                <?php
                                    $book_id = $readingBook->book->id;
                                    $bookmarkerId = Auth::user()->id;
                                    $hasBookmarks = \App\PilotReaderBookBookmark::where('book_id', $book_id)
                                    ->where('bookmarker_id', $bookmarkerId)
                                    ->get();
                                ?>
                                @foreach($hasBookmarks as $hasBookmark)
                                    <a href="{{ url('/account/book-author/book/'.$hasBookmark->book_id
                                    .'/chapter/'.$hasBookmark->chapter_id.'?bookmark=true') }}" class="bookmark">
                                        <i class="fa fa-bookmark text-danger"></i>
                                        <div class="preview">
                                            {!! strlen($hasBookmark->paragraph_text) > 150 ?
                                             substr($hasBookmark->paragraph_text, 0, 150).'...' : $hasBookmark->paragraph_text
                                             !!}
                                        </div>
                                    </a>
                                @endforeach
                            </li>
                        @empty
                            You're not currently reading any books.
                        @endforelse
                        </ul>
                    </div>

                    @if($finishedBooks->count())
                        <div class="row margin-top">
                            <div class="col-sm-4">
                                <h2 class="no-margin-top group-label">Books I've Finished</h2>
                            </div>
                        </div>

                            <div class="col-sm-12 margin-top">
                                <ul class="book-list">
                                    @forelse ($finishedBooks as $finishedBook)
                                        <li>
                                            <a href="{{ route('learner.book-author-book-show', $finishedBook->book->id) }}">
                                                {{ $finishedBook->book->title }}
                                            </a>
                                        </li>
                                    @empty
                                        You're not currently reading any books.
                                    @endforelse
                                </ul>
                            </div>
                    @endif

                    <div class="row margin-top">
                        <div class="col-sm-4">
                            <h2 class="no-margin-top group-label">User Menu</h2>
                        </div>
                    </div>

                    <div class="col-sm-12 margin-top">
                        <a href="{{ route('learner.pilot-reader.account.index') }}">View Profile</a> <br>
                        <a href="{{ route('learner.private-groups.index') }}">Private Groups</a>
                    </div>
                </div> <!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- col-sm-12 col-md-10 sub-right-content -->

        <div class="clearfix"></div>
    </div>
@stop