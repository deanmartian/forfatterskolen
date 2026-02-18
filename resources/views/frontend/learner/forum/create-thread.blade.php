@extends('frontend.layouts.course-portal')

@section('title')
<title>Ny trad &rsaquo; Forum &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="mb-3">
                <a href="{{ route('learner.forum.index') }}" class="text-muted">
                    <i class="fa fa-arrow-left"></i> Tilbake til forum
                </a>
            </div>

            <h1 class="page-title">Start en ny trad</h1>

            <div class="card global-card">
                <div class="card-body">
                    <form action="{{ route('learner.forum.thread.store') }}" method="POST"
                          onsubmit="disableSubmit(this)">
                        @csrf

                        <div class="form-group">
                            <label for="forum_category_id" class="font-weight-bold">Kategori</label>
                            <select name="forum_category_id" id="forum_category_id" class="form-control" required>
                                <option value="" disabled selected>Velg en kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('forum_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('forum_category_id'))
                                <small class="text-danger">{{ $errors->first('forum_category_id') }}</small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="title" class="font-weight-bold">Tittel</label>
                            <input type="text" name="title" id="title" class="form-control"
                                   value="{{ old('title') }}" placeholder="Skriv inn en tittel..." required>
                            @if($errors->has('title'))
                                <small class="text-danger">{{ $errors->first('title') }}</small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="body" class="font-weight-bold">Innhold</label>
                            <textarea name="body" id="body" class="form-control" rows="8"
                                      placeholder="Skriv inn innholdet ditt her..." required>{{ old('body') }}</textarea>
                            @if($errors->has('body'))
                                <small class="text-danger">{{ $errors->first('body') }}</small>
                            @endif
                        </div>

                        <div class="form-group text-right">
                            <a href="{{ route('learner.forum.index') }}" class="btn btn-outline-secondary mr-2">
                                Avbryt
                            </a>
                            <button type="submit" class="btn site-btn-global">
                                <i class="fa fa-paper-plane"></i> Publiser trad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
