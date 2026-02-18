@extends('frontend.layouts.course-portal')

@section('title')
<title>Rediger trad &rsaquo; Forum &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="mb-3">
                <a href="{{ route('learner.forum.thread', $thread->id) }}" class="text-muted">
                    <i class="fa fa-arrow-left"></i> Tilbake til traden
                </a>
            </div>

            <h1 class="page-title">Rediger trad</h1>

            <div class="card global-card">
                <div class="card-body">
                    <form action="{{ route('learner.forum.thread.update', $thread->id) }}" method="POST"
                          onsubmit="disableSubmit(this)">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="forum_category_id" class="font-weight-bold">Kategori</label>
                            <select name="forum_category_id" id="forum_category_id" class="form-control" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $thread->forum_category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="title" class="font-weight-bold">Tittel</label>
                            <input type="text" name="title" id="title" class="form-control"
                                   value="{{ old('title', $thread->title) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="body" class="font-weight-bold">Innhold</label>
                            <textarea name="body" id="body" class="form-control" rows="8"
                                      required>{{ old('body', $thread->body) }}</textarea>
                        </div>

                        <div class="form-group text-right">
                            <a href="{{ route('learner.forum.thread', $thread->id) }}"
                               class="btn btn-outline-secondary mr-2">
                                Avbryt
                            </a>
                            <button type="submit" class="btn site-btn-global">
                                <i class="fa fa-save"></i> Lagre endringer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
