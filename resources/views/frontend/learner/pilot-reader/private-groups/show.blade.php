@extends('frontend.learner.pilot-reader.private-groups.layout')

@section('private-content')
    <div class="global-card font-14-body">
        <div class="card-body">
            <h1 class="font-weight-light detail mt-0">{{ $privateGroup->name }}</h1>
            <div class="form-group welcome-msg-div">
                <div class="lead detail" id="welcome_msg">{!! $privateGroup->welcome_msg !!}</div>
                @if($manager)
                    <button class="beta-button btn-sm mt-2" onclick="methods.inlineEdit(this)">
                        <i class="fa fa-pencil right-space"></i>
                        <span>Edit Welcome Message</span>
                    </button>
                @endif
            </div> <!-- end form-group welcome-msg-div -->

            <div class="form-group display-none welcome-msg-form">
                <textarea name="welcome_msg" id="welcome_msg_editor"></textarea>
                <div class="form-group mt-2 clearfix">
                    <button class="btn btn-primary btn-sm pull-right" onclick="methods.inlineSave(this)">Save</button>
                    <button class="btn btn-danger btn-sm pull-right mr-1" onclick="methods.inlineCancel(this)">Cancel</button>
                </div>
            </div> <!-- end form-group display-none welcome-msg-form -->

            <h1 class="font-weight-light">
                Featured Books
            </h1>
            @if($manager)
                <small class="text-muted d-block manager-note">
                    <h5 class="d-inline-block mr-2"><span class="badge badge-warning text-white">Manager Note</span></h5>
                    You can add featured books from the "Books" tab.
                </small>
            @endif

            @if($featured_books->count())
                @foreach($featured_books as $featured_book)
                    <div class="form-group mt-3">
                        <?php
                            $isReader = 0;
                            $reader = $featured_book->book->readers()->where(['book_id' => $featured_book->book->id,
                                'user_id' => Auth::user()->id])->first();

                            if ($reader && $reader->status !== 2) {
                                $isReader++;
                            }
                        ?>
                        <div class="global-card with-border">
                            <div class="card-body px-3">
                                <h2 class="mb-0 mt-0">
                                    @if ($manager || $isReader)
                                        <a href="{{ route('learner.book-author-book-show', $featured_book->book->id) }}"
                                           class="no-underline text-info-global hand">
                                            {{ $featured_book->book->title }}
                                        </a>
                                    @else
                                        <a href="javascript:void(0)"
                                           class="no-underline text-info-global hand"
                                            onclick="methods.notReaderModal({{ $featured_book->book->id }}, {{ $reader && $reader->status ===2 ? 1 : 0  }})">
                                            {{ $featured_book->book->title }}
                                        </a>
                                    @endif
                                </h2>
                            </div>
                            <div class="card-footer bg-info-global text-white col-sm-12">
                                <span class="pull-left">By {{ $featured_book->book->author->full_name }}</span>
                                <span class="pull-right">Shared on {{ \App\Http\FrontendHelpers::formatByMd($featured_book->created_at) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                @endforeach
            @endif

            <h1 class="font-weight-light">
                Announcements
            </h1>

            <ul class="list-group font-14-body compact">
                @if($manager)
                    <li class="text-muted manager-note small">
                        <h5 class="d-inline-block mr-2"><span class="badge badge-warning text-white">Manager Note</span></h5>
                        You can make announcements from the discussion page and they will be listed here.
                    </li>
                @endif
                @foreach($announcements as $announcement)
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-6 vcenter">
                                <a class="no-underline font-weight-bold" href="{{ route('learner.private-groups.discussion.show',
                                ['id' => $privateGroup->id, 'discussion_id' => $announcement->id]) }}">
                                    <i class="fa fa-exclamation-circle text-success"></i> test
                                </a>
                            </div>

                            <div class="col-md-1 vcenter">
                                <i class="fa fa-comments"></i>
                                {{ $announcement->replies->count() + 1 }}
                            </div>

                            <div class="col-md-4 vcenter">
                                {{ \Carbon\Carbon::parse($announcement->created_at)->format('M d, h:i A') }} <br>
                                by {{ $announcement->user->full_name }}
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

        </div> <!-- end card-body -->
    </div> <!-- end global-card-->

    @include('frontend.learner.pilot-reader.private-groups.modal.book_preview')
@stop
