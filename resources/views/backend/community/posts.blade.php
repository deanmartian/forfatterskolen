@extends('backend.layout')

@section('page_title', 'Innlegg &rsaquo; Fellesskap &rsaquo; Forfatterskolen Admin')

@section('page-title', 'Fellesskap')

@section('content')
<div class="col-sm-12">
    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
        <li><a href="{{ route('admin.community.index') }}">Oversikt</a></li>
        <li><a href="{{ route('admin.community.members') }}">Medlemmer</a></li>
        <li class="active"><a href="{{ route('admin.community.posts') }}">Innlegg</a></li>
        <li><a href="{{ route('admin.community.discussions') }}">Diskusjoner</a></li>
        <li><a href="{{ route('admin.community.course-groups') }}">Kursgrupper</a></li>
        <li><a href="{{ route('admin.community.live') }}">🔴 Live fellesskap</a></li>
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
                    <label>Post som</label>
                    <select name="post_as" class="form-control">
                        <option value="school">⭐ Forfatterskolen (offisiell)</option>
                        <option value="self">👤 {{ Auth::user()->full_name }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Bilde (valgfritt)</label>
                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                    <div style="margin-top:6px;display:flex;gap:6px;">
                        <button type="button" class="btn btn-xs btn-info" onclick="fetchUnsplashImage()"><i class="fa fa-camera"></i> Hent fra Unsplash</button>
                        <button type="button" class="btn btn-xs btn-warning" onclick="generateAiImage()"><i class="fa fa-magic"></i> Generer AI-bilde</button>
                    </div>
                    <div id="image-preview" style="display:none;margin-top:8px;">
                        <img id="preview-img" style="max-width:300px;border-radius:8px;">
                        <input type="hidden" name="image_url" id="image-url-input">
                        <br><button type="button" class="btn btn-xs btn-default" onclick="clearImage()" style="margin-top:4px;">Fjern bilde</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Kursgruppe (valgfritt)</label>
                    <select name="course_group_id" class="form-control">
                        <option value="">Alle — synlig for hele fellesskapet</option>
                        @foreach(\App\Course::where('status', 1)->where('show_in_course_groups', 1)->orderBy('title')->get() as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Velg et kurs for å kun vise innlegget i den kursgruppen.</small>
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
                        $groupName = $post->course_group_id ? (\App\Course::find($post->course_group_id)?->title ?? '') : '';
                    @endphp
                    <tr @if($post->pinned) style="background: #fff8e1;" @endif @if(($post->status ?? '') === 'draft') style="background: #e3f2fd; border-left: 3px solid #1565c0;" @endif>
                        <td>
                            @if(($post->status ?? '') === 'draft')
                                <span class="label label-primary"><i class="fa fa-pencil"></i> Utkast</span><br>
                            @endif
                            @if($post->is_bot_post ?? false)
                                <span class="label label-info"><i class="fa fa-star"></i> Forfatterskolen</span>
                            @else
                                {{ $name }}
                            @endif
                            @if($groupName)
                                <br><span class="label label-default" style="font-size:10px;">{{ $groupName }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="cursor:pointer;" onclick="var el=document.getElementById('post-{{ $post->id }}');el.style.display=el.style.display==='none'?'block':'none';">
                                {{ Str::limit($post->content, 80) }}
                                <small style="color:#862736;">▼ vis mer</small>
                            </div>
                            <div id="post-{{ $post->id }}" style="display:none;margin-top:10px;padding:14px;background:#faf9f7;border-radius:8px;border:1px solid #e8e4de;white-space:pre-wrap;font-size:13px;line-height:1.7;">{{ $post->content }}</div>
                            @if($post->image_url)
                                <div style="margin-top:6px;"><img src="{{ $post->image_url }}" style="max-width:200px;border-radius:6px;"></div>
                            @endif
                            @if($post->comments->count() > 0)
                                <div style="margin-top:8px;padding:8px 12px;background:#f0f0f0;border-radius:6px;font-size:12px;">
                                    <strong>{{ $post->comments->count() }} kommentarer:</strong>
                                    @foreach($post->comments->take(3) as $comment)
                                        <div style="margin-top:4px;padding-left:8px;border-left:2px solid #ddd;">
                                            <strong>{{ $comment->user->first_name ?? 'Ukjent' }}:</strong> {{ Str::limit($comment->body, 100) }}
                                        </div>
                                    @endforeach
                                    @if($post->comments->count() > 3)
                                        <div style="margin-top:4px;color:#888;">+ {{ $post->comments->count() - 3 }} til</div>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>{{ $post->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if($post->pinned)
                                <span class="label label-warning"><i class="fa fa-thumb-tack"></i></span>
                            @endif
                        </td>
                        <td>
                            @if(($post->status ?? '') === 'draft')
                                <form action="{{ route('admin.community.posts.publish', $post->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success" title="Publiser"><i class="fa fa-check"></i> Publiser</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.community.posts.toggle-pin', $post->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-xs {{ $post->pinned ? 'btn-default' : 'btn-warning' }}" title="{{ $post->pinned ? 'Løsne' : 'Fest' }}">
                                    <i class="fa fa-thumb-tack"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.community.posts.destroy', $post->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Slette?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Ingen innlegg ennå.</td></tr>
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

// Stock image from Picsum (reliable, always works)
function fetchUnsplashImage() {
    var random = Math.floor(Math.random() * 500);
    var imgUrl = 'https://picsum.photos/seed/' + random + '/800/400';
    document.getElementById('preview-img').src = imgUrl;
    document.getElementById('image-url-input').value = imgUrl;
    document.getElementById('image-preview').style.display = 'block';
}

// AI image generation via Pollinations.ai (free, no API key)
function generateAiImage() {
    var content = document.getElementById('bot-post-content').value;
    if (!content) { alert('Skriv innholdet først'); return; }
    var prompt = content.substring(0, 200).replace(/[^\w\sæøåÆØÅ]/g, '') + ', beautiful illustration, warm colors, cozy writing atmosphere';
    var imgUrl = 'https://image.pollinations.ai/prompt/' + encodeURIComponent(prompt) + '?width=800&height=400&nologo=true';
    document.getElementById('preview-img').src = imgUrl;
    document.getElementById('image-url-input').value = imgUrl;
    document.getElementById('image-preview').style.display = 'block';
}

function clearImage() {
    document.getElementById('image-preview').style.display = 'none';
    document.getElementById('preview-img').src = '';
    document.getElementById('image-url-input').value = '';
}
</script>
@stop
