@extends('backend.layout')

@section('title')
<title>Innlegg &rsaquo; Fellesskap &rsaquo; Forfatterskolen Admin</title>
@stop

@section('page-title', 'Fellesskap')

@section('content')
<div class="col-sm-12">
    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
        <li><a href="{{ route('admin.community.index') }}">Oversikt</a></li>
        <li><a href="{{ route('admin.community.members') }}">Medlemmer</a></li>
        <li class="active"><a href="{{ route('admin.community.posts') }}">Innlegg</a></li>
        <li><a href="{{ route('admin.community.discussions') }}">Diskusjoner</a></li>
        <li><a href="{{ route('admin.community.course-groups') }}">Kursgrupper</a></li>
    </ul>

    <div style="margin-bottom: 20px;">
        <button class="btn btn-primary" onclick="document.getElementById('bot-post-form').style.display = document.getElementById('bot-post-form').style.display === 'none' ? 'block' : 'none'">
            <i class="fa fa-plus"></i> Nytt innlegg fra Forfatterskolen
        </button>
    </div>

    <div id="bot-post-form" class="panel panel-default" style="display: none; margin-bottom: 20px;">
        <div class="panel-heading">
            <h4 style="margin: 0;">Nytt innlegg fra Forfatterskolen</h4>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.community.posts.store-bot') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Innhold</label>
                    <textarea name="content" id="bot-post-content" class="form-control" rows="5" placeholder="Skriv innholdet her..." required></textarea>
                </div>
                <div class="form-group">
                    <label>Bilde (valgfritt)</label>
                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                </div>
                <div class="checkbox">
                    <label><input type="checkbox" name="pinned"> Fest innlegget</label>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Publiser</button>
                    <button type="button" class="btn btn-default" id="generate-ai-btn"><i class="fa fa-magic"></i> Generer med AI</button>
                </div>
            </form>
            <div id="ai-prompt-section" style="display: none; margin-top: 15px; padding: 15px; background: #f9f9f9; border-radius: 6px;">
                <div class="form-group">
                    <label>Hva slags innlegg vil du generere?</label>
                    <input type="text" id="ai-prompt-input" class="form-control" placeholder="F.eks. 'Et skrivetips om dialogskriving' eller 'Inspirasjon for nybegynnere'">
                </div>
                <button type="button" class="btn btn-warning" id="ai-generate-submit"><i class="fa fa-magic"></i> Generer</button>
                <span id="ai-loading" style="display: none; margin-left: 10px;"><i class="fa fa-spinner fa-spin"></i> Genererer...</span>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <table class="table">
            <thead>
                <tr>
                    <th>Forfatter</th>
                    <th>Innhold</th>
                    <th>Kommentarer</th>
                    <th>Dato</th>
                    <th>Festet</th>
                    <th style="width: 120px;">Handlinger</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                    @php
                        $profile = $post->user->profile ?? null;
                        $name = $profile ? ucwords($profile->name) : ($post->user->fullName ?? 'Ukjent');
                    @endphp
                    <tr @if($post->pinned) style="background: #fff8e1;" @endif>
                        <td>
                            @if($post->is_bot_post ?? false)
                                <span class="label label-info"><i class="fa fa-star"></i> Forfatterskolen</span>
                            @else
                                {{ $name }}
                            @endif
                        </td>
                        <td>{{ Str::limit($post->content, 80) }}</td>
                        <td>{{ $post->comments->count() }}</td>
                        <td>{{ $post->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if($post->pinned)
                                <span class="label label-warning"><i class="fa fa-thumb-tack"></i> Ja</span>
                            @else
                                <span class="text-muted">Nei</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.community.posts.toggle-pin', $post->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-xs {{ $post->pinned ? 'btn-default' : 'btn-warning' }}" title="{{ $post->pinned ? 'Løsne' : 'Fest' }}">
                                    <i class="fa fa-thumb-tack"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.community.posts.destroy', $post->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Er du sikker på at du vil slette dette innlegget?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" title="Slett">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Ingen innlegg ennå.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {!! $posts->render() !!}
</div>
@stop

@section('scripts')
<script>
document.getElementById('generate-ai-btn').addEventListener('click', function() {
    document.getElementById('ai-prompt-section').style.display = 'block';
});

document.getElementById('ai-generate-submit').addEventListener('click', function() {
    var prompt = document.getElementById('ai-prompt-input').value;
    var loading = document.getElementById('ai-loading');
    loading.style.display = 'inline';

    fetch('{{ route("admin.community.posts.generate-ai") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ prompt: prompt })
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        loading.style.display = 'none';
        if (data.content) {
            document.getElementById('bot-post-content').value = data.content;
        } else if (data.error) {
            alert(data.error);
        }
    })
    .catch(function(err) {
        loading.style.display = 'none';
        alert('Feil ved generering: ' + err.message);
    });
});
</script>
@stop
