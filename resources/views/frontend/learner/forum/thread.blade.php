@extends('frontend.layouts.course-portal')

@section('title')
<title>{{ $thread->title }} &rsaquo; Forum &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="mb-3">
                <a href="{{ route('learner.forum.index') }}" class="text-muted">
                    <i class="fa fa-arrow-left"></i> Tilbake til forum
                </a>
            </div>

            {{-- Thread header --}}
            <div class="card global-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <div>
                        <h2 class="mb-1">
                            @if($thread->is_pinned)
                                <i class="fa fa-thumbtack text-info" title="Festet"></i>
                            @endif
                            @if($thread->is_locked)
                                <i class="fa fa-lock text-secondary" title="Last"></i>
                            @endif
                            {{ $thread->title }}
                        </h2>
                        @if($thread->category)
                            <span class="badge badge-light">{{ $thread->category->name }}</span>
                        @endif
                    </div>
                    @if($thread->user_id == Auth::id())
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    data-toggle="dropdown">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('learner.forum.thread.edit', $thread->id) }}">
                                    <i class="fa fa-pen"></i> Rediger
                                </a>
                                <form action="{{ route('learner.forum.thread.delete', $thread->id) }}" method="POST"
                                      onsubmit="return confirm('Er du sikker pa at du vil slette denne traden?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fa fa-trash"></i> Slett
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="forum-post">
                        <div class="forum-post-avatar">
                            <div class="avatar-circle">
                                {{ strtoupper(substr($thread->user->first_name, 0, 1)) }}{{ strtoupper(substr($thread->user->last_name, 0, 1)) }}
                            </div>
                            <small class="text-muted d-block text-center mt-1">{{ $thread->user->full_name }}</small>
                        </div>
                        <div class="forum-post-content">
                            <div class="forum-post-body">
                                {!! nl2br(e($thread->body)) !!}
                            </div>
                            <small class="text-muted">
                                {{ $thread->created_at->format('d.m.Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Posts / Replies --}}
            @if($posts->count())
                <h4 class="mb-3">Svar ({{ $posts->total() }})</h4>
                @foreach($posts as $post)
                    <div class="card global-card mb-3" id="post-{{ $post->id }}">
                        <div class="card-body">
                            <div class="forum-post">
                                <div class="forum-post-avatar">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($post->user->first_name, 0, 1)) }}{{ strtoupper(substr($post->user->last_name, 0, 1)) }}
                                    </div>
                                    <small class="text-muted d-block text-center mt-1">{{ $post->user->full_name }}</small>
                                </div>
                                <div class="forum-post-content">
                                    @if($post->user_id == Auth::id())
                                        <div class="float-right">
                                            <button class="btn btn-sm btn-outline-secondary"
                                                    onclick="document.getElementById('edit-post-{{ $post->id }}').classList.toggle('d-none')">
                                                <i class="fa fa-pen"></i>
                                            </button>
                                            <form action="{{ route('learner.forum.post.delete', $post->id) }}" method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Er du sikker pa at du vil slette dette innlegget?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                    <div class="forum-post-body">
                                        {!! nl2br(e($post->body)) !!}
                                    </div>
                                    <small class="text-muted">
                                        {{ $post->created_at->format('d.m.Y H:i') }}
                                        @if($post->updated_at->gt($post->created_at))
                                            (redigert)
                                        @endif
                                    </small>

                                    {{-- Inline edit form --}}
                                    @if($post->user_id == Auth::id())
                                        <div id="edit-post-{{ $post->id }}" class="d-none mt-3">
                                            <form action="{{ route('learner.forum.post.update', $post->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group">
                                                    <textarea name="body" class="form-control" rows="4">{{ $post->body }}</textarea>
                                                </div>
                                                <button type="submit" class="btn btn-sm site-btn-global">Lagre</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        onclick="document.getElementById('edit-post-{{ $post->id }}').classList.add('d-none')">
                                                    Avbryt
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mb-3">
                    {{ $posts->links() }}
                </div>
            @endif

            {{-- Reply form --}}
            @if(!$thread->is_locked)
                <div class="card global-card">
                    <div class="card-header">
                        <h4 class="mb-0">Skriv et svar</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('learner.forum.post.store', $thread->id) }}" method="POST"
                              onsubmit="disableSubmit(this)">
                            @csrf
                            <div class="form-group">
                                <textarea name="body" class="form-control" rows="5"
                                          placeholder="Skriv ditt svar her..." required></textarea>
                            </div>
                            <button type="submit" class="btn site-btn-global">
                                <i class="fa fa-paper-plane"></i> Publiser svar
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-secondary text-center">
                    <i class="fa fa-lock"></i> Denne traden er last for nye svar.
                </div>
            @endif
        </div>
    </div>
@stop
