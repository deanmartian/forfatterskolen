@extends('backend.layout')

@section('title')
    <title>Rediger: {{ $info['name'] }} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<style>
    .edit-card {
        background: #fff; border-radius: 8px; padding: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.08); max-width: 800px;
    }
    .edit-card .form-group label { font-weight: 600; }
    .edit-meta {
        background: #f8f8f8; border-radius: 8px; padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .edit-meta dt { font-size: 0.75rem; text-transform: uppercase; color: #999; margin-bottom: 2px; }
    .edit-meta dd { margin-bottom: .75rem; font-size: 0.9rem; }
    .var-badge {
        display: inline-block; background: #eef; color: #336; border-radius: 4px;
        padding: 1px 6px; font-size: 0.8rem; font-family: monospace; margin-right: 4px; margin-bottom: 4px;
    }
</style>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-pencil"></i> Rediger: {{ $info['name'] }}</h3>
        <div class="pull-right">
            <a href="{{ route('admin.emails.index') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Tilbake
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="edit-card">
        {{-- Metadata --}}
        <div class="edit-meta">
            <dl>
                <dt>Kategori</dt>
                <dd>{{ $info['category'] }}</dd>

                <dt>Beskrivelse</dt>
                <dd>{{ $info['description'] }}</dd>

                <dt>Mailable-klasse</dt>
                <dd><code>{{ $info['mailable'] }}</code></dd>

                <dt>Tilgjengelige variabler</dt>
                <dd>
                    @foreach($info['variables'] as $var)
                        <span class="var-badge">{{ '{{' }} ${{ $var }} {{ '}}' }}</span>
                    @endforeach
                </dd>
            </dl>
        </div>

        {{-- Redigeringsform --}}
        <form method="POST" action="{{ route('admin.emails.update', $type) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Emne (Subject)</label>
                <input type="text" name="subject" class="form-control"
                       value="{{ old('subject', $template->subject ?? '') }}"
                       placeholder="Emnet til e-posten">
                @error('subject')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Innhold (HTML)</label>
                <textarea name="email_content" class="form-control" rows="15"
                          placeholder="HTML-innhold for e-posten..."
                          style="font-family: monospace; font-size: 0.85rem;">{{ old('email_content', $template->email_content ?? '') }}</textarea>
                @error('email_content')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:flex; gap:1rem; align-items:center;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Lagre endringer
                </button>
                <a href="{{ route('admin.emails.preview', $type) }}" class="btn btn-default">
                    <i class="fa fa-eye"></i> Forhåndsvis
                </a>
            </div>
        </form>
    </div>
@stop
