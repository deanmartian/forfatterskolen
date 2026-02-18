@extends('frontend.layouts.course-portal')

@section('title')
<title>Forum &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title mb-0">Forum</h1>
                <a href="{{ route('learner.forum.thread.create') }}" class="btn site-btn-global">
                    <i class="fa fa-plus"></i> Ny trad
                </a>
            </div>

            {{-- Category filter pills --}}
            @if($categories->count())
                <div class="forum-category-pills mb-4">
                    <a href="{{ route('learner.forum.index') }}"
                       class="btn btn-sm {{ !isset($category) ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Alle
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('learner.forum.category', $cat->id) }}"
                           class="btn btn-sm {{ (isset($category) && $category->id == $cat->id) ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            @if(isset($category))
                <p class="text-muted mb-3">{{ $category->description }}</p>
            @endif

            <div class="card global-card">
                <div class="card-body p-0">
                    @if($threads->count())
                        <table class="table table-global forum-table mb-0">
                            <thead>
                                <tr>
                                    <th>Emne</th>
                                    <th class="text-center" width="100">Svar</th>
                                    <th width="200">Siste innlegg</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($threads as $thread)
                                    <tr>
                                        <td>
                                            <div class="forum-thread-row">
                                                @if($thread->is_pinned)
                                                    <span class="badge badge-info mr-1">
                                                        <i class="fa fa-thumbtack"></i>
                                                    </span>
                                                @endif
                                                @if($thread->is_locked)
                                                    <span class="badge badge-secondary mr-1">
                                                        <i class="fa fa-lock"></i>
                                                    </span>
                                                @endif
                                                <a href="{{ route('learner.forum.thread', $thread->id) }}" class="forum-thread-link">
                                                    {{ $thread->title }}
                                                </a>
                                                <div class="forum-thread-meta">
                                                    @if($thread->category)
                                                        <span class="badge badge-light">{{ $thread->category->name }}</span>
                                                    @endif
                                                    <span class="text-muted">
                                                        av {{ $thread->user->full_name }} &middot;
                                                        {{ $thread->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-pill badge-light">{{ $thread->posts_count }}</span>
                                        </td>
                                        <td class="align-middle">
                                            @if($thread->latestPost)
                                                <small class="text-muted">
                                                    {{ $thread->latestPost->user->full_name }}<br>
                                                    {{ $thread->latestPost->created_at->diffForHumans() }}
                                                </small>
                                            @else
                                                <small class="text-muted">Ingen svar enna</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="p-3">
                            {{ $threads->links() }}
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fa fa-comments fa-2x mb-2 d-block"></i>
                            Ingen trader enna. Vær den forste til a starte en diskusjon!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
